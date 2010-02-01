<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @version     0.9 2009-10-14
 * @package     Jea.site
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

require_once JPATH_COMPONENT.DS.'view.php';

class JeaViewProperties extends JeaView 
{

	function display( $tpl = null )
	{
		// Request category
		$this->cat	= JRequest::getVar('cat', $this->params->get('cat', 'renting'));

		$id	= JRequest::getInt('id', 0);

		if( $id ){
		    if ($this->getLayout() == 'default'){
			    $tpl = 'item';
		    }
			$this->getItemDetail( $id );

		} else {

			$this->getItemsList();
		}

		JHTML::script('jea.js', 'media/com_jea/js/', false);

		parent::display($tpl);
	}


	function getItemsList()
	{
		$res = $this->get('properties');
		jimport('joomla.html.pagination');
		
		$this->pagination = new JPagination($res['total'], $res['limitstart'], $res['limit']);
		$this->assign($res);
	}

	function getItemDetail( $id )
	{
	  
		$row =& $this->get('property');
		
	    if(!$row->id){
            return;
        }
		
		$this->assignRef('row', $row);
		
		$res = ComJea::getImagesById($row->id);
		$this->assignRef('main_image', $res['main_image']);
		$this->assignRef('secondaries_images', $res['secondaries_images']);
		
	  
		$page_title = ucfirst( JText::sprintf('PROPERTY TYPE IN TOWN',
		$this->escape($row->type), $this->escape($row->town)));

		$this->assign( 'page_title', $page_title );
	  
		$mainframe =& JFactory::getApplication();
		$pathway =& $mainframe->getPathway();
		$pathway->addItem( $page_title );
		
		$document=& JFactory::getDocument();
		$document->setTitle( $page_title );
	  
	}


