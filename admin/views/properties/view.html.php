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

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\User\User;

/**
 * Properties list View.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @since       2.0
 */
class JeaViewProperties extends HtmlView
{
	/**
	 * The component parameters
	 *
	 * @var Joomla\Registry\Registry
	 */
	protected $params;

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
	 * @param   string $tpl The name of the template file to parse.
	 *
	 * @see     HtmlView::display()
	 *
	 * @return  mixed A string if successful, otherwise an Error object.
	 */
	public function display($tpl = null)
	{
		$this->params = ComponentHelper::getParams('com_jea');

		JeaHelper::addSubmenu('properties');

		$this->user = Factory::getApplication()->getIdentity();
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');

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

		ToolbarHelper::title(Text::_('COM_JEA_PROPERTIES_MANAGEMENT'), 'jea');

		if ($canDo->get('core.create'))
		{
			ToolBarHelper::addNew('property.add');
			ToolBarHelper::custom('properties.copy', 'copy.png', 'copy_f2.png', 'COM_JEA_COPY');
		}

		if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own')))
		{
			ToolBarHelper::editList('property.edit');
		}

		if ($canDo->get('core.edit.state'))
		{
			ToolBarHelper::divider();
			ToolBarHelper::publish('properties.publish', 'JTOOLBAR_PUBLISH', true);
			ToolBarHelper::unpublish('properties.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			ToolBarHelper::custom('properties.featured', 'featured.png', 'featured_f2.png', 'JFEATURED', true);
		}

		if ($canDo->get('core.delete'))
		{
			ToolBarHelper::divider();
			ToolBarHelper::deleteList(Text::_('COM_JEA_MESSAGE_CONFIRM_DELETE'), 'properties.delete');
		}

		if ($canDo->get('core.admin'))
		{
			ToolBarHelper::divider();
			ToolBarHelper::preferences('com_jea');
		}
	}
}
