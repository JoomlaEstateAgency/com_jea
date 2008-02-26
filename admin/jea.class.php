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



function dump($var, $label=null, $echo=true){
        // format the label
        $label = ($label===null) ? '' : rtrim($label) . ' ';

        // var_dump the variable into a buffer and keep the output
        ob_start();
        var_dump($var);
        $output = ob_get_clean();

        // neaten the newlines and indents
        $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
        if (PHP_SAPI == 'cli') {
            $output = PHP_EOL . $label
                    . PHP_EOL . $output
                    . PHP_EOL;
        } else {
            $output = '<pre>'
                    . $label
                    . htmlentities($output, ENT_QUOTES, 'UTF-8')
                    . '</pre>';
        }

        if ($echo) {
            echo($output);
        }
        return $output;
}

class ComJea
{
    
    function &getParams()
    {
		static $instance;
        
		if ( !is_object($instance) ) {
		    
		    $instance =& JComponentHelper::getParams('com_jea');
		    
		    //Sets a default values if not already assigned
		    $instance->def('surface_measure', html_entity_decode( 'm&sup2;', ENT_COMPAT, 'UTF-8' ));
		    $instance->def('currency_symbol', html_entity_decode( '&euro;', ENT_COMPAT, 'UTF-8' ));
		    $instance->def('thousands_separator', html_entity_decode( '&nbsp;', ENT_COMPAT, 'UTF-8' ));
		    $instance->def('decimals_separator', ',');
		    $instance->def('symbol_place', 1);
		    $instance->def('sort_price', 0);
		    $instance->def('sort_livingspace', 0);
		    $instance->def('sort_livingarea', 0);
		    $instance->def('list_limit', 10);
		    $instance->def('show_print_icon', 1);
		    $instance->def('jpg_quality', 90);
		    $instance->def('max_thumbnails', 120);
		    $instance->def('max_previews', 400);
		}

		return $instance ;
    }
    
    
    
    function runAdmin()
    {
		// Require the base controller
		require_once( JPATH_COMPONENT.DS.'AbstractController.php' );
		
		// Component Helper
		jimport('joomla.application.component.helper');
		
		// Require specific controller if requested
		$controller = JRequest::getWord('controller') ;
		$path = JPATH_COMPONENT.DS.'controllers'.DS ;
		
		$controller =  ucfirst($controller.'Controller');
		    
		   
		if (! file_exists( $path.$controller.'.php' ) ) {
		    
		   $controller = 'IndexController' ; //default controller
		    
		}
		
		require $path.$controller.'.php' ;
		 
		$classname = 'JEA_'.$controller ;
		
		if ( !class_exists($classname) )   
			die( $classname . ' : class was not defined' );
			
			
		$controller = new $classname();
		
		$action = JRequest::getVar( 'task' ) ? JRequest::getVar( 'task' ).'Action' : 'indexAction' ;
		
		
		$controller->preDispatch();
		
		if ( method_exists( $controller, $action ) ) {
		    
			// Perform the Request task
			$controller->execute( $action );
		
		}
		
		$controller->postDispatch();
		
		// Redirect if set by the controller
		$controller->redirect();
        
    }
    
    function runSite()
    {
		// Require the base controller
        require_once(JPATH_COMPONENT.DS.'controller.php');
		
		// Component Helper
		jimport('joomla.application.component.helper');
		
		// Create the controller
		$controller = new JeaController();
		
		// Perform the Request task
		$controller->execute(JRequest::getVar('task', null, 'default', 'cmd'));
		$controller->redirect();
        
    }
    
    
    
}