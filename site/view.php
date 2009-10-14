<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @version     0.9 2009-10-14
 * @package     Jea.site
 * @copyright   Copyright (C) 2008 PHILIP Sylvain. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 * Joomla Estate Agency is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses.
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view');

class JeaView extends JView
{
    /**
     * component configuration instance
     *
     * @var JParameter
     */
    var $params = null;
    
    
    /**
     * constructor
     *
     * @param array $config
     */
    function __construct($config = array())
    {
        parent::__construct($config);

        // Get the page/component configuration
        $this->params =& ComJea::getParams();
    }
    
    
    /**
     * Format price following the component configuration.
     * If price is empty, it return string default value.
     * 
     * @param float|int $price
     * @param string $default
     * @return unknown
     */
    function formatPrice ( $price , $default="" )
    {
        if ( !empty($price) ) {
             
            $price = $this->formatNumber( $price );
             
            $currency_symbol = $this->params->get('currency_symbol', '&euro;');
             
            // Is currency symbol before or after price ?
            if ( $this->params->get('symbol_place', 1) ) {
                 
                return $this-> escape( $price .' '. $currency_symbol );

            } else {
                 
                return $this-> escape( $currency_symbol .' '. $price );
            }
             
        } else {
             
            return $default ;
        }
    }
    
    
    /**
     * Format a number
     *
     * @param float|int $number
     * @param int $decimals
     * @return string
     */
    function formatNumber( $number=0, $decimals=0 )
    {
        //verify if we need to represent decimal (ex : 2.00 = 2)
        $temp = intval($number);
        if (($temp - $number) == 0.0 ) {
            $decimals=0 ;
        }
        
        //decode charset before using number_format
        jimport('joomla.utilities.string');
        if (function_exists('iconv')) {
            $decimal_separator   = JString::transcode( $this->params->get('decimals_separator', ',') , $this->_charset, 'ISO-8859-1' );
            $thousands_separator = JString::transcode( $this->params->get('thousands_separator', ' '), $this->_charset, 'ISO-8859-1' );
        } else {
            $decimal_separator   = utf8_decode( $this->params->get('decimals_separator', ','));
            $thousands_separator = utf8_decode( $this->params->get('thousands_separator', ' '));
        }
        $number = number_format( $number, $decimals, $decimal_separator, $thousands_separator ) ;
         
        //re-encode
        if (function_exists('iconv')) {
            $number = JString::transcode( $number, 'ISO-8859-1', $this->_charset );
        } else {
            $number = utf8_encode( $number );
        }
        
        return $number;
    }
}
