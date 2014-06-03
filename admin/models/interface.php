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
defined('_JEXEC') or die();

jimport('joomla.application.component.model');
jimport('joomla.error.log');
jimport('joomla.utilities.date');

require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'propertyInterface.php';

/**
 * Interface model class.
 *
 * This class provides an interface to import data from third party bridges
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 */
abstract class JeaModelInterface extends JModelLegacy
{
    public $persistance = false;

    public $created = 0;

    public $updated = 0;

    public $removed = 0;

    public $total = 0;

    public $imported = 0;

    protected $_logger = null;

    protected $_log_file = 'jea.logs.php';

    protected $_auto_deletion = true;

    protected $_force_utf8 = false;

    protected $_bridge_code = '';

    protected $_propertiesPerStep = 0;


    /**
     * The properties already imported
     *
     * @var array();
     */
    protected $_importedProperties = array();


    public function import()
    {
        if ($this->persistance == true && $this->hasPersistentProperties()) {
            $properties = $this->getPersistentProperties();
        } else {
            $properties =& $this->parse();
        }

        $this->total = count($properties);

        if (empty($this->total)) {
            // Do nothing if there is no properties
            return;
        }

        $ids_to_remove = array();

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*')->from('#__jea_properties');

        if (!empty($this->_bridge_code)) {
            $query->where('bridge_code=' . $db->quote($this->_bridge_code));
        }

        $db->setQuery($query);
        $existingProperties = $db->loadObjectList();

        foreach($existingProperties as $row) {

            if (isset($this->_importedProperties[$row->ref])) {
                // Le bien a déjà été traité donc on passe au suivant
                continue;
            }

            if (isset($properties[$row->ref]) && $properties[$row->ref] instanceof JEAPropertyInterface) {

                if ($this->persistance == true && $this->_propertiesPerStep > 0
                    && $this->updated == $this->_propertiesPerStep) {
                    break;
                }
                // verify update time
                $date = new JDate($row->modified);
                if ($date->toUnix() < $properties[$row->ref]->modified) {
                    // Update needed
                    if ($properties[$row->ref]->save($this->_bridge_code, $row->id)) {
                        $this->updated++;
                    }
                }
                $this->imported++;
                $this->_importedProperties[$row->ref] = true;
                unset($properties[$row->ref]);

            } elseif ($this->_auto_deletion === true) {
                // Property not in the $imported_properties
                // So we can delete it if the autodeletion option is set to true
                $ids_to_remove[] = $row->id;
            }
        }

        if ($this->_auto_deletion === true) {
            // Remove outdated properties
            $this->removeProperties($ids_to_remove);
        }

        foreach($properties as $ref => $row) {

            if ($this->persistance == true && $this->_propertiesPerStep > 0
                && ($this->updated + $this->created) == $this->_propertiesPerStep) {
                break ;
            }

            if ($row instanceof JEAPropertyInterface) {
                if ($row->save($this->_bridge_code)) {
                    $this->created++;
                    $this->_importedProperties[$ref] = true;
                } else {
                    $msg = "Property can't be saved : ";
                    foreach($row->getErrors() as $error) {
                        $msg .= " $error\n";
                    }
                    $msg .= print_r($row->getProperties(), true);
                    $this->log($msg, 'WARN');
                    JError::raiseNotice(200, "A property cant'be saved. See logs for more infos");
                }
            }
            $this->imported++;
            unset($properties[$ref]);
        }

        if ($this->persistance == true && !empty($properties)) {
            $this->setPersistentProperties($properties);
        } elseif ($this->persistance == true && empty($properties)) {
            $this->cleanPersistentProperties();
        }

        if (empty($properties)) {
            $msg = JText::sprintf('COM_JEA_IMPORT_SUCCESSFULLY_ENDED', ucfirst(strtolower($this->_bridge_code)));
            $this->log($msg);
        }
    }

    /**
     * This method must be overriden
     * (polymorphism)
     *
     */
    protected function &parse()
    {
        $properties = array();
        return $properties;
    }

    public function hasPersistentProperties()
    {
        $cache = JFactory::getCache('jea_interface', 'output', 'file');
        $cache->setCaching(true);
        $properties = $cache->get('properties');
        if (is_string($properties)) {
            return true;
        }
        return false;
    }

    public function getPersistentProperties()
    {
        $cache = JFactory::getCache('jea_interface', 'output', 'file');
        $cache->setCaching(true);

        $infos = $cache->get('infos');

        if (is_string($infos)) {
            $infos = unserialize($infos);
            $this->_importedProperties = $infos->importedProperties;
        }

        $properties = $cache->get('properties');
        if ($properties === false) {
            return array();
        }
        return unserialize($properties);
    }

