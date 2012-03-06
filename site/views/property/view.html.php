<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @version     $Id$
 * @package     Joomla.Site
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2012 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.view');

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

class JeaViewProperty extends JView
{
    function display( $tpl = null )
    {
        $state  = $this->get('State');
        $item   = $this->get('Item');
        $params = &$state->params;

        $this->assignRef('params', $params);
        $this->assignRef('state', $state);
        $this->assignRef('row', $item);

        if (empty($item->title)) {
            $pageTitle = ucfirst( JText::sprintf('COM_JEA_PROPERTY TYPE IN TOWN',
            $this->escape($item->type), $this->escape($item->town)));
        } else {
            $pageTitle = $this->escape($item->title) ;
        }

        $this->assign( 'page_title', $pageTitle );
         
        $app = JFactory::getApplication();
        $pathway = $app->getPathway();
        $pathway->addItem($pageTitle);

        $document= JFactory::getDocument();
        $document->setTitle($pageTitle);


        parent::display($tpl);
    }

    /**
     * Format price following the component configuration.
     * If price is empty, it return string default value.
     *
     * @param float|int $price
     * @param string $default
     * @return unknown
     */
    protected function formatPrice ( $price , $default="" )
    {
        if (!empty($price)) {
            $price = (float) $price;
            $currency_symbol     = $this->params->get('currency_symbol', '&euro;');
            $decimal_separator   = $this->params->get('decimals_separator', ',');
            $thousands_separator = $this->params->get('thousands_separator', ' ');
            $decimals            = (int) $this->params->get('decimals_number', '0');
            $price = number_format( $price, $decimals, $decimal_separator, $thousands_separator);
             
            // Is currency symbol before or after price ?
            if ($this->params->get('symbol_place', 1)) {
                 
                return $this->escape( $price .' '. $currency_symbol );

            } else {
                 
                return $this->escape( $currency_symbol .' '. $price );
            }
             
        } else {
             
            return $default ;
        }
    }

}
