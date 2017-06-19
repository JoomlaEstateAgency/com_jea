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
 * Property list feed view.
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
		$app = JFactory::getApplication();

		$document = JFactory::getDocument();
		$params = $app->getParams();

		$document->link = JRoute::_('index.php?option=com_jea&view=properties');

		JFactory::getApplication()->input->set('limit', $app->get('feed_limit'));

		$rows = $this->get('Items');

		foreach ($rows as $row)
		{
			if (empty($row->title))
			{
				$title = ucfirst(JText::sprintf('COM_JEA_PROPERTY_TYPE_IN_TOWN', $this->escape($row->type), $this->escape($row->town)));
			}
			else
			{
				// Strip html from feed item title
				$title = $this->escape($row->title);
			}

			$row->slug = $row->alias ? ($row->id . ':' . $row->alias) : $row->id;

			// Url link to article
			$item->link = JRoute::_('index.php?view=properties&id=' . $row->slug);

			// Strip html from feed item description text
			$description = strip_tags($row->description);

			// Load individual item creator class
			$item = new JFeedItem;
			$item->title = html_entity_decode($title);
			$item->link = $link;
			$item->description = $description;
			$item->date = $row->date_insert;
			$item->category = $row->type_id;

			// Loads item info into rss array
			$document->addItem($item);
		}
	}
}
