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
    function display( $tpl = null )
	{
	    
	    $params = JComponentHelper::getParams('com_jea');
		$this->assignRef('params' , $params );

		JeaHelper::addSubmenu('features');

		$this->items = $this->get('Tables');
		
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

		JToolBarHelper::title( JText::_('FEATURES management'), 'jea.png' );

		if ($canDo->get('core.manage')) {
			JToolBarHelper::custom('features.import', 'import', '', 'Import', false);
		}
		
		JToolBarHelper::custom('features.export', 'export', '', 'Export', false);
	}
	
}