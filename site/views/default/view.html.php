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

		JHTML::script('jea.js', 'media/com_jea/js/', false);

		parent::display($tpl);
	}


	function getItemsList()
	{
		$mainframe =& JFactory::getApplication();

		$limit = $mainframe->getUserStateFromRequest( 'com_jea.limit', 'limit', 
		                                               $this->params->get('list_limit', 10), 'int' );
		$ordering = $mainframe->getUserStateFromRequest( 'com_jea.ordering', 'ordering', null, 'int' );
		 
	  
		$limitstart	= JRequest::getInt('limitstart', 0);

		$model =& $this->getModel();

		$params = array();
		$params['offset'] = $limitstart;
		$params['limit'] = $limit;

		if( JRequest::getVar('task') == 'search'){

			$session =& JFactory::getSession();
	
			$model->setCategory($session->get('cat', '', 'jea_search'));
			$params['type_id'] = $session->get('type_id' , 0, 'jea_search');
			$params['department_id'] = $session->get('department_id', 0, 'jea_search');
			$params['town_id']= $session->get('town_id', 0, 'jea_search');

		} else {

			$model->setCategory($this->cat);
			$params['type_id'] = $this->params->get('type_id', 0);
			$params['department_id'] = $this->params->get('department_id', 0);
			$params['town_id']= $this->params->get('town_id', 0);
			$params['area_id'] = $this->params->get('area_id', 0);
		}
	  
		$params['ordering'] = $ordering;
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
	  
		$page_title = ucfirst( JText::sprintf('PROPERTY TYPE IN TOWN',
		$this->escape($this->row->type), $this->escape($this->row->town)));

		$this->assign( 'page_title', $page_title );
	  
		$mainframe =& JFactory::getApplication();
		$pathway =& $mainframe->getPathway();
		$pathway->addItem( $page_title );
		
		$document=& JFactory::getDocument();
		$document->setTitle( $page_title );
	  
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



	function getViewUrl ( $id=0, $params='' )
	{
		$url = 'index.php?option=com_jea&view=default&Itemid=' . JRequest::getInt('Itemid', 0 ) ;
	  
		if ( $id ) {
			$url .= '&id=' . intval( $id ) ;
		}
	  
		return JRoute::_( $url . $params );
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
	  
		$model = new JEA_CharacteristicsModel('advantages');
		$res = $model->getItems();
	  
		if ( empty($advantages) && $format == 'checkbox' ) {

			foreach ( $res['rows'] as $k=> $row ) {
				$html .= '<label class="advantage">'
				.'<input type="checkbox" name="advantages[' . $k . ']" value="'. $row->id .'" />'
				. $row->value  . '</label><br />' . PHP_EOL ;
			}

		} else {
	   
			foreach ( $res['rows'] as $k=> $row ) {
	    
				if ( in_array($row->id, $advantages) ) {
	     
					if ( $format == 'ul' ) {

						$html .=  "\t<li>{$row->value}</li>\n";

					}  else  {

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
		}

		// $html .= '<div style="clear:both">&nbsp</div>';
		return $html;
	}

	function getSearchparameters()
	{
		require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'characteristicsmodel.php';
		
		$session =& JFactory::getSession();

		$html='';
		$html .= '<strong>' . Jtext::_($session->get('cat', '', 'jea_search')) . '</strong><br />' . PHP_EOL ;

		if( $type_id = $session->get('type_id', 0, 'jea_search') ) {
			$model = new JEA_CharacteristicsModel('types');
			$type = $model->load($type_id);
			$html .= '<strong>' . Jtext::_('Property type') . ' : </strong>'
			. $type->value . '<br />' . PHP_EOL;
		}
	  
		if( $department_id = $session->get('department_id', 0, 'jea_search') ) {
			$model = new JEA_CharacteristicsModel('departments');
			$department = $model->load($department_id);
			$html .= '<strong>' . Jtext::_('Department') . ' : </strong>'
			. $department->value . '<br />' . PHP_EOL;
		}

		if( $town_id = $session->get('town_id', 0, 'jea_search') ) {
			$model = new JEA_CharacteristicsModel('towns');
			$town = $model->load($town_id);
			$html .= '<strong>' . Jtext::_('Department') . ' : </strong>'
			. $town->value . '<br />' . PHP_EOL;
		}
		if( $budget_min = $session->get('budget_min', 0, 'jea_search') ) {
			$html .= '<strong>' . Jtext::_('Budget min') . ' : </strong>'
			. $this->formatPrice($budget_min) . '<br />' . PHP_EOL;
		}

		if( $budget_max = $session->get('budget_max', 0, 'jea_search') ) {
			$html .= '<strong>' . Jtext::_('Budget max') . ' : </strong>'
			. $this->formatPrice($budget_max) . '<br />' . PHP_EOL;
		}

		if( $living_space_min = $session->get('living_space_min', 0, 'jea_search' ) ) {
			$html .= '<strong>' . Jtext::_('Living space min') . ' : </strong>'
			. $living_space_min .' '. $this->params->get( 'surface_measure' ) . '<br />' . PHP_EOL;
		}

		if( $living_space_max = $session->get('living_space_max', 0, 'jea_search') ) {
			$html .= '<strong>' . Jtext::_('Living space max') . ' : </strong>'
			. $living_space_max .' '. $this->params->get( 'surface_measure' ) . '<br />' . PHP_EOL;
		}

		if( $rooms_min = $session->get('rooms_min', 0, 'jea_search') ) {
			$html .= '<strong>' . Jtext::_('Minimum number of rooms') . ' : </strong>'
			. $rooms_min . '<br />' . PHP_EOL;
		}


		if ( $advantages = $session->get('advantages', array(), 'jea_search') ){
				
			$model = new JEA_CharacteristicsModel('advantages');
			$html .= '<strong>' . Jtext::_('Advantages') . ' : </strong>' . PHP_EOL
			. '<ul>'. PHP_EOL ;
				
			foreach($advantages as $id){
				$advantage = $model->load($id);
				$html .= '<li>' . $advantage->value .'</li>' . PHP_EOL ;
			}
			$html .= '</ul>' . PHP_EOL ;
		}

		return $html;
	}


	function getHtmlList($table, $title, $id ){

		require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'characteristicsmodel.php';
		$model = new JEA_CharacteristicsModel($table);
		return JHTML::_('select.genericlist', $model->getListForHtml($title) , $id, 'class="inputbox" size="1" ' , 'value', 'text', 0);
	}

}
