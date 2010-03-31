<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 * 
 * @version     $Id$
 * @package		Jea
 * @copyright	Copyright (C) 2008 PHILIP Sylvain. All rights reserved.
 * @license		GNU/GPL, see LICENSE.txt
 * Joomla Estate Agency is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses.
 * 
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class ComJea_Uninstall
{

    function removeAclAroGroup($groupName)
    {	
    	$db =& JFactory::getDBO();
    	$db->debug(1);
    	if(empty($groupName)) return;
    	
    	$groupName= $db->Quote($groupName);
    	
    	// Select the node
    	$db->setQuery('SELECT id,lft,rgt' . PHP_EOL
    	              . 'FROM #__core_acl_aro_groups' . PHP_EOL
    	              . 'WHERE name ='. $groupName
    	              );
    	              
    	$group = $db->loadObject();
    	
    	//Delete the node
    	$sql = 'DELETE FROM #__core_acl_aro_groups WHERE id=' . $group->id;
        $db->setQuery( $sql );
        $db->query();
    	
    	//Update other nodes
    	$db->setQuery('UPDATE #__core_acl_aro_groups SET rgt=rgt-2' . PHP_EOL
    	              .'WHERE rgt>=' . $group->rgt );
    	$db->query();
    	
    	$db->setQuery('UPDATE #__core_acl_aro_groups SET lft=lft-2' . PHP_EOL
    	              .'WHERE lft>=' . $group->rgt );
    	$db->query();
    
    	return;
    }
}

function com_uninstall()
{
	ComJea_Uninstall::removeAclAroGroup('Jea Agent');
}
