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
}
