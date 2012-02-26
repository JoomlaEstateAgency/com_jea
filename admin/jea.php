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

// Include dependancies
jimport('joomla.application.component.controller');

if (JRequest::getCmd('task') == '') {
    // In order to define controllers/default.php as default controller
    JRequest::setVar('task', 'default.display');
}

$controller = JController::getInstance('jea');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
