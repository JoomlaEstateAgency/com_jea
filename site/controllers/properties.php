<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 * 
 * @version		0.4 2008-06
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
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class JeaControllerProperties extends JController
{
    
    /**
	 * Custom Constructor
	 */
	function __construct( $default = array() )
	{
		// Set a default view if none exists
		if ( ! JRequest::getCmd( 'view' ) ) {
			
			JRequest::setVar('view', 'properties' );
		}
		
		//clear search session if there is not a search
		if( ( JRequest::getVar( 'task' ) != 'search' ) &&  ( isset( $_SESSION['jea_search'] ) ) ) {
			unset( $_SESSION['jea_search'] );
		}
		
		//$this->addModelPath( JPATH_COMPONENT_ADMINISTRATOR.DS.'models' );
		
		
		$id = JRequest::getInt('id', 0);
		
		// Add Filter Order to router (if defined in request parameters)
		// only when we list properties
		if(!$id ) {
			$filter_order = JRequest::getCmd('filter_order');
			if ( $filter_order ) {
				$app	= &JFactory::getApplication();
				$router = &$app->getRouter();
				$router->setVar( 'filter_order', $filter_order);
			}
		}
		
		//add ACL
        $acl = & JFactory::getACL();
        $acl->addACL( 'com_jea', 'edit', 'users', 'jea agent', 'property', 'own' );
        $acl->addACL( 'com_jea', 'edit', 'users', 'manager', 'property', 'all' );
        $acl->addACL( 'com_jea', 'edit', 'users', 'administrator', 'property', 'all' );
        $acl->addACL( 'com_jea', 'edit', 'users', 'super administrator', 'property', 'all' );
		
		parent::__construct( $default );
	}
	
	function search()
	{
		$json = JRequest::getVar('json', '');
		
		if(empty($json)) {
			
			$session =& JFactory::getSession();
			
			if ( JRequest::checkToken() ) {
				$params = array(
					'Itemid'           => JRequest::getInt('Itemid', 0),
					'cat'              => JRequest::getVar('cat', ''),
					'type_id'          => JRequest::getInt('type_id', 0),
					'department_id'    => JRequest::getInt('department_id', 0),
					'town_id'          => JRequest::getInt('town_id', 0),
					'budget_min'       => JRequest::getFloat('budget_min', 0.0),
					'budget_max'       => JRequest::getFloat('budget_max', 0.0),
					'living_space_min' => JRequest::getInt('living_space_min', 0),
					'living_space_max' => JRequest::getInt('living_space_max', 0),
					'rooms_min'        => JRequest::getInt('rooms_min', 0),
					'advantages'       => JRequest::getVar('advantages', array(), '', 'array')
				);
				$session->set('params', $params, 'jea_search');
			}
			
			JRequest::set( $session->get('params', array() , 'jea_search') , 'POST');
			$this->display();
			
		} else {
			
			require JPATH_COMPONENT_ADMINISTRATOR . DS. 'library' . DS .'JSON.php' ;
			$document = & JFactory::getDocument();
			$document->setMimeEncoding('application/json') ;
			
			$jsonService = new Services_JSON(); 
			$post = $jsonService->decode($json);
			
			JRequest::set((array) $post, 'POST');
			JRequest::setVar('limit', 0);
			
			$model =& $this->getModel('Properties');
			$res = $model->getProperties();
		
			$result = array();
			$result['types'][] = array( 'value' => 0, 'text' => '- '. Jtext::_('Property type') .' -' );
			$result['towns'][]   = array( 'value' => 0, 'text' => '- '. Jtext::_('town') .' -' );
			$result['departments'][]   = array( 'value' => 0, 'text' => '- '. Jtext::_('Department') .' -' );
		
			$temp = array();
			$temp['types'] = array();
			$temp['towns'] = array();
			$temp['departments'] = array();
		
			foreach ($res['rows'] as $row){
	
			    if( $row->type_id && !isset($temp['types'][$row->type_id]) ) {
			            
			            $result['types'][] = array( 'value' => $row->type_id , 'text' =>  $row->type );
			            $temp['types'][$row->type_id] = true;
			    }
			    
			    if($row->town_id && !isset($temp['towns'][$row->town_id]) ) {
			        
			            $result['towns'][] = array( 'value' => $row->town_id , 'text' =>  $row->town );
			            $temp['towns'][$row->town_id] = true;
			    }
			    
			    if($row->department_id && !isset($temp['departments'][$row->department_id]) ) {
	
			            $result['departments'][] = array( 'value' => $row->department_id , 'text' =>  $row->department );
			            $temp['departments'][$row->department_id] = true ;
			    }
			}
			
			echo $jsonService->encode($result);
		}
		
	}
	
	function sendmail()
	{
		jimport('joomla.mail.helper');
		jimport('joomla.utilities.utility');
		$config =& JFactory::getConfig();
		
		$email = JMailHelper::cleanAddress( JRequest::getVar('email', '') );
		$name = JRequest::getVar('name', '');
		$subject = JRequest::getVar('subject', '') . ' [' .$config->getValue('fromname', '') . ']';
		$message = JRequest::getVar('e_message', '');			
		
		/*verification */
		if ( empty($name) ) {
			JError::raiseWarning( 500, JText::_( 'You must to specify your name'));
			
		} elseif ( !JMailHelper::isEmailAddress($email) ) {
			JError::raiseWarning( 500, JText::sprintf( 'Invalid email', $email ));
			
		} else {
			
			$reciptient = $config->getValue('mailfrom', '');
			$sendOk = JUtility::sendMail($email, $name, $reciptient ,$subject , $message, false);
						   
			if( $sendOk ) {
				
				$mainframe =& JFactory::getApplication();
				$mainframe->enqueueMessage(JText::_('Message successfully sent'));
				
				JRequest::setVar('name' , '');
				JRequest::setVar('subject', '');
				JRequest::setVar('email', '');
				JRequest::setVar('e_message', '');
			} else {
				JError::raiseWarning( 500, JText::_( 'SENDMAIL_ERROR_MSG'));
				
			}
		}		
		$this->display();
	}
	
	
}
