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

jimport('joomla.application.component.view');

require JPATH_COMPONENT.DS.'helpers'.DS.'jea.php';

/**
 * JEA tools view.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 */
class JeaViewTools extends JViewLegacy
{
    public function display( $tpl = null )
    {
        JeaHelper::addSubmenu('tools');
        JToolBarHelper::title( JText::_('COM_JEA_TOOLS'), 'jea.png' );

        $canDo  = JeaHelper::getActions();

        if ($canDo->get('core.admin')) {
            JToolBarHelper::preferences('com_jea');
        }
        if ((float) JVERSION > 3) {
            $this->sidebar = JHtmlSidebar::render();
        }
        parent::display($tpl);
    }

    protected function getIcons()
    {
        $buttons = JeaHelper::getToolsIcons();

        foreach ($buttons as $button) {
            if (!empty($button['name'])) {
                $styleSheet = 'media/com_jea/'. $button['name'].'/styles.css';
                if (file_exists(JPATH_ROOT . '/' .$styleSheet)) {
                    JHtml::stylesheet($styleSheet);
                }
            }
        }

        return JHtml::_('icons.buttons', $buttons);
    }

}

