<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 * 
 * @version     0.9 2009-10-14
 * @package		Jea.admin
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

jimport( 'joomla.application.component.view');

class JeaViewProperties extends JView

{
	var $pagination = null ;
	var $user = null;

	function display( $tpl = null )
	{
		$this->user = & JFactory::getUser();
		
		switch ($tpl) {
			case 'form':
				$this->editItem();
				break;
			default :
				$this->listIems();

		}
		$params =& ComJea::getParams();
		$this->assignRef('params' , $params );

		parent::display($tpl);
	}


	function listIems()
	{
		jimport( 'joomla.html.pagination' );
		JHTML::_('behavior.tooltip');
		
		$model = $this->getModel();
		$items = $this->get('items');
		$this->assign( $items );
		$this->pagination = new JPagination($items['total'], $items['limitstart'], $items['limit']);

	    JToolBarHelper::title( JText::_( ucfirst( $this->get('category') ) . ' management' ), 'jea.png' );
	    JToolBarHelper::publish();
	    JToolBarHelper::unpublish();
	    JToolBarHelper::addNew();
	    JToolBarHelper::customX( 'copy', 'copy.png', 'copy_f2.png', 'Copy' );
	    JToolBarHelper::editList();
	    JToolBarHelper::deleteList( JText::_( 'CONFIRM_DELETE_MSG' ) );

	}

	function editItem()
	{
		JRequest::setVar( 'hidemainmenu', 1 );

		$item =& $this->get('item');
		
		$this->assign( $item );
	    
	    $title  = $this->get('category') == 'renting' ? JText::_( 'Renting' ) : JText::_( 'Selling' ) ;
	    $title .= ' : ' ;
	    $title .= $this->row->id ? JText::_( 'Edit' ) . ' ' . $this->escape( $this->row->ref ) : JText::_( 'New' ) ;
	    JToolBarHelper::title( $title , 'jea.png' ) ;
	    
	    JToolBarHelper::save() ;
	    JToolBarHelper::apply() ;
	    JToolBarHelper::cancel() ;
	}
	
	function getHtmlList($tableName, $default=0, $grid=false )
	{   
	    static $lists = null;
	    
	    if (!is_array($lists)) {
		
			$t_department    = '- ' . JText::_( 'Department' ).' -' ;
		    $t_condition     = '- ' . JText::_( 'Condition' ).' -' ;
		    $t_area          = '- ' . JText::_( 'Area' ).' -' ;
		    $t_slogan        = '- ' . JText::_( 'Slogan' ).' -' ;
		    $t_town          = '- ' . JText::_( 'Town' ).' -' ;
		    $t_property_type = '- ' . JText::_( 'Property type' ).' -' ;
		    $t_heating_type  = '- ' . JText::_( 'Heating type' ).' -' ;
		    $t_hotwater_type = '- ' . JText::_( 'Hot water type' ).' -' ;
		    
		    $lists = array( 'departments' => array( $t_department , 'department_id'),
		                    'conditions' => array( $t_condition , 'condition_id' ),
		                    'areas' => array( $t_area , 'area_id' ),
		                    'slogans' => array( $t_slogan , 'slogan_id' ),
		                    'towns' => array( $t_town , 'town_id' ),
		                    'types' => array( $t_property_type , 'type_id' ),
		                    'heatingtypes' => array( $t_heating_type , 'heating_type' ),
		                    'hotwatertypes' => array( $t_hotwater_type , 'hot_water_type' ),
		                  );
		}
	    
		if ( isset($lists[$tableName]) ) {
			
			$onChange = $grid ? 'onchange="document.adminForm.submit();"' : '' ;
			$featuresModel =& $this->getModel('features');
	    	$featuresModel->setTableName( $tableName );
	    	
	    	return JHTML::_('select.genericlist', 
	    	                $featuresModel->getListForHtml($lists[$tableName][0]) , 
	    	                $lists[$tableName][1], 
	    	                'class="inputbox" size="1" '.$onChange , 
	    	                'value', 
	    	                'text', 
	    	                $default );
		}
		
		return 'list Error';	
	}
	
	function getAdvantagesRadioList()
	{
	    $html = '';
	    
	    $featuresModel =& $this->getModel('features');
	    $featuresModel->setTableName( 'advantages' );
	    $res = $featuresModel->getItems(true);
	    
	    $advantages = array();
	    
	    if ( !empty( $this->row->advantages ) ) {
	        $advantages = explode( '-' , $this->row->advantages );
	    }
	    
	    foreach ( $res['rows'] as $k=> $row ) {
	        
	        $checked = '';
	        
	        if ( in_array($row->id, $advantages) ) {
	            $checked = 'checked="checked"' ;
	        }
	        
	        $html .= '<label class="advantage">' . PHP_EOL 
	              .'<input type="checkbox" name="advantages[' . $k . ']" value="' 
				  . $row->id . '" ' . $checked . ' />' . PHP_EOL 
				  . $row->value . PHP_EOL 
	              . '</label>' . PHP_EOL ;
	    }
	    return $html;
	}
	
	function is_checkout( $checked_out )
	{
		if ($this->user && JTable::isCheckedOut( $this->user->get('id'), $checked_out ) ) {
			return true;
		}
		return false;
	}
	
	

}