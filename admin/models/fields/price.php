<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2020 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Form\Field\TextField;

defined('JPATH_PLATFORM') or die;

/**
 * Form Field class for JEA.
 * Provides a one line text field with currency symbol
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @see         JFormFieldText
 *
 * @since       2.0
 */
class JFormFieldPrice extends TextField
{
	/**
	 * The form field type.
	 *
	 * @var string
	 */
	protected $type = 'Price';

	/**
	 * Method to change the label
	 *
	 * @param   string $label The field label
	 *
	 * @return void
	 */
	public function setLabel($label = '')
	{
		$this->label = $label;
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return string The field input markup.
	 */
	protected function getInput()
	{
		$input = parent::getInput();

		$params = ComponentHelper::getParams('com_jea');
		$symbol_place = $params->get('symbol_place', 1);
		$currency_symbol = $params->get('currency_symbol', '€');

		if ($symbol_place == 0)
		{
			return '<span class="input-prefix">' . $currency_symbol . '</span> ' . $input;
		}

		return $input . ' <span class="input-suffix">' . $currency_symbol . '</span>';
	}
}
