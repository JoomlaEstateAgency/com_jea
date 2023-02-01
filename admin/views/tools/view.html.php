<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2020 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\HTML\Helpers\Sidebar;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

defined('_JEXEC') or die;

/**
 * JEA tools view.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @since       2.0
 */
class JeaViewTools extends HtmlView
{
    /**
     * The sidebar output
     *
     * @var string
     */
    protected $sidebar = '';

    /**
     * Overrides parent method.
     *
     * @param string $tpl The name of the template file to parse.
     * @see     HtmlView::display()
     */
    public function display($tpl = null)
    {
        JeaHelper::addSubmenu('tools');
        ToolbarHelper::title(Text::_('COM_JEA_TOOLS'), 'jea');

        $canDo = JeaHelper::getActions();

        if ($canDo->get('core.admin')) {
            ToolbarHelper::preferences('com_jea');
        }

        $this->sidebar = Sidebar::render();

        parent::display($tpl);
    }

    /**
     * Return tools icons.
     *
     * @return  array  An array of icons
     */
    protected function getIcons()
    {
        $buttons = JeaHelper::getToolsIcons();

        foreach ($buttons as $button) {
            if (!empty($button['name'])) {
                $styleSheet = 'media/com_jea/' . $button['name'] . '/styles.css';

                if (file_exists(JPATH_ROOT . '/' . $styleSheet)) {
                    HTMLHelper::stylesheet($styleSheet);
                }
            }
        }

        return HTMLHelper::_('icons.buttons', $buttons);
    }
}
