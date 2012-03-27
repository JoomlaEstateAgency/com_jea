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

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

class JeaViewProperty extends JView
{
    public function display( $tpl = null )
    {
        $state  = $this->get('State');
        $item   = $this->get('Item');
        $params = &$state->params;

        $this->assignRef('params', $params);
        $this->assignRef('state', $state);
        $this->assignRef('row', $item);

        if (empty($item->title)) {
            $pageTitle = ucfirst( JText::sprintf('COM_JEA_PROPERTY TYPE IN TOWN',
            $this->escape($item->type), $this->escape($item->town)));
        } else {
            $pageTitle = $this->escape($item->title) ;
        }

        $this->assign( 'page_title', $pageTitle );
         
        $app = JFactory::getApplication();
        $pathway = $app->getPathway();
        $pathway->addItem($pageTitle);

        $document= JFactory::getDocument();
        $document->setTitle($pageTitle);

        parent::display($tpl);
    }

}

