<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2020 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

defined('_JEXEC') or die;

/**
 * Gateway table class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @since       1.0
 */
class TableGateway extends Table
{
	/**
	 * Constructor
	 *
	 * @param   DatabaseDriver $db A database diver object
	 */
	public function __construct(DatabaseDriver $db)
	{
		parent::__construct('#__jea_gateways', 'id', $db);
	}

	/**
	 * Method to bind an associative array or object to the TableInterface instance.
	 *
	 * @param   mixed $array    An associative array or object to bind to the TableInterface instance.
	 * @param   mixed $ignore   An optional array or space separated list of properties to ignore while binding.
	 *
	 * @return  boolean  True on success.
	 *
	 * @see Table::bind()
	 */
	public function bind($array, $ignore = '')
	{
		$array['params'] = isset($array['params']) && is_array($array['params']) ?
		json_encode($array['params']) : '{}';

		return parent::bind($array, $ignore);
	}
}
