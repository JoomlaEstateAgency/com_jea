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

jimport('joomla.filesystem.folder');

/**
 * Feature list model class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @see         JModelList
 *
 * @since       2.0
 */
class JeaModelFeaturelist extends JModelList
{
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
			$config['filter_fields'] = array(
				'id',
				'f.id',
				'value',
				'f.value',
				'ordering',
				'f.ordering'
			);
		}

		// Set the internal state marker to true
		$config['ignore_request'] = true;

		parent::__construct($config);

		// Initialize state information and use id as default column ordering
		$this->populateState('f.id', 'desc');
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
		$this->context .= '.featurelist';

		// The active feature
		$feature = $this->getUserStateFromRequest($this->context . '.feature.name', 'feature');
		$this->setState('feature.name', $feature);

		// Retrieve the feature table params
		$xmlPath = JPATH_COMPONENT . '/models/forms/features/';
		$xmlFiles = JFolder::files($xmlPath);

		foreach ($xmlFiles as $filename)
		{
			if (preg_match('/^[0-9]{2}-([a-z]*).xml/', $filename, $matches))
			{
				if ($feature == $matches[1])
				{
					$form = simplexml_load_file($xmlPath . '/' . $filename);
					$this->setState('feature.table', (string) $form['table']);

					if (isset($form['filters']))
					{
						$this->setState('feature.filters', (string) $form['filters']);
					}

					// Check if this feature uses language
					$lang = $form->xpath("//field[@name='language']");

					if (! empty($lang))
					{
						$this->setState('language_enabled', true);
						$this->filter_fields[] = 'language';
						$this->filter_fields[] = 'f.language';
					}

					break;
				}
			}
		}

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		if ($filters = $this->getState('feature.filters'))
		{
			$filters = explode(',', $filters);

			foreach ($filters as $filter)
			{
				$filter = explode(':', $filter);
				$filterKey = $filter[0];
				$filterState = $this->getUserStateFromRequest($this->context . '.filter.' . $filterKey, $filterKey);
				$this->setState('filter.' . $filterKey, $filterState);
			}
		}

		if ($this->getState('language_enabled'))
		{
			$language = $this->getUserStateFromRequest($this->context . '.filter.language', 'filter_language', '');
			$this->setState('filter.language', $language);
		}

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
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('f.*')->from($db->escape($this->getState('feature.table')) . ' AS f');

		// Join over the language
		if ($this->getState('language_enabled'))
		{
			$query->select('l.title AS language_title');
			$query->join('LEFT', $db->quoteName('#__languages') . ' AS l ON l.lang_code = f.language');
		}

		if ($filters = $this->getState('feature.filters'))
		{
			$filters = explode(',', $filters);

			foreach ($filters as $filter)
			{
				$filter = explode(':', $filter);
				$filterKey = $db->escape($filter[0]);

				if ($filterState = $this->getState('filter.' . $filterKey, ''))
				{
					$query->where('f.' . $filterKey . ' =' . $db->Quote($filterState));
				}
			}
		}

		// Filter by search
		if ($search = $this->getState('filter.search'))
		{
			$search = $db->Quote('%' . $db->escape($search, true) . '%');
			$query->where('f.value LIKE ' . $search);
		}

		// Filter on the language.
		if ($this->getState('language_enabled'))
		{
			if ($language = $this->getState('filter.language'))
			{
				$query->where('f.language = ' . $db->quote($language));
			}
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		// If language order selected order by languagetable title
		if ($this->getState('language_enabled'))
		{
			if ($orderCol == 'language')
			{
				$orderCol = 'l.title';
			}
		}

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}
}
