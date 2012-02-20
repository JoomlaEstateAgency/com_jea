<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 * 
 * @version     $Id$
 * @package		Jea.admin
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

jimport('joomla.application.component.controller');

class JeaControllerAjax extends JController
{

    public function get_areas()
    {
    	$response = false;

    	// Require town id
    	if ($town_id = JRequest::getInt('town_id', 0)) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('f.id , f.value');
            $query->from('#__jea_areas AS f');
    		$query->where('town_id='. $town_id);
    		$db->setQuery($query);
    		$response = $db->loadObjectList();
    	}
    	
    	echo json_encode($response);
    }

	public function get_towns()
    {
    	$response = false;
    	
    	// Require department id
    	if ($department_id = JRequest::getInt('department_id', 0)) {
    	    $db = JFactory::getDbo();
            $query = $db->getQuery(true);
	    	$query->select('f.id , f.value');
            $query->from('#__jea_towns AS f');
        	$query->where('department_id='. $department_id);
            $db->setQuery($query);
            $response = $db->loadObjectList();
    	}
    	
    	echo json_encode($response);
    }

    function getCoordinates()
    {
    	$response = false;
    	
    	if($id = JRequest::getInt('id', 0)) {
    	     $db =& JFactory::getDbo();
    		$query = 'SELECT latitude, longitude FROM #__jea_properties'
    		       . ' WHERE id='. intval($id) ;
	    	$db->setQuery($query);
	    	$response = $db->loadObject();
    	}
    	
    	echo json_encode($response);
    }
}
