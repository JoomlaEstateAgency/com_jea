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
defined('_JEXEC') or die('Restricted access');

/**
 * Properties table class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 */
class TableProperties extends JTable
{
    /**
     * Constructor
     * @param    Database    A database connector object
     */
    public function __construct(&$db)
    {
        parent::__construct('#__jea_properties', 'id', $db);
    }


    /* (non-PHPdoc)
     * @see JTable::_getAssetName()
     */
    protected function _getAssetName()
    {
        $k = $this->_tbl_key;
        return 'com_jea.property.' . (int) $this->$k;
    }


    /* (non-PHPdoc)
     * @see JTable::_getAssetTitle()
     */
    protected function _getAssetTitle()
    {
        return $this->title;
    }


    /* (non-PHPdoc)
     * @see JTable::_getAssetParentId()
     */
    protected function _getAssetParentId($table = null, $id = null)
    {
        $asset = JTable::getInstance('Asset');
        $asset->loadByName('com_jea');
        return $asset->id;
    }


    /* (non-PHPdoc)
     * @see JTable::bind()
     */
    public function bind($array, $ignore = '')
    {
        // Bind the images.
        if (isset($array['images']) && is_array($array['images'])) {
            $images = array();
            foreach ($array['images'] as &$image) {
                $images[] = (object) $image;
            }
            $array['images'] = json_encode($images);
        }

        // Bind the rules.
        if (isset($array['rules']) && is_array($array['rules']))
        {
            $rules = new JAccessRules($array['rules']);
            $this->setRules($rules);
        }

        return parent::bind($array, $ignore);
    }


    /* (non-PHPdoc)
     * @see JTable::check()
     */
    public function check()
    {
        if (empty( $this->type_id)) {
            $this->setError( JText::_('COM_JEA_MSG_SELECT_PROPERTY_TYPE') );
            return false;
        }

        // Check the publish down date is not earlier than publish up.
        if ($this->publish_down > $this->_db->getNullDate() && $this->publish_down < $this->publish_up) {
            $this->setError(JText::_('JGLOBAL_START_PUBLISH_AFTER_FINISH'));
            return false;
        }

        // Auto Generate a reference if empty
        if (empty($this->ref)) {
            $this->ref = uniqid();
        }

        // Alias cleanup
        if (empty($this->alias)) {
            $this->alias = $this->title;
        }

        $this->alias = JFilterOutput::stringURLSafe($this->alias);

        //serialize amenities
        if (!empty($this->amenities) && is_array($this->amenities)) {
            //Sort in order to find easily property amenities in sql where clause
            sort($this->amenities);
            $this->amenities = '-'. implode('-' , $this->amenities) . '-';

        } else {
            $this->amenities = '';
        }

        //check availability
        if (! preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}/', trim( $this->availability ))){
            $this->availability = '0000-00-00';
        }

        // Clean description for xhtml transitional compliance
        $this->description = str_replace( '<br>', '<br />', $this->description );

        //For new insertion
        if (empty($this->id)) {
            $user = JFactory::getUser();
            $this->ordering = $this->getNextOrder();
            $this->created = date('Y-m-d H:i:s');
            $this->created_by = $user->get('id');
        } else {
            $this->modified = date('Y-m-d H:i:s');
        }
        return true;
    }


    /* (non-PHPdoc)
     * @see JTable::delete()
     */
    public function delete($pk = null)
    {
        $name = $this->_getAssetName();
        $asset = JTable::getInstance('Asset');
        // Force to delete even if property asset doesn't exist.
        if (!$asset->loadByName($name)) {
            $this->_trackAssets = false;
        }

        return parent::delete($pk);
    }

}