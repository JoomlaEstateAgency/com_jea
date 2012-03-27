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
// no direct access
defined('_JEXEC') or die;

/**
 * @package     Joomla.Site
 * @subpackage  com_jea
 */
abstract class JHtmlUtility
{

    protected static $_params = null;

    /**
     * Format price following the component configuration.
     * If price is empty, return a default string value.
     *
     * @param float|int $price
     * @param string $default
     * @return unknown
     */
    public static function formatPrice($price=0 , $default='')
    {
        $params = self::getParams();
        if (!empty($price)) {
            $currency_symbol     = $params->get('currency_symbol', '&euro;');
            $decimal_separator   = $params->get('decimals_separator', ',');
            $price = self::formaNumber($price);

            // Is currency symbol before or after price ?
            if ($params->get('symbol_place', 1)) {
                $price = $price .' '. $currency_symbol;
            } else {
                $price = $currency_symbol .' '. $price;
            }
            return $price;

        } else {
            return $default ;
        }
    }

    /**
     * Format surface following the component configuration.
     * If surface is empty, return a default string value.
     *
     * @param float|int $price
     * @param string $default
     * @return unknown
     */
    public static function formatSurface($surface=0, $default='')
    {
        $params = self::getParams();
        if (!empty($surface)) {
            $surfaceMeasure   = $params->get('surface_measure', 'm&sup2;');
            $surface = self::formaNumber($surface);
            return $surface . ' ' . $surfaceMeasure;
        } else {
            return $default ;
        }
    }

    /**
     * Format number following the component configuration.
     *
     * @param float|int $price
     * @return unknown
     */
    public static function formaNumber ($number=O)
    {
        $params = self::getParams();
        $number = (float) $number;
        $decimal_separator   = $params->get('decimals_separator', ',');
        $thousands_separator = $params->get('thousands_separator', ' ');
        $decimals            = (int) $params->get('decimals_number', '0');
        return number_format( $number, $decimals, $decimal_separator, $thousands_separator);
    }


    protected static function getParams()
    {
        if (self::$_params == null) {
            self::$_params = JComponentHelper::getParams('com_jea');
        }
        return self::$_params;
    }

}

