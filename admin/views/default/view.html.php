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

jimport('joomla.application.component.view');

require JPATH_COMPONENT.DS.'helpers'.DS.'jea.php';

class JeaViewDefault extends JView
{

	public function display( $tpl = null )
	{
		
		JeaHelper::addSubmenu('default');
	    
	    JToolBarHelper::title( 'Joomla Estate Agency', 'jea.png' );
		JToolBarHelper::preferences('com_jea');
		
		
		
		parent::display($tpl);
	}
	
	protected function getVersion()
	{
    	if (is_file(JPATH_COMPONENT . DS . 'jea.xml')) {
	        $xml = JFactory::getXML(JPATH_COMPONENT . DS . 'jea.xml');
	        return $xml->version;
    	}
    	
        return '';
	}




}