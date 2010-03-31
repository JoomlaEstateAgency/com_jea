<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 * 
 * @version     $Id $
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

require JPATH_COMPONENT_ADMINISTRATOR . DS. 'library' . DS .'JSON.php' ;

class JeaControllerAjax extends JController
{
	/**
	 * Custom Constructor
	 */
	function __construct( $default = array() )
	{
		$document = & JFactory::getDocument();
		$doc = &JDocument::getInstance('raw');
		$document = $doc;
		$document->setMimeEncoding('application/json') ;
		parent::__construct( $default );
	}
    
    function get_areas()
    {
    	$db =& JFactory::getDBO();
    	$where = '';
    	
    	if($town_id = JRequest::getInt('town_id', 0)) {
    		$where = ' WHERE town_id='. $town_id ;
    	}
    	
    	$query = 'SELECT * FROM #__jea_areas' . $where
    	       . ' ORDER BY `value`';
    	$db->setQuery($query);
    	$response = $db->loadObjectList();
    	
    	$this->_output($response);
    }
    
	function get_towns()
    {
    	$db =& JFactory::getDBO();
    	$response = false;
    	
    	if($department_id = JRequest::getInt('department_id', 0)) {
    		// Require department id
    		$query = 'SELECT * FROM #__jea_towns'
    		       . ' WHERE department_id='. $department_id
    	           . ' ORDER BY `value`';
	    	$db->setQuery($query);
	    	$response = $db->loadObjectList();
    	}
    	
    	$this->_output($response);
    }
    
    function _output($response)
    {
    	$jsonService = new Services_JSON();
        echo $jsonService->encode($response);
    }
    
	
	
}
