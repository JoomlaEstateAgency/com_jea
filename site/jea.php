<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Site
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2012 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

// Include dependancies
jimport('joomla.application.component.controller');

$input = JFactory::getApplication()->input;

if ($input->getCmd('task') == '')
{
	// In order to execute controllers/default.php as default controller
	// and display as default method
	$input->set('task', 'default.display');
}

$controller = JControllerLegacy::getInstance('jea');
$controller->execute($input->getCmd('task'));
$controller->redirect();
