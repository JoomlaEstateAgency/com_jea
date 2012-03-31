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

    /**
     * Get the previous and next links relative to the property
     * @param string $previousPrefix
     * @param string $nextPrefix
     * @return string
     */
    protected function getPrevNextNavigation($previousPrefix='&lt;&lt; ', $nextPrefix=' &gt;&gt;')
    {
        $res = $this->get('previousAndNext');
        $html = '';
        $previous = $previousPrefix. JText::_('JPREVIOUS') ;
        $next     = JText::_('JNEXT') . $nextPrefix ;

        if ( $res['prev_item'] ) {

            $html .= '<a class="previous" href="' . $this->buildPropertyLink($res['prev_item']) . '">' . $previous . '</a>' ;
        } else {
            $html .= '<span class="previous">' . $previous . '</span>';
        }

        if ($res['next_item']) {

            $html .= '<a class="next" href="' . $this->buildPropertyLink($res['next_item']) . '">' . $next . '</a>' ;
        }  else {

            $html .= '<span class="next">' . $next . '</span>';
        }

        return $html;

    }

    protected function buildPropertyLink(&$item)
    {
        $slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
        return JRoute::_('index.php?option=com_jea&view=property&id='. $slug);
    }



}

