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
            $form->removeField('rate_frequency');
        } elseif ($item->transaction_type == 'RENTING') {
            $form->setFieldAttribute('price', 'label', 'Rent');
        }

        // Check for existing article.
        // Modify the form based on Edit State access controls.
        if ($id != 0 && (!$user->authorise('core.edit.state', 'com_jea.property.'.(int) $id))
        || ($id == 0 && !$user->authorise('core.edit.state', 'com_jea'))
        )
        {
            // Disable fields for display.
            $form->setFieldAttribute('featured', 'disabled', 'true');
            $form->setFieldAttribute('published', 'disabled', 'true');

            // Disable fields while saving.
            // The controller has already verified this is an article you can edit.
            $form->setFieldAttribute('featured', 'filter', 'unset');
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
        // Alter the title for save as copy
        if (JRequest::getVar('task') == 'save2copy') {
            $data['ref']   = JString::increment($data['ref']); 
		    $data['title'] = JString::increment($data['title']); 
		    $data['alias'] = JString::increment($data['alias'], 'dash');
        }
        
        if (empty($data['images'])) {
            // Set a default empty json array
            $data['images'] = '[]';
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

        if (JFolder::exists($uploadDir)) {
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

    }
    
	/**
     * Method to toggle the featured setting of properties.
     *
     * @param    array    The ids of the items to toggle.
     * @param    int        The value to toggle to.
     *
     * @return    boolean    True on success.
     */
    public function featured($pks, $value = 0)
    {
        // Sanitize the ids.
        $pks = (array) $pks;
        JArrayHelper::toInteger($pks);

        if (empty($pks)) {
            $this->setError(JText::_('COM_JEA_NO_ITEM_SELECTED'));
            return false;
        }

        try {
            $db = $this->getDbo();
            $db->setQuery('UPDATE #__jea_properties' .
                ' SET featured = '.(int) $value.
                ' WHERE id IN ('.implode(',', $pks).')'
            );
            if (!$db->query()) {
                throw new Exception($db->getErrorMsg());
            }
            return true;

        } catch (Exception $e) {
            $this->setError($e->getMessage());
        }

        return false;
    }
    
    public function copy($pks)
	{
		// Sanitize the ids.
        $pks = (array) $pks;
        JArrayHelper::toInteger($pks);

		$table = $this->getTable();
		$nextOrdering = $table->getNextOrder();
		
		//only one request
		$inserts = array();
		$fields = $table->getProperties();
		$db = $this->getDbo();
		
		unset($fields['id']);
		unset($fields['checked_out']);
		unset($fields['checked_out_time']);
		
		$fields = array_keys($fields);
		$query = 'SELECT '.implode(', ', $fields).' FROM #__jea_properties WHERE id IN (' .implode(',', $pks). ')';
		$rows = $this->_getList($query);
		
		foreach ($rows as $row){
		    $row = (array) $row;
		    $row['ref'] = JString::increment($row['ref']); 
		    $row['title'] = JString::increment($row['title']); 
		    $row['alias'] = JString::increment($row['alias'], 'dash');
		    $row['ordering'] = $nextOrdering;
		    $row['created']  = date('Y-m-d H:i:s');
		    foreach($row as $k => $values) {
		        $row[$k] = $db->Quote($values);
		    }
		    $inserts[]= '(' . implode(', ', $row) . ')';
		    $nextOrdering++;
		}
		
		$query = 'INSERT INTO #__jea_properties ('.implode(', ', $fields).') VALUES' . "\n"
		       . implode(", \n", $inserts);
		       
		 try {
		    $db->setQuery($query);
		
    	    if (!$db->query()) {
    	        
    			 throw new Exception($db->getErrorMsg());
    		}
    	    return true;
    		
		} catch (Exception $e) {
            $this->setError($e->getMessage());
        }
		
		return false;
	}
	
	


    /**
     * Method to get the data that should be injected in the form.
     *
     * @return	mixed	The data for the form.
     * @since	1.6
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data. See JControllerForm::save()
		$data = JFactory::getApplication()->getUserState('com_jea.edit.property.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}
		
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


