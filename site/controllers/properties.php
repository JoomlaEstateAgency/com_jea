<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Site
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2017 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Properties controller class.
 *
 * @package     Joomla.Site
 * @subpackage  com_jea
 *
 * @since       2.0
 */
class JeaControllerProperties extends JControllerLegacy
{
	protected $default_view = 'properties';

	/**
	 * Search action
	 *
	 * @return void
	 */
	public function search()
	{
		$app = JFactory::getApplication();
		$app->input->set('layout', 'default');
		$this->display();
	}
}
