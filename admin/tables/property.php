<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2017 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Property table class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @since       3.4
 */
class TableProperty extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  &$db  A database diver object
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__jea_properties', 'id', $db);
	}

	/**
	 * Method to compute the default name of the asset.
	 *
	 * @see JTable::_getAssetName()
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
	 *
	 * @see JTable::_getAssetTitle()
	 */
	protected function _getAssetTitle()
	{
		return $this->title;
	}

	/**
	 * Method to get the parent asset under which to register this one.
	 *
	 * @param   JTable   $table  A JTable object for the asset parent.
	 * @param   integer  $id     Id to look up
	 *
	 * @return  integer
	 *
	 * @see JTable::_getAssetParentId()
	 */
	protected function _getAssetParentId(JTable $table = null, $id = null)
	{
		$asset = JTable::getInstance('Asset');
		$asset->loadByName('com_jea');

		return $asset->id;
	}

	/**
	 * Method to bind an associative array or object to the JTableInterface instance.
	 *
	 * @param   mixed  $array   An associative array or object to bind to the JTableInterface instance.
	 * @param   mixed  $ignore  An optional array or space separated list of properties to ignore while binding.
	 *
	 * @return  boolean  True on success.
	 *
	 * @see JTable::bind()
	 */
	public function bind($array, $ignore = '')
	{
		// Bind the images.
		if (isset($array['images']) && is_array($array['images']))
		{
			$images = array();

			foreach ($array['images'] as &$image)
			{
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

	/**
	 * Method to perform sanity checks before to store in the database.
	 *
	 * @return  boolean  True if the instance is sane and able to be stored in the database.
	 *
	 * @see JTable::check()
	 */
	public function check()
	{
		if (empty($this->type_id))
		{
			$this->setError(JText::_('COM_JEA_MSG_SELECT_PROPERTY_TYPE'));

			return false;
		}

		// Check the publish down date is not earlier than publish up.
		if ($this->publish_down > $this->_db->getNullDate() && $this->publish_down < $this->publish_up)
		{
			$this->setError(JText::_('JGLOBAL_START_PUBLISH_AFTER_FINISH'));

			return false;
		}

		// Auto Generate a reference if empty
		if (empty($this->ref))
		{
			$this->ref = uniqid();
		}

		// Alias cleanup
		if (empty($this->alias))
		{
			$this->alias = $this->title;
		}

		$this->alias = JFilterOutput::stringURLSafe($this->alias);

		// Serialize amenities
		if (! empty($this->amenities) && is_array($this->amenities))
		{
			// Sort in order to find easily property amenities in sql where clause
			sort($this->amenities);

			$this->amenities = '-' . implode('-', $this->amenities) . '-';
		}
		else
		{
			$this->amenities = '';
		}

		// Check availability
		if (! preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}/', trim($this->availability)))
		{
			$this->availability = '0000-00-00';
		}

		// Clean description for xhtml transitional compliance
		$this->description = str_replace('<br>', '<br />', $this->description);

		// For new insertion
		if (empty($this->id))
		{
			$user = JFactory::getUser();
			$this->ordering = $this->getNextOrder();
			$this->created = $this->created ? $this->created : date('Y-m-d H:i:s');
			$this->created_by = $this->created_by ? $this->created_by : $user->get('id');
		}
		else
		{
			$this->modified = $this->modified ? $this->modified : date('Y-m-d H:i:s');
		}

		return true;
	}

	/**
	 * Method to delete a row from the database table by primary key value.
	 *
	 * @param   mixed  $pk  An optional primary key value to delete.
	 *
	 * @return  boolean  True on success.
	 *
	 * @see JTable::check()
	 */
	public function delete($pk = null)
	{
		$name = $this->_getAssetName();
		$asset = JTable::getInstance('Asset');

		// Force to delete even if property asset doesn't exist.
		if (! $asset->loadByName($name))
		{
			$this->_trackAssets = false;
		}

		return parent::delete($pk);
	}
}
