<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @version     O.7 2009-01-22
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
require_once JPATH_COMPONENT.DS.'models'.DS.'properties.php';
require_once JPATH_COMPONENT_ADMINISTRATOR .DS.'models'.DS.'features.php';

class JeaViewManage extends JeaView 
{
    
    
    function &getModel()
    {
        static $model = null;
        if ($model === null){
            $model = new JeaModelProperties();
        }
        return $model;
    }
    
    
	function display( $tpl = null )
	{
        $access =& ComJea::getAccess();
	    
	    if(!($access->canEdit || $access->canEditOwn)){
            echo JText::_('Unauthorized access');
            return;
        }

		if( $this->getLayout() == 'form'){

			$this->getItemDetail();

		} else {

			$this->getItemsList();
		}

        $this->assignRef('access', $access);
		parent::display($tpl);
	}


	function getItemsList()
	{
	    $model =& $this->getModel();
	    $res = $model->getUserProperties();
		jimport('joomla.html.pagination');
		
		$this->pagination = new JPagination($res['total'], $res['limitstart'], $res['limit']);
		
		$this->assign($res);
	}

	function getItemDetail()
	{
        
	    $model =& $this->getModel();
	    
	    $row =& $model->getProperty();
	    
		$this->assignRef('row', $row);

		$res = ComJea::getImagesById($row->id);
		
		
	    if (!empty($res['main_image']) && is_array($res['main_image'])) {
            $res['main_image']['delete_url'] = JRoute::_('&task=deleteimg&id='.$row->id ) ;
        }
            
        foreach ( $res['secondaries_images']  as $k => $v) {
            $res['secondaries_images'][$k]['delete_url'] = JRoute::_(   '&task=deleteimg&image='.$v['name'] 
                                                                       . '&id='.$row->id ) ;
        }
		
        if($row->id){
            $page_title = ucfirst( JText::sprintf('Edit property', $this->escape($row->ref)));
        } else {
            $page_title = ucfirst( JText::_('New property'));
        }
    
		$this->assignRef('main_image', $res['main_image']);
		$this->assignRef('secondaries_images', $res['secondaries_images']);

		$this->assign( 'page_title', $page_title );

		$mainframe =& JFactory::getApplication();
		$pathway =& $mainframe->getPathway();
		$pathway->addItem( $page_title );

		$document=& JFactory::getDocument();
		$document->setTitle( $page_title );

	}
	
	
    function getHtmlList($tableName, $default=0 )
    {   
        static $lists = null;
        
        if (!is_array($lists)) {
        
            $t_department    = '- ' . JText::_( 'Department' ).' -' ;
            $t_condition     = '- ' . JText::_( 'Condition' ).' -' ;
            $t_area          = '- ' . JText::_( 'Area' ).' -' ;
            $t_slogan        = '- ' . JText::_( 'Slogan' ).' -' ;
            $t_town          = '- ' . JText::_( 'Town' ).' -' ;
            $t_property_type = '- ' . JText::_( 'Property type' ).' -' ;
            $t_heating_type  = '- ' . JText::_( 'Heating type' ).' -' ;
            $t_hotwater_type = '- ' . JText::_( 'Hot water type' ).' -' ;
            
            $lists = array( 'departments' => array( $t_department , 'department_id'),
                            'conditions' => array( $t_condition , 'condition_id' ),
                            'areas' => array( $t_area , 'area_id' ),
                            'slogans' => array( $t_slogan , 'slogan_id' ),
                            'towns' => array( $t_town , 'town_id' ),
                            'types' => array( $t_property_type , 'type_id' ),
                            'heatingtypes' => array( $t_heating_type , 'heating_type' ),
                            'hotwatertypes' => array( $t_hotwater_type , 'hot_water_type' ),
                          );
        }
        
        if ( isset($lists[$tableName]) ) {
            
            $featuresModel = new JeaModelFeatures();
            $featuresModel->setTableName( $tableName );
            
            return JHTML::_('select.genericlist', 
                            $featuresModel->getListForHtml($lists[$tableName][0]) , 
                            $lists[$tableName][1], 
                            'class="inputbox" size="1" ' , 
                            'value', 
                            'text', 
                            $default );
        }
        
        return 'list Error';    
    }
    
    function getAdvantagesRadioList()
    {
        $html = '';
        
        $featuresModel = new JeaModelFeatures();
        $featuresModel->setTableName( 'advantages' );
        $res = $featuresModel->getItems();
        
        $advantages = array();
        
        if ( !empty( $this->row->advantages ) ) {
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
        return $html;
    }
    
    function is_checkout( $checked_out )
    {
        if ($this->user && JTable::isCheckedOut( $this->user->get('id'), $checked_out ) ) {
            return true;
        }
        return false;
    }


}
