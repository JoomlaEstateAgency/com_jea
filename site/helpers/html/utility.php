<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Site
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2017 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Jea Utility helper
 *
 * @package     Joomla.Site
 * @subpackage  com_jea
 *
 * @since       2.0
 */
abstract class JHtmlUtility
{
	protected static $params = null;

	/**
	 * Format price following the component configuration.
	 * If price is empty, return a default string value.
	 *
	 * @param   float|int  $price    The price as number
	 * @param   string     $default  Default value if price equals 0
	 *
	 * @return  string
	 */
	public static function formatPrice($price = 0, $default = '')
	{
		$params = self::getParams();

		if (! empty($price))
		{
			$currency_symbol = $params->get('currency_symbol', '&euro;');
			$decimal_separator = $params->get('decimals_separator', ',');
			$price = self::formaNumber($price);

			// Is currency symbol before or after price ?
			if ($params->get('symbol_position', 1))
			{
				$price = $price . ' ' . $currency_symbol;
			}
			else
			{
				$price = $currency_symbol . ' ' . $price;
			}

			return $price;
		}
		else
		{
			return $default;
		}
	}

	/**
	 * Format surface following the component configuration.
	 * If surface is empty, return a default string value.
	 *
	 * @param   float|int  $surface  The surface as number
	 * @param   string     $default  Default value if surface equals 0
	 *
	 * @return  string
	 */
	public static function formatSurface($surface = 0, $default = '')
	{
		$params = self::getParams();

		if (!empty($surface))
		{
			$surfaceMeasure = $params->get('surface_measure', 'm&sup2;');
			$surface = self::formaNumber($surface);

			return $surface . ' ' . $surfaceMeasure;
		}

		return $default;
	}

	/**
	 * Format number following the component configuration.
	 *
	 * @param   float|int  $number  The number to format
	 *
	 * @return  string
	 */
	public static function formaNumber($number = O)
	{
		$params = self::getParams();
		$number = (float) $number;
		$decimal_separator = $params->get('decimals_separator', ',');
		$thousands_separator = $params->get('thousands_separator', ' ');
		$decimals = (int) $params->get('decimals_number', '0');

		return number_format($number, $decimals, $decimal_separator, $thousands_separator);
	}

	/**
	 * Get JEA params
	 *
	 * @return Joomla\Registry\Registry
	 */
	protected static function getParams()
	{
		if (self::$params == null)
		{
			self::$params = JComponentHelper::getParams('com_jea');
		}

		return self::$params;
	}
}
