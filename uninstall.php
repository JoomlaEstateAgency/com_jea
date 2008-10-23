<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

function com_uninstall()
{
	removeAclAroGroup('Jea Agent');
}


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