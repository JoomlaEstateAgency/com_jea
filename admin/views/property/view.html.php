<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2020 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

defined('_JEXEC') or die;

/**
 * View to edit property.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @since       2.0
 */
class JeaViewProperty extends HtmlView
{
	/**
	 * The form object
	 *
	 * @var Form
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
	 * The actions the user is authorised to perform
	 *
	 * @var  JObject
	 */
	protected $canDo;

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
	protected function addToolbar()
	{
		Factory::getApplication()->input->set('hidemainmenu', true);
		$user = Factory::getApplication()->getIdentity();

		$isNew = ($this->item->id == 0);
		$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $user->id);

		$title = Text::_('COM_JEA_PROPERTIES_MANAGEMENT') . ' : ';
		$title .= $isNew ? Text::_('JACTION_CREATE') : Text::_('JACTION_EDIT');

		ToolbarHelper::title($title, 'jea');

		// Built the actions for new and existing records.
		// For new records, check the create permission.
		if ($isNew && ($this->canDo->get('core.create')))
		{
			ToolbarHelper::apply('property.apply');
			ToolbarHelper::save('property.save');
			ToolbarHelper::save2new('property.save2new');
			ToolbarHelper::cancel('property.cancel');
		}
		else
		{
			// Can't save the record if it's checked out.
			if (!$checkedOut)
			{
				// Since it's an existing record, check the edit permission, or
				// fall back to edit own if the owner.
				if ($this->canDo->get('core.edit') || ($this->canDo->get('core.edit.own') && $this->item->created_by == $user->id))
				{
					ToolbarHelper::apply('property.apply');
					ToolbarHelper::save('property.save');

					// We can save this record, but check the create permission
					// to see if we can return to make a new one.
					if ($this->canDo->get('core.create'))
					{
						ToolbarHelper::save2new('property.save2new');
					}
				}
			}

			// If checked out, we can still save
			if ($this->canDo->get('core.create'))
			{
				ToolbarHelper::save2copy('property.save2copy');
			}

			ToolbarHelper::cancel('property.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
