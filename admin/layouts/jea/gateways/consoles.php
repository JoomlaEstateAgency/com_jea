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

use Joomla\CMS\Application\AdministratorApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

$app = Factory::getApplication();
assert($app instanceof AdministratorApplication);
$am = $app->getDocument()->getWebAssetManager();
$am->useScript('jquery');
$am->useScript('bootstrap.tab');

/* @var $displayData array */

$action = $displayData['action'];

echo HTMLHelper::_('bootstrap.startTabSet', 'consoles-panel', array('active' => 'console-ajax'));

echo HTMLHelper::_('bootstrap.addTab', 'consoles-panel', 'console-ajax', Text::_('COM_JEA_' . strtoupper($action) . '_AJAX'));

echo '<div class="p-2">', LayoutHelper::render('jea.gateways.console.ajax', $displayData) , '</div>';

echo HTMLHelper::_('bootstrap.endTab');

echo HTMLHelper::_('bootstrap.addTab', 'consoles-panel', 'console-cli', Text::_('COM_JEA_' . strtoupper($action) . '_CLI'));

echo '<div class="p-2">', LayoutHelper::render('jea.gateways.console.cli', $displayData) , '</div>';

echo HTMLHelper::_('bootstrap.endTab');

echo HTMLHelper::_('bootstrap.endTabSet');
?>

