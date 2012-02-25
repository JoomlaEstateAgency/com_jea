<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 * 
 * @version     $Id: view.html.php 257 2012-02-05 23:04:04Z ilhooq $
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

class JeaViewFeaturelist extends JView
{
	
    function display( $tpl = null )
	{
	    
	    $params = JComponentHelper::getParams('com_jea');
		$this->assignRef('params' , $params );

		JeaHelper::addSubmenu('features');
		
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
	    $feature = $this->state->get('feature.name');

		JToolBarHelper::title( JText::_(JString::strtoupper("com_jea_list_of_{$feature}_title")) , 'jea.png' );

		if ($canDo->get('core.create')) {
			JToolBarHelper::addNew('feature.add');
		}

		if ($canDo->get('core.edit')) {
			JToolBarHelper::editList('feature.edit');
		}

		if ($canDo->get('core.delete')) {
		    JToolBarHelper::divider();
			JToolBarHelper::deleteList(JText::_('CONFIRM_DELETE_MSG'), 'featurelist.delete');
		}
	}


}