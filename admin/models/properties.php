<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 * 
 * @version     $Id$
 * @package		Jea.admin
 * @copyright	Copyright (C) 2008 PHILIP Sylvain. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla Estate Agency is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses.
 * 
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.modellist');

class JeaModelProperties extends JModelList
{
    
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'p.id',
				'ref', 'p.ref',
				'title', 'p.title',
				'alias', 'p.alias',
				'price', 'p.price',
				'checked_out', 'p.checked_out',
				'checked_out_time', 'p.checked_out_time',
				'published', 'p.published',
				'created', 'p.created',
				'created_by', 'p.created_by',
				'ordering', 'p.ordering',
				'featured', 'p.emphasis',
				'hits', 'p.hits'
			);
		}
		
		// Set the internal state marker to true
		$config['ignore_request'] = true;

		parent::__construct($config);
		
		// Initialize state information and use id as default column ordering
		$this->populateState('p.id', 'desc');
	}
	
	
	
	
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$this->context .= '.properties';

		$transaction_type = $this->getUserStateFromRequest($this->context.'.filter.transaction_type', 'filter_transaction_type');
		$this->setState('filter.transaction_type', $transaction_type);

		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		$type_id = $this->getUserStateFromRequest($this->context.'.filter.type_id', 'filter_type_id');
		$this->setState('filter.type_id', $type_id);
		
		$department_id = $this->getUserStateFromRequest($this->context.'.filter.department_id', 'filter_department_id');
		$this->setState('filter.department_id', $department_id);

        $town_id = $this->getUserStateFromRequest($this->context.'.filter.town_id', 'filter_town_id');
		$this->setState('filter.town_id', $town_id);
		
		parent::populateState($ordering, $direction);
	}
    
    
    
    
    /**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	protected function getListQuery()
	{
	    // Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$user	= JFactory::getUser();
		
		$query->select('p.id, p.ref, p.address, p.price, p.date_insert, 
		p.emphasis, p.published, p.ordering, p.checked_out, p.checked_out_time,
		p.created_by, p.hits ');
		
		$query->from('#__jea_properties AS p');
		
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

		// Filter by transaction type
	    if ($transactionType = $this->getState('filter.transaction_type')) {
			$query->where('p.transaction_type ='. $db->Quote($db->escape($transactionType)));
		}

		// Filter by property type
		if ($typeId = $this->getState('filter.type_id')) {
			$query->where('p.type_id ='.(int) $typeId);
		}
		
	    // Filter by departments
		if ($departmentId = $this->getState('filter.department_id')) {
			$query->where('p.department_id ='.(int) $departmentId);
		}

	    // Filter by town
		if ($townId = $this->getState('filter.town_id')) {
			$query->where('p.town_id ='.(int) $townId);
		}
		
	    // Filter by search
		if ($search = $this->getState('filter.search')) {
		    $search = $db->Quote('%'.$db->escape($search, true).'%');
		    $search = '(p.ref LIKE '.$search.' OR p.title LIKE '.$search
		            . 'OR u.username LIKE ' .$search .')';
			$query->where($search);
		}

		
		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		
		$query->order($db->escape($orderCol.' '.$orderDirn));

		// echo $query;
		
		
		
		return $query;
		
	}
     
}

