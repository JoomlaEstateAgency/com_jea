<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2017 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
require_once JPATH_COMPONENT_ADMINISTRATOR . '/gateways/gateway.php';

/**
 * The base class for import gateways
 *
 * @since  3.4
 */
abstract class JeaGatewayImport extends JeaGateway
{
	/**
	 * Delete Jea properties which are not included in the import set
	 *
	 * @var boolean
	 */
	protected $autoDeletion = true;

	/**
	 * Remember the last import
	 *
	 * @var boolean
	 */
	protected $persistance = false;

	/**
	 * The number of properties to import.
	 * Used to split the import in several requests (AJAX)
	 *
	 * @var integer
	 */
	protected $propertiesPerStep = 0;

	/**
	 * The number of properties created during import
	 *
	 * @var integer
	 */
	protected $created = 0;

	/**
	 * The number of properties updated during import
	 *
	 * @var integer
	 */
	protected $updated = 0;

	/**
	 * The number of properties created or imported during import
	 *
	 * @var integer
	 */
	protected $imported = 0;

	/**
	 * The number of properties removed during import
	 *
	 * @var integer
	 */
	protected $removed = 0;

	/**
	 * The number of properties that should be imported
	 *
	 * @var integer
	 */
	protected $total = 0;

	/**
	 * Array of properties already imported
	 *
	 * @var array();
	 */
	protected $importedProperties = array();

	/**
	 * Constructor
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An optional associative array of configuration settings.
	 */
	public function __construct (&$subject, $config = array())
	{
		parent::__construct($subject, $config);
		$this->autoDeletion = $this->params->get('auto_deletion', true);
	}

	/**
	 * Remember the last import
	 *
	 * @return void
	 */
	public function activatePersistance()
	{
		$this->persistance = true;
		$this->propertiesPerStep = $this->params->get('properties_per_step', 5);
	}

	/**
	 * The import handler method
	 *
	 * @return array the import summary
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
			return $this->getSummary();
		}

		$ids_to_remove = array();

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')->from('#__jea_properties');

		if (! empty($this->provider))
		{
			$query->where('provider=' . $db->quote($this->provider));
		}

		$db->setQuery($query);
		$existingProperties = $db->loadObjectList();

		foreach ($existingProperties as $row)
		{
			if (isset($this->importedProperties[$row->ref]))
			{
				// Property already been imported
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
					if ($properties[$row->ref]->save($this->provider, $row->id))
					{
						$this->updated ++;
					}
				}

				$this->imported ++;
				$this->importedProperties[$row->ref] = true;
				unset($properties[$row->ref]);
			}
			elseif ($this->autoDeletion === true)
			{
				// Property not in the $imported_properties
				// So we can delete it if the autodeletion option is set to true
				$ids_to_remove[] = $row->id;
			}
		}

		if ($this->autoDeletion === true)
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
				if ($row->save($this->provider))
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

					$msg .= print_r($row->getProperties(), true);
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
			$msg = JText::sprintf('COM_JEA_IMPORT_END_MESSAGE', $this->title);
			$this->out($msg);
		}

		return $this->getSummary();
	}

	/**
	 * Return import summary
	 *
	 * @return number[]
	 */
	protected function getSummary()
	{
		return array(
				'total' => $this->total,
				'created' => $this->created,
				'updated' => $this->updated,
				'imported' => $this->imported,
				'removed' => $this->removed
		);
	}

	/**
	 * The gateway parser method.
	 * This method must be overriden in child class
	 *
	 * @return JEAPropertyInterface[]
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
	protected function hasPersistentProperties()
	{
		$cache = JFactory::getCache('jea_import', 'output', 'file');
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
	protected function getPersistentProperties()
	{
		$cache = JFactory::getCache('jea_import', 'output', 'file');
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
	protected function setPersistentProperties($properties = array())
	{
		$cache = JFactory::getCache('jea_import', 'output', 'file');
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
	protected function cleanPersistentProperties()
	{
		$cache = JFactory::getCache('jea_import', 'output', 'file');
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
	protected function removeProperties($ids = array())
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
}
