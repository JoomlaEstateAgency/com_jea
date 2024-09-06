<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Site
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2020 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

defined('_JEXEC') or die;

JLoader::register('JeaUpload', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/upload.php');

$input = Factory::getApplication()->input;

$task = $input->getCmd('task');

if (empty($task))
{
	// Set 'controllers/default.php' as default controller and 'display' as default method
	$input->set('task', 'default.display');
}
elseif (strpos($task, '.') === false)
{
	$input->set('task', 'default.' . $task);
}

if (!in_array($input->getCmd('view'), ['properties', 'property', 'form']))
{
	// A workaround while waiting to make a real router for JEA
	$input->set('view', 'properties');
}

$controller = BaseController::getInstance('jea');
$controller->execute($input->getCmd('task'));
$controller->redirect();
