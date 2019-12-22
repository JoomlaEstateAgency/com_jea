<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2019 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Properties list View.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @since       2.0
 */
class JeaViewProperties extends JViewLegacy
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
	 * @var JUser
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
	 * @var JPagination
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
	 * @var JForm
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
	 * @param   string  $tpl  The name of the template file to parse.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 *
	 * @see     JViewLegacy::display()
	 */
	public function display($tpl = null)
	{
		$this->params = JComponentHelper::getParams('com_jea');

		JeaHelper::addSubmenu('properties');

		$this->user = JFactory::getUser();
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');

		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		$this->sidebar = JHtmlSidebar::render();

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

		JToolBarHelper::title(JText::_('COM_JEA_PROPERTIES_MANAGEMENT'), 'jea');

		if ($canDo->get('core.create'))
		{
			JToolBarHelper::addNew('property.add');
			JToolBarHelper::custom('properties.copy', 'copy.png', 'copy_f2.png', 'COM_JEA_COPY');
		}

		if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own')))
		{
			JToolBarHelper::editList('property.edit');
		}

		if ($canDo->get('core.edit.state'))
		{
			JToolBarHelper::divider();
			JToolBarHelper::publish('properties.publish', 'JTOOLBAR_PUBLISH', true);
			JToolBarHelper::unpublish('properties.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::custom('properties.featured', 'featured.png', 'featured_f2.png', 'JFEATURED', true);
		}

		if ($canDo->get('core.delete'))
		{
			JToolBarHelper::divider();
			JToolBarHelper::deleteList(JText::_('COM_JEA_MESSAGE_CONFIRM_DELETE'), 'properties.delete');
		}

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_jea');
		}
	}
}
