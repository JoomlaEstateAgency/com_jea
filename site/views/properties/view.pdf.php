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

class JeaViewProperties extends JeaView
{

    function display( $tpl = null )
    {
        // Request category
        $this->cat	= $this->params->get('cat', 'renting');

        $id	= JRequest::getInt('id', 0);

        if( $id && $this->getLayout() == 'default' ){
            echo $this->getItemDetail();
        }
    }

    function getItemDetail()
    {
        //try to increase memory
		ini_set( 'memory_limit' , '32M' );
    	
    	$text = '';
        $row =& $this->get('property');

        if(!$row->id){
            return $text;
        }

        $res = ComJea::getImagesById($row->id);

        $webpage_to_property = 'http://' .$_SERVER['SERVER_NAME']
        . str_replace('&format=pdf', '', $_SERVER['REQUEST_URI']) ;

         
        $page_title = ucfirst( JText::sprintf('PROPERTY TYPE IN TOWN',
        $this->escape($row->type), $this->escape($row->town)));

        $document=& JFactory::getDocument();
        $document->setTitle(  $page_title . ' ' . JText::_('ref'). ' : ' . $row->ref );
        $document->setName($row->ref);

        $text .= '<p><img src="'.$res['main_image']['preview_url'] .'" alt="" /></p>' ;
        $text .= $row->description . '<br /><br />' ;

        $text .= $row->is_renting ?  JText::_('Renting price') : JText::_('Selling price') ;
        $text .= ' : ' . $this->formatPrice( floatval($row->price) , JText::_('Consult us') ) . '<br />';

        if ( $row->charges ){
            $text .= JText::_('Charges') . ' : '
            . $this->formatPrice( floatval($row->charges) , JText::_('Consult us') ) . '<br />';
        }

        if ( $row->fees ){
            $text .= JText::_('Fees') . ' : '
            . $this->formatPrice( floatval($row->fees) , JText::_('Consult us') ) . '<br />';
        }

        if ( $row->condition ){
            $text .= JText::_('Condition') . ' : '
            . ucfirst($this->escape($row->condition)) . '<br />';
        }

        if ($row->living_space) {
            $text .=  JText::_( 'Living space' ) . ' : ' . $row->living_space . ' '
            . $this->params->get( 'surface_measure' ) . '<br />' ;
        }

        if ($row->land_space) {
            $text .=  JText::_( 'Land space' ) . ' : ' . $row->land_space . ' '
            . $this->params->get( 'surface_measure' ) . '<br />' ;
        }

        if ($row->rooms) {
            $text .=  JText::_( 'Number of rooms' ) . ' : ' . $row->rooms . '<br />' ;
        }

        if ($row->floor) {
            $text .=  JText::_( 'Number of floors' ) . ' : ' . $row->floor . '<br />' ;
        }

        if ($row->bathrooms) {
            $text .=  JText::_( 'Number of bathrooms' ) . ' : ' . $row->bathrooms . '<br />' ;
        }

        if ($row->toilets) {
            $text .=  JText::_( 'Number of toilets' ) . ' : ' . $row->toilets . '<br />' ;
        }

        if ($row->hot_water_type) {
            $text .=  JText::_( 'Hot water type' ) . ' : ' . ucfirst($this->escape( $row->hot_water )) . '<br />' ;
        }

        if ($row->heating_type) {
            $text .=  JText::_( 'Heating type' ) . ' : ' . ucfirst($this->escape( $row->heating )) . '<br />' ;
        }

        if (intval($row->availability)){
            $text .= '<p><em>' . JText::_('Availability date') . ' : ' . $row->availability . '</em> </p>' ;
        }
        
        $text .= '<br /><br />';

        if ( $row->advantages ) {
            $text .= '<h3>'. JText::_('Advantages') . ' :</h3>' ;
            $text .= $this->getAdvantages( $row->advantages );
        }

        $text .= '<h3>' . JText::_('Adress') . ' :</h3><br />';

        if ($row->adress)    $text .= trim( $row->adress ) . ",<br />\n";
        if ($row->zip_code) $text .= trim ( $row->zip_code ) . ' ' ;
        if ($row->town)     $text .= strtoupper( $this->escape($row->town) )."<br />\n" ;
        if ($row->area)
        $text .=  JText::_('Area') . ' : ' .  $this->escape( $row->area ). "\n" ;

        $text .= '<br /><br />' . $webpage_to_property ;

        return $text;
         
    }


    function getAdvantages( $advantages="" )
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

        foreach ( $options as $k=> $row ) { 
            if ( in_array($row->value, $advantages) ) {
                $html .=  "\t<li>{$row->text}</li>";   
            }
        }

        return "<ul>{$html}</ul>";
    }

}
