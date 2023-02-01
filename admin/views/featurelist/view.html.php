<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2020 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\HTML\Helpers\Sidebar;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\User\User;
use Joomla\String\StringHelper;

/**
 * View to manage a feature list.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @since       2.0
 */
class JeaViewFeaturelist extends HtmlView
{
    /**
     * The user object
     *
     * @var User
     */
    protected $user;

    /**
     * Array of database records
     *
     * @var Jobject[]
     */
    protected $items;

    /**
     * The pagination object
     *
     * @var Pagination
     */
    protected $pagination;

    /**
     * The model state
     *
     * @var Jobject
     */
    protected $state;

    /**
     * The sidebar output
     *
     * @var string
     */
    protected $sidebar = '';

    /**
     * The form object for search filters
     *
     * @var Form
     */
    public $filterForm;

    /**
     * The active search filters
     *
     * @var  array
     */
    public $activeFilters;

    /**
     * Overrides parent method.
     *
     * @param string $tpl The name of the template file to parse.
     *
     * @see     HtmlView::display()
     */
    public function display($tpl = null)
    {
        JeaHelper::addSubmenu('features');

        $this->user = Factory::getApplication()->getIdentity();
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        $this->sidebar = Sidebar::render();
        $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

        $this->addToolbar();

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return void
     */
    protected function addToolbar()
    {
        $canDo = JeaHelper::getActions();
        $feature = $this->state->get('feature.name');

        ToolbarHelper::title(Text::_(StringHelper::strtoupper("com_jea_list_of_{$feature}_title")), 'jea');

        if ($canDo->get('core.create')) {
            ToolbarHelper::addNew('feature.add');
        }

        if ($canDo->get('core.edit')) {
            ToolbarHelper::editList('feature.edit');
        }

        if ($canDo->get('core.delete')) {
            ToolbarHelper::divider();
            ToolbarHelper::deleteList(Text::_('COM_JEA_MESSAGE_CONFIRM_DELETE'), 'featurelist.delete');
        }
    }
}
