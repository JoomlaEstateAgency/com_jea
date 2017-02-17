<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2017 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.modellist');

/**
 * Gateways model class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
*/
class JeaModelGateways extends JModelList
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
                'type', 'type',
                'title', 'title',
                'provider', 'provider',
                'published', 'published',
                'ordering', 'ordering'
            );
        }

        parent::__construct($config);
    }

    /* (non-PHPdoc)
     * @see JModelList::getListQuery()
    */
    protected function getListQuery()
    {
        // Create a new query object.
        $db    = $this->getDbo();
        $query = $db->getQuery(true);

        $query->select('*');
        $query->from('#__jea_gateways');
        if ($type = $this->state->get('filter.type')) {
            $query->where('type='. $db->Quote($type));
        }

        // Add the list ordering clause.
        $orderCol    = $this->state->get('list.ordering', 'id');
        $orderDirn    = $this->state->get('list.direction', 'DESC');
        
        $query->order($db->escape($orderCol.' '.$orderDirn));

        return $query;
    }
     
}