    public function setPersistentProperties($properties=array())
    {
        $cache = JFactory::getCache('jea_interface', 'output', 'file');
        $cache->setCaching(true);
        $cache->store(serialize($properties), 'properties');
        $infos = $cache->get('infos');
        if (!$infos) {
            $infos = new stdClass();
            $infos->total = $this->total;
            $infos->updated = $this->updated;
            $infos->removed = $this->removed;
            $infos->created = $this->created;
            $infos->imported = $this->imported;
            $infos->importedProperties = $this->_importedProperties;
            $cache->store(serialize($infos), 'infos');
        } else {
            $infos = unserialize($infos);
            $infos->updated  += $this->updated;
            $infos->removed  += $this->removed;
            $infos->created  += $this->created;
            $infos->imported += $this->imported;
            foreach ($this->_importedProperties as $k=>$v) {
                $infos->importedProperties[$k] = $v;
            }
            $cache->store(serialize($infos), 'infos');
        }
        // Update interface informations
        $this->total   = $infos->total;
        $this->updated = $infos->updated;
        $this->removed = $infos->removed;
        $this->created = $infos->created;
        $this->imported = $infos->imported;
    }

    public function cleanPersistentProperties()
    {
        $cache = JFactory::getCache('jea_interface', 'output', 'file');
        $cache->setCaching(true);
        $infos = $cache->get('infos');

        if (is_string($infos)) {
            $infos = @unserialize($infos);
            // Update interface informations
            $this->total     = $infos->total;
            $this->updated  += $infos->updated;
            $this->removed  += $infos->removed;
            $this->created  += $infos->created;
            $this->imported += $infos->imported;
        }

        // Delete cache infos
        $cache->clean();
    }

    public function removeProperties($ids = array())
    {
        if (empty($ids)) {
            return false;
        }

        $model = JModelLegacy::getInstance('Property', 'JeaModel');

        $flip = array_flip($ids);
        $model->delete($ids);
        $count = 0;
        foreach ($ids as $id) {
            if (!isset($flip[$id])) {
                break;
            }
            $count++;
        }

        $this->removed = $count;
        return true;
    }


    public function setLogFileName($fileName='')
    {
        $this->_log_file = $fileName;
    }

    /**
     * Ecrire un message de log
     *
     * EMERG   = 0;  // Emergency: system is unusable
     * ALERT   = 1;  // Alert: action must be taken immediately
     * CRIT    = 2;  // Critical: critical conditions
     * ERR     = 3;  // Error: error conditions
     * WARN    = 4;  // Warning: warning conditions
     * NOTICE  = 5;  // Notice: normal but significant condition
     * INFO    = 6;  // Informational: informational messages
     * DEBUG   = 7;  // Debug: debug messages
     *
     * @param string $message
     */
    public function log($message, $status='')
    {
        jimport('joomla.log.log');
        // A category name
        $cat = strtolower($this->_bridge_code);

        JLog::addLogger(
            array(
                //Sets file name
                'text_file' => $this->_log_file
            ),
            //Sets all JLog messages to be set to the file
            JLog::ALL,
            //Chooses a category name
            $cat
        );
        $status = strtoupper($status);
        $levels = array(
            'EMERG'  => JLog::EMERGENCY,
            'ALERT'  => JLog::ALERT,
            'CRIT'   => JLog::CRITICAL,
            'ERR'    => JLog::ERROR,
            'WARN'   => JLog::WARNING,
            'NOTICE' => JLog::NOTICE,
            'INFO'   => JLog::INFO,
            'DEBUG'  => JLog::DEBUG
        );

        if (isset($levels[$status])) {
            $status = $levels[$status];
        } else {
            $status = JLog::INFO;
        }

        JLog::add($message, $status, $cat);
    }

    /**
     * Récupération des logs
     *
     * @return string
     */
    public function getLogsContent()
    {
        $path = JFactory::getConfig()->get('log_path');
        $logFile = $path.DS.$this->_log_file;
        if (JFile::exists($logFile)) {
            return file_get_contents($logFile);
        }
        return '';
    }

    /**
     * Efface le fichier des logs
     *
     * @return bool
     */
    public function deleteLogs()
    {
        $path = JFactory::getConfig()->get('log_path');
        $logFile = $path.DS.$this->_log_file;
        if(JFile::exists($logFile)) {
            return JFile::delete($logFile);
        }
        return false;
    }

}
