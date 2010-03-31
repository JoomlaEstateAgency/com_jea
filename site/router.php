<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 * 
 * @version     $Id: jea.php 145 2010-03-31 10:03:47Z ilhooq $
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

function JeaBuildRoute(&$query)
{
    $segments = array();
    
    if(isset($query['view'])) {
        unset( $query['view'] );
    }
    
    if(isset($query['layout'])) {
        $segments[] = $query['layout'];
        unset( $query['layout'] );
    }
    
    if(isset($query['id'])) {
        $segments[] = $query['id'];
        unset( $query['id'] );
    };
    
    return $segments;
}

function JeaParseRoute($segments)
{
    $vars = array();

    if(empty($segments)) {
        $vars['view'] = 'properties';
        return $vars;
    }

	//Get the active menu item
	$menu =& JSite::getMenu();
	$item =& $menu->getActive();

	// Count route segments
	$count = count($segments);

	//Standard routing for property
	if(!isset($item))
	{
		$vars['view']  = 'properties';
		$vars['id']    = $segments[$count-1];
		return $vars;
	}

	//Handle View and Identifier
	switch($item->query['view']) {
		case 'properties' :
			if($count == 1) {
				$vars['view']  = 'properties';
				$vars['id'] = $segments[$count-1];
			}
            break;

		case 'manage'   :
	        
		    if($count == 1) {
				$vars['view']  = 'manage';
				$vars['layout']  = $segments[$count-1];
			}
		    
		    if($count == 2) {
				$vars['view']  = 'manage';
				$vars['layout']  = $segments[$count-2];
				$vars['id'] = $segments[$count-1];
			}
            break;
	}

	return $vars;
}

