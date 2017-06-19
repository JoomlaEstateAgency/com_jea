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

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

require JPATH_COMPONENT_ADMINISTRATOR . '/tables/features.php';
require JPATH_COMPONENT_ADMINISTRATOR . '/helpers/utility.php';

/**
 * Features model class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @see         JModelLegacy
 *
 * @since       2.0
 */
class JeaModelFeatures extends JModelLegacy
{
	/**
	 * Get the list of features
	 *
	 * @return  array of stdClass objects
	 */
	public function getItems()
	{
		$xmlPath = JPATH_COMPONENT_ADMINISTRATOR . '/models/forms/features';
		$xmlFiles = JFolder::files($xmlPath);
		$items = array();

		foreach ($xmlFiles as $key => $filename)
		{
			if (preg_match('/^[0-9]{2}-([a-z]*).xml/', $filename, $matches))
			{
				$form = simplexml_load_file($xmlPath . '/' . $filename);

				// Generate object
				$feature = new stdClass;
				$feature->id = $key;
				$feature->name = $matches[1];
				$feature->table = (string) $form['table'];
				$feature->language = false;

				// Check if this feature uses language
				$lang = $form->xpath("//field[@name='language']");

				if (! empty($lang))
				{
					$feature->language = true;
				}

				$items[$feature->name] = $feature;
			}
		}

		return $items;
	}

	/**
	 * Return table data as CSV string
	 *
	 * @param   string  $tableName  The name of the feature table
	 *
	 * @return  string  CSV formatted
	 */
	public function getCSVData($tableName = '')
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')->from($db->escape($tableName));
		$db->setQuery($query);
		$rows = $db->loadRowList();
		$csv = '';

		foreach ($rows as $row)
		{
			$csv .= JeaHelperUtility::arrayToCSV($row);
		}

		return $csv;
	}

	/**
	 * Import rows from CSV file and return the number of inserted rows
	 *
	 * @param   string  $file       The file path
	 * @param   string  $tableName  The feature table name to insert csv data
	 *
	 * @return  integer  The number of inserted rows
	 */
	public function importFromCSV($file = '', $tableName = '')
	{
		$row = 0;

		if (($handle = fopen($file, 'r')) !== false)
		{
			$db = JFactory::getDbo();
			$tableName = $db->escape($tableName);
			$table = new FeaturesFactory($tableName, 'id', $db);
			$cols = array_keys($table->getProperties());
			$query = $db->getQuery(true);
			$query->select('*')->from($tableName);
			$db->setQuery($query);
			$ids = $db->loadObjectList('id');

			$query->clear();
			$query->select('ordering')
				->from($tableName)
				->order('ordering DESC');
			$db->setQuery($query);
			$maxOrdering = $db->loadResult();

			if ($maxOrdering == null)
			{
				$maxOrdering = 1;
			}

			while (($data = fgetcsv($handle, 1000, ';', '"')) !== false)
			{
				$num = count($data);
				$bind = array();

				for ($c = 0; $c < $num; $c ++)
				{
					if (isset($cols[$c]))
					{
						$bind[$cols[$c]] = $data[$c];
					}
				}

				if (isset($bind['id']) && isset($ids[$bind['id']]))
				{
					// Load row to update
					$table->load((int) $bind['id']);
				}
				elseif (isset($bind['ordering']))
				{
					$bind['ordering'] = $maxOrdering;
					$maxOrdering ++;
				}

				$table->save($bind, '', 'id');

				// To force new insertion
				$table->id = null;
				$row ++;
			}
		}

		return $row;
	}
}
