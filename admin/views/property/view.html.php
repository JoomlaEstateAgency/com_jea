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
 * View to edit property.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @since       2.0
 */
class JeaViewProperty extends JViewLegacy
{
	protected $form;

	protected $item;

	protected $state;

	protected $canDo;

	/**
	 * Overrides parent method.
	 *
	 * @param   string  $tpl  The name of the template file to parse.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 *
	 * @see     JViewLegacy::display()
	 */
	public function display ($tpl = null)
	{
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->state = $this->get('State');
		$this->canDo = JeaHelper::getActions($this->item->id);

		$this->addToolbar();

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 */
	protected function addToolbar ()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);
		$user = JFactory::getUser();

		$isNew = ($this->item->id == 0);
		$checkedOut = ! ($this->item->checked_out == 0 || $this->item->checked_out == $user->id);

		$title = JText::_('COM_JEA_PROPERTIES_MANAGEMENT') . ' : ';
		$title .= $isNew ? JText::_('JACTION_CREATE') : JText::_('JACTION_EDIT');

		JToolBarHelper::title($title, 'jea.png');

		// Built the actions for new and existing records.
		// For new records, check the create permission.
		if ($isNew && ($this->canDo->get('core.create')))
		{
			JToolBarHelper::apply('property.apply');
			JToolBarHelper::save('property.save');
			JToolBarHelper::save2new('property.save2new');
			JToolBarHelper::cancel('property.cancel');
		}
		else
		{
			// Can't save the record if it's checked out.
			if (! $checkedOut)
			{
				// Since it's an existing record, check the edit permission, or
				// fall back to edit own if the owner.
				if ($this->canDo->get('core.edit') || ($this->canDo->get('core.edit.own') && $this->item->created_by == $user->id))
				{
					JToolBarHelper::apply('property.apply');
					JToolBarHelper::save('property.save');

					// We can save this record, but check the create permission
					// to see if we can return to make a new one.
					if ($this->canDo->get('core.create'))
					{
						JToolBarHelper::save2new('property.save2new');
					}
				}
			}

			// If checked out, we can still save
			if ($this->canDo->get('core.create'))
			{
				JToolBarHelper::save2copy('property.save2copy');
			}

			JToolBarHelper::cancel('property.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
