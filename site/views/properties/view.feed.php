<?php
/**
 * This file is (not quite yet) a part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @version     $Id$
 * @package     Jea.site
 * @copyright   Copyright (C) 2010 Ændrew Rininsland. Based on work by PHILIP Sylvain. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 * Joomla Estate Agency is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses.
 *
 */



// Check to ensure this file is included in Joomla!

defined('_JEXEC') or die();

require_once JPATH_COMPONENT.DS.'view.php';

class JeaViewProperties extends JeaView 
{
    
    function display($tpl = null)
    {
        global $mainframe;

        $document    =& JFactory::getDocument();
        $params =& $mainframe->getParams();
        
        $siteEmail = $mainframe->getCfg('mailfrom');
                
        $document->link = JRoute::_('index.php?option=com_jea&view=properties');

        JRequest::setVar('limit', $mainframe->getCfg('feed_limit'));
        
        $res = $this->get('properties');
        
        foreach ( $res['rows'] as $row ) {

            if(empty($row->title)) {
    		    $title = ucfirst( JText::sprintf('PROPERTY TYPE IN TOWN',
    		    $this->escape($row->type), $this->escape($row->town)));
    		} else {
    		    // strip html from feed item title
    		    $title = $this->escape( $row->title );
    		}
            
            
            // url link to article
            $item->link = JRoute::_('index.php?view=properties&id='. $row->id);
 
            // strip html from feed item description text
            $description   = strip_tags($row->description);
            $author        = "Author"; // soon, will get the author name
                        
            // load individual item creator class
            $item = new JFeedItem();
            $item->title         = html_entity_decode($title);
            $item->link         = $link;
            $item->description     = $description;
            $item->date            = $row->date_insert;
            $item->category       = $row->type_id;
            $item->author        = $author;
            $item->authorEmail = $feedemail;
            
            // loads item info into rss array
            $document->addItem( $item );
        }
    }
}