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


class ComJea_Install {
    
    function updateJea_0_9_to_1_0()
    {
        $db =& JFactory::getDBO();
        
        $db->setQuery('SHOW COLUMNS FROM #__jea_properties');
        $cols = $db->loadObjectList('Field');
        if(!isset($cols['title']) && !isset($cols['alias'])){
            $query = 'ALTER TABLE `#__jea_properties` '
                   . 'ADD `title` VARCHAR( 255 ) NOT NULL DEFAULT \'\' AFTER `ref`, '
                   . 'ADD `alias` VARCHAR( 255 ) NOT NULL DEFAULT \'\' AFTER `title`, ' 
                   . 'ADD `hits` INT( 11 ) NOT NULL DEFAULT \'0\'';
            $db->setQuery($query);
            $db->query();
        }
        
        $query = 'UPDATE `#__jea_properties` SET `alias`=`ref` WHERE `alias`=\'\'';
        $db->setQuery($query);
        $db->query();
        
        if(isset($cols['date_insert'])) {
            $query = 'ALTER TABLE `#__jea_properties` CHANGE `date_insert` '
                   . '`date_insert` DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\'';
            $db->setQuery($query);
            $db->query();    
        }
        
        $db->setQuery('SHOW COLUMNS FROM #__jea_areas');
        $cols = $db->loadObjectList('Field');
        if(!isset($cols['town_id'])){
            $query = 'ALTER TABLE `#__jea_areas` '
                   . 'ADD `town_id` INT(11) NOT NULL DEFAULT \'0\' AFTER `value`';
            $db->setQuery($query);
            $db->query();
        }
        
        $db->setQuery('SHOW COLUMNS FROM #__jea_towns');
        $cols = $db->loadObjectList('Field');
        if(!isset($cols['department_id'])){
            $query = 'ALTER TABLE `#__jea_towns` '
                   . 'ADD `department_id` INT(11) NOT NULL DEFAULT \'0\' AFTER `value`';
            $db->setQuery($query);
            $db->query();
        }
    }
    
    function updateJea_1_0_to_1_1()
    {
        $db =& JFactory::getDBO();
        $db->setQuery('SHOW COLUMNS FROM #__jea_properties');
        $cols = $db->loadObjectList('Field');
        if(!isset($cols['latitude']) && !isset($cols['longitude'])){
            $query = 'ALTER TABLE `#__jea_properties` '
                   . 'ADD `latitude` VARCHAR( 255 ) NOT NULL DEFAULT \'0\', '
                   . 'ADD `longitude` VARCHAR( 255 ) NOT NULL DEFAULT \'0\'';
            $db->setQuery($query);
            $db->query();
        }
        
        $db->setQuery('SHOW INDEXES FROM #__jea_properties');
        $cols = $db->loadObjectList('Key_name');
        if(isset($cols['departement_id'])) {
            $db->setQuery('ALTER TABLE `#__jea_properties` DROP INDEX `departement_id`');
            $db->query();
        }
        
        if(isset($cols['ref'])) {
            $query = 'ALTER TABLE `#__jea_properties` '
                   . 'DROP INDEX `ref`, '
                   . 'ADD UNIQUE INDEX `idx_jea_ref` (`ref`)';
            $db->setQuery($query);
            $db->query();
        }
        
        if(!isset($cols['idx_jea_isrenting']) &&
           !isset($cols['idx_jea_typeid']) &&
           !isset($cols['idx_jea_departmentid']) &&
           !isset($cols['idx_jea_townid'])) {

           $query = 'ALTER TABLE `#__jea_properties` '
                   . 'ADD INDEX `idx_jea_isrenting` (`is_renting`), '
                   . 'ADD INDEX `idx_jea_typeid` (`type_id`), '
                   . 'ADD INDEX `idx_jea_departmentid` (`department_id`), '
                   . 'ADD INDEX `idx_jea_townid` (`town_id`)';
            $db->setQuery($query);
            $db->query();
               
       }
        
        
        
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
}

function com_install()
{
	ComJea_Install::updateJea_0_9_to_1_0();
	ComJea_Install::updateJea_1_0_to_1_1();
    ComJea_Install::addAclAroGroup('Jea Agent');
}

