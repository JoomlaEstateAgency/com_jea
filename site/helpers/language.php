<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @version     $Id$
 * @package     Joomla.Site
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2012 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.helper');

/**
 * @package     Joomla.Site
 * @subpackage  com_jea
 */
class JeaHelperLanguage {
	
	/**
	 * Checks if a feature uses language
	 * @param string $feature - feature to check (department, hotwatertype, etc.)
	 * @return boolean
	 */
	public static function featureUsesLanguage($feature) {
		$disabledFeatures = array ('department', 'town', 'area');
		if (!is_null($feature) && !in_array($feature, $disabledFeatures) ) {
			return true;
		}
		return false;
	}
	
}