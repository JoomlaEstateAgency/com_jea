<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2012 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die();

jimport('joomla.application.component.controller');

/**
 * Features controller class for AJAX requests.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @since       2.0
 */
class JeaControllerFeatures extends JControllerLegacy
{
	/**
	 * Get list of areas in relation with a town
	 *
	 * @return void
	 */
	public function get_areas ()
	{
		$response = false;

		// Require town id
		if ($town_id = JRequest::getInt('town_id', 0))
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('f.id , f.value');
			$query->from('#__jea_areas AS f');
			$query->where('town_id=' . $town_id);
			$query->order('f.value ASC');
			$db->setQuery($query);
			$response = $db->loadObjectList();
		}

		echo json_encode($response);
	}

	/**
	 * Get list of towns in relation with a department
	 *
	 * @return void
	 */
	public function get_towns ()
	{
		$response = false;

		// Require department id
		if ($department_id = JRequest::getInt('department_id', 0))
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('f.id , f.value');
			$query->from('#__jea_towns AS f');
			$query->where('department_id=' . $department_id);
			$query->order('f.value ASC');
			$db->setQuery($query);
			$response = $db->loadObjectList();
		}

		echo json_encode($response);
	}

	/**
	 * Get a feature list filtered by language
	 *
	 * @return void
	 */
	public function get_list ()
	{
		$response = false;

		$jinput = JFactory::getApplication()->input;
		$featName = $jinput->get('feature', null, 'alnum');

		if (! is_null($featName))
		{
			$model = $this->getModel('Features', 'JeaModel');
			$features = $model->getItems();

			if (isset($features[$featName]))
			{
				if (! $language = $jinput->get('language', '*', 'string'))
				{
					$language = '*';
				}

				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->select('f.id , f.value');
				$query->from($db->quoteName($features[$featName]->table) . ' AS f');

				if ($language != '*')
				{
					$query->where('f.language=' . $db->quote($language) . 'OR f.language=\'*\'');
				}

				$query->order('f.value ASC');
				$db->setQuery($query);
				$response = $db->loadObjectList();
			}
		}

		echo json_encode($response);
	}
}
