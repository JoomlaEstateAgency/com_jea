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
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\DatabaseDriver;

defined('_JEXEC') or die;

/**
 * Gateways model class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @see         ListModel
 *
 * @since       3.4
 */
class JeaModelGateways extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array $config An optional associative array of configuration settings.
	 *
	 * @see     JModelList
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id',
				'title',
				'provider',
				'published',
				'ordering',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Overrides parent method
	 *
	 * @return  JDatabaseQuery  A JDatabaseQuery object to retrieve the data set.
	 *
	 * @see ListModel::getListQuery()
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = Factory::getContainer()->get(DatabaseDriver::class);
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from('#__jea_gateways');

		if ($type = $this->state->get('filter.type'))
		{
			$query->where('type=' . $db->Quote($type));
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'id');
		$orderDirn = $this->state->get('list.direction', 'desc');

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}
}
