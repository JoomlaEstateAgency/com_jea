<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2017 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;

/**
 * Content administrator html helper class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @since       2.0
 */
abstract class JHtmlContentAdministrator
{
	/**
	 * Helper to display the featured icon in a list of items
	 *
	 * @param   int      $value      The state value
	 * @param   int      $i          The list counter value
	 * @param   boolean  $canChange  The user right to change the state
	 *
	 * @return string
	 */
	static public function featured($value = 0, $i = 0, $canChange = true)
	{
		// Array of image, task, title, action
		$states = array(
			0 => array(
				'disabled.png',
				'properties.featured',
				'COM_JEA_UNFEATURED',
				'COM_JEA_TOGGLE_TO_FEATURE'
			),
			1 => array(
				'featured.png',
				'properties.unfeatured',
				'COM_JEA_FEATURED',
				'COM_JEA_TOGGLE_TO_UNFEATURE'
			)
		);

		$state = ArrayHelper::getValue($states, (int) $value, $states[1]);
		$html = JHtml::_('image', 'admin/' . $state[0], JText::_($state[2]), null, true);

		if ($canChange)
		{
			$html = '<a href="#" onclick="return listItemTask(\'cb' . $i . '\',\'' . $state[1] . '\')" title="' . JText::_($state[3]) . '">'
					. $html . '</a>';
		}

		return $html;
	}
}
