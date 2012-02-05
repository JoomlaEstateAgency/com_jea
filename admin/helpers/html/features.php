<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 * 
 * @version     $Id: properties.php 237 2011-07-26 21:11:56Z ilhooq $
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
defined('_JEXEC') or die;

/**
 * @package		Joomla.Administrator
 * @subpackage	com_jea
 */
abstract class JHtmlFeatures
{
    
    static public function types($value=0, $name='type_id', $attr='')
	{
		return self::_getHTMLSelectList($value, $name, 'Property type', $attr, 'types', '', 'f.ordering');
	}

    static public function departments($value=0, $name='department_id', $attr='')
	{
		return self::_getHTMLSelectList($value, $name, 'Department', $attr, 'departments');
	}
	
    static public function towns($value=0, $name, $department_id=null, $attr='')
	{
		$condition = '';
		
	    if ($department_id !== null) {
    	    // Potentially Too much results so this will give en empty result
            $condition = 'department_id = -1';

    		if ($department_id > 0) {
    		    $condition = 'department_id ='. intval($department_id);
    		}
		}
		
		
		return self::_getHTMLSelectList($value, $name, 'Town', $attr, 'towns', $condition );
	}
	
	
	static private function _getHTMLSelectList($value=0, $name='', $defaultOptionLabel='- Select -', $attr='', $featureTable='', $conditions=null, $ordering='f.value asc' )
	{
	    $db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('f.id , f.value');
		$query->from('#__jea_'. $featureTable . ' AS f');
		
		if (!empty($conditions)) {
    		if (is_string($conditions)) {
    		    $query->where($conditions);
    		} elseif (is_array($conditions)) {
    		    foreach ($conditions as $condition) {
    		        $query->where($condition);
    		    }
    		}
		}

		$query->order($ordering);
		$db->setQuery($query);
		$items = $db->loadObjectList();

		// Assemble the list options.
		$options = array();
		$options[] = JHTML::_('select.option', '0', '- ' . JText::_( $defaultOptionLabel ).' -' );

		foreach ($items as &$item) {
			$options[] = JHtml::_('select.option', $item->id, $item->value);
		}
		
		return JHTML::_(
			'select.genericlist', 
			$options, 
			$name, 
			'class="inputbox" size="1" '. $attr, 
			'value', 
			'text', 
			$value 
		);
	}
	
	
}
