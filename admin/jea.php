<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 * 
 * @version     $Id$
 * @package		Jea.admin
 * @copyright	Copyright (C) 2008 PHILIP Sylvain. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla Estate Agency is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses.
 * 
 */


// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Include dependancies
jimport('joomla.application.component.controller');

if (JRequest::getCmd('task') == '') {
    // In order to define controllers/default.php as default controller
    JRequest::setVar('task', 'default.display');
}

$controller	= JController::getInstance('jea');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
