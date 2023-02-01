<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2020 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

JLoader::register('JeaHelper', __DIR__ . '/helpers/jea.php');
JLoader::register('JeaUpload', __DIR__ . '/helpers/upload.php');
JLoader::register('JeaHelperUtility', __DIR__ . '/helpers/utility.php');

$input = Factory::getApplication()->getInput();

if ($input->getCmd('task') == '')
{
	// In order to execute controllers/default.php as default controller and display as default method
	$input->set('task', 'default.display');
}

$controller = JControllerLegacy::getInstance('jea');
$controller->execute($input->getCmd('task'));
$controller->redirect();
