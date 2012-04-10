<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @version     $Id$
 * @package     Joomla.Site
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2012 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// Check to ensure this file is included in Joomla!

defined('_JEXEC') or die();

jimport('joomla.application.component.view');

class JeaViewProperties extends JView
{

    function display($tpl = null)
    {
        $app = JFactory::getApplication();

        $document    = JFactory::getDocument();
        $params = $app->getParams();

        $document->link  = JRoute::_('index.php?option=com_jea&view=properties');

        JRequest::setVar('limit', $app->getCfg('feed_limit'));

        $rows = $this->get('Items');

        foreach ($rows as $row ) {

            if(empty($row->title)) {
                $title = ucfirst( JText::sprintf('COM_JEA_PROPERTY_TYPE_IN_TOWN',
                $this->escape($row->type), $this->escape($row->town)));
            } else {
                // strip html from feed item title
                $title = $this->escape($row->title);
            }

            $row->slug = $row->alias ? ($row->id . ':' . $row->alias) : $row->id;
            // url link to article
            $item->link = JRoute::_('index.php?view=properties&id='. $row->slug);

            // strip html from feed item description text
            $description   = strip_tags($row->description);

            // load individual item creator class
            $item = new JFeedItem();
            $item->title         = html_entity_decode($title);
            $item->link          = $link;
            $item->description   = $description;
            $item->date          = $row->date_insert;
            $item->category      = $row->type_id;

            // loads item info into rss array
            $document->addItem( $item );
        }
    }
}
