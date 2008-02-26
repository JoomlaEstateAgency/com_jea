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

class ConfigView extends JView
{
    var $form = null;
    var $ini = '';
    
    function index( )
	{ 
	    // Create the form
		//$this->form = new JParameter( $this->ini, JPATH_COMPONENT.DS.'models'.DS.'Config.xml' );
		
		$this->form =& ComJea::getParams();
		$this->form->loadSetupFile( JPATH_COMPONENT.DS.'models'.DS.'Config.xml' );
		
		$style = '<link rel="stylesheet" type="text/css" href="components/com_jea/views/jea.css" />';
		
		$mainframe =& JFactory::getApplication();
		$mainframe->addCustomHeadTag($style);
		
		
	    JHTML::_('behavior.tooltip');
	    JToolBarHelper::title(   'JEA : ' . JText::_( 'Configuration' ), 'cpanel.png' );
	    JToolBarHelper::save();
	    JToolBarHelper::makeDefault();
	}
}