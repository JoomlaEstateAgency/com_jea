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
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

defined('_JEXEC') or die;

/**
 * JEA about view.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @since       2.0
 */
class JeaViewAbout extends HtmlView
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
     *
     * @see     HtmlView::display()
     */
    public function display($tpl = null)
    {
        JeaHelper::addSubmenu('about');
        ToolbarHelper::title('Joomla Estate Agency', 'jea');

        $canDo = JeaHelper::getActions();

        if ($canDo->get('core.admin')) {
            ToolbarHelper::preferences('com_jea');
        }

        $this->sidebar = Sidebar::render();

        parent::display($tpl);
    }

    /**
     * Get version of JEA
     *
     * @return string
     */
    protected function getVersion()
    {
        if (is_file(JPATH_COMPONENT . '/jea.xml')) {
            $xml = simplexml_load_file(JPATH_COMPONENT . '/jea.xml');

            return $xml->version;
        }

        return '';
    }
}
