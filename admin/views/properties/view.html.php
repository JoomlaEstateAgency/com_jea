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

class JeaViewProperties extends JView
{
	
    function display( $tpl = null )
	{
	    
	    $params = JComponentHelper::getParams('com_jea');
		$this->assignRef('params' , $params );

		JeaHelper::addSubmenu('properties');

		$this->user		= JFactory::getUser();
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		
		// var_dump($items);
		
		$this->addToolbar();

		parent::display($tpl);
	}
	
	
	/**
	 * Add the page title and toolbar.
	 *
	 */
	protected function addToolbar()
	{
	    $canDo	= JeaHelper::getActions();
		$user	= JFactory::getUser();

		JToolBarHelper::title( JText::_('Properties management'), 'jea.png' );

		if ($canDo->get('core.create')) {
			JToolBarHelper::addNew('property.add');
			JToolBarHelper::custom('properties.copy', 'copy.png', 'copy_f2.png', 'Copy');
		}

		if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own'))) {
			JToolBarHelper::editList('property.edit');
		}

		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::divider();
			JToolBarHelper::publish('property.publish', 'JTOOLBAR_PUBLISH', true);
			JToolBarHelper::unpublish('property.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::custom('properties.featured', 'featured.png', 'featured_f2.png', 'JFEATURED', true);
		}

		if ($canDo->get('core.delete')) {
		    JToolBarHelper::divider();
			JToolBarHelper::deleteList(JText::_('CONFIRM_DELETE_MSG'), 'properties.delete');
		}
		
	    if ($canDo->get('core.admin')) {
	        JToolBarHelper::divider();
			JToolBarHelper::preferences('com_jea');
		}
	}


}