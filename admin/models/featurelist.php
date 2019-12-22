<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2019 PHILIP Sylvain. All rights reserved.
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
				'f.id',
				'f.value',
				'f.ordering',
				'l.title',
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
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string A store id.
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('feature.name');
		$id .= ':' . $this->getState('filter.search');

		$filters = $this->getState('feature.filters');

		if (is_array($filters) && !empty($filters))
		{
			foreach ($filters as $filter)
			{
				$state = $this->getState('filter.' . $filter);

				if (!empty($state))
				{
					$id .= ':' . $state;
				}
			}
		}

		return parent::getStoreId($id);
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
	protected function populateState($ordering = 'f.id', $direction = 'desc')
	{
		// The active feature
		$feature = $this->getUserStateFromRequest($this->context . '.feature.name', 'feature');
		$this->setState('feature.name', $feature);

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

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
					$this->setState('feature.form', $form);
					$this->setState('feature.table', (string) $form['table']);

					$filterFields = $form->xpath("/form/fields[@name='filter']");
					$filters = array();

					if (isset($filterFields[0]) && $filterFields[0] instanceof SimpleXMLElement)
					{
						foreach ($filterFields[0]->children() as $filterField)
						{
							$filter = (string) $filterField['name'];
							$filterState = $this->getUserStateFromRequest($this->context . '.filter.' . $filter, 'filter_' . $filter, '');
							$this->setState('filter.' . $filter, $filterState);
							$filters[] = $filter;
							$this->filter_fields[] = $filter;
						}
					}

					$this->setState('feature.filters', $filters);

					// Check if this feature uses language
					$lang = $form->xpath("//field[@name='language']");

					if (!empty($lang))
					{
						$this->setState('language_enabled', true);
					}

					break;
				}
			}
		}

		parent::populateState($ordering, $direction);
	}

	/**
	 * Get the filter form
	 *
	 * @param   array    $data      data
	 * @param   boolean  $loadData  load current data
	 *
	 * @return  \JForm|boolean  The \JForm object or false on error
	 *
	 * @since   3.2
	 */
	public function getFilterForm($data = array(), $loadData = true)
	{
		$form = parent::getFilterForm($data, $loadData);

		if ($form instanceof JForm)
		{
			$featureForm = $this->getState('feature.form');

			if ($featureForm instanceof SimpleXMLElement)
			{
				$form->load($featureForm);

				if ($loadData)
				{
					$data = $this->loadFormData();
					$form->bind($data);
				}
			}
		}

		return $form;
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
			foreach ($filters as $filter)
			{
				if ($filterState = $this->getState('filter.' . $filter, ''))
				{
					$query->where('f.' . $filter . ' =' . $db->Quote($filterState));
				}
			}
		}

		// Filter by search
		if ($search = $this->getState('filter.search'))
		{
			$search = $db->Quote('%' . $db->escape($search, true) . '%');
			$query->where('f.value LIKE ' . $search);
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'f.id');
		$orderDirn = $this->state->get('list.direction', 'desc');

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}
}
