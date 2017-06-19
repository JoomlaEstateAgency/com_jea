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

$action = $displayData['action'];

echo JHtml::_('bootstrap.startTabSet', 'consoles-panel', array('active' => 'console-ajax'));

echo JHtml::_('bootstrap.addTab', 'consoles-panel', 'console-ajax', JText::_('COM_JEA_'. strtoupper($action) . '_AJAX'));

echo JLayoutHelper::render('jea.gateways.console.ajax', $displayData);

echo JHtml::_('bootstrap.endTab');

echo JHtml::_('bootstrap.addTab', 'consoles-panel', 'console-cli', JText::_('COM_JEA_'. strtoupper($action) . '_CLI'));

echo JLayoutHelper::render('jea.gateways.console.cli', $displayData);

echo JHtml::_('bootstrap.endTab');

echo JHtml::_('bootstrap.endTabSet');
?>

