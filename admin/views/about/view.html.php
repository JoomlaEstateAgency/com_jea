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
 * JEA about view.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @since       2.0
 */
class JeaViewAbout extends JViewLegacy
{
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
		JeaHelper::addSubmenu('about');
		JToolBarHelper::title('Joomla Estate Agency', 'jea.png');

		$canDo = JeaHelper::getActions();

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_jea');
		}

		$this->sidebar = JHtmlSidebar::render();

		parent::display($tpl);
	}

	/**
	 * Get version of JEA
	 *
	 * @return string
	 */
	protected function getVersion()
	{
		if (is_file(JPATH_COMPONENT . '/jea.xml'))
		{
			$xml = simplexml_load_file(JPATH_COMPONENT . '/jea.xml');

			return $xml->version;
		}

		return '';
	}
}
