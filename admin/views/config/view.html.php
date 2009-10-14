<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 * 
 * @version     0.9 2009-10-14
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

jimport( 'joomla.application.component.view');

class JeaViewConfig extends JView
{
    var $form = null;
    var $ini = '';
    
    function display( $tpl = null )
	{ 
	    // Create the form
		$this->form =& ComJea::getParams();
		$this->form->loadSetupFile( JPATH_COMPONENT.DS.'models'.DS.'Config.xml' );
		
	    JHTML::_('behavior.tooltip');
	    JToolBarHelper::title(   'JEA : ' . JText::_( 'Configuration' ), 'config.png' );
	    JToolBarHelper::save();
	    JToolBarHelper::makeDefault();
	    
	    parent::display($tpl);
	}
}