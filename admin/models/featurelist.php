<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @version     $Id$
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2012 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.modellist');

/**
 * Feature list model class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 */
class JeaModelFeaturelist extends JModelList
{
    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see      JModelList
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'f.id',
                'ordering', 'f.ordering',
            );
        }

        // Set the internal state marker to true
        $config['ignore_request'] = true;

        parent::__construct($config);

        // Initialize state information and use id as default column ordering
        $this->populateState('f.id', 'desc');
    }


    /* (non-PHPdoc)
     * @see JModelList::populateState()
     */
    protected function populateState($ordering = null, $direction = null)
    {
        $this->context .= '.featurelist';

        $feature = $this->getUserStateFromRequest($this->context.'.feature.name', 'feature');
        $this->setState('feature.name', $feature);

        // Retrieve the feature table params
        $xmlPath = JPATH_COMPONENT.'/models/forms/features/';
        $xmlFiles = JFolder::files($xmlPath);

        foreach ($xmlFiles as $filename) {
            if (preg_match('/^[0-9]{2}-([a-z]*).xml/', $filename, $matches)) {
                if ($feature == $matches[1]) {
                    $form = simplexml_load_file($xmlPath.DS.$filename);
                    $this->setState('feature.table', (string) $form['table']);

                    if (isset($form['filters'])) {
                        $this->setState('feature.filters', (string) $form['filters']);
                    }
                }
            }
        }

        $search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        if ($filters = $this->getState('feature.filters')) {
            $filters = explode(',', $filters);
            foreach ($filters as $filter) {
                $filter = explode(':', $filter);
                $filterKey = $filter[0];
                $filterState = $this->getUserStateFromRequest($this->context.'.filter.'.$filterKey, $filterKey);
                $this->setState('filter.'.$filterKey, $filterState);
            }
        }

        parent::populateState($ordering, $direction);
    }


    /* (non-PHPdoc)
     * @see JModelList::getListQuery()
     */
    protected function getListQuery()
    {
        // Create a new query object.
        $db        = $this->getDbo();
        $query    = $db->getQuery(true);

        $query->select('f.*')->from($db->escape($this->getState('feature.table')).' AS f');

        if ($filters = $this->getState('feature.filters')) {
            $filters = explode(',', $filters);
            foreach ($filters as $filter) {
                $filter = explode(':', $filter);
                $filterKey = $db->escape($filter[0]);
                if ($filterState = $this->getState('filter.'.$filterKey, '')) {
                    $query->where('f.' . $filterKey . ' ='.$db->Quote($filterState));
                }
            }
        }

        // Filter by search
        if ($search = $this->getState('filter.search')) {
            $search = $db->Quote('%'.$db->escape($search, true).'%');
            $query->where('f.value LIKE '.$search);
        }

        // Add the list ordering clause.
        $orderCol    = $this->state->get('list.ordering');
        $orderDirn   = $this->state->get('list.direction');

        $query->order($db->escape($orderCol.' '.$orderDirn));
        // echo $query;
        return $query;

    }
     
}

