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
 * Displays amenities as a list of check boxes.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @see         JFormField
 *
 * @since       2.0
 */
class JFormFieldAmenities extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var string
	 */
	protected $type = 'Amenities';

	/**
	 * Flag to tell the field to always be in multiple values mode.
	 *
	 * @var boolean
	 */
	protected $forceMultiple = true;

	/**
	 * Method to get the field input markup for check boxes.
	 *
	 * @return string The field input markup.
	 */
	protected function getInput()
	{
		$options = $this->getOptions();
		$output = '<ul id="amenities">';

		if (! empty($this->value))
		{
			// Preformat data if comes from db
			if (! is_array($this->value))
			{
				$this->value = explode('-', $this->value);
			}
		}
		else
		{
			$this->value = array();
		}

		foreach ($options as $k => $row)
		{
			$checked = '';
			$class = '';

			if (in_array($row->id, $this->value))
			{
				$checked = 'checked="checked"';
				$class = 'active';
			}

			$title = '';
			$label = JHtml::_('string.truncate', $row->value, 23, false, false);

			if ($row->value != $label)
			{
				$title = ' title="' . $row->value . '"';
			}

			$output .= '<li class="amenity ' . $class . '">';

			$output .= '<input class="am-input" type="checkbox" name="' . $this->name . '"'
					. ' id="' . $this->id . $k . '"' . ' value="' . $row->id . '" ' . $checked . ' />'
					. '<label class="am-title" for="' . $this->id . $k . '" ' . $title . '>' . $label . '</label>';

			$output .= '</li>';
		}

		$output .= '</ul>';

		return $output;
	}

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 */
	protected function getOptions()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('f.id , f.value');
		$query->from('#__jea_amenities AS f');

		if (JFactory::getApplication()->isClient('site'))
		{
			$query->where('f.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
		}

		$query->order('f.value ASC');
		$db->setQuery($query);

		return $db->loadObjectList();
	}
}
