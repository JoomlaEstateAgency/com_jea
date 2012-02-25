<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @version     $Id$
 * @package     Jea.admin
 * @copyright   Copyright (C) 2008 PHILIP Sylvain. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 * Joomla Estate Agency is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses.
 *
 */

// no direct access
defined('_JEXEC') or die();

// jimport('joomla.application.component.model');
jimport('joomla.application.component.modeladmin');
jimport('joomla.filesystem.file');

require_once JPATH_COMPONENT.DS.'tables'.DS.'features.php';
require JPATH_COMPONENT.DS.'helpers'.DS.'utility.php';

class JeaModelFeatures extends JModelAdmin
{
    
     /**
     * Method to get the record form.
     *
     * @param	array	$data		Data for the form.
     * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
     *
     * @return	mixed	A JForm object on success, false on failure
     * @since	1.6
     */
    public function getForm($data = array(), $loadData = true)
    {
        
    }
    
    
    
    public function getTables()
    {
        $files = JFolder::files(JPATH_COMPONENT.'/models/forms/features/');

        $items = array();

        foreach($files as $filename) {
            if (preg_match('/^[0-9]{2}-([a-z]*).xml/', $filename, $matches)) {
                $items[] = $matches[1];
            }
        }

        return $items;
    }

    public function getCSVData($tableName='')
    {
        $db = JFactory::getDbo();
        
        $query    = $db->getQuery(true);
        $query->select('*')->from($db->escape($tableName));
        $db->setQuery($query);
        $rows = $db->loadRowList();
        $csv = '';
        foreach ($rows as $row) {
            $csv .= JeaHelperUtility::arrayToCSV($row);
        }
        return $csv;
    }

    

    /**
     * Import rows from CSV file and return the number of inserted rows
     *
     * @param string $file
     * @param string $tableName
     * @return int
     */

    function importFromCSV($file='', $tableName='')
    {
        $row=0;

        if (($handle = fopen($file, 'r')) !== FALSE) { 
            $db = JFactory::getDbo();

            $tableName = $db->escape($tableName);
            $table = new FeaturesFactory($tableName, 'id', $db);
            $cols = array_keys($table->getProperties());

            $query    = $db->getQuery(true);
            $query->select('*')->from($tableName);
            $db->setQuery($query);
            $ids = $db->loadObjectList('id');

            $query->select('ordering')->from($tableName)->order('ordering DESC');
            $db->setQuery($query);
            $maxOrdering = $db->loadResult();

            if ($maxOrdering == null) {
                $maxOrdering = 1;
            }

            while (($data = fgetcsv($handle, 1000, ';', '"')) !== FALSE) {

                $num = count($data);
                $bind = array();

                for ($c=0; $c < $num; $c++) {
                   if (isset($cols[$c])) {
                       $bind[$cols[$c]] = $data[$c];
                   }
                }

                try {

                    if (isset($bind['id']) && isset($ids[$bind['id']])) {
                       // Load row to update
                       $table->load((int) $bind['id']);
                       
                    } elseif (isset($bind['ordering'])) {
                        $bind['ordering'] = $maxOrdering;
                        $maxOrdering++;
                    }
                    
                    $table->save($bind, '', 'id');

                } catch (Exception $e) {
                    $this->setError($e->getMessage());
                    continue;
                }

                // To force new insertion
                $table->id = null;
                $row++;
            }
        }

        return $row;
    }

}