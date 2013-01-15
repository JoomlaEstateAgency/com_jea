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
 * View to manage all features tables.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 */
class JeaViewFeatures extends JView
{
    function display( $tpl = null )
    {
        JeaHelper::addSubmenu('features');
        $this->items = $this->get('Items');

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

        JToolBarHelper::title( JText::_('COM_JEA_FEATURES_MANAGEMENT'), 'jea.png' );

        if ($canDo->get('core.manage')) {
            JToolBarHelper::custom('features.import', 'import', '', 'Import', false);
        }

        JToolBarHelper::custom('features.export', 'export', '', 'Export', false);

        if ($canDo->get('core.admin')) {
            JToolBarHelper::divider();
            JToolBarHelper::preferences('com_jea');
        }
    }

}
