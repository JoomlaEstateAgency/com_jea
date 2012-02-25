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

class JeaViewFeature extends JView
{
	
    protected $form;
	protected $item;
	protected $state;
	protected $canDo;
    
    function display( $tpl = null )
	{
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');
		$this->canDo	= JeaHelper::getActions();
		
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
	    JRequest::setVar('hidemainmenu', true);
		$isNew		= ($this->item->id == 0);
		
		$title = $this->item->id ? JText::_( 'Edit' ) . ' ' . $this->escape( $this->item->value ) : JText::_( 'New' ) ;
	    JToolBarHelper::title( $title , 'jea.png' ) ;

		// Built the actions for new and existing records.

		// For new records, check the create permission.
		if ($this->canDo->get('core.create')) {
			JToolBarHelper::apply('feature.apply');
			JToolBarHelper::save('feature.save');
			JToolBarHelper::save2new('feature.save2new');
		}
		
		JToolBarHelper::cancel('feature.cancel');
	}

}