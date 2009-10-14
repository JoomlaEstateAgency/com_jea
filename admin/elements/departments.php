<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 * 
 * @version     0.9 2009-10-14
 * @package		Jea.admin
 * @copyright	Copyright (C) 2008 PHILIP Sylvain. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla Estate Agency is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses.
 * 
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class JElementDepartments extends JElement
{
	/**
	 * Element name
	 * @access	protected
	 * @var		string
	 */
	var	$_name = 'Departments';

	function fetchElement( $name, $value, &$node, $control_name )
	{
		$default       = '- ' . JText::_( 'All' ).' -' ;
		
		$sql = "SELECT `id` AS value ,`value` AS text FROM #__jea_departments ORDER BY text" ;
        
        $db =& JFactory::getDBO();
        
        $db->setQuery($sql);
        $res = $db->loadObjectList();
        if(!$res) $res = array();

		//unshift default option
		array_unshift($res, JHTML::_('select.option', '0', $default ));

	     return JHTML::_('select.genericlist', $res , $control_name.'['.$name.']', 'class="inputbox" size="1" ' , 'value', 'text', $value);
	}
}