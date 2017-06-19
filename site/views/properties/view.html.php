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

require_once JPATH_COMPONENT_ADMINISTRATOR . '/tables/features.php';

/**
 * Property list view.
 *
 * @package     Joomla.Site
 * @subpackage  com_jea
 *
 * @since       2.0
 */
class JeaViewProperties extends JViewLegacy
{
	/**
	 * Overrides parent method.
	 *
	 * @param   string  $tpl  The name of the template file to parse.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 *
	 * @see     JViewLegacy::display()
	 */
	public function display ($tpl = null)
	{
		$state = $this->get('State');
		$this->params = $state->params;
		$this->state = $state;

		$layout = $this->getLayout();

		if ($layout == 'default' || $layout == 'manage')
		{
			if ($layout == 'manage')
			{
				$this->items = $this->get('MyItems');
			}
			else
			{
				$this->items = $this->get('Items');
			}

			$this->pagination = $this->get('Pagination');
		}

		if ($layout == 'default')
		{
			$this->prepareSortLinks();

			// Add alternate feed link
			if ($this->params->get('show_feed_link', 1) == 1)
			{
				$link = 'index.php?option=com_jea&view=properties&format=feed&limitstart=';
				$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
				$this->document->addHeadLink(JRoute::_($link . '&type=rss'), 'alternate', 'rel', $attribs);
				$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
				$this->document->addHeadLink(JRoute::_($link . '&type=atom'), 'alternate', 'rel', $attribs);
			}
		}

		parent::display($tpl);
	}

	/**
	 * Prepare sort links
	 *
	 * @return void
	 */
	protected function prepareSortLinks()
	{
		$sort_links = array();

		$order = $this->state->get('list.ordering');
		$direction = $this->state->get('list.direction');

		if ($this->params->get('sort_date'))
		{
			$sort_links[] = $this->sort('COM_JEA_SORT_BY_DATE', 'p.created', $direction, $order);
		}

		if ($this->params->get('sort_price'))
		{
			$sort_links[] = $this->sort('COM_JEA_SORT_BY_PRICE', 'p.price', $direction, $order);
		}

		if ($this->params->get('sort_livingspace'))
		{
			$sort_links[] = $this->sort('COM_JEA_SORT_BY_LIVING_SPACE', 'p.living_space', $direction, $order);
		}

		if ($this->params->get('sort_landspace'))
		{
			$sort_links[] = $this->sort('COM_JEA_SORT_BY_LAND_SPACE', 'p.land_space', $direction, $order);
		}

		if ($this->params->get('sort_hits'))
		{
			$sort_links[] = $this->sort('COM_JEA_SORT_BY_POPULARITY', 'p.hits', $direction, $order);
		}

		if ($this->params->get('sort_towns'))
		{
			$sort_links[] = $this->sort('COM_JEA_SORT_BY_TOWN', 'town', $direction, $order);
		}

		if ($this->params->get('sort_departements'))
		{
			$sort_links[] = $this->sort('COM_JEA_SORT_BY_DEPARTMENT', 'department', $direction, $order);
		}

		if ($this->params->get('sort_areas'))
		{
			$sort_links[] = $this->sort('COM_JEA_SORT_BY_AREA', 'area', $direction, $order);
		}

		$this->sort_links = $sort_links;
	}

	/**
	 * Displays a sort link
	 *
	 * @param   string  $title      The link title
	 * @param   string  $order      The order field for the column
	 * @param   string  $direction  The current direction
	 * @param   string  $selected   The selected ordering
	 *
	 * @return string The HTML link
	 */
	protected function sort($title, $order, $direction = 'asc', $selected = 0)
	{
		$direction = strtolower($direction);

		$images = array('sort_asc.png', 'sort_desc.png');

		$index = intval($direction == 'desc');
		$direction = ($direction == 'desc') ? 'asc' : 'desc';
		$html = '<a href="javascript:changeOrdering(\'' . $order . '\',\'' . $direction . '\');" >';
		$html .= JText::_($title);

		if ($order == $selected)
		{
			$html .= '<img src="' . $this->baseurl . '/media/com_jea/images/' . $images[$index] . '" alt="" />';
		}

		$html .= '</a>';

		return $html;
	}

	/**
	 * Get the first image url in the row
	 *
	 * @param   object  &$row  A property row
	 *
	 * @return  string
	 */
	protected function getFirstImageUrl(&$row)
	{
		$images = json_decode($row->images);
		$image = null;

		if (! empty($images) && is_array($images))
		{
			$image = array_shift($images);
			$imagePath = JPATH_ROOT . '/images/com_jea';

			if (file_exists($imagePath . '/thumb-min/' . $row->id . '-' . $image->name))
			{
				// If the thumbnail already exists, display it directly
				return $this->baseurl . '/images/com_jea/thumb-min/' . $row->id . '-' . $image->name;
			}
			elseif (file_exists($imagePath . '/images/' . $row->id . '/' . $image->name))
			{
				// If the thumbnail doesn't exist, generate it and output it on the fly
				$url = 'index.php?option=com_jea&task=thumbnail.create&size=min&id=' . $row->id . '&image=' . $image->name;

				return JRoute::_($url);
			}
		}

		return '';
	}

	/**
	 * Get a feature value
	 *
	 * @param   number  $featureId     The feature ID
	 * @param   string  $featureTable  The feature Table name
	 *
	 * @return  string
	 */
	protected function getFeatureValue ($featureId = 0, $featureTable = '')
	{
		// TODO: Refactor this. Use cache?
		$db = JFactory::getDbo();
		$table = new FeaturesFactory('#__jea_' . $db->escape($featureTable), 'id', $db);
		$table->load((int) $featureId);

		return $table->value;
	}
}
