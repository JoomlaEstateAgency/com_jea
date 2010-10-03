<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 * 
 * @version     $Id$
 * @package		Jea.admin
 * @copyright	Copyright (C) 2008 PHILIP Sylvain. All rights reserved.
 * @license		GNU/GPL, see LICENSE.txt
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
    function version ()
    {
    	if(is_file(JPATH_COMPONENT_ADMINISTRATOR . DS . 'jea.xml')) {
        	jimport('joomla.utilities.simplexml');
        	$xml = new JSimpleXML();
            $xml->loadFile(JPATH_COMPONENT_ADMINISTRATOR . DS . 'jea.xml');
            return $xml->document->version[0]->data() ;
    	}
        return '';
    }
	
	/**
	 * Enter description here...
	 *
	 * @return JParameter
	 */
	function &getParams()
    {
		static $instance;
        
		if ( !is_object($instance) ) {
		    
		    $instance =& JComponentHelper::getParams('com_jea');
		    
		    // fix bug #10973 Warning: cannot yet handle MBCS in html_entity_decode()!	
		    $surface_measure = 'm' . JText::_('SYMBOL_SUP2');
		    $currency_symbol = JText::_('SYMBOL_EURO');
		    $thousands_separator = ' ';
		    // end fix bug #10973
		    
		    //Sets a default values if not already assigned
		    $instance->def('surface_measure', $surface_measure);
		    $instance->def('currency_symbol', $currency_symbol);
		    $instance->def('thousands_separator', $thousands_separator);
		    $instance->def('decimals_separator', ',');
		    $instance->def('symbol_place', 1);
		    $instance->def('orderby', '');
		    $instance->def('orderby_direction', 'asc');
		    $instance->def('sort_date', 0);
		    $instance->def('sort_price', 0);
		    $instance->def('sort_livingspace', 0);
		    $instance->def('sort_landspace', 0);
		    $instance->def('sort_hits', 0);
		    $instance->def('list_limit', 10);
		    $instance->def('show_print_icon', 1);
		    $instance->def('show_pdf_icon', 1);
		    $instance->def('images_layout', 'gallery');
		    $instance->def('show_contactform', 1);
		    $instance->def('default_mail', '');
		    $instance->def('send_form_to_agent', 0);
		    $instance->def('show_googlemap', 0);
		    $instance->def('googlemap_apikey', '');
		    $instance->def('jpg_quality', 90);
		    $instance->def('max_thumbnails_width', 120);
		    $instance->def('max_thumbnails_height', 90);
		    $instance->def('max_previews_width', 400);
		    $instance->def('max_previews_height', 400);
		    $instance->def('crop_thumbnails', 0);
		    $instance->def('secondaries_img_upload_number', 3);
		    $instance->def('relationship_dpts_towns_area', 1);
		}

		return $instance ;
    }
    
    function run( $defaultController='default' )
    {
		// Component Helper
		jimport('joomla.application.component.helper');
		
		
		// Require specific controller if requested
		if( $controller = JRequest::getWord('controller', $defaultController) ) {
			
			$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
			
			if ( file_exists($path) ) {
				require_once $path;
			} else {
				$controller = '';
			}
		}
		
		// Create the controller
		$classname	= 'JeaController'.ucfirst($controller);
		$controller	= new $classname( );
		
		// Perform the Request task
		$controller->execute( JRequest::getCmd( 'task' ) );
		$controller->redirect();
        
    }
    
    function &getAccess()
    {
        static $access = null;
        
        if ( $access === null) {
        
            $user   =& JFactory::getUser();

            // Create a user access object for the user
            $access                 = new stdClass();
            $access->canEdit        = $user->authorize('com_jea', 'edit', 'property', 'all');
            $access->canEditOwn     = $user->authorize('com_jea', 'edit', 'property', 'own');
        }
        
        return $access;
    }
    
    
    function getImagesById($id)
    {
    	$result = array();
    	
    	$rootURL = JURI::root();
    	
    	//main image
        $img = JPATH_ROOT.DS.'images'.DS.'com_jea'.DS.'images'.DS.$id.DS.'main.jpg';
        $img_url_base = $rootURL . 'images/com_jea/images/' . $id . '/' ;
        
        $result['main_image'] = array();
        
        require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'library/Iptc.php';

        if(is_file($img)){
        	$result['main_image']['name'] = 'main.jpg';
            $result['main_image']['url'] = $img_url_base . 'main.jpg';
            $result['main_image']['preview_url'] = $img_url_base . 'preview.jpg';
            $result['main_image']['min_url'] = $img_url_base . 'min.jpg';
            	
            $im = @getimagesize($img);
            $result['main_image']['width'] = $im[0];
            $result['main_image']['height'] = $im[1];
            	
            $file = stat ($img);
            $result['main_image']['weight'] = round(($file[7]/1024),1) ;// poid en Ko
            
            //iptc infos
            $iptc = new iptc($img);
            $result['main_image']['title']       = $iptc->get(IPTC_HEADLINE);
			$result['main_image']['description'] = $iptc->get(IPTC_CAPTION);
        }


        //secondaries images
        $dir = JPATH_ROOT.DS.'images'.DS.'com_jea'.DS.'images'.DS.$id.DS.'secondary';
        $result['secondaries_images'] = array();
        
        jimport('joomla.filesystem.folder');
        
        if( JFolder::exists( $dir ) ){
                
            $filesList = JFolder::files( $dir );

            $viewfilesList = array();
            foreach ( $filesList as $filename ) {

                $detail = array();
                $im = @getimagesize($dir.DS.$filename);
                if ($im !== FALSE){
                    $detail['name'] = $filename;
                    $detail['width'] = $im[0];
                    $detail['height'] = $im[1];
                    	
                    $file = stat ($dir.DS.$filename);
                    $detail['weight'] = round(($file[7]/1024),1) ;// poid en Ko
                    	
                    $detail['url'] = $img_url_base . 'secondary/' . $filename;
                    $detail['preview_url'] = $img_url_base . 'secondary/preview/' . $filename;
                    $detail['min_url'] = $img_url_base . 'secondary/min/' . $filename ;
                    
                    //iptc infos on medium image
		            $iptc = new iptc($dir.DS.'preview'.DS.$filename);
		            $detail['title']       = $iptc->get(IPTC_HEADLINE);
					$detail['description'] = $iptc->get(IPTC_CAPTION);
                    	
                    $viewfilesList[] = $detail ;
                }
            }
            
            $result['secondaries_images'] =  $viewfilesList ; 
        }
        
        return $result ;
    }
    
    
    
}