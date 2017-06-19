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

use Joomla\Utilities\ArrayHelper;

/**
 * Jea Amenities HTML helper
 *
 * @package     Joomla.Site
 * @subpackage  com_jea
 *
 * @since       2.0
 */
abstract class JHtmlAmenities
{
	protected static $amenities = null;

	/**
	 * Method to get an HTML list of amenities
	 *
	 * @param   mixed   $value   string or array of amenities ids
	 * @param   string  $format  The wanted format (ol, li, raw (default))
	 *
	 * @return string HTML for the list.
	 */
	static public function bindList($value = 0, $format = 'raw')
	{
		if (is_string($value) && !empty($value))
		{
			$ids = explode('-', $value);
		}
		elseif (empty($value))
		{
			$ids = array();
		}
		else
		{
			$ids = ArrayHelper::toInteger($value);
		}

		$html = '';
		$amenities = self::getAmenities();
		$items = array();

		foreach ($amenities as $row)
		{
			if (in_array($row->id, $ids))
			{
				if ($format == 'ul')
				{
					$items[] = "<li>{$row->value}</li>\n";
				}
				else
				{
					$items[] = $row->value;
				}
			}
		}

		if ($format == 'ul')
		{
			$html = "<ul>\n" . implode("\n", $items) . "</ul>\n";
		}
		else
		{
			$html = implode(', ', $items);
		}

		return $html;
	}

	/**
	 * Return HTML list of amenities as checkboxes
	 *
	 * @param   array   $values    The checkboxes values
	 * @param   string  $name      The attribute name for the checkboxes
	 * @param   string  $idSuffix  An optional ID suffix for the checkboxes
	 *
	 * @return string Html list
	 */
	static public function checkboxes ($values = array(), $name = 'amenities', $idSuffix = '')
	{
		$amenities = self::getAmenities();
		$values = (array) $values;
		$html = '';

		if (!empty($amenities))
		{
			$html .= "<ul>\n";

			foreach ($amenities as $row)
			{
				$checked = '';
				$id = 'amenity' . $row->id . $idSuffix;

				if (in_array($row->id, $values))
				{
					$checked = 'checked="checked"';
				}

				$html .= '<li><input name="' . $name . '[]" id="' . $id . '" type="checkbox" value="' . $row->id . '" ' . $checked . ' /> '
						. '<label for="' . $id . '">' . $row->value . '</label></li>' . "\n";
			}

			$html .= "</ul>";
		}

		return $html;
	}

	/**
	 * Get Jea amenities from database
	 *
	 * @return array An array of amenity row objects
	 */
	static public function getAmenities()
	{
		if (self::$amenities === null)
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('a.id , a.value');
			$query->from('#__jea_amenities AS a');
			$query->where('a.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
			$query->order('a.ordering');
			$db->setQuery($query);

			self::$amenities = $db->loadObjectList();
		}

		return self::$amenities;
	}
}
