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

class JeaControllerProperties extends JController
{
    
    /**
     * property category ( renting or selling )
     *
     * @var string $_cat
     */

    var $_cat='';
	

	/**
	 * Base controller url
	 *
	 * @var string
	 */
	var $_controllerUrl = '';
	
	/**
	 * Default controller model
	 *
	 * @var object
	 */
	var $_model = NULL;
    
    

	/**
	 * Custom Constructor
	 */
	function __construct( $default = array() )
	{
		// Set a default view if none exists
		if ( ! JRequest::getCmd( 'view' ) ) {
			
			JRequest::setVar('view', 'properties' );
		}
		
		parent::__construct( $default );

		$this->registerTask( 'apply', 'save' );
		$this->registerTask( 'add', 'edit' );
		
		$mainframe = &JFactory::getApplication();
		$this->_cat = $mainframe->getUserStateFromRequest( 'com_jea.properties.cat', 'cat', '', 'word' );
		$this->_model =& $this->getModel( 'Properties' );
		$this->_model->setCategory( $this->_cat );
		$this->_controllerUrl = 'index.php?option=com_jea&controller=properties&cat=' . $this->_cat ;
	}
	
	
	function display()
	{
		$this->_display();
	}
	
	
	function edit()
	{
		if ( $this->_model->checkout() ){
			$this->_display('form');
		} else {
			$this->_setDefaultRedirect();
		}
	}
	
	
	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
		
		if ( false ===  $this->_model->save() ) {

		    $this->edit();
		    
		} else {
			
			$row =& $this->_model->getRow();
			
		  	if ( 'apply' == $this->getTask() ) {
		  		$this->_controllerUrl .= '&task=edit&id=' . $row->id ;
		  	}
		  	
		  	$msg = JText::sprintf( 'Successfully saved property', $row->ref ) ;
		    $this->setRedirect( $this->_controllerUrl  , $msg );
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
		
		if ( $this->_model->remove() ) {
			
			$msg = JText::sprintf('SUCCESSFULLY REMOVED ITEMS', count($this->_model->getCid()));
			$this->setRedirect( 'index.php?option=com_jea&controller=properties', $msg );
			
		} else {
			
			$this->_setDefaultRedirect();
		}	
	}
	
    function copy()
	{
	    // Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
		
		if ( $this->_model->copy() ) {
			
			$msg = JText::sprintf('SUCCESSFULLY COPY ITEMS', count($this->_model->getCid()));
			$this->setRedirect( $this->_controllerUrl  , $msg  );
			
		} else {
			
			$this->_setDefaultRedirect();
		}
	}
	
	function deleteimg()
	{
		$id = JRequest::getInt('id',0);
	    
	    $this->_model->delete_img();
		$this->setRedirect( $this->_controllerUrl . '&task=edit&id=' . $id );
	}
	
    function editiptc()
	{
		$this->_display('iptc');
	}
	
	function saveiptc()
	{
		$this->_model->saveIptc();
		$id = JRequest::getInt('id');
    	$image = JRequest::getVar('image', '');
    	$redirect =  $this->_controllerUrl . '&task=editiptc&tmpl=component&id=' 
    	          . $id . '&image=' . $image ;
    	          
		$this->setRedirect($redirect);
	}
	
	function unpublish()
	{
		$this->_publish(false);
	}
	
	function publish()
	{
		$this->_publish(true);
	}
	
	function orderdown()
	{
		$this->_order(-1);
	}
	
	function orderup()
	{
		$this->_order(1);
	}
	
	function emphasize()
	{
		$this->_model->emphasize();
		$this->_setDefaultRedirect();
	}
	
	/******************************  Private functions   *****************************************/
	

	function _order( $inc )
	{
		$this->_model->order($inc);
		$this->_setDefaultRedirect();
	}
	
	function _setDefaultRedirect()
	{
		$this->setRedirect( $this->_controllerUrl );
	}
	
	function _publish($bool)
	{
		if ( $this->_model->publish($bool) ) {
			
			$state = $bool ? 'published' : 'unpublished' ;
			$msg = JText::sprintf('Properties(s) successfully '. $state , count($this->_model->getCid()));
			$this->setRedirect( $this->_controllerUrl , $msg );
			
		} else {
			
			$this->_setDefaultRedirect();
		}
	}
	
	function _display( $tpl=null )
	{
		//create the view
		$view = & $this->getView('properties', 'html');
		// Push the model into the view (as default)
		$view->setModel($this->_model, true);
		
		$featuresModel = & $this->getModel('Features');
		// Push the  features model into the view
		$view->setModel( $featuresModel );
		
		// Display the view
		$view->display($tpl);
	}
	
	
}
