<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2017 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Install Script file of JEA component
 *
 * @since  2.0
 */
class Com_JeaInstallerScript
{
	/**
	 * Constructor
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 */
	public function __construct(JAdapterInstance $adapter)
	{
	}

	/**
	 * Called on installation
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function install(JAdapterInstance $adapter)
	{
		return true;
	}

	/**
	 * Called on uninstallation
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return void
	 */
	public function uninstall(JAdapterInstance $adapter)
	{
	}

	/**
	 * Called on update
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function update (JAdapterInstance $adapter)
	{
		return true;
	}

	/**
	 * Called before any type of action
	 *
	 * @param   string            $route    Which action is happening (install|uninstall|discover_install|update)
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function preflight($route, JAdapterInstance $adapter)
	{
		$manifest = $adapter->getParent()->getManifest();

		// Fix the missing schema upddate in the previous JEA 2.0 version
		if ($route == 'update')
		{
			$row = JTable::getInstance('extension');

			$eid = $row->find(array('element' => 'com_jea', 'type' => 'component'));

			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('version_id ');
			$query->from('#__schemas');
			$query->where($query->qn('extension_id') . ' = ' . (int) $eid);
			$db->setQuery($query);
			$result = $db->loadResult();

			if (! $result)
			{
				$query->insert('#__schemas');
				$query->columns('extension_id, version_id');
				$query->values("$eid, '2.0'");
				$db->setQuery($query);
				$db->query();
			}
		}
	}

	/**
	 * Called after any type of action
	 *
	 * @param   string            $route    Which action is happening (install|uninstall|discover_install|update)
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function postflight($route, JAdapterInstance $adapter)
	{
		return true;
	}
}
