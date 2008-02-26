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

class JEA_PropertiesController extends JEA_AbstractController
{
    /**
     * property category ( renting or selling )
     *
     * @var string $_cat
     */

    var $_cat='';
	
    var $_controllerUrl = '';
    

    function preDispatch()
	{
		$this->_cat = $this->_application->getUserStateFromRequest( 'com_jea.biens.cat', 'cat', '', 'word' );
		$this->_viewDatas['cat'] = $this->_cat;
		
		// Get/Create the model
		$this->_model = & $this->getModel( 'PropertiesModel', 'JEA_' );
		$this->_model->setCategory( $this->_cat );
		
		$this->_controllerUrl = 'index.php?option=com_jea&controller=properties&cat=' . $this->_cat ;
	}
	
	
	function indexAction()
	{
	    $context = 'com_jea.biens.'.$this->_cat ;
	    $this->_viewDatas['limit'] = $this->_application->getUserStateFromRequest( $context.'limit', 
								                                                   'limit', 
								                                                   $this->_application->getCfg('list_limit'), 
								                                                   'int' );

	    $this->_viewDatas['offset'] = $this->_application->getUserStateFromRequest( $context.'offset', 'limitstart', 0, 'int' );
	    $this->_viewDatas['type_id'] = $this->_application->getUserStateFromRequest( $context.'type_id', 'type_id', 0, 'int' );
	    $this->_viewDatas['town_id'] = $this->_application->getUserStateFromRequest( $context.'town_id', 'town_id', 0, 'int' );
	    $this->_viewDatas['department_id'] = $this->_application->getUserStateFromRequest( $context.'department_id', 'department_id', 0, 'int' );
	    $this->_viewDatas['ordering'] = JRequest::getCmd( 'ordering', '' );
	    $this->_viewDatas['find_ref'] = JRequest::getVar( 'find_ref', '' );

	    parent::display('listProperties');
	}
	
	function addAction()
	{ 
	    $this->_load(0);
	}
	
	function editAction()
	{
		$id = JRequest::getInt('id', 0);
		if (!$id) $id = $this->_cid[0] ;
		
		$this->_load($id);
	}
	
	function applyAction()
	{
		$id = JRequest::getInt( 'id', 0 );
	   
		$redirectUri = $this->_controllerUrl . '&task=edit&id=' ;
		
	    if ( false ===  $this->_save($id) ) {

		    $this->setRedirect( $redirectUri . $id );
		    
		} else {
		    $row =& $this->_model->getRow();
		  	$msg = JText::sprintf( 'Successfully saved property', $row->ref ) ;
		  	
		    $this->setRedirect( $redirectUri . $row->id , $msg );
		}
	    
	}
	
	function saveAction()
	{
		$id = JRequest::getInt( 'id', 0 );
		
	    if ( false ===  $this->_save($id) ) {

		    $this->setRedirect( $this->_controllerUrl . '&task=edit&id=' . $id );
		    
		} else {

		  	$row =& $this->_model->getRow();
		  	$msg = JText::sprintf( 'Successfully saved property', $row->ref ) ;
		  	
		    $this->setRedirect( $this->_controllerUrl , $msg );
		}
		
	}
	
	function cancelAction()
	{
		$this->setRedirect( $this->_controllerUrl );
	}
	
	function deleteimgAction()
	{
		$id = JRequest::getInt( 'id', 0 );
		$image	= JRequest::getVar( 'image' , '', 'GET' );
		
		if($id){
		    
			$this->_model->delete_img($id, $image);
			$this->setRedirect( $this->_controllerUrl . '&task=edit&id=' . $id );
			
		} else {
		    $this->setRedirect( $this->_controllerUrl );
		}
	}
	

	
	function removeAction()
	{	
	    if ( !empty($this->_cid) || $this->_cid[0] != 0 ){
			
	        if(!$this->_model->remove($this->_cid)){
	            
			    $this->setRedirect( $this->_controllerUrl );
			    
			} else {
			    
			   $msg = JText::_('Successfully removed items') ;
			   $this->setRedirect( $this->_controllerUrl , $msg );
			}
		}
	}
	
