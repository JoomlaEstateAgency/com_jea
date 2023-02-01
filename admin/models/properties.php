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
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Database\DatabaseDriver;
use Joomla\Event\Event;

defined('_JEXEC') or die;

/**
 * Properties model class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @see         JModelList
 *
 * @since       2.0
 */
class JeaModelProperties extends ListModel
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
				'p.id',
				'p.ordering',
				'p.price',
				'p.featured',
				'p.published',
				'access_level',
				'author',
				'p.created',
				'p.hits',
				'language',
				'search',
				'transaction_type',
				'type_id',
				'department_id',
				'town_id',
				'language'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string $id A prefix for the store id.
	 *
	 * @return  string A store id.
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.transaction_type');
		$id .= ':' . $this->getState('filter.department_id');
		$id .= ':' . $this->getState('filter.town_id');
		$id .= ':' . $this->getState('filter.language');

		return parent::getStoreId($id);
	}

	/**
	 * Overrides parent method
	 *
	 * @param   string $ordering  An optional ordering field.
	 * @param   string $direction An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @see JModelList::populateState()
	 */
	protected function populateState($ordering = 'p.id', $direction = 'desc')
	{
		$transaction_type = $this->getUserStateFromRequest($this->context . '.filter.transaction_type', 'filter_transaction_type');
		$this->setState('filter.transaction_type', $transaction_type);

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$type_id = $this->getUserStateFromRequest($this->context . '.filter.type_id', 'filter_type_id');
		$this->setState('filter.type_id', $type_id);

		$department_id = $this->getUserStateFromRequest($this->context . '.filter.department_id', 'filter_department_id');
		$this->setState('filter.department_id', $department_id);

		$town_id = $this->getUserStateFromRequest($this->context . '.filter.town_id', 'filter_town_id');
		$this->setState('filter.town_id', $town_id);

		$language = $this->getUserStateFromRequest($this->context . '.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);

		parent::populateState($ordering, $direction);
	}

	/**
	 * Overrides parent method
	 *
	 * @return  JDatabaseQuery  A JDatabaseQuery object to retrieve the data set.
	 *
	 * @see JModelList::getListQuery()
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = Factory::getContainer()->get(DatabaseDriver::class);
		$query = $db->getQuery(true);

		$dispatcher = Factory::getApplication()->getDispatcher();

		// Include the jea plugins for the onBeforeSearchQuery event.
		PluginHelper::importPlugin('jea');

		$query->select(
			'p.id, p.ref, p.transaction_type, p.address, p.price, p.rate_frequency, p.created,
             p.featured, p.published, p.publish_up, p.publish_down, p.access, p.ordering, p.checked_out, p.checked_out_time,
             p.created_by, p.hits, p.language '
		);

		$query->from('#__jea_properties AS p');

		// Join viewlevels
		$query->select('al.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS al ON al.id = p.access');

		// Join departments
		$query->select('d.value AS `department`');
		$query->join('LEFT', '#__jea_departments AS d ON d.id = p.department_id');

		// Join properties types
		$query->select('t.value AS `type`');
		$query->join('LEFT', '#__jea_types AS t ON t.id = p.type_id');

		// Join towns
		$query->select('town.value AS `town`');
		$query->join('LEFT', '#__jea_towns AS town ON town.id = p.town_id');

		// Join users
		$query->select('u.username AS `author`');
		$query->join('LEFT', '#__users AS u ON u.id = p.created_by');

		// Join over the language
		$query->select('l.title AS language_title');
		$query->join('LEFT', $db->quoteName('#__languages') . ' AS l ON l.lang_code = p.language');

		// Filter by transaction type
		if ($transactionType = $this->getState('filter.transaction_type'))
		{
			$query->where('p.transaction_type =' . $db->Quote($db->escape($transactionType)));
		}

		// Filter by property type
		if ($typeId = $this->getState('filter.type_id'))
		{
			$query->where('p.type_id =' . (int) $typeId);
		}

		// Filter by departments
		if ($departmentId = $this->getState('filter.department_id'))
		{
			$query->where('p.department_id =' . (int) $departmentId);
		}

		// Filter by town
		if ($townId = $this->getState('filter.town_id'))
		{
			$query->where('p.town_id =' . (int) $townId);
		}

		// Filter by search
		if ($search = $this->getState('filter.search'))
		{
			$search = $db->Quote('%' . $db->escape($search, true) . '%');
			$search = '(p.ref LIKE ' . $search . ' OR p.title LIKE ' . $search . ' OR p.id LIKE ' . $search . ' OR u.username LIKE ' . $search . ')';
			$query->where($search);
		}

		// Filter by language.
		if ($language = $this->getState('filter.language'))
		{
			$query->where('p.language = ' . $db->quote($language));
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'p.id');
		$orderDirn = $this->state->get('list.direction', 'desc');

		// If language order selected order by languagetable title
		if ($orderCol == 'language')
		{
			$orderCol = 'l.title';
		}

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		$dispatcher->dispatch('onBeforeSearch', new Event('onCheckAnswer', array(&$query, &$this->state)));

		return $query;
	}
}
