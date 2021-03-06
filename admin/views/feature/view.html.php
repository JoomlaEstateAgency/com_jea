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

/**
 * View to edit a feature.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @since       2.0
 */
class JeaViewFeature extends JViewLegacy
{
	/**
	 * The form object
	 *
	 * @var JForm
	 */
	protected $form;

	/**
	 * The database record
	 *
	 * @var JObject|boolean
	 */
	protected $item;

	/**
	 * The model state
	 *
	 * @var JObject
	 */
	protected $state;

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
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->state = $this->get('State');

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
		JFactory::getApplication()->input->set('hidemainmenu', true);
		$canDo = JeaHelper::getActions();

		$title = $this->item->id ? JText::_('JACTION_EDIT') . ' ' . $this->escape($this->item->value) : JText::_('JACTION_CREATE');
		JToolBarHelper::title($title, 'jea');

		// For new records, check the create permission.
		if ($canDo->get('core.create'))
		{
			JToolBarHelper::apply('feature.apply');
			JToolBarHelper::save('feature.save');
			JToolBarHelper::save2new('feature.save2new');
		}

		JToolBarHelper::cancel('feature.cancel');
	}
}
