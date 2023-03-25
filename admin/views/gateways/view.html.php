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
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\User\User;

/**
 * Gateways View
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @since       2.0
 */
class JeaViewGateways extends HtmlView
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
		JeaHelper::addSubmenu('tools');

		$this->state = $this->get('State');

		$title = Text::_('COM_JEA_GATEWAYS');

		switch ($this->_layout)
		{
			case 'export':
				$title = Text::_('COM_JEA_EXPORT');
				ToolbarHelper::back('JTOOLBAR_BACK', 'index.php?option=com_jea&view=tools');
				break;
			case 'import':
				$title = Text::_('COM_JEA_IMPORT');
				ToolbarHelper::back('JTOOLBAR_BACK', 'index.php?option=com_jea&view=tools');
				break;
			default:
				$this->user = Factory::getApplication()->getIdentity();
				$this->items = $this->get('Items');
				$this->pagination = $this->get('Pagination');
				ToolbarHelper::addNew('gateway.add');
				ToolbarHelper::editList('gateway.edit');
				ToolbarHelper::publish('gateways.publish', 'JTOOLBAR_PUBLISH', true);
				ToolbarHelper::unpublish('gateways.unpublish', 'JTOOLBAR_UNPUBLISH', true);
				ToolbarHelper::deleteList(Text::_('COM_JEA_MESSAGE_CONFIRM_DELETE'), 'gateways.delete');
		}

		ToolbarHelper::title($title, 'jea');

		parent::display($tpl);
	}
}
