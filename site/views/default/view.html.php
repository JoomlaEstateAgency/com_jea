<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 * 
 * @version		0.1 2008-02-26
 * @package		Jea.site
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

require JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'characteristicsmodel.php' ;

class JeaViewDefault extends JView
{
	var $params = null;
	
	function display( $tpl = null )
	{
		// Get the page/component configuration
		$this->params =& ComJea::getParams();
		
		// Request category
		$this->cat	= $this->params->get('cat', 'renting');
		
		$id	= JRequest::getInt('id', 0);
		
		if( $id ){
		    $tpl = 'item';
		    $this->getItemDetail( $id );
		
		} else {
		    
		    $this->getItemsList();
		}
		
		$style = '<link rel="stylesheet" type="text/css" href="components/com_jea/medias/css/jea.css" />';
		$js    = '<script type="text/javascript" src="components/com_jea/medias/js/jea.js"></script>' ;
		
		$mainframe =& JFactory::getApplication();
		$mainframe->addCustomHeadTag($style);
		$mainframe->addCustomHeadTag($js);
		
		parent::display($tpl);
	}
	
	
	function getItemsList()
	{   
	    $mainframe =& JFactory::getApplication();

	    $limit = $mainframe->getUserStateFromRequest( 'com_jea.limit', 
								                      'limit', 
								                       $this->params->get('list_limit', 10), 
								                      'int' );
	    
		$limitstart	= JRequest::getInt('limitstart', 0);
		
		$model =& $this->getModel();
		$model->setCategory($this->cat);
		
		$params = array();
	    $params['offset'] = $limitstart;
	    $params['limit'] = $limit;

	    $params['type_id'] = $this->params->get('type_id', 0);
	    $params['department_id'] = $this->params->get('department_id', 0);
		$params['town_id']= $this->params->get('town_id', 0);
	    $params['area_id'] = $this->params->get('area_id', 0);
	    $params['ordering'] = JRequest::getCmd( 'ordering', null );
		$params['published'] = 1;
		
		$res = $model->getItems($params);

		$this->assign('total',	$res['total']);
		$this->assignRef('rows',$res['rows']);
		
	    jimport('joomla.html.pagination');
	    $this->pagination = new JPagination($res['total'], $limitstart, $limit);
	    
	}

	function getItemDetail( $id )
	{
	    
	    $model =& $this->getModel();
	    $model->setCategory($this->cat);
	    
	    $this->assign( $model->load($id) );
	    $this->assign(  );
	}
	
	
	function getPrevNextItems( $id )
	{
	    $model =& $this->getModel();
	    $res = $model->getPreviousAndNext($id);
	    
	    $html = '';
	    
	    $previous =  '&lt;&lt; ' . JText::_('Previous') ;
	    $next     =   JText::_('Next') . ' &gt;&gt;' ;
	    
	    if ( $res['prev_item'] ) {
	         
	         $html .= '<a href="' . $this->getViewUrl($res['prev_item']->id) . '">' . $previous . '</a>' ;
         } else {
             $html .=  $previous ;
         }
         
         $html .= '&nbsp;&nbsp;&nbsp;&nbsp;' ;
  
		if ($res['next_item']) {
		    
	         $html .= '<a href="' . $this->getViewUrl($res['next_item']->id) . '">' . $next . '</a>' ;
		}  else {
		 
		 $html .= $next ;
		}
		
		return $html;
		
	}
	
	
	
	function getViewUrl ( $id=0 )
	{
	    $url = 'index.php?option=com_jea&view=default&Itemid=' . JRequest::getInt('Itemid', 0 ) ;
	    
	    if ( $id ) {
	        $url .= '&id=' . intval( $id ) ;
	    }
	    
	    return JRoute::_( $url );
	}
	
	function formatPrice ( $price , $default="" )
	{

	    if ( $price ) {
	        
	        //decode charset before using number_format
	        jimport('joomla.utilities.string');
	        $decimal_separator   = JString::transcode( $this->params->get('decimals_separator', ',') , $this->_charset, 'ISO-8859-1' );
	        $thousands_separator = JString::transcode( $this->params->get('thousands_separator', ' '), $this->_charset, 'ISO-8859-1' );

	        $price = number_format( $price, 0, $decimal_separator, $thousands_separator ) ;
	        
	        //re-encode
	        $price = JString::transcode( $price, 'ISO-8859-1', $this->_charset );	        
	        
	        $currency_symbol = $this->params->get('currency_symbol', '&euro;');
	                                
	        //is currency symbol before or after price?
	        if ( $this->params->get('symbol_place', 1) ) {
	            
	            return $this-> escape( $price .' '. $currency_symbol );
	           
	        } else {
	            
	            return $this-> escape( $currency_symbol .' '. $price ); 
	        }
	        
	    } else {
	        
	        return $default ;
	    }
	}
	
	function getAdvantages( $advantages , $format="" )
	{
	    
		if ( empty($advantages) ) {
	        return '';
	    }
	    
	    $advantages = explode( '-' , $advantages );
	    
	    $html = '';
	    
	    $model = new JEA_CharacteristicsModel('advantages');
	    $res = $model->getItems();
	    
	    foreach ( $res['rows'] as $k=> $row ) {
	        
	        if ( in_array($row->id, $advantages) ) {
	            
	            if ( $format == 'ul' ){
	                
	                $html .=  "\t<li>{$row->value}</li>\n";

	            } else {
	                if ( !isset($count) ){
		                $html .= $row->value;
		                $count = 1;
		            } else {
		                $html .= ', ' . $row->value;
		            }
	            }
	        }
	    }
	    
	    if ( $format == 'ul' ) {
	        $html = "<ul>\n{$html}</ul>\n" ;
	    }
	   // $html .= '<div style="clear:both">&nbsp</div>';
	    return $html;
	}
	
	
	
	
}
