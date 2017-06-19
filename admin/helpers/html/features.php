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

/**
 * Fatures html helper class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @since       2.0
 */
abstract class JHtmlFeatures
{
	/**
	 * Method to get property types in a HTML <select> element
	 *
	 * @param   string  $value  The selected value
	 * @param   string  $name   The element name
	 * @param   mixed   $attr   An array or a string of element attributes
	 *
	 * @return  string HTML for the select list.
	 */
	static public function types ($value = 0, $name = 'type_id', $attr = '')
	{
		$cond = self::getLanguageCondition();

		return self::getHTMLSelectList($value, $name, 'COM_JEA_FIELD_PROPERTY_TYPE_LABEL', $attr, 'types', $cond, 'f.ordering');
	}

	/**
	 * Method to get departments in a HTML <select> element
	 *
	 * @param   string  $value  The selected value
	 * @param   string  $name   The element name
	 * @param   mixed   $attr   An array or a string of element attributes
	 *
	 * @return  string HTML for the select list.
	 */
	static public function departments ($value = 0, $name = 'department_id', $attr = '')
	{
		return self::getHTMLSelectList($value, $name, 'COM_JEA_FIELD_DEPARTMENT_LABEL', $attr, 'departments');
	}

	/**
	 * Method to get towns in a HTML <select> element
	 *
	 * @param   string  $value          The selected value
	 * @param   string  $name           The element name
	 * @param   mixed   $attr           An array or a string of element attributes
	 * @param   string  $department_id  To get the department town list
	 *
	 * @return  string HTML for the select list.
	 */
	static public function towns ($value = 0, $name = 'town_id', $attr = '', $department_id = null)
	{
		$condition = '';

		if ($department_id !== null)
		{
			// Potentially Too much results so this will give en empty result
			$condition = 'f.department_id = -1';

			if ($department_id > 0)
			{
				$condition = 'f.department_id =' . intval($department_id);
			}
		}

		return self::getHTMLSelectList($value, $name, 'COM_JEA_FIELD_TOWN_LABEL', $attr, 'towns', $condition);
	}

	/**
	 * Method to get areas in a HTML <select> element
	 *
	 * @param   string  $value    The selected value
	 * @param   string  $name     The element name
	 * @param   mixed   $attr     An array or a string of element attributes
	 * @param   string  $town_id  To get the town area list
	 *
	 * @return  string HTML for the select list.
	 */
	static public function areas ($value = 0, $name = 'area_id', $attr = '', $town_id = null)
	{
		$condition = '';

		if ($town_id !== null)
		{
			$condition = 'f.town_id = -1';

			if ($town_id > 0)
			{
				$condition = 'f.town_id =' . intval($town_id);
			}
		}

		return self::getHTMLSelectList($value, $name, 'COM_JEA_FIELD_AREA_LABEL', $attr, 'areas', $condition);
	}

	/**
	 * Method to get conditions in a HTML <select> element
	 *
	 * @param   string  $value  The selected value
	 * @param   string  $name   The element name
	 * @param   mixed   $attr   An array or a string of element attributes
	 *
	 * @return  string HTML for the select list.
	 */
	static public function conditions ($value = 0, $name = 'condition_id', $attr = '')
	{
		$cond = self::getLanguageCondition();

		return self::getHTMLSelectList($value, $name, 'COM_JEA_FIELD_CONDITION_LABEL', $attr, 'conditions', $cond);
	}

	/**
	 * Method to get hot water types in a HTML <select> element
	 *
	 * @param   string  $value  The selected value
	 * @param   string  $name   The element name
	 * @param   mixed   $attr   An array or a string of element attributes
	 *
	 * @return  string HTML for the select list.
	 */
	static public function hotwatertypes ($value = 0, $name = 'hot_water_type', $attr = '')
	{
		$cond = self::getLanguageCondition();

		return self::getHTMLSelectList($value, $name, 'COM_JEA_FIELD_HOTWATERTYPE_LABEL', $attr, 'hotwatertypes', $cond);
	}

