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

jimport('joomla.application.component.controller');
require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'jea.php';

/**
 * Properties Ajax controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 */
class JeaControllerProperties extends JController
{

	public function updateFeature()
	{
		$response = false;
		
		$jinput = JFactory::getApplication()->input;
		$featName = $jinput->get('feature', null,'alnum');
		if (!is_null($featName)) {
			$features = JeaHelper::getFeatures();
			if (array_key_exists($featName, $features)) {
				$feature = $features[$featName];
				if (!$language = $jinput->get('language', '*', 'string')) {
					$language = '*';
				}
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->select('f.id , f.value');
				$query->from($feature->table.' AS f');
				$query->where('language='. $db->quote($language));
				$db->setQuery($query);
				$response = $db->loadObjectList();
			}
		}
	
		echo json_encode($response);
	}
}
