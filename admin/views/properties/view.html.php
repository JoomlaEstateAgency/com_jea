<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @version     $Id$
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2012 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */


// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view');

require JPATH_COMPONENT.DS.'helpers'.DS.'jea.php';

/**
 * View to list properties.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 */
class JeaViewProperties extends JView
{

    function display( $tpl = null )
    {
        $params = JComponentHelper::getParams('com_jea');
        $this->assignRef('params' , $params );

        JeaHelper::addSubmenu('properties');

        $this->user        = JFactory::getUser();
        $this->items       = $this->get('Items');
        $this->pagination  = $this->get('Pagination');
        $this->state       = $this->get('State');

        $this->addToolbar();

        parent::display($tpl);
    }


    /**
     * Add the page title and toolbar.
     *
     */
    protected function addToolbar()
    {
        $canDo  = JeaHelper::getActions();
        $user   = JFactory::getUser();

        JToolBarHelper::title( JText::_('COM_JEA_PROPERTIES_MANAGEMENT'), 'jea.png' );

        if ($canDo->get('core.create')) {
            JToolBarHelper::addNew('property.add');
            JToolBarHelper::custom('properties.copy', 'copy.png', 'copy_f2.png', 'COM_JEA_COPY');
        }

        if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own'))) {
            JToolBarHelper::editList('property.edit');
        }

        if ($canDo->get('core.edit.state')) {
            JToolBarHelper::divider();
            JToolBarHelper::publish('properties.publish', 'JTOOLBAR_PUBLISH', true);
            JToolBarHelper::unpublish('properties.unpublish', 'JTOOLBAR_UNPUBLISH', true);
            JToolBarHelper::custom('properties.featured', 'featured.png', 'featured_f2.png', 'JFEATURED', true);
        }

        if ($canDo->get('core.delete')) {
            JToolBarHelper::divider();
            JToolBarHelper::deleteList(JText::_('COM_JEA_MESSAGE_CONFIRM_DELETE'), 'properties.delete');
        }

        if ($canDo->get('core.admin')) {
            JToolBarHelper::divider();
            JToolBarHelper::preferences('com_jea');
        }
    }

}
