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

class JeaViewProperties extends JView
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

		JHTML::script('jea.js', 'media/com_jea/js/', false);

		parent::display($tpl);
	}


	function getItemsList()
	{
		$res = $this->get('properties');
		jimport('joomla.html.pagination');
		
		$this->pagination = new JPagination($res['total'], $res['limitstart'], $res['limit']);
		
		$this->assign('total',	$res['total']);
		$this->assign('order',	$res['order']);
		$this->assignRef('rows',$res['rows']);
	}

	function getItemDetail( $id )
	{
	  
		$row =& $this->get('property');
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

	function formatPrice ( $price , $default="" )
	{
		if ( !empty($price) ) {
			 
			//decode charset before using number_format
			jimport('joomla.utilities.string');
			if (function_exists('iconv')) {
				$decimal_separator   = JString::transcode( $this->params->get('decimals_separator', ',') , $this->_charset, 'ISO-8859-1' );
				$thousands_separator = JString::transcode( $this->params->get('thousands_separator', ' '), $this->_charset, 'ISO-8859-1' );
			} else {
				$decimal_separator   = utf8_decode( $this->params->get('decimals_separator', ','));
				$thousands_separator = utf8_decode( $this->params->get('thousands_separator', ' '));
			}
			$price = number_format( $price, 0, $decimal_separator, $thousands_separator ) ;
			 
			//re-encode
			if (function_exists('iconv')) {
				$price = JString::transcode( $price, 'ISO-8859-1', $this->_charset );
			} else {
				$price = utf8_encode( $price );
			}
			 
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
			$html .= '<strong>' . Jtext::_('Department') . ' : </strong>'
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

}
