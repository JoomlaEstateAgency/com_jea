<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 * 
 * @version     $Id$
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
require JPATH_COMPONENT.DS.'helpers'.DS.'jea.php';


class JeaViewFeatures extends JView

{
	var $pagination = null ;
	
	var $tablesTranslations = array(
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

	function display( $tpl = null )
	{
		// Get the parameters
		$params = JComponentHelper::getParams('com_jea');
		$this->assignRef('params' , $params );
		
		JeaHelper::addSubmenu('features');
	    
		
		if ($tpl == 'form') {
		    $this->editItem();
		    
		} elseif ($this->getLayout() == 'export') {
		    JToolBarHelper::title( JText::_( 'CSV export', 'jea.png' ));
		    JToolBarHelper::back();
		} elseif ($this->getLayout() == 'import') {
		    JToolBarHelper::title( JText::_( 'CSV import', 'jea.png' ));
		    JToolBarHelper::back();
		} else {
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

        $options = array();

        foreach ( $this->tablesTranslations as $tableName => $translation ) {
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
	    JToolBarHelper::custom('import', 'import', '', 'Import', false);
	    JToolBarHelper::custom('export', 'export', '', 'Export', false);

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
		$this->assign('tableName' , $this->get('tableName') );
	}
	
    function getDeptsList($default=0)
	{
		$featuresModel =& $this->getModel('features');
		$title         = '- ' . JText::_( 'Departments' ).' -' ;
		$list = array();
	    

		$featuresModel->setTableName('departments');
		
		$list = $featuresModel->getListForHtml($title, 'text');
		
		return JHTML::_(
			'select.genericlist', 
			$list, 
			'department_id', 
			'class="inputbox" size="1" ', 
			'value', 
			'text', 
			$default 
		);
	}
	
    function getTownsList($default=0)
	{
		$featuresModel =& $this->getModel('features');
		$title         = '- ' . JText::_( 'Towns' ).' -' ;
		$list = array();
	    

		$featuresModel->setTableName('towns');
		
		$list = $featuresModel->getListForHtml($title, 'text');
		
		return JHTML::_(
			'select.genericlist', 
			$list, 
			'town_id', 
			'class="inputbox" size="1" ', 
			'value', 
			'text', 
			$default 
		);
	}
	
}