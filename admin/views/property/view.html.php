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
	
    protected $form;
	protected $item;
	protected $state;
	protected $canDo;
    
    function display( $tpl = null )
	{
	    
	    // $params = JComponentHelper::getParams('com_jea');
		// $this->assignRef('params' , $params );

		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');
		$this->canDo	= JeaHelper::getActions($this->item->id);
		
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
	 * Inspired from ContentViewArticle in com_content
	 *
	 */
	function addToolbar()
	{
	    JRequest::setVar('hidemainmenu', true);
		$user		= JFactory::getUser();
		$userId		= $user->get('id');
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
		
		$title = $this->item->id ? JText::_( 'Edit' ) . ' ' . $this->escape( $this->item->ref ) : JText::_( 'New' ) ;
	    JToolBarHelper::title( $title , 'jea.png' ) ;

		// Built the actions for new and existing records.

		// For new records, check the create permission.
		if ($isNew && ($this->canDo->get('core.create'))) {
			JToolBarHelper::apply('property.apply');
			JToolBarHelper::save('property.save');
			JToolBarHelper::save2new('property.save2new');
			JToolBarHelper::cancel('property.cancel');
		} else {
			// Can't save the record if it's checked out.
			if (!$checkedOut) {
				// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
				if ($this->canDo->get('core.edit') || ($this->canDo->get('core.edit.own') && $this->item->created_by == $userId)) {
					JToolBarHelper::apply('property.apply');
					JToolBarHelper::save('property.save');

					// We can save this record, but check the create permission to see if we can return to make a new one.
					if ($this->canDo->get('core.create')) {
						JToolBarHelper::save2new('property.save2new');
					}
				}
			}

			// If checked out, we can still save
			if ($this->canDo->get('core.create')) {
				JToolBarHelper::save2copy('property.save2copy');
			}

			JToolBarHelper::cancel('property.cancel', 'JTOOLBAR_CLOSE');
		}
	}

}