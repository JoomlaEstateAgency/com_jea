<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 * 
 * @version     0.6 2008-12-11
 * @package		Jea.site
 * @copyright	Copyright (C) 2008 PHILIP Sylvain. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla Estate Agency is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses.
 * 
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'jea.class.php';

//add ACL
$acl = & JFactory::getACL();
$acl->addACL( 'com_jea', 'edit', 'users', 'jea agent', 'property', 'own' );
$acl->addACL( 'com_jea', 'edit', 'users', 'manager', 'property', 'all' );
$acl->addACL( 'com_jea', 'edit', 'users', 'administrator', 'property', 'all' );
$acl->addACL( 'com_jea', 'edit', 'users', 'super administrator', 'property', 'all' );

ComJea::run('properties');