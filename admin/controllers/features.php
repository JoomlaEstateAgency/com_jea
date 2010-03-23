<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 * 
 * @version     0.9 2009-10-14
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

class JeaControllerFeatures extends JController
{
    
    /**
	 * Custom Constructor
	 */
	function __construct( $default = array() )
	{
		// Set a default view if none exists
		if ( ! JRequest::getCmd( 'view' ) ) {
			
			JRequest::setVar('view', 'features' );
		}
		
		parent::__construct( $default );

		$this->registerTask( 'apply', 'save' );
		$this->registerTask( 'add', 'edit' );
	}
	
	
	function edit()
	{
		//create the view
		$view = & $this->getView('features', 'html');
		
		// Get/Create the model
		$model = & $this->getModel('Features');
		
		// Push the model into the view (as default)
		$view->setModel($model, true);
		
		$view->display('form');
	}
	
	
	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
		
		// Get/Create the model
		$model = & $this->getModel('Features');
		
		$url = 'index.php?option=com_jea&controller=features' ;
		
		if ( false ===  $model->save() ) {

		    $this->edit();
		    
		} else {

		  	if ( 'apply' == $this->getTask() ) {
		  		
		  		$row =& $model->getRow();
		  		$url = $url . '&task=edit&id=' . $row->id ;
		  	}
		  	
		  	$msg = JText::_( 'Successfully saved feature' ) ;
		    $this->setRedirect( $url , $msg );
		}
	}
	
	
	function cancel()
	{
		$this->_setDefaultRedirect();
	}
	
	
	function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
		
		$model = & $this->getModel('Features');
		
		if ( $model->remove() ) {
			
			$msg = JText::sprintf('SUCCESSFULLY REMOVED ITEMS', count($model->getCid()));
			$this->setRedirect( 'index.php?option=com_jea&controller=features', $msg );
			
		} else {
			
			$this->_setDefaultRedirect();
		}	
	}
	
	function orderdown()
	{
		$this->_order(1);
	}
	
	function orderup()
	{
		$this->_order(-1);
	}
	
	/******************************  Private functions   *****************************************/
	

	function _order( $inc )
	{
	    $model = & $this->getModel('Features');
		$row =& $model->getRow();
		$row->move( $inc );
		$this->_setDefaultRedirect();
	}
	
	function _setDefaultRedirect()
	{
		$this->setRedirect( 'index.php?option=com_jea&controller=features' );
	}
	
	
}
