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
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

defined('_JEXEC') or die;

/**
 * View to manage all features tables.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @since       2.0
 */
class JeaViewFeatures extends HtmlView
{
	/**
	 * Array of managed features
	 *
	 * @var stdClass[]
	 */
	protected $items;

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
		$this->items = $this->get('Items');
		$this->state = $this->get('State');

		JeaHelper::addSubmenu('features');

		$this->addToolbar();

		$this->sidebar = Sidebar::render();

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

		ToolbarHelper::title(Text::_('COM_JEA_FEATURES_MANAGEMENT'), 'jea');

		if ($canDo->get('core.manage'))
		{
			ToolbarHelper::custom('features.import', 'database', '', 'Import', false);
		}

		ToolbarHelper::custom('features.export', 'download', '', 'Export', false);

		if ($canDo->get('core.admin'))
		{
			ToolbarHelper::divider();
			ToolbarHelper::preferences('com_jea');
		}
	}
}
