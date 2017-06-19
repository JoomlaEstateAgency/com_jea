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

jimport('joomla.error.log');
jimport('joomla.utilities.date');

require_once JPATH_COMPONENT_ADMINISTRATOR . '/models/propertyInterface.php';

/**
 * Interface model class.
 * This class provides an interface to import data from third party bridges
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @see         JModelLegacy
 *
 * @since       2.0
 */
abstract class JeaModelInterface extends JModelLegacy
{
	public $persistance = false;

	public $created = 0;

	public $updated = 0;

	public $removed = 0;

	public $total = 0;

	public $imported = 0;

	protected $logger = null;

	protected $log_file = 'jea.logs.php';

	protected $auto_deletion = true;

	protected $force_utf8 = false;

	protected $bridge_code = '';

	protected $propertiesPerStep = 0;

	/**
	 * The properties already imported
	 *
	 * @var array();
	 */
	protected $importedProperties = array();

	/**
	 * Import method
	 *
	 * @return  void
	 */
	public function import()
	{
		if ($this->persistance == true && $this->hasPersistentProperties())
		{
			$properties = $this->getPersistentProperties();
		}
		else
		{
			$properties = & $this->parse();
		}

		$this->total = count($properties);

		if (empty($this->total))
		{
			// Do nothing if there is no properties
			return;
		}

		$ids_to_remove = array();

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')->from('#__jea_properties');

		if (! empty($this->bridge_code))
		{
			$query->where('bridge_code=' . $db->quote($this->bridge_code));
		}

		$db->setQuery($query);
		$existingProperties = $db->loadObjectList();

		foreach ($existingProperties as $row)
		{
			if (isset($this->importedProperties[$row->ref]))
			{
				continue;
			}

			if (isset($properties[$row->ref]) && $properties[$row->ref] instanceof JEAPropertyInterface)
			{
				if ($this->persistance == true && $this->propertiesPerStep > 0 && $this->updated == $this->propertiesPerStep)
				{
					break;
				}

				// Verify update time
				$date = new JDate($row->modified);

				if ($date->toUnix() < $properties[$row->ref]->modified)
				{
					// Update needed
					if ($properties[$row->ref]->save($this->bridge_code, $row->id))
					{
						$this->updated ++;
					}
				}

				$this->imported ++;
				$this->importedProperties[$row->ref] = true;
				unset($properties[$row->ref]);
			}
			elseif ($this->auto_deletion === true)
			{
				// Property not in the $imported_properties
				// So we can delete it if the autodeletion option is set to true
				$ids_to_remove[] = $row->id;
			}
		}

		if ($this->auto_deletion === true)
		{
			// Remove outdated properties
			$this->removeProperties($ids_to_remove);
		}

		foreach ($properties as $ref => $row)
		{
			if ($this->persistance == true && $this->propertiesPerStep > 0 && ($this->updated + $this->created) == $this->propertiesPerStep)
			{
				break;
			}

			if ($row instanceof JEAPropertyInterface)
			{
				if ($row->save($this->bridge_code))
				{
					$this->created ++;
					$this->importedProperties[$ref] = true;
				}
				else
				{
					$msg = "Property can't be saved : ";

					foreach ($row->getErrors() as $error)
					{
						$msg .= " $error\n";
					}

					$this->log($msg, 'WARN');
					JError::raiseNotice(200, "A property cant'be saved. See logs for more infos");
				}
			}

			$this->imported ++;
			unset($properties[$ref]);
		}

		if ($this->persistance == true && ! empty($properties))
		{
			$this->setPersistentProperties($properties);
		}
		elseif ($this->persistance == true && empty($properties))
		{
			$this->cleanPersistentProperties();
		}

		if (empty($properties))
		{
			$msg = JText::sprintf('COM_JEA_IMPORT_END_MESSAGE', ucfirst(strtolower($this->bridge_code)));
			$this->log($msg);
		}
	}

	/**
	 * This method must be overriden
	 * (polymorphism)
	 *
	 * @return array &$properties an array of JEAPropertyInterface objects
	 */
	protected function &parse()
	{
		$properties = array();

		return $properties;
	}

	/**
	 * Check for properties stored in cache
	 *
	 * @return boolean
	 */
	public function hasPersistentProperties()
	{
		$cache = JFactory::getCache('jea_interface', 'output', 'file');
		$cache->setCaching(true);
		$properties = $cache->get('properties');

		if (is_string($properties))
		{
			return true;
		}

		return false;
	}

	/**
	 * Get properties stored in cache
	 *
	 * @return array
	 */
	public function getPersistentProperties()
	{
		$cache = JFactory::getCache('jea_interface', 'output', 'file');
		$cache->setCaching(true);

		$infos = $cache->get('infos');

		if (is_string($infos))
		{
			$infos = unserialize($infos);
			$this->importedProperties = $infos->importedProperties;
		}

		$properties = $cache->get('properties');

		if ($properties === false)
		{
			return array();
		}

		return unserialize($properties);
	}

