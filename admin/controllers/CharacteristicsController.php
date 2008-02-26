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
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once JPATH_COMPONENT.DS.'models'.DS.'characteristicsmodel.php';

class JEA_CharacteristicsController extends JEA_AbstractController
{	
	/**
	 * Name of the current displayed table.
	 *
	 * @var string $_tableName
	 */
    
    var $_tableName = '';

	
    function preDispatch()
	{
	    $tables = array(
		    'types', 
		    'conditions', 
		    'departments', 
		    'slogans', 
		    'towns', 
		    'areas', 
		    'advantages',
	        'heatingtypes',
		    'hotwatertypes'
		);

	    $table = $this->_application->getUserStateFromRequest( 'com_jea.characteristic', 'table', 'types', 'word' );
		
	    if( !in_array( $table, $tables ) ){
		    
		    $table = 'types' ;
		}
	    
		$this->_viewDatas['tableName'] = $this->_tableName = $table ;
		
		// Get/Create the model
		$this->_model = new JEA_CharacteristicsModel( $this->_tableName );
	}	
	
	
	function indexAction()
	{
		
	    $context = 'com_jea.characteristics.' . $this->_tableName ;
	    $this->_viewDatas['limit'] = $this->_application->getUserStateFromRequest( $context.'limit', 
								                                                   'limit', 
								                                                   $this->_application->getCfg('list_limit'), 
								                                                   'int' );

	    $this->_viewDatas['offset'] = $this->_application->getUserStateFromRequest( $context.'offset', 'limitstart', 0, 'int' );

	    parent::display('listCharacteristics');
	}
	
	
	/**
	 * Add new item
	 *
	 */
	function addAction()
	{
		parent::display('editCharacteristic');
	}
	
	/**
	 * Edit an item
	 *
	 */
	function editAction()
	{	
		$id = JRequest::getInt('id', 0);
		if (!$id) $id = $this->_cid[0] ;
		
		$this->_viewDatas['id'] = $id ;
	    parent::display('editCharacteristic');	
	}

	
	function applyAction()
	{
		$id    = JRequest::getInt('id', 0);
		$value = JRequest::getVar('value', '') ;
		$link  = 'index.php?option=com_jea&controller=characteristics&task=edit&id=' ;
				
		if ( false === $this->_model->save( $id, $value ) ) {

		    $this->setRedirect( $link . $id );
		    
		} else {
		    
		  	$msg = 'succes!' ;//TODO:traduire
		    $this->setRedirect( $link . $this->_model->getLastSavedRowId() , $msg );
		}
		
		
	}
	
	function saveAction()
	{ 
	    $id    = JRequest::getInt( 'id', 0 );
		$value = JRequest::getVar( 'value', '' ) ;
		
		if ( false === $this->_model->save( $id, $value ) ) {
		    
			$this->setRedirect( 'index.php?option=com_jea&controller=characteristics&task=edit&id=' . $id );
			
		} else {
		    
			$msg = "Item bien enregistre" ; //TODO:traduire
			$this->setRedirect( 'index.php?option=com_jea&controller=characteristics' , $msg);
		}
		
	}
	
	function cancelAction()
	{
		$this->setRedirect( 'index.php?option=com_jea&controller=characteristics' );
	}
	
	function removeAction()
	{   
		$this->_model->remove( $this->_cid );
		$this->setRedirect( 'index.php?option=com_jea&controller=characteristics' );
	}
	
	function orderdownAction()
	{
		$this->_order(1);
	}
	
	function orderupAction()
	{
		$this->_order(-1);
	}
	
	
	/******************************  Private functions   *****************************************/
	

	function _order( $inc )
	{
	    $this->_model->orderList( $inc, $this->_cid[0] );
		$this->setRedirect( 'index.php?option=com_jea&controller=characteristics' );
	}
	
	
}