	function getPrevNextItems()
	{
		$model =& $this->getModel();
		$res = $this->get('previousAndNext');
	  
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

	function getViewUrl ( $id=0, $params='' )
	{
		if ( $id ) {
			$params .= '&id=' . intval( $id ) ;
		}
	  
		return JRoute::_( $params );
	}
	

	function getAdvantages( $advantages="" , $format="" )
	{
	  
		if ( !empty($advantages) ) {
			$advantages = explode( '-' , $advantages );
		} else {
			$advantages=array();
		}
	  
		$html = '';

		$model =& $this->getModel();
		$options = $model->getFeatureList('advantages');
		array_shift($options);
	  
		if ( empty($advantages) && $format == 'checkbox' ) {

			foreach ( $options as $k=> $row ) {
				$html .= '<label class="advantage">'
				.'<input type="checkbox" name="advantages[' . $k . ']" value="'. $row->value .'" />'
				. $row->text  . '</label><br />' . PHP_EOL ;
			}
			
		}  else {

			foreach ( $options as $k=> $row ) {
	    
				if ( in_array($row->value, $advantages) ) {
	     
					if ( $format == 'ul' ) {

						$html .=  "\t<li>{$row->text}</li>\n";

					}  else  {

						if ( !isset($count) ){
							$html .= $row->text;
							$count = 1;
						} else {
							$html .= ', ' . $row->text;
						}
					}
				}
			}
			 
			if ( $format == 'ul' ) {
				$html = "<ul>\n{$html}</ul>\n" ;
			}
		}

		// $html .= '<div style="clear:both">&nbsp</div>';
		return $html;
	}

	function getSearchparameters()
	{
		
		$html='';
		$model =& $this->getModel();
		
		$html .= '<strong>' . Jtext::_(JRequest::getCmd('cat', '')) . '</strong><br />' . PHP_EOL ;

		if( $type_id = JRequest::getInt('type_id', 0) ) {
			$type =& $model->getFeature('types');
			$type->load($type_id);
			$html .= '<strong>' . Jtext::_('Property type') . ' : </strong>'
			. $type->value . '<br />' . PHP_EOL;
		}
	  
		if( $department_id = JRequest::getInt('department_id', 0) ) {
			$department =& $model->getFeature('departments');
			$department->load($department_id);
			$html .= '<strong>' . Jtext::_('Department') . ' : </strong>'
			. $department->value . '<br />' . PHP_EOL;
		}

		if( $town_id = JRequest::getInt('town_id', 0) ) {
			$town =& $model->getFeature('towns');
			$town->load($town_id);
			$html .= '<strong>' . Jtext::_('Town') . ' : </strong>'
			. $town->value . '<br />' . PHP_EOL;
		}
		if( $budget_min = JRequest::getFloat('budget_min', 0.0) ) {
			$html .= '<strong>' . Jtext::_('Budget min') . ' : </strong>'
			. $this->formatPrice($budget_min) . '<br />' . PHP_EOL;
		}

		if( $budget_max = JRequest::getFloat('budget_max', 0.0) ) {
			$html .= '<strong>' . Jtext::_('Budget max') . ' : </strong>'
			. $this->formatPrice($budget_max) . '<br />' . PHP_EOL;
		}

		if( $living_space_min = JRequest::getInt('living_space_min', 0, 'jea_search' ) ) {
			$html .= '<strong>' . Jtext::_('Living space min') . ' : </strong>'
			. $living_space_min .' '. $this->params->get( 'surface_measure' ) . '<br />' . PHP_EOL;
		}

		if( $living_space_max = JRequest::getInt('living_space_max', 0, 'jea_search') ) {
			$html .= '<strong>' . Jtext::_('Living space max') . ' : </strong>'
			. $living_space_max .' '. $this->params->get( 'surface_measure' ) . '<br />' . PHP_EOL;
		}

		if( $rooms_min = JRequest::getInt('rooms_min', 0, 'jea_search') ) {
			$html .= '<strong>' . Jtext::_('Minimum number of rooms') . ' : </strong>'
			. $rooms_min . '<br />' . PHP_EOL;
		}


		if ( $advantages = JRequest::getVar( 'advantages', array(), '', 'array' ) ){
				
			$options = $model->getFeatureList('advantages');
			array_shift($options);
			
			$temp = array();
			
			foreach ($options as  $advantage) {
				$temp[$advantage->value] = $advantage->text ;
			}
			
			$html .= '<strong>' . Jtext::_('Advantages') . ' : </strong>' . PHP_EOL
			. '<ul>'. PHP_EOL ;
				
			foreach($advantages as $id){
				if (isset($temp[$id]))
					$html .= '<li>' . $temp[$id] .'</li>' . PHP_EOL ;
			}
			$html .= '</ul>' . PHP_EOL ;
		}

		return $html;
	}


	function getHtmlList($table, $title, $id )
	{
		$model =& $this->getModel();
		$options = $model->getFeatureList($table, $title);
		return JHTML::_('select.genericlist', $options , $id, 'class="inputbox" size="1" ' , 'value', 'text', 0);
	}
	
	function activateGoogleMap(&$row, $domId )
	{
	    #mootools bugfix
	    JHTML::_('behavior.mootools');
		
		$key = $this->params->get('googlemap_apikey', '');
	    
	    if((!$key) || (!$row->adress) || (!$row->town)) return false;
	    
	    $address = str_replace(array("\n", "\r\n"), '', addslashes($row->adress)); 
	    $town = str_replace(array("\n", "\r\n"), '', addslashes($row->town));
	    
	    $document = &JFactory::getDocument();
	    $a_lang = explode('-', $document->getLanguage());
        $document->addScript( 'http://maps.google.com/maps?file=api&amp;v=2.x&amp;key=' 
                              . $key . '&amp;hl=' . $a_lang[0] );
        
        $script = <<<EOD
var map = null;
var geocoder = null;
    
function initializeMap() {
    if (GBrowserIsCompatible()) {
        map = new GMap2(document.getElementById("$domId"));
        map.enableScrollWheelZoom();
        map.setCenter(new GLatLng(50, 0), 2);
        map.addControl(new GLargeMapControl());
        map.addControl(new GMenuMapTypeControl());
        geocoder = new GClientGeocoder();
    }
}

function showAddress(address) {
  if (geocoder) {
    geocoder.getLatLng(
      address,
      function(point) {
        if (!point) {
          alert(address + " not found");
        } else {
          map.setCenter(point, 13);
          var marker = new GMarker(point);
          map.addOverlay(marker);
          marker.openInfoWindowHtml(address);
        }
      }
    );
  }
}

window.addEvent("domready", function(){
    initializeMap();
    showAddress("$address, $town")   
});

window.addEvent("unload", function(){
    GUnload();   
});
EOD;
        $document->addScriptDeclaration($script);
        
	    return true ;
	}
	
    
}
