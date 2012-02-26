<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @version     $Id$
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2012 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.modeladmin');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'upload.php';

/**
 * Property model class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 */
class JeaModelProperty extends JModelAdmin
{

    /* (non-PHPdoc)
     * @see JModelForm::getForm()
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
        ) {
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


    /* (non-PHPdoc)
     * @see JModelAdmin::save()
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


    /**
     * Method to manage new uploaded images and
     * to remove non existing images from the gallery
     */
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
     * @param    int      The value to toggle to.
     * @return    boolean  True on success.
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


    /**
     * Method to copy  a set of properties.
     *
     * @param    array    The ids of the items to copy.
     * @return    boolean  True on success.
     */
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
            foreach ($row as $k => $values) {
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


    /* (non-PHPdoc)
     * @see JModelForm::loadFormData()
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


    /* (non-PHPdoc)
     * @see JModelAdmin::getReorderConditions()
     */
    protected function getReorderConditions($table)
    {
        $condition = array();
        $condition[] = 'transaction_type = '. $table->transaction_type;
        return $condition;
    }


    /* (non-PHPdoc)
     * @see JModel::getTable()
     */
    public function getTable($name = 'properties', $prefix = 'Table', $options = array())
    {
        return parent::getTable($name, $prefix, $options);
    }
}


