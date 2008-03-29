<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 * 
 * @version		0.1 2008-02-26
 * @package		Jea.site
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

jimport('joomla.application.component.controller');

class JeaController extends JController
{	
	
    /**
	 * Custom Constructor
	 */
	function JeaController( $default = array() )
	{
		// Set a default view if none exists
		if ( ! JRequest::getCmd( 'view' ) ) {
			
			JRequest::setVar('view', 'default' );
		}
		
		$this->addModelPath( JPATH_COMPONENT_ADMINISTRATOR.DS.'models' );
		
		parent::__construct( $default );
	}
	
	function display($tpl='')
	{
		
		
		$document =& JFactory::getDocument();

		$viewType	= $document->getType();
		$viewName	= JRequest::getCmd( 'view', $this->getName() );

		$view = & $this->getView( $viewName, $viewType, '', array( 'base_path'=>$this->_basePath));

		// Get/Create the models
		if ($model = & $this->getModel( 'PropertiesModel', 'JEA_' )) {
			// Push the default model into the view
			$view->setModel($model, true);
		}
		
		parent::display($tpl);
	}
	
	function search()
	{
		$json = JRequest::getVar('json', $jsontest);
		if(empty($json)) {
			
			$this->display();
			
		} else {
			
			require JPATH_COMPONENT_ADMINISTRATOR . DS. 'library' . DS .'JSON.php' ;
			$document = & JFactory::getDocument();
			$document->setMimeEncoding('application/json') ;
			
			$jsonService = new Services_JSON(); 
			$post = $jsonService->decode($json);
			
			$params['published'] = 1;
			$params['type_id'] = isset($post->type_id)? $post->type_id : 0;
		    $params['department_id'] = isset($post->department_id)? $post->department_id : 0;
			$params['town_id']= isset($post->town_id)? $post->town_id : 0;
			$cat = isset($post->cat)? $post->cat : 0;
			
			$model = & $this->getModel( 'PropertiesModel', 'JEA_' );
			$model->setCategory($cat);
			$res = $model->getItems($params);
		
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
			}
		}		
		$this->display();
	}
	
}
