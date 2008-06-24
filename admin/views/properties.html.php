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

class PropertiesView extends JView
{

	var $id;
    var $cat = '';
    var $params = null;
    var $offset = 0;
    var $find_ref = 0;
	var $limit = 0;
	var $type_id = 0;
	var $town_id = 0;
	var $department_id = 0;
	var $condition_id = 0;
	var $area_id = 0;
	var $slogan_id = 0;
	var $hot_water_type = 0;
	var $heating_type = 0 ;
	var $ordering = 0;
	var $lists = array();
	var $pagination = null;
	var $row = null;
    
    
    function index()
	{
	    
	    jimport( 'joomla.html.pagination' );
	    
	    $model =& $this->getModel();
	    //$model->setCategory($this->cat);
	    
	    $params = array();
	    $params['offset'] = $this->offset;
	    $params['limit'] = $this->limit;
	    $params['type_id'] = $this->type_id;
	    $params['town_id']= $this->town_id;
	    $params['department_id'] = $this->department_id;
	    $params['ordering'] = $this->ordering;
	    $params['find_ref'] = $this->find_ref;
	    
	    $res = $model->getItems($params);
	    
	    $this->pagination = new JPagination($res['total'], $this->offset, $this->limit);
	    
	    $this->assign($res);
	    $this->_getHtmlLists();
	    $this->params =& ComJea::getParams();
	    
	    JToolBarHelper::title( JText::_( ucfirst( $this->cat ) . ' management' ), 'generic.png' );
	    
	    JToolBarHelper::publish();
	    JToolBarHelper::unpublish();
	    JToolBarHelper::addNew();
	    JToolBarHelper::editList();
	    JToolBarHelper::deleteList( JText::_( 'CONFIRM_DELETE_MSG' ) );
	    
	}
	

	
	function editProperty()
	{
	    $model =& $this->getModel();
	    $this->assign( $model->load( $this->id ) );
	    //dump($this->row);
	    
	    
	    $this->department_id  = $this->row->department_id ;
	    $this->condition_id   = $this->row->condition_id ;
	    $this->area_id        = $this->row->area_id ;
	    $this->slogan_id      = $this->row->slogan_id ;
	    $this->town_id        = $this->row->town_id ;
	    $this->type_id        = $this->row->type_id ;
	    $this->heating_type   = $this->row->heating_type ;
	    $this->hot_water_type = $this->row->hot_water_type ;
	    
	    
	    /*
	     * <?php echo ucfirst($categories_bien) ?> : <small> <?php echo $this->row->id ? 'Editer '.stripslashes($this->row->ref) : 'Nouveau';?> </small>
	     */
	    
	    $this->_getHtmlLists( false );
	    $this->params =& ComJea::getParams();

		JHTML::stylesheet('jea.admin.css', 'media/com_jea/css/');
	    
	    $title  = $this->cat == 'renting' ? JText::_( 'Renting' ) : JText::_( 'Selling' ) ;
	    $title .= ' : ' ;
	    $title .= $this->row->id ? JText::_( 'Edit' ) . ' ' . $this->escape( $this->row->ref ) : JText::_( 'New' ) ;
	    JToolBarHelper::title( $title , 'generic.png' ) ;
	    
	    JToolBarHelper::save() ;
	    JToolBarHelper::apply() ;
	    JToolBarHelper::cancel() ;
	}
	
	function _getHtmlLists( $grid = true )
	{
	    require_once JPATH_COMPONENT.DS.'models'.DS.'characteristicsmodel.php';
	    
	    $t_department    = '- ' . JText::_( 'Department' ).' -' ;
	    $t_condition     = '- ' . JText::_( 'Condition' ).' -' ;
	    $t_area          = '- ' . JText::_( 'Area' ).' -' ;
	    $t_slogan        = '- ' . JText::_( 'Slogan' ).' -' ;
	    $t_town          = '- ' . JText::_( 'Town' ).' -' ;
	    $t_property_type = '- ' . JText::_( 'Property type' ).' -' ;
	    $t_heating_type  = '- ' . JText::_( 'Heating type' ).' -' ;
	    $t_hotwater_type = '- ' . JText::_( 'Hot water type' ).' -' ;
	    
	    $lists = array( 'departments' => array( $t_department , 'department_id' , $this->department_id ),
	                    'conditions' => array( $t_condition , 'condition_id' , $this->condition_id ),
	                    'areas' => array( $t_area , 'area_id' , $this->area_id ),
	                    'slogans' => array( $t_slogan , 'slogan_id' , $this->slogan_id ),
	                    'towns' => array( $t_town , 'town_id' , $this->town_id ),
	                    'types' => array( $t_property_type , 'type_id' , $this->type_id ),
	                    'heatingtypes' => array( $t_heating_type , 'heating_type' , $this->heating_type ),
	                    'hotwatertypes' => array( $t_hotwater_type , 'hot_water_type' , $this->hot_water_type ),
	                  );

	    $onChange = $grid ? 'onchange="document.adminForm.submit();"' : '' ;
	    
	    foreach ( $lists as $k => $v){
	        $model = new JEA_CharacteristicsModel($k);
	        $lists[$k] = JHTML::_('select.genericlist', $model->getListForHtml($v[0]) , $v[1], 'class="inputbox" size="1" '.$onChange , 'value', 'text', $v[2]);    
	    }
	    
	    
		
	    $this->lists = $lists;
	}

	
	function getAdvantagesRadioList()
	{
	    //$html = '<div style="clear:both">&nbsp</div>';
	    $html = '';
	    $model = new JEA_CharacteristicsModel('advantages');
	    $res = $model->getItems();
	    $advantages = array();
	    
	    if ( !empty($this->row->advantages) ) {
	        $advantages = explode( '-' , $this->row->advantages );
	    }
	    
	    foreach ( $res['rows'] as $k=> $row ) {
	        
	        $checked = '';
	        
	        if ( in_array($row->id, $advantages) ) {
	            $checked = 'checked="checked"' ;
	        }
	        
	        $html .= '<label class="advantage">' . PHP_EOL 
	              .'<input type="checkbox" name="advantages[' . $k . ']" value="' 
				  . $row->id . '" ' . $checked . ' />' . PHP_EOL 
				  . $row->value . PHP_EOL 
	              . '</label>' . PHP_EOL ;
	    }
	   // $html .= '<div style="clear:both">&nbsp</div>';
	    return $html;
	}
	
}
