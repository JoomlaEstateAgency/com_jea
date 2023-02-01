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
 * View to edit a feature.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @since       2.0
 */
class JeaViewFeature extends HtmlView
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
		$canDo = JeaHelper::getActions();

		$title = $this->item->id ? Text::_('JACTION_EDIT') . ' ' . $this->escape($this->item->value) : Text::_('JACTION_CREATE');
		ToolbarHelper::title($title, 'jea');

		// For new records, check the create permission.
		if ($canDo->get('core.create'))
		{
			ToolbarHelper::apply('feature.apply');
			ToolbarHelper::save('feature.save');
			ToolbarHelper::save2new('feature.save2new');
		}

		ToolbarHelper::cancel('feature.cancel');
	}
}
