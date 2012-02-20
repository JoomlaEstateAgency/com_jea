<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @version     $Id$
 * @package		Jea.admin
 * @copyright	Copyright (C) 2008 PHILIP Sylvain. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla Estate Agency is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses.
 *
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.modeladmin');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'upload.php';

class JeaModelProperty extends JModelAdmin
{
    /**
     * Method to get the record form.
     *
     * @param	array	$data		Data for the form.
     * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
     *
     * @return	mixed	A JForm object on success, false on failure
     * @since	1.6
     */
    public function getForm($data = array(), $loadData = true)
    {

        $form = $this->loadForm('com_jea.property', 'property', array('control' => 'jform', 'load_data' => $loadData));

        if (empty($form)) {
            return false;
        }

        $jinput = JFactory::getApplication()->input;
        $id =  $jinput->get('id', 0);
        $user = JFactory::getUser();
        $item = $this->getItem($id);

        // Remove deposit field if transaction type is not SELLING
        if (empty($item->transaction_type) || $item->transaction_type == 'SELLING') {
            $form->removeField('deposit');
        } elseif ($item->transaction_type == 'RENTING') {
            $form->setFieldAttribute('price', 'label', 'Rent');
        }

        // Check for existing article.
        // Modify the form based on Edit State access controls.
        if ($id != 0 && (!$user->authorise('core.edit.state', 'com_jea.propery.'.(int) $id))
        || ($id == 0 && !$user->authorise('core.edit.state', 'com_jea'))
        )
        {
            // Disable fields for display.
            $form->setFieldAttribute('emphasis', 'disabled', 'true');
            $form->setFieldAttribute('published', 'disabled', 'true');

            // Disable fields while saving.
            // The controller has already verified this is an article you can edit.
            $form->setFieldAttribute('emphasis', 'filter', 'unset');
            $form->setFieldAttribute('published', 'filter', 'unset');

        }

        return $form;
    }


    /**
     * Method to save the form data.
     *
     * @param	array	The form data.
     *
     * @return	boolean	True on success.
     * @since	1.6
     */
    public function save($data)
    {
        if (isset($data['images']) && is_array($data['images'])) {
            $data['images'] = json_encode($data['images']);
        } else {
            $empty = array();
            $data['images'] = json_encode($empty);
        }

        // Alter the title for save as copy
        if (JRequest::getVar('task') == 'save2copy') {
            // $data['title']	= $title;
            // $data['alias']	= $alias;
        }

        if (parent::save($data)) {
            $this->processImages();
            return true;
        }

        return false;
    }


    public function processImages()
    {
        $upload = JeaUpload::getUpload('newimages');
        $item = $this->getItem();
        $images = json_decode($item->images);
        if (empty($images)) {
            $images = array();
        }
        $imageNames = array();
        foreach ($images as $row) {
            $imageNames[$row->name] = $row->name;
        }
         
        $baseUploadDir = JPATH_ROOT.DS.'images'.DS.'com_jea'.DS.'images' ;
        $validExtensions = array('jpg','JPG','jpeg','JPEG','gif','GIF','png','PNG') ;

        if (!JFolder::exists($baseUploadDir)) {
            JFolder::create($baseUploadDir);
        }

        $uploadDir = $baseUploadDir . DS . $item->id;

        /*
         $params = JComponentHelper::getParams('com_jea');
         $maxPreviewWidth = $params->get('max_previews_width', 400) ;
         $maxPreviewHeight = $params->get('max_previews_height', 400) ;
         $maxThumbnailWidth = $params->get('max_thumbnails_width', 120);
         $maxThumbnailHeight = $params->get('max_thumbnails_height', 90);
         $jpgQuality = $params->get( 'jpg_quality' , 90) ;
         $cropThumbnails = $params->get( 'crop_thumbnails' , 0) ;
         */


        if (is_array($upload)) {
            foreach ($upload as $file) {
                if ($file->isPosted()) {
                    $file->setValidExtensions($validExtensions)->nameToSafe();
                     
                    if (!JFolder::exists($uploadDir)) {
                        JFolder::create($uploadDir);
                    }

                    if ($file->moveTo($uploadDir)) {

                        if (!isset($imageNames[$file->name])) {
                            $image = new stdClass();
                            $image->name = $file->name;
                            $image->title = '';
                            $image->description = '';
                            $images[] = $image;
                            // Update the list of image names
                            $imageNames[$image->name] = $image->name;
                        }

                    } else {
                        $errors = $file->getErrors();
                        foreach ($errors as $error) {
                            $this->setError($error);
                        }
                    }
                }
            }

            $table = $this->getTable();
            $table->load($item->id);
            $table->images = json_encode($images);
            $table->store();
        }

        
        // Remove image files
        $list = JFolder::files($uploadDir);
        foreach ($list as $filename) {
            
            if (strpos($filename, 'thumb') === 0) {
                continue;
            }
            
            if (!isset($imageNames[$filename])) {
                $removeList = JFolder::files($uploadDir, $filename.'$', false, true);
                foreach ($removeList as $removeFile) {
                    JFile::delete($removeFile);
                }
            }
        }

    }


    /**
     * Method to get the data that should be injected in the form.
     *
     * @return	mixed	The data for the form.
     * @since	1.6
     */
    protected function loadFormData()
    {
        $data = $this->getItem();

        return $data;
    }


    /**
     * A protected method to get a set of ordering conditions.
     *
     * @param	object	A record object.
     *
     * @return	array	An array of conditions to add to add to ordering queries.
     */
    protected function getReorderConditions($table)
    {
        $condition = array();
        $condition[] = 'transaction_type = '. $table->transaction_type;
		return $condition;
	}

	
	/**
	 * Proxy for getTable.
	 *
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 *
	 * @return  JTable  A JTable object
	 *
	 */
	public function getTable($name = 'properties', $prefix = 'Table', $options = array())
	{
		return parent::getTable($name, $prefix, $options);
	}
     
}


