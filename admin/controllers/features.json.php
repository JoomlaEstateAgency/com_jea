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

jimport('joomla.application.component.controller');

/**
 * Features Ajax controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 */
class JeaControllerFeatures extends JController
{

    /**
     * Get list of areas in relation with a town
     */
    public function get_areas()
    {
        $response = false;

        // Require town id
        if ($town_id = JRequest::getInt('town_id', 0)) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('f.id , f.value');
            $query->from('#__jea_areas AS f');
            $query->where('town_id='. $town_id);
            $db->setQuery($query);
            $response = $db->loadObjectList();
        }

        echo json_encode($response);
    }

    /**
     * Get list of towns in relation with a department
     */
    public function get_towns()
    {
        $response = false;

        // Require department id
        if ($department_id = JRequest::getInt('department_id', 0)) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('f.id , f.value');
            $query->from('#__jea_towns AS f');
            $query->where('department_id='. $department_id);
            $db->setQuery($query);
            $response = $db->loadObjectList();
        }

        echo json_encode($response);
    }

}
