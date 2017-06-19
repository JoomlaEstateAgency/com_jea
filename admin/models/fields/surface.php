<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2017 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

/**
 * Form Field class for JEA.
 * Provides a one line text field with the surface symbol
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @see         JFormFieldText
 *
 * @since       2.0
 */
class JFormFieldSurface extends JFormFieldText
{
	/**
	 * The form field type.
	 *
	 * @var string
	 */
	protected $type = 'Surface';

	/**
	 * Method to get the field input markup.
	 *
	 * @return string The field input markup.
	 */
	protected function getInput()
	{
		$input = parent::getInput();
		$params = JComponentHelper::getParams('com_jea');
		$surface_measure = $params->get('surface_measure', 'm²');

		return $input . ' <span class="input-suffix">' . $surface_measure . '</span>';
	}
}
