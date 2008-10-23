<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

function com_install()
{
	addAclAroGroup('Jea Agent');
}


function addAclAroGroup($newName, $parent_name='Registered')
{	
	$db =& JFactory::getDBO();
	
	if(empty($newName)) return;
	
	$new_Name = $db->Quote($newName);
	$parent_name = $db->Quote($parent_name);
	
	// Select the parent node to insert after
	$db->setQuery('SELECT id,lft,rgt' . PHP_EOL
	              . 'FROM #__core_acl_aro_groups' . PHP_EOL
	              . 'WHERE name ='. $parent_name
	              );
	              
	$parent = $db->loadObject();
	
	//Make room for the new node
	$db->setQuery('UPDATE #__core_acl_aro_groups SET rgt=rgt+2' . PHP_EOL
	              .'WHERE rgt>=' . $parent->rgt );
	$db->query();
	
	$db->setQuery('UPDATE #__core_acl_aro_groups SET lft=lft+2' . PHP_EOL
	              .'WHERE lft>=' . $parent->rgt );
	$db->query();
	
	//Insert the new node
	$sql = 'INSERT INTO #__core_acl_aro_groups (parent_id,name,lft,rgt,value)' . PHP_EOL
           ."VALUES ({$parent->id}, {$new_Name}, {$parent->rgt}, {$parent->rgt}+1,{$new_Name })";
	
    $db->setQuery( $sql );
    $db->query();
	return;
}