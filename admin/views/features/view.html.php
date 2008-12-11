<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 * 
 * @version     0.6 2008-12-11
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

class JeaViewFeatures extends JView

{
	var $pagination = null ;

	function display( $tpl = null )
	{
		switch ($tpl) {
			case 'form':
				$this->editItem();
				break;
			default :
				$this->listIems();

		}

		parent::display($tpl);
	}


	function listIems()
	{
		jimport( 'joomla.html.pagination' );
		
		$model = $this->getModel();
		$items = $this->get('items');
		
		$this->pagination = new JPagination($items['total'], $items['limitstart'], $items['limit']);

		$tablesTranslations = array(
		    'types'         => 'Property types' , 
	        'conditions'    => 'Property conditions' , 
	        'departments'   => 'Departments' , 
	        'towns'         => 'Towns' , 
	        'areas'         => 'Areas' ,
	        'advantages'    => 'Advantages' ,
	        'heatingtypes'  => 'Heating types' ,
	        'hotwatertypes' => 'Hot water types',
	        'slogans'       => 'Slogans' 
	    );

        $options = array();

        foreach ( $tablesTranslations as $tableName => $translation ) {
        	$options[] = JHTML::_('select.option', $tableName, JText::_( $translation ) );
        }
         
        $selectTableList = JHTML::_( 'select.genericlist',
                                     $options,
	                                 'table', 
	                                 'class="inputbox" size="1" onchange="document.adminForm.submit();"' , 
	                                 'value', 
	                                 'text', 
                                      $this->get('tableName')
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
			
		$this->assign( $items );
		$this->assignRef('selectTableList' , $selectTableList );
			
		JToolBarHelper::title( JText::_( $titles[$this->get('tableName')] ), 'jea.png' );
	    JToolBarHelper::addNew();
	    JToolBarHelper::editList();
	    JToolBarHelper::deleteList( JText::_( 'CONFIRM_DELETE_MSG' ) );

	}

	function editItem()
	{

		JRequest::setVar( 'hidemainmenu', 1 );

		$row =& $this->get('row');
		
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
		
		$table_name = JText::_( $tablesTranslations[$this->get('tableName')] ) ;
        
        
        if ( ! $row->id ) {
	        
	        $title = $table_name . ' [ ' . JText::_( 'New' ) . ' ]' ;
	        
	    } else {
	        
		    $title  = $table_name . ' [ ' . JText::_( 'Edit' ) . ' : ' .  $row->value . ' ]' ;
	    }

		JToolBarHelper::title( $title , 'jea.png' );
		JToolBarHelper::save() ;
		JToolBarHelper::apply() ;
		JToolBarHelper::cancel() ;
	  
		$this->assignRef('row' , $row );
	}

}