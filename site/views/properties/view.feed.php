<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Site
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2020 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Document\Feed\FeedItem;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

/**
 * Property list feed view.
 *
 * @package     Joomla.Site
 * @subpackage  com_jea
 *
 * @since       2.0
 */
class JeaViewProperties extends HtmlView
{
	/**
	 * Overrides parent method.
	 *
	 * @param   string $tpl The name of the template file to parse.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 *
	 * @see     HtmlView::display()
	 */
	public function display($tpl = null)
	{
		$app = Factory::getApplication();
		$document = Factory::getDocument();
		$document->link = Route::_('index.php?option=com_jea&view=properties');
		Factory::getApplication()->input->set('limit', $app->get('feed_limit'));
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
			$link = Route::_('index.php?view=properties&id=' . $row->slug);

			// Strip html from feed item description text
			$description = strip_tags($row->description);

			// Load individual item creator class
			$item = new FeedItem;
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
