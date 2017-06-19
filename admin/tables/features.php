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

/**
 * FeaturesFactory table class.
 * This class provides a way to instantiate a feature table on the fly
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @since       2.0
 */
class FeaturesFactory extends JTable
{
	/**
	 * Method to perform sanity checks before to store in the database.
	 *
	 * @return  boolean  True if the instance is sane and able to be stored in the database.
	 */
	public function check()
	{
		// For new insertion
		if (empty($this->id))
		{
			$this->ordering = $this->getNextOrder();
		}

		return true;
	}
}
