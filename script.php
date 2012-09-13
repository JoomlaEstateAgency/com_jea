<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @version     $Id$
 * @package		Jea
 * @copyright	Copyright (C) 2008 PHILIP Sylvain. All rights reserved.
 * @license		GNU/GPL, see LICENSE.txt
 * Joomla Estate Agency is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses.
 *
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Install Script file of JEA component
 */
class com_jeaInstallerScript
{
    /**
     * method to install the component
     *
     * @return void
     */
    function install($parent)
    {

    }

    /**
     * method to uninstall the component
     *
     * @return void
     */
    function uninstall($parent)
    {

    }

    /**
     * method to update the component
     *
     * @return void
     */
    function update($parent)
    {

    }

    /**
     * method to run before an install/update/uninstall method
     *
     * @return void
     */
    function preflight($type, $parent)
    {
        $manifest = $parent->getParent()->getManifest();

        // Fix the missing schema upddate in the previous JEA 2.0 version
        if ($type == 'update' && $manifest->version == '2.1') {
            
            $row = JTable::getInstance('extension');
            $eid = $row->find(array('element' => 'com_jea', 'type' => 'component'));

            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('version_id ');
            $query->from('#__schemas');
            $query->where($query->qn('extension_id') . ' = ' . (int) $eid);
            $db->setQuery($query);
            $result = $db->loadResult();
            if (!$result) {
                $query->insert('#__schemas');
                $query->columns('extension_id', 'version_id');
                $query->values(array($eid, '2.0'));
                $db->setQuery($query);
            }
        }
    }

    /**
     * method to run after an install/update/uninstall method
     *
     * @return void
     */
    function postflight($type, $parent)
    {

    }
}


