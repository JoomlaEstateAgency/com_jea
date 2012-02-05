<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
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

class JeaViewProperty extends JView
{
	
    
    
    function display( $tpl = null )
	{
	    
	    // $params = JComponentHelper::getParams('com_jea');
		// $this->assignRef('params' , $params );

		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');
		
	    // Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		$this->addToolbar();

		parent::display($tpl);
	}
	
	
	/**
	 * Add the page title and toolbar.
	 *
	 */
	

	function addToolbar()
	{
		/*
	    // Keep post data if there is an error 
		$exceptions = JError::getErrors();
		if(!empty($exceptions)) {
    		$this->row->bind(JRequest::get('post'));
            if(is_array($this->row->advantages)) {
                $this->row->advantages = implode('-', $this->row->advantages);
            }
		}
		*/
	    
	    $title .= $this->item->id ? JText::_( 'Edit' ) . ' ' . $this->escape( $this->item->ref ) : JText::_( 'New' ) ;
	    JToolBarHelper::title( $title , 'jea.png' ) ;
	    
	    /*
	    $mainframe = &JFactory::getApplication();
	    //Get the last slider pannel openning
	    $this->assign('sliderOffset',  $mainframe->getUserState( 'com_jea.sliderOffset'));
	    */
	    
	    JToolBarHelper::save() ;
	    JToolBarHelper::apply() ;
	    JToolBarHelper::cancel() ;
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

}