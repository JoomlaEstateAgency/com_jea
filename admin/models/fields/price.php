<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Form Field class for the Joomla Platform.
 * Supports a one line text field.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @link        http://www.w3.org/TR/html-markup/input.text.html#input.text
 * @since       11.1
 */
class JFormFieldPrice extends JFormFieldText
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 *
	 * @since  11.1
	 */
	protected $type = 'Price';
	
	
	/**
	 * Method to change the label
	 * @param string $label
	 */
	public function setLabel($label='')
	{
	    $this->label = $label;
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		$input = parent::getInput();
		
		$params = JComponentHelper::getParams('com_jea');
		$symbol_place = $params->get('symbol_place', 1);
		$currency_symbol = $params->get('currency_symbol', '€');
		$currency_symbol = '<span class="input-suffix">' . $currency_symbol . '</span>';
		
		if ($symbol_place == 0) {
		    return $currency_symbol . ' ' . $input;
		}
		
		return $input . ' ' . $currency_symbol;
		
	}
}
