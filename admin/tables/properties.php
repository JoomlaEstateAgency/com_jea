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
defined('_JEXEC') or die('Restricted access');

class TableProperties extends JTable
{
    /**
     * Constructor
     * @param	JDatabase	A database connector object
     */
    public function __construct(&$db)
    {
        $dispatcher = JDispatcher::getInstance();
        JPluginHelper::importPlugin( 'jea' );

        parent::__construct('#__jea_properties', 'id', $db);

        $dispatcher->trigger('onInitTableProperty', array($this));
    }

    /**
     * Method to compute the default name of the asset.
     * The default name is in the form table_name.id
     * where id is the value of the primary key of the table.
     *
     * @return  string
     */
    protected function _getAssetName()
    {
        $k = $this->_tbl_key;
        return 'com_jea.property.' . (int) $this->$k;
    }

    /**
     * Method to return the title to use for the asset table.
     *
     * @return  string
     */
    protected function _getAssetTitle()
    {
        return $this->title;
    }
    
	/**
	 * Get the parent asset id for the record
	 * 
	 * @param   JTable   $table  A JTable object for the asset parent.
	 * @param   integer  $id     Id to look up
	 *
	 * @return	int
	 */
	protected function _getAssetParentId($table = null, $id = null)
	{
		$asset = JTable::getInstance('Asset');
		$asset->loadByName('com_jea');
		return $asset->id;
	}
	
	/**
	 * Overloaded bind function
	 *
	 * @param   array  $array   Named array
	 * @param   mixed  $ignore  An optional array or space separated list of properties
	 * to ignore while binding.
	 *
	 * @return  mixed  Null if operation was satisfactory, otherwise returns an error string
	 *
	 * @see     JTable::bind
	 */
	public function bind($array, $ignore = '')
	{
	    // Bind the images.
	    if (isset($array['images']) && is_array($array['images'])) {
            $array['images'] = json_encode($array['images']);
        }
	    
	    // Bind the rules.
		if (isset($array['rules']) && is_array($array['rules']))
		{
			$rules = new JAccessRules($array['rules']);
			$this->setRules($rules);
		}

		return parent::bind($array, $ignore);
	}



    public function check()
    {
        if (empty( $this->type_id)) {
            $this->setError( JText::_('Select a type of property') );
            return false;

        }

        // Alias cleanup
        if(empty($this->alias)) {
            $this->alias = $this->title;
        }

        $this->alias = JFilterOutput::stringURLSafe($this->alias);

        //serialize amenities
        if ( !empty($this->amenities) && is_array($this->amenities) ) {

            //Sort in order to find easily property amenities in sql where clause
            sort($this->amenities);
            $this->amenities = '-'. implode('-' , $this->amenities ) . '-';

        } else {
            $this->amenities = '';
        }

        //check availability

        if ( ! preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}/', trim( $this->availability ) ) ){
            $this->availability = '0000-00-00';
        }

        // Clean description for xhtml transitional compliance
        $this->description = str_replace( '<br>', '<br />', $this->description );

        //For new insertion
        if (empty($this->id)) {
            $user = JFactory::getUser();
            //Save ordering at the end
            $where =  'is_renting=' . (int) $this->is_renting ;
            $this->ordering = $this->getNextOrder( $where );
            $this->created = date('Y-m-d H:i:s');
            $this->created_by = $user->get('id');
        } else {
            $this->modified = date('Y-m-d H:i:s');
        }

        return true;
    }




    /**
     * Method to delete a row from the database table by primary key value.
     *
     * @param   mixed  $pk
     *
     * @see JTable::delete()
     * @return  boolean  True on success.
     *
     */
    public function delete($pk = null)
    {
        $name = $this->_getAssetName();
        $asset = JTable::getInstance('Asset');
        if (!$asset->loadByName($name)) {
            $this->_trackAssets = false;
        }

        return parent::delete($pk);
    }



}