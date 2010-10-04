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

class JeaControllerFeatures extends JController
{
    
    /**
	 * Custom Constructor
	 */
	function __construct( $default = array() )
	{
		// Set a default view if none exists
		if ( ! JRequest::getCmd( 'view' ) ) {
			
			JRequest::setVar('view', 'features' );
		}
		
		parent::__construct( $default );

		$this->registerTask( 'apply', 'save' );
		$this->registerTask( 'add', 'edit' );
	}
	
	
	function edit()
	{
		//create the view
		$view = & $this->getView('features', 'html');
		
		// Get/Create the model
		$model = & $this->getModel('Features');
		
		// Push the model into the view (as default)
		$view->setModel($model, true);
		
		$view->display('form');
	}
	
	
	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
		
		// Get/Create the model
		$model = & $this->getModel('Features');
		
		$url = 'index.php?option=com_jea&controller=features' ;
		
		if ( false ===  $model->save() ) {

		    $this->edit();
		    
		} else {

		  	if ( 'apply' == $this->getTask() ) {
		  		
		  		$row =& $model->getRow();
		  		$url = $url . '&task=edit&id=' . $row->id ;
		  	}
		  	
		  	$msg = JText::_( 'Successfully saved feature' ) ;
		    $this->setRedirect( $url , $msg );
		}
	}
	
	
	function cancel()
	{
		$this->_setDefaultRedirect();
	}
	
	
	function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
		
		$model = & $this->getModel('Features');
		
		if ( $model->remove() ) {
			
			$msg = JText::sprintf('SUCCESSFULLY REMOVED ITEMS', count($model->getCid()));
			$this->setRedirect( 'index.php?option=com_jea&controller=features', $msg );
			
		} else {
			
			$this->_setDefaultRedirect();
		}	
	}
	
	function orderdown()
	{
		$this->_order(1);
	}
	
	function orderup()
	{
		$this->_order(-1);
	}
	
	function export()
	{
	    $tables = JRequest::getVar( 'export_table', array(), 'post', 'array' );

	    if(!empty($tables)) {
	        jimport('joomla.filesystem.folder');
	        jimport('joomla.filesystem.file');
	        jimport('joomla.filesystem.archive');
	        
	        $config =& JFactory::getConfig();
            $tmp_path = $config->getValue('config.tmp_path');
            $model = & $this->getModel('Features');
	        
	        if(JFolder::create($tmp_path.DS.'jea_export') == false) {
	            $this->_setDefaultRedirect();
	            return;
	        }
	        $files = array();
    	    foreach($tables as $table) {
    	       $csVdata = $model->getCSVData($table);
    	       $file = $table.'.csv';
    	       JFile::write($tmp_path.DS.'jea_export'.DS. $file, $csVdata );
    	       $files[] = array('data' => $csVdata, 'name' => $file);
    	    }
    	    
    	    $zipFile = $tmp_path . DS . 'jea_export_'.uniqid().'.zip';
    	    $zip =& JArchive::getAdapter('zip');
    	    $zip->create($zipFile, $files);
    	    
    	    $document = & JFactory::getDocument();
    		$doc = &JDocument::getInstance('raw');
    		$document = $doc;
    		$document->setMimeEncoding('application/zip') ;
    		JResponse::setHeader( 'Content-Disposition', 'attachment; filename="jea_features.zip"');
    		JResponse::setHeader( 'Content-Transfer-Encoding', 'binary');
    		echo readfile($zipFile);
    	    
    		//clean tmp files
    		JFile::delete($zipFile);
    		JFolder::delete($tmp_path.DS.'jea_export');
    	    
	    } else {
	        JRequest::setVar('layout', 'export');
	        $this->display();
	    }
	}
	
	function import()
	{
	    $files = JRequest::get('files');

	    if(!empty($files)) {
	        require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'library/Http_File.php';
    	    jimport('joomla.filesystem.folder');
	        jimport('joomla.filesystem.file');
	        $config =& JFactory::getConfig();
            $tmp_path = $config->getValue('config.tmp_path');
            $tablePrefix = $config->getValue('config.dbprefix');
            $model = & $this->getModel('Features');
            $application =& JFactory::getApplication();
	        $importDirectory = $tmp_path . DS . 'jea_import';
	        
	        if(JFolder::create($importDirectory) == false) {
	            $this->_setDefaultRedirect();
	            return;
	        }
    	    
            foreach ($files as $tableName => $FileInfos) {
                $file = new Http_File($FileInfos);
                if ( $file->isPosted() ){
                    $file->setValidExtensions(array('CSV','csv','txt','TXT'));
                    $file->setName($tableName.'.csv');
            	
                    if (!$file->moveTo($importDirectory)) {
                        JError::raiseWarning( 200, JText::_( $file->getError() ) );
                        continue;
                    }
                    
                    $lines = $model->importFromCSV($importDirectory.DS.$tableName.'.csv', $tableName);
                    
                    $tableName = $tablePrefix.'jea_'.$tableName;
                    $msg = JText::sprintf('Num lines imported on table', $lines, $tableName);
                    $application->enqueueMessage($msg);
                }
            }

            // Clean tmp path
            JFolder::delete($importDirectory);
            $this->_setDefaultRedirect();
    	    
	    } else {
	        JRequest::setVar('layout', 'import');
	        $this->display();
	    }
	}
	
	/******************************  Private functions   *****************************************/
	

	function _order( $inc )
	{
	    $model = & $this->getModel('Features');
		$row =& $model->getRow();
		$row->move( $inc );
		$this->_setDefaultRedirect();
	}
	
	function _setDefaultRedirect($msg = null)
	{
		$this->setRedirect( 'index.php?option=com_jea&controller=features', $msg );
	}
	
	
}
