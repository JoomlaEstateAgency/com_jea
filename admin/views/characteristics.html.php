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

class CharacteristicsView extends JView
{

	var $id = 0;
    var $tableName = '';
    var $offset = 0;
	var $limit = 0;
	var $pagination = null;
	var $rows = array();
	var $row = null;
	var $tablesList = '';
    
    function index()
	{
	    
	    jimport( 'joomla.html.pagination' );
	    
	    $model =& $this->getModel();
	    
	    
	    $res = $model->getItems( $this->offset, $this->limit );
	    
	    $this->pagination = new JPagination( $res['total'], $this->offset, $this->limit );
	    
	    $this->assign($res);
	    
	    $tablesTranslations = array( 
		    'types'       => 'Property types' , 
	        'conditions'  => 'Property conditions' , 
	        'departments' => 'Departments' , 
	        'towns'       => 'Towns' , 
	        'areas'       => 'Areas' ,
	        'advantages'  => 'Advantages' ,
	        'heatingtypes' => 'Heating types' ,
	        'hotwatertypes' => 'Hot water types',
	        'slogans'     => 'Slogans' 
        );
	                     
	     $table_option = array();
	     
	    foreach ( $tablesTranslations as $tableName => $translation ) {
	        
	        $table_option[] = JHTML::_('select.option', $tableName, JText::_( $translation ) );
	    }
	    
	    $this->tablesList = JHTML::_( 'select.genericlist', 
		                              $table_option, 
		                              'table', 
		                              'class="inputbox" size="1" onchange="document.adminForm.submit();"' , 
		                              'value', 
		                              'text', 
		                              $this->tableName
		                             );
		                       

		$titles = array(
			'types'         => 'Property types list' , 
			'conditions'    => 'Property conditions list' , 
			'departments'   => 'Departments list' , 
			'towns'         => 'Towns list' , 
			'areas'         => 'Areas List' ,
			'advantages'    => 'Advantages List' ,
			'heatingtypes'  => 'Heating types List' ,
	        'hotwatertypes' => 'Hot water types List',
			'slogans'       => 'Slogans list'
		);
	    
	    JToolBarHelper::title( JText::_( $titles[$this->tableName] ), 'generic.png' );
	    JToolBarHelper::addNew();
	    JToolBarHelper::editList();
	    JToolBarHelper::deleteList( JText::_( 'CONFIRM_DELETE_MSG' ) );
	    
	}
	
	function editCharacteristic()
	{
	    JRequest::setVar( 'hidemainmenu', 1 );
	    
	    $model =& $this->getModel();
	    $this->row = $model->load( $this->id ) ;
	    
	    $tablesTranslations = array( 
		    'types'       => 'Property type' , 
	        'conditions'  => 'Property condition' , 
	        'departments' => 'Department' , 
	        'slogans'     => 'Slogan' , 
	        'towns'       => 'Town' , 
	        'areas'       => 'Area' ,
	        'advantages'  => 'Advantage',
	        'heatingtypes' => 'Heating type' ,
	        'hotwatertypes' => 'Hot water type'
        );
	    
	    if ( $this->id === 0 ) {
	        
	        $title = JText::_( $tablesTranslations[$this->tableName] ) . ' [ ' . JText::_( 'New' ) . ' ]' ;
	        
	    } else {
	        
		    $title  = JText::_( $tablesTranslations[$this->tableName] ) 
		            . ' [ ' . JText::_( 'Edit' ) . ' : ' .  $this->row->value . ' ]' ;
	    }
	    
	    JToolBarHelper::title( $title , 'generic.png' ) ;
	    
	    JToolBarHelper::save() ;
	    JToolBarHelper::apply() ;
	    JToolBarHelper::cancel() ;
	}
	
}