	/**
	 * Method to get hot heating types in a HTML <select> element
	 *
	 * @param   string  $value  The selected value
	 * @param   string  $name   The element name
	 * @param   mixed   $attr   An array or a string of element attributes
	 *
	 * @return  string HTML for the select list.
	 */
	static public function heatingtypes ($value = 0, $name = 'heating_type', $attr = '')
	{
		$cond = self::getLanguageCondition();

		return self::getHTMLSelectList($value, $name, 'COM_JEA_FIELD_HEATINGTYPE_LABEL', $attr, 'heatingtypes', $cond);
	}

	/**
	 * Method to get slogans in a HTML <select> element
	 *
	 * @param   string  $value  The selected value
	 * @param   string  $name   The element name
	 * @param   mixed   $attr   An array or a string of element attributes
	 *
	 * @return  string HTML for the select list.
	 */
	static public function slogans ($value = 0, $name = 'slogan_id', $attr = '')
	{
		$cond = self::getLanguageCondition();

		return self::getHTMLSelectList($value, $name, 'COM_JEA_FIELD_SLOGAN_LABEL', $attr, 'slogans', $cond);
	}

	/**
	 * Generic method to get HTML list of feature in a <select> element
	 *
	 * @param   string  $value               The selected value
	 * @param   string  $name                The element name
	 * @param   string  $defaultOptionLabel  The first option label
	 * @param   mixed   $attr                An array or a string of element attributes
	 * @param   string  $featureTable        The feature table name without the prefix "#__jea_"
	 * @param   mixed   $conditions          A string or an array of where conditions to filter the database request
	 * @param   string  $ordering            The list ordering
	 *
	 * @return  string  HTML for the select list.
	 */
	static public function getHTMLSelectList($value = 0, $name = '', $defaultOptionLabel = 'JOPTION_ANY', $attr = '', $featureTable = '',
		$conditions = null, $ordering = 'f.value asc')
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('f.id , f.value');
		$query->from('#__jea_' . $featureTable . ' AS f');

		if (! empty($conditions))
		{
			if (is_string($conditions))
			{
				$query->where($conditions);
			}
			elseif (is_array($conditions))
			{
				foreach ($conditions as $condition)
				{
					$query->where($condition);
				}
			}
		}

		$query->order($ordering);
		$db->setQuery($query);
		$items = $db->loadObjectList();

		// Assemble the list options.
		$options = array();
		$options[] = JHTML::_('select.option', '0', '- ' . JText::_($defaultOptionLabel) . ' -&nbsp;');

		foreach ($items as &$item)
		{
			$options[] = JHtml::_('select.option', $item->id, $item->value);
		}

		// Manage attributes
		$idTag = false;

		if (is_array($attr))
		{
			if (isset($attr['id']))
			{
				$idTag = $attr['id'];
				unset($attr['id']);
			}

			if (empty($attr['size']))
			{
				$attr['size'] = 1;
			}

			if (empty($attr['class']))
			{
				$attr['class'] = 'inputbox';
			}

			$attr['class'] = trim($attr['class']);
		}
		else
		{
			if ((float) JVERSION > 3 && JFactory::getApplication()->isClient('administrator'))
			{
				$attr = 'class="inputbox span12 small" size="1" ' . $attr;
			}
			else
			{
				$attr = 'class="inputbox" size="1" ' . $attr;
			}
		}

		return JHTML::_('select.genericlist', $options, $name, $attr, 'value', 'text', $value, $idTag);
	}

	/**
	 * Get language condition
	 *
	 * @return  string
	 */
	protected static function getLanguageCondition()
	{
		$condition = '';

		if (JFactory::getApplication()->isClient('site'))
		{
			$db = JFactory::getDbo();
			$condition = 'f.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')';
		}

		return $condition;
	}
}