	function unpublishAction()
	{
		$this->_publish(false);
	}
	
	function publishAction()
	{
		$this->_publish(true);
	}		
	
	function orderdownAction()
	{
		$this->_order(1);
	}
	
	function orderupAction()
	{
		$this->_order(-1);
	}
	
	function emphasizeAction()
	{
		$this->_model->emphasize($this->_cid[0]);
		$this->setRedirect( $this->_controllerUrl );
	}
	
	
	/******************************  Private methods   *****************************************/
	

	function _order( $inc )
	{
		$this->_model->order($inc, $this->_cid[0]);
		$this->setRedirect( $this->_controllerUrl );
	}
	
	function _publish($bool)
	{

		$this->_model->publish($this->_cid, $bool);
		$this->setRedirect( $this->_controllerUrl );
	}
	
	function _load($id)
	{
	    $this->_viewDatas['id'] = $id ;
	    parent::display('editProperty');
	}
	
	
	function _save( $id )
	{
	    
		$datas = array();

		$datas['ref'] = JRequest::getCmd( 'ref', '', 'POST' );
		$datas['type_id'] = JRequest::getInt( 'type_id', 0 , 'POST' );
		$datas['price'] = JRequest::getFloat( 'price', 0.0, 'POST' );
		$datas['adress'] = JRequest::getVar( 'adress' , '', 'POST' );
		$datas['town_id'] = JRequest::getInt( 'town_id', 0 , 'POST' );
		$datas['area_id']=   JRequest::getInt( 'area_id', 0 , 'POST' );
		$datas['zip_code'] = JRequest::getVar( 'zip_code' , '', 'POST' );
		$datas['department_id'] = JRequest::getInt( 'department_id', 0 , 'POST' );
		$datas['condition_id'] = JRequest::getInt( 'condition_id', 0 , 'POST' );
		$datas['living_space'] = JRequest::getInt( 'living_space', 0 , 'POST' );
		$datas['land_space'] = JRequest::getInt( 'land_space', 0 , 'POST' );
		$datas['rooms'] = JRequest::getInt( 'rooms', 0 , 'POST' );
		$datas['charges'] = JRequest::getFloat( 'charges', 0.0, 'POST' );
		$datas['fees'] = JRequest::getFloat( 'fees', 0.0, 'POST' );
		$datas['hot_water_type'] = JRequest::getInt( 'hot_water_type', 0 , 'POST' );
		$datas['heating_type'] = JRequest::getInt( 'heating_type', 0 , 'POST' );
		$datas['bathrooms'] = JRequest::getInt( 'bathrooms', 0 , 'POST' );
		$datas['toilets'] = JRequest::getInt( 'toilets', 0 , 'POST' );
		$datas['availability'] = JRequest::getVar( 'availability' , '', 'POST' );
		$datas['floor'] = JRequest::getInt( 'floor', 0 , 'POST' );
		$datas['advantages'] = JRequest::getVar( 'advantages', array(), 'POST', 'array' );
		$datas['description'] = JRequest::getVar( 'description', '', 'POST', 'string', JREQUEST_ALLOWRAW );
		$datas['slogan_id'] = JRequest::getInt( 'slogan_id', 0 , 'POST' );
		$datas['published'] = JRequest::getInt( 'published', 0 , 'POST' );
		$datas['emphasis'] = JRequest::getInt( 'emphasis', 0 , 'POST' );
		
		require_once 'Http_File.php';
		
		$mainFile = new Http_File( JRequest::getVar( 'main_image', array(), 'files', 'array' ) ) ;
		$secondFile = new Http_File( JRequest::getVar( 'second_image', array(), 'files', 'array' ) );
		
		if( !$this->_model->save( $id, $datas, $mainFile, $secondFile )){
		    return false;
		}
		    
		 return true;
	}
}

?>