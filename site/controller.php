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
	
	function display($tpl='')
	{
		// Set a default view if none exists
		if ( ! JRequest::getCmd( 'view' ) ) {
			
			JRequest::setVar('view', 'default' );
		}
		
		$this->addModelPath( JPATH_COMPONENT_ADMINISTRATOR.DS.'models' );
		
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
		$this->display();
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
