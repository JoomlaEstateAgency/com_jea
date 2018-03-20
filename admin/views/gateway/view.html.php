<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2017 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require JPATH_COMPONENT . '/helpers/jea.php';

/**
 * Gateway View
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @since       2.0
 */
class JeaViewGateway extends JViewLegacy
{
	/**
	 * The component paramaters
	 *
	 * @var Joomla\Registry\Registry
	 */
	protected $params = null;

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

		JeaHelper::addSubmenu('tools');

		$this->state = $this->get('State');

		$type = $this->state->get('type');
		$title = JText::_('COM_JEA_GATEWAYS');

		$this->item = $this->get('Item');

		switch ($this->_layout)
		{
			case 'edit':
				$this->form = $this->get('Form');

				JToolBarHelper::apply('gateway.apply');
				JToolBarHelper::save('gateway.save');
				JToolBarHelper::cancel('gateway.cancel');
				$isNew = ($this->item->id == 0);
				$title .= ' : ' . ($isNew ? JText::_('JACTION_CREATE') : JText::_('JACTION_EDIT') . ' : ' . $this->item->title);
				break;
		}

		JToolBarHelper::title($title, 'jea.png');

		parent::display($tpl);
	}
}
