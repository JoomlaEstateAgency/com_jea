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
 * JEA tools view.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @since       2.0
 */
class JeaViewTools extends JViewLegacy
{
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
		JeaHelper::addSubmenu('tools');
		JToolBarHelper::title(JText::_('COM_JEA_TOOLS'), 'jea.png');

		$canDo = JeaHelper::getActions();

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_jea');
		}

		$this->sidebar = JHtmlSidebar::render();

		parent::display($tpl);
	}

	/**
	 * Return tools icons.
	 *
	 * @return  array  An array of icons
	 */
	protected function getIcons()
	{
		$buttons = JeaHelper::getToolsIcons();

		foreach ($buttons as $button)
		{
			if (! empty($button['name']))
			{
				$styleSheet = 'media/com_jea/' . $button['name'] . '/styles.css';

				if (file_exists(JPATH_ROOT . '/' . $styleSheet))
				{
					JHtml::stylesheet($styleSheet);
				}
			}
		}

		return JHtml::_('icons.buttons', $buttons);
	}
}
