<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 * 
 * @version     $Id $
 * @package		Jea.admin
 * @copyright	Copyright (C) 2008 PHILIP Sylvain. All rights reserved.
 * @license		GNU/GPL, see LICENSE.txt
 * Joomla Estate Agency is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses.
 * 
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class JeaControllerConfig extends JController
{
    
    /**
	 * Custom Constructor
	 */
	function __construct( $default = array() )
	{
		// Set a default view if none exists
		if ( ! JRequest::getCmd( 'view' ) ) {
			
			JRequest::setVar('view', 'config' );
		}
		
		parent::__construct( $default );
		
		$this->registerTask( 'default', 'defaultConfig' );
	}
    
    
    function save()
    {

        $units    = JRequest::getVar( 'units'    , array() , 'POST' , 'array' );
        $currency = JRequest::getVar( 'currency' , array() , 'POST' , 'array' );
        $lists    = JRequest::getVar( 'lists'    , array() , 'POST' , 'array' );
        $property = JRequest::getVar( 'property' , array() , 'POST' , 'array' );
        $pictures = JRequest::getVar( 'pictures' , array() , 'POST' , 'array' );
        
        $config = array_merge( $units, $currency, $lists, $property, $pictures );
        
        //transform normal space(s) into non-break space(s)
        //$unsecableSpace = html_entity_decode ('&nbsp;', ENT_COMPAT, 'UTF-8' );
        //$config['thousands_separator'] = str_replace (' ', $unsecableSpace, $config['thousands_separator'] ) ;
        
        $component =& JComponentHelper::getComponent('com_jea');
        
        $t_component =& JTable::getInstance('component');
        $t_component->load( $component->id );
        $t_component->bind( array('params'=> $config ));

        $redirectLink = 'index.php?option=com_jea&controller=config' ;
        $this->setRedirect( $redirectLink );
        
        if ( !$t_component->store() ) {
            
            JError::raiseWarning( 200, 'Error while trying to save config' );
           
        } else {
            
            $msg = JText::_('Config was successfully saved');
			$this->setRedirect( $redirectLink , $msg );
        }
        
    }
    
    
    /**
     * Restore default configuration
     *
     */
    function defaultConfig()
    {
		$component =& JComponentHelper::getComponent('com_jea');

        $t_component =& JTable::getInstance('component');
       
        $t_component->load( $component->id );
        $t_component->params='';       
        
        
        $redirectLink = 'index.php?option=com_jea&controller=config' ;
        $this->setRedirect( $redirectLink );
        
        if ( !$t_component->store() ) {
            
            JError::raiseWarning( 200, 'Error while trying to restore the default config' );
           
            
        } else {
            
            $msg = JText::_('Default config was successfully restored') ;
			$this->setRedirect( $redirectLink , $msg );
        }

    }
	
	
}
