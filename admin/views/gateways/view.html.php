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
 * Gateways View
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @since       2.0
 */
class JeaViewGateways extends JViewLegacy
{
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
		JeaHelper::addSubmenu('tools');

		$this->state = $this->get('State');

		$this->sidebar = JHtmlSidebar::render();

		$title = JText::_('COM_JEA_GATEWAYS');

		switch ($this->_layout)
		{
			case 'export':
				$title = JText::_('COM_JEA_EXPORT');
				JToolBarHelper::back('JTOOLBAR_BACK', 'index.php?option=com_jea&view=tools');
				break;
			case 'import':
				$title = JText::_('COM_JEA_IMPORT');
				JToolBarHelper::back('JTOOLBAR_BACK', 'index.php?option=com_jea&view=tools');
				break;
			default:
				$this->user = JFactory::getUser();
				$this->items = $this->get('Items');
				$this->pagination = $this->get('Pagination');
				JToolBarHelper::addNew('gateway.add');
				JToolBarHelper::editList('gateway.edit');
				JToolBarHelper::publish('gateways.publish', 'JTOOLBAR_PUBLISH', true);
				JToolBarHelper::unpublish('gateways.unpublish', 'JTOOLBAR_UNPUBLISH', true);
				JToolBarHelper::deleteList(JText::_('COM_JEA_MESSAGE_CONFIRM_DELETE'), 'gateways.delete');
		}

		JToolBarHelper::title($title, 'jea');

		parent::display($tpl);
	}
}
