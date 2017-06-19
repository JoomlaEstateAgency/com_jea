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

use Joomla\Utilities\ArrayHelper;

/**
 * Properties model class.
 *
 * @package     Joomla.Site
 * @subpackage  com_jea
 *
 * @see         JModelList
 *
 * @since       2.0
 */
class JeaModelProperties extends JModelList
{
	/**
	 * Model context string.
	 *
	 * @var string
	 */
	protected $context = 'com_jea.properties';

	/**
	 * Filters and their default values used in the query
	 *
	 * @var array
	 */
	protected $filters = array(
		'search' => '',
		'transaction_type' => '',
		'type_id' => 0,
		'department_id' => 0,
		'town_id' => 0,
		'area_id' => 0,
		'zip_codes' => '',
		'budget_min' => 0,
		'budget_max' => 0,
		'living_space_min' => 0,
		'living_space_max' => 0,
		'land_space_min' => 0,
		'land_space_max' => 0,
		'rooms_min' => 0,
		'bedrooms_min' => 0,
		'bathrooms_min' => 0,
		'floor' => '',
		'hotwatertype' => 0,
		'heatingtype' => 0,
		'condition' => 0,
		'orientation' => 0,
		'amenities' => array()
	);

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see JModelList
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			// This fields concern the ordering
			$config['filter_fields'] = array(
				'p.id',
				'p.price',
				'p.created',
				'p.ordering',
				'p.living_space',
				'p.land_space',
				'p.hits',
				'p.ref',
				'type',
				'departement',
				'town',
				'area'
			);
		}

		// Add a context by Itemid
		$itemId = JFactory::getApplication()->input->getInt('Itemid', 0);

		if ($itemId > 0)
		{
			$this->context .= '.menuitem' . $itemId;
		}

		parent::__construct($config);
	}

	/**
	 * Overrides parent method
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @see JModelList::populateState()
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication('site');

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);

		$searchContext = false;

		foreach ($this->filters as $name => $defaultValue)
		{
			$state = $this->getUserStateFromRequest($this->context . '.filter.' . $name, 'filter_' . $name, $defaultValue, 'none', false);

			if (! $searchContext && ! empty($state))
			{
				/* This flag indiquate that some filters are set by an user, so the context is a search.
				 * It will be usefull in the view to retrieve this flag.
				 */
				$searchContext = true;
			}
			else
			{
				// Get component menuitem parameters
				$state2 = $params->get('filter_' . $name, $defaultValue);

				if (! empty($state2))
				{
					$state = $state2;
				}
			}

			// If the state is an array, check if it not contains only a zero value
			if (is_array($state) && in_array(0, $state))
			{
				$key = array_search(0, $state);
				unset($state[$key]);
			}

			$this->setState('filter.' . $name, $state);
		}

		$this->setState('filter.language', $app->getLanguageFilter());

		$this->setState('searchcontext', $searchContext);

		// List state information
		$limit = $this->getUserStateFromRequest($this->context . '.filter.limit', 'limit', $params->get('list_limit', 10), 'uint');
		$this->setState('list.limit', $limit);

		$orderCol = $app->getUserStateFromRequest($this->context . '.ordercol', 'filter_order', $ordering);

		if ($orderCol)
		{
			$this->setState('list.ordering', $orderCol);
		}

		$orderDirn = $app->getUserStateFromRequest($this->context . '.orderdirn', 'filter_order_Dir', $direction);

		if ($orderDirn)
		{
			$this->setState('list.direction', $orderDirn);
		}

		$value = $app->input->get('limitstart', 0, 'uint');
		$limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
		$this->setState('list.start', $limitstart);
	}

	/**
	 * Return the model filters
	 *
	 * @return array
	 */
	public function getFilters ()
	{
		return $this->filters;
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
		$dispatcher = JDispatcher::getInstance();

		// Include the jea plugins for the onBeforeSearchQuery event.
		JPluginHelper::importPlugin('jea');

		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('p.*');
		$query->from('#__jea_properties AS p');

		// Join properties types
		$query->select('t.value AS `type`');
		$query->join('LEFT', '#__jea_types AS t ON t.id = p.type_id');

		// Join departments
		$query->select('d.value AS department');
		$query->join('LEFT', '#__jea_departments AS d ON d.id = p.department_id');

		// Join towns
		$query->select('town.value AS town');
		$query->join('LEFT', '#__jea_towns AS town ON town.id = p.town_id');

		// Join areas
		$query->select('area.value AS area');
		$query->join('LEFT', '#__jea_areas AS area ON area.id = p.area_id');

		// Join conditions
		$query->select('c.value AS `condition`');
		$query->join('LEFT', '#__jea_conditions AS c ON c.id = p.condition_id');

		// Join users
		$query->select('u.username AS author');
		$query->join('LEFT', '#__users AS u ON u.id = p.created_by');

		// Join slogans
		$query->select('s.value AS slogan');
		$query->join('LEFT', '#__jea_slogans AS s ON s.id = p.slogan_id');

		if ($this->getState('manage') == true)
		{
			$lang = $this->getUserStateFromRequest($this->context . '.filter.language', 'filter_language', '');

			if ($lang)
			{
				$query->where('p.language =' . $db->Quote($db->escape($lang)));
			}

			$this->setState('filter.language', $lang);

			$user = JFactory::getUser();
			$canEdit = $user->authorise('core.edit', 'com_jea');
			$canEditOwn = $user->authorise('core.edit.own', 'com_jea');

			if (!$canEdit && $canEditOwn)
			{
				// Get only the user properties
				$query->where('p.created_by =' . (int) $user->id);
			}

			if (!$canEditOwn)
			{
				throw new \RuntimeException(JText::_('JERROR_ALERTNOAUTHOR'));
			}
		}
		else
		{
			if ($this->getState('filter.language'))
			{
				$query->where('p.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
			}

			$query->where('p.published=1');

			// Filter by access level
			$user = JFactory::getUser();
			$groups = implode(',', $user->getAuthorisedViewLevels());
			$query->where('p.access IN (' . $groups . ')');

			// Filter by start and end dates.
			$nullDate = $db->Quote($db->getNullDate());
			$nowDate = $db->Quote(JFactory::getDate()->toSql());

			$query->where('(p.publish_up = ' . $nullDate . ' OR p.publish_up <= ' . $nowDate . ')');
			$query->where('(p.publish_down = ' . $nullDate . ' OR p.publish_down >= ' . $nowDate . ')');
		}

		// Filter by search
		if ($value = $this->getState('filter.search'))
		{
			$value = $db->Quote('%' . $db->escape($value, true) . '%');
			$search = '(p.ref LIKE ' . $value . ' OR p.title LIKE ' . $value . ')';
			$query->where($search);
		}

		// Filter by transaction type
		if ($value = $this->getState('filter.transaction_type'))
		{
			$query->where('p.transaction_type =' . $db->Quote($db->escape($value)));
		}

		// Filter by property type
		if ($value = $this->getState('filter.type_id'))
		{
			if (is_array($value))
			{
				$value = ArrayHelper::toInteger($value);
				$query->where('p.type_id IN(' . implode(',', $value) . ')');
			}
			else
			{
				$query->where('p.type_id =' . (int) $value);
			}
		}

		// Filter by departments
		if ($value = $this->getState('filter.department_id'))
		{
			$query->where('p.department_id =' . (int) $value);
		}

		// Filter by town
		if ($value = $this->getState('filter.town_id'))
		{
			$query->where('p.town_id =' . (int) $value);
		}

		// Filter by area
		if ($value = $this->getState('filter.area_id'))
		{
			$query->where('p.area_id =' . (int) $value);
		}

		// Filter by zip codes
		if ($value = $this->getState('filter.zip_codes'))
		{
			$zip_codes = explode(',', $value);

			foreach ($zip_codes as &$v)
			{
				$v = $db->Quote($db->escape(trim($v)));
			}

			$query->where('p.zip_code IN(' . implode(',', $zip_codes) . ')');
		}

		// Filter by budget min
		if ($value = $this->getState('filter.budget_min'))
		{
			$query->where('p.price >=' . (int) $value);
		}

		// Filter by budget max
		if ($value = $this->getState('filter.budget_max'))
		{
			$query->where('p.price <=' . (int) $value);
		}

		// Filter by living space min
		if ($value = $this->getState('filter.living_space_min'))
		{
			$query->where('p.living_space >=' . (int) $value);
		}

		// Filter by living space max
		if ($value = $this->getState('filter.living_space_max'))
		{
			$query->where('p.living_space <=' . (int) $value);
		}

		// Filter by land space min
		if ($value = $this->getState('filter.land_space_min'))
		{
			$query->where('p.land_space >=' . (int) $value);
		}

		// Filter by land space max
		if ($value = $this->getState('filter.land_space_max'))
		{
			$query->where('p.land_space <=' . (int) $value);
		}

		// Filter by rooms min
		if ($value = $this->getState('filter.rooms_min'))
		{
			$query->where('p.rooms >=' . (int) $value);
		}

		// Filter by bedrooms
		if ($value = $this->getState('filter.bedrooms_min'))
		{
			$query->where('p.bedrooms >=' . (int) $value);
		}

		// Filter by bathrooms
		if ($value = $this->getState('filter.bathrooms_min'))
		{
			$query->where('p.bathrooms >=' . (int) $value);
		}

		// Filter by floor
		// 0 is a valid value as it corresponds to ground floor
		if ($value = $this->getState('filter.floor') != '')
		{
			$query->where('p.floor =' . (int) $value);
		}

		// Filter by hot water type
		if ($value = $this->getState('filter.hotwatertype'))
		{
			$query->where('p.hot_water_type =' . (int) $value);
		}

		// Filter by heating type condition
		if ($value = $this->getState('filter.heatingtype'))
		{
			$query->where('p.heating_type =' . (int) $value);
		}

		// Filter by condition
		if ($value = $this->getState('filter.condition'))
		{
			$query->where('p.condition_id =' . (int) $value);
		}

		// Filter by orientation
		if ($value = $this->getState('filter.orientation'))
		{
			$query->where('p.orientation =' . $db->Quote($db->escape($value)));
		}

		// Filter by amenities
		if ($value = $this->getState('filter.amenities'))
		{
			$amenities = ArrayHelper::toInteger((array) $value);

			foreach ($amenities as $id)
			{
				if ($id > 0)
				{
					$query->where("p.amenities LIKE '%-{$id}-%'");
				}
			}
		}

		// Add the list ordering clause.
		$params = $this->state->get('params');

		if ($params != null)
		{
			$orderCol = $this->state->get('list.ordering', $params->get('orderby', 'p.id'));
			$orderDirn = $this->state->get('list.direction', $params->get('orderby_direction', 'DESC'));
		}
		else
		{
			$orderCol = $this->state->get('list.ordering', 'p.id');
			$orderDirn = $this->state->get('list.direction', 'DESC');
		}

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		$dispatcher->trigger('onBeforeSearch', array(&$query, &$this->state));

		return $query;
	}

	/**
	 * Retrieve the list of items which can be managed
	 *
	 * @return multitype:array|boolean
	 */
	public function getMyItems()
	{
		$this->setState('manage', true);

		return $this->getItems();
	}

	/**
	 * Return the min max values for a column
	 *
	 * @param   string  $fieldName         The column name
	 * @param   string  $transaction_type  Optional transaction type to filter on
	 *
	 * @return  integer[]
	 */
	public function getFieldLimit($fieldName = '', $transaction_type = '')
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$col = '`' . $db->escape($fieldName) . '`';
		$query->select("MIN($col) AS min_value, MAX($col) AS max_value");
		$query->from('#__jea_properties');

		if ($transaction_type)
		{
			$query->where('transaction_type =' . $db->Quote($db->escape($transaction_type)));
		}

		$db->setQuery($query);
		$row = $db->loadObject();

		if (empty($row))
		{
			return array(0, 0);
		}

		return array((int) $row->min_value, (int) $row->max_value);
	}
}
