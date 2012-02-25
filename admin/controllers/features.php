<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @version     $Id$
 * @package		Jea.admin
 * @copyright	Copyright (C) 2008 PHILIP Sylvain. All rights reserved.
 * @license		GNU/GPL, see LICENSE.txt
 * Joomla Estate Agency is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses.
 *
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.archive');

require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'upload.php';


/**
 * Features list controller class.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_jea
 * @since	1.6
 */
class JeaControllerFeatures extends JController
{

    public function export()
    {
        $features = JRequest::getVar( 'cid', array(), 'post', 'array' );

        if(!empty($features)) {
             
            $config   = JFactory::getConfig();
            $exportPath = $config->getValue('tmp_path').DS.'jea_export';

            if (JFolder::create($exportPath) === false) {                   
	            $msg= JText::_('JLIB_FILESYSTEM_ERROR_FOLDER_CREATE').' : '.$exportPath;
	            $this->setRedirect('index.php?option=com_jea&view=features', $msg, 'warning');
            } else {
                 
                $xmlPath = JPATH_COMPONENT.'/models/forms/features/';
                $xmlFiles = JFolder::files($xmlPath);
                $model = $this->getModel();
                $files = array();
                
                foreach($xmlFiles as $filename) {
                    if (preg_match('/^[0-9]{2}-([a-z]*).xml/', $filename, $matches)) {
                        $feature = $matches[1];
                        if (in_array($feature, $features)) {
                            $form = simplexml_load_file($xmlPath.DS.$filename);
                            $table = (string) $form['table'];
                            $files[] = array(
                            	'data' => $model->getCSVData($table), 
                            	'name' => $table.'.csv'
                            );
                        }
                    }
                }
                
                $zipFile = $exportPath.DS.'jea_export_'.uniqid().'.zip';
                $zip = JArchive::getAdapter('zip');
                $zip->create($zipFile, $files);
                
                $document = JFactory::getDocument();
                $newDocument = JDocument::getInstance('raw');
                $newDocument->setMimeEncoding('application/zip') ;
                $document =& $newDocument;
                
                JResponse::setHeader('Content-Disposition', 'attachment; filename="jea_features.zip"');
                JResponse::setHeader('Content-Transfer-Encoding', 'binary');
                
                echo readfile($zipFile);

                // clean tmp files
                JFile::delete($zipFile);
                JFolder::delete($exportPath);
            }
            	
        } else {
            $msg= JText::_('JERROR_NO_ITEMS_SELECTED');
	        $this->setRedirect('index.php?option=com_jea&view=features', $msg);
        }
    }



    public function import()
    {
        $application = JFactory::getApplication();
        $upload = JeaUpload::getUpload('csv');
        $validExtensions = array('csv','CSV','txt','TXT') ;

        $xmlPath = JPATH_COMPONENT.'/models/forms/features/';
        $xmlFiles = JFolder::files($xmlPath);
        $model = $this->getModel();
        $tables = array();
        
        // Retrieve the table names 
        foreach($xmlFiles as $filename) {
            if (preg_match('/^[0-9]{2}-([a-z]*).xml/', $filename, $matches)) {
                $feature = $matches[1];
                if (!isset($tables[$feature])) {
                    $form = simplexml_load_file($xmlPath.DS.$filename);
                    $tables[$feature]= (string) $form['table'];
                }
            }
        }
        
        foreach ($upload as $file) {
            if ($file->isPosted() && isset($tables[$file->key])) {
                $file->setValidExtensions($validExtensions);
                $fileErrors = $file->getErrors();
                if (!$fileErrors) {
                    $rows = $model->importFromCSV($file->temp_name, $tables[$file->key]);
                    $msg = JText::sprintf('Num lines imported on table', $rows, $tables[$file->key]);
                    $application->enqueueMessage($msg);
                    $errors = $model->getErrors();
                    if ($errors) {
                        foreach ($errors as $error) {
                            $application->enqueueMessage($error, 'warning');
                        }
                    }
                } else {
                    foreach ($fileErrors as $error) {
                        $application->enqueueMessage($error, 'warning');
                    }
                }
            }
        }

        $this->setRedirect('index.php?option=com_jea&view=features');
    }


    /**
     * Proxy for getModel.
     *
     * @param	string	$name	The name of the model.
     * @param	string	$prefix	The prefix for the PHP class name.
     *
     * @return	JModel
     */
    public function getModel($name = 'Features', $prefix = 'JeaModel', $config = array())
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }


}