	/**
	 * Store properties in cache
	 *
	 * @param   JEAPropertyInterface[]  $properties  An array of JEAPropertyInterface instances
	 *
	 * @return void
	 */
	public function setPersistentProperties($properties = array())
	{
		$cache = JFactory::getCache('jea_interface', 'output', 'file');
		$cache->setCaching(true);
		$cache->store(serialize($properties), 'properties');
		$infos = $cache->get('infos');

		if (! $infos)
		{
			$infos = new stdClass;
			$infos->total = $this->total;
			$infos->updated = $this->updated;
			$infos->removed = $this->removed;
			$infos->created = $this->created;
			$infos->imported = $this->imported;
			$infos->importedProperties = $this->importedProperties;
			$cache->store(serialize($infos), 'infos');
		}
		else
		{
			$infos = unserialize($infos);
			$infos->updated += $this->updated;
			$infos->removed += $this->removed;
			$infos->created += $this->created;
			$infos->imported += $this->imported;

			foreach ($this->importedProperties as $k => $v)
			{
				$infos->importedProperties[$k] = $v;
			}

			$cache->store(serialize($infos), 'infos');
		}

		// Update interface informations
		$this->total = $infos->total;
		$this->updated = $infos->updated;
		$this->removed = $infos->removed;
		$this->created = $infos->created;
		$this->imported = $infos->imported;
	}

	/**
	 * Remove properties stored in cache
	 *
	 * @return void
	 */
	public function cleanPersistentProperties()
	{
		$cache = JFactory::getCache('jea_interface', 'output', 'file');
		$cache->setCaching(true);
		$infos = $cache->get('infos');

		if (is_string($infos))
		{
			$infos = @unserialize($infos);

			// Update interface informations
			$this->total = $infos->total;
			$this->updated += $infos->updated;
			$this->removed += $infos->removed;
			$this->created += $infos->created;
			$this->imported += $infos->imported;
		}

		// Delete cache infos
		$cache->clean();
	}

	/**
	 * Remove JEA properties
	 *
	 * @param   array  $ids  An array of ids to remove
	 *
	 * @return boolean
	 */
	public function removeProperties($ids = array())
	{
		if (empty($ids))
		{
			return false;
		}

		$model = JModelLegacy::getInstance('Property', 'JeaModel');

		$flip = array_flip($ids);
		$model->delete($ids);
		$count = 0;

		foreach ($ids as $id)
		{
			if (! isset($flip[$id]))
			{
				break;
			}

			$count ++;
		}

		$this->removed = $count;

		return true;
	}

	/**
	 * Set the log file name
	 *
	 * @param   string  $fileName  The log file name
	 *
	 * @return  void
	 */
	public function setLogFileName ($fileName = '')
	{
		$this->log_file = $fileName;
	}

	/**
	 * Write a log message
	 *
	 * Status codes :
	 *
	 * EMERG = 0; // Emergency: system is unusable
	 * ALERT = 1; // Alert: action must be taken immediately
	 * CRIT = 2; // Critical: critical conditions
	 * ERR = 3; // Error: error conditions
	 * WARN = 4; // Warning: warning conditions
	 * NOTICE = 5; // Notice: normal but significant condition
	 * INFO = 6; // Informational: informational messages
	 * DEBUG = 7; // Debug: debug messages
	 *
	 * @param   string  $message  The log message
	 * @param   string  $status   See status codes above
	 *
	 * @return  void
	 */
	public function log ($message, $status = '')
	{
		jimport('joomla.log.log');

		// A category name
		$cat = strtolower($this->bridge_code);

		JLog::addLogger(
			// Sets file name
			array('text_file' => $this->log_file),
			// Sets all JLog messages to be set to the file
			JLog::ALL,
			// Chooses a category name
			$cat
		);

		$status = strtoupper($status);
		$levels = array(
				'EMERG' => JLog::EMERGENCY,
				'ALERT' => JLog::ALERT,
				'CRIT' => JLog::CRITICAL,
				'ERR' => JLog::ERROR,
				'WARN' => JLog::WARNING,
				'NOTICE' => JLog::NOTICE,
				'INFO' => JLog::INFO,
				'DEBUG' => JLog::DEBUG
		);

		if (isset($levels[$status]))
		{
			$status = $levels[$status];
		}
		else
		{
			$status = JLog::INFO;
		}

		JLog::add($message, $status, $cat);
	}

	/**
	 * Get logs
	 *
	 * @return string
	 */
	public function getLogsContent ()
	{
		$path = JFactory::getConfig()->get('log_path');
		$logFile = $path . '/' . $this->log_file;

		if (JFile::exists($logFile))
		{
			return file_get_contents($logFile);
		}

		return '';
	}

	/**
	 * Delete logs
	 *
	 * @return bool
	 */
	public function deleteLogs ()
	{
		$path = JFactory::getConfig()->get('log_path');
		$logFile = $path . '/' . $this->log_file;

		if (JFile::exists($logFile))
		{
			return JFile::delete($logFile);
		}

		return false;
	}
}
