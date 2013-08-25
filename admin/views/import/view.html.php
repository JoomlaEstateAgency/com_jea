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
 * Import View
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 */
class JeaViewImport extends JViewLegacy
{

    function display( $tpl = null )
    {
        $params = JComponentHelper::getParams('com_jea');
        $this->assignRef('params' , $params );

        JeaHelper::addSubmenu('tools');

        $this->user        = JFactory::getUser();
        $this->form        = $this->get('Form');
        $this->state       = $this->get('State');

        $this->addToolbar();

        if ((float) JVERSION > 3) {
            $this->sidebar = JHtmlSidebar::render();
        }

        parent::display($tpl);
    }


    /**
     * Add the page title and toolbar.
     *
     */
    protected function addToolbar()
    {
        switch($this->_layout) {
            case 'jea':
                JToolBarHelper::title( JText::_('COM_JEA_IMPORT_FROM_JEA'), 'jea.png' );
                break;
             case 'csv':
                JToolBarHelper::title( JText::_('COM_JEA_IMPORT_FROM_CSV'), 'jea.png' );
                break;   
        }
    }

}
