<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 * 
 * @version		0.1 2008-02-26
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
defined('_JEXEC') or die('Restricted access');

class TableProperties extends JTable
{

	var $id=null;
	var $ref=null;
    var $type_id=null;
	var $is_renting = null;
	var $price=null;
	var $adress =null;
	var $town_id=null;
	var $area_id =null;
	var $zip_code =null;
	var $department_id =null;
	var $condition_id =null;
	var $living_space = null;
	var $land_space = null;
	var $rooms = null;
	var $charges = null;
	var $fees = null;
	var $hot_water_type = null;
	var $heating_type = null;
	var $bathrooms = null;
	var $toilets = null;
	var $availability = null;
	var $floor = null;
	var $advantages = null;
	var $description = null;
	var $slogan_id = null;
	var $published = null;
	var $ordering = null;
	var $emphasis = null;
	var $date_insert = null;
	
	function TableProperties(& $db) {
		
        parent::__construct('#__jea_properties', 'id', $db);
	}
	
	/**
	 * Returns an array of public properties (table columns) (joomla 1.0)
	 * @return array
	 */
	function getPublicProperties() {
		static $cache = null;
		if (is_null( $cache )) {
			$cache = array();
			foreach (get_class_vars( get_class( $this ) ) as $key=>$val) {
				if (substr( $key, 0, 1 ) != '_') {
					$cache[] = $key;
				}
			}
		}
		return $cache;
	}	
	
	
	
}