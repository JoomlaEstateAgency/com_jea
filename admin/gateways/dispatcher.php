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

use Joomla\Registry\Registry;

jimport('joomla.filesystem.file');

/**
 * Custom Event dispatcher class for JEA gateways
 *
 * @since  3.4
 */
class GatewaysEventDispatcher extends JEventDispatcher
{
	/**
	 * Stores the singleton instance of the dispatcher.
	 *
	 * @var GatewaysEventDispatcher
	 */
	protected static $g_instance = null;

	/**
	 * Get unique Instance of GatewaysEventDispatcher
	 *
	 * @return GatewaysEventDispatcher
	 */
	public static function getInstance()
	{
		if (self::$g_instance === null)
		{
			self::$g_instance = new static;
		}

		return self::$g_instance;
	}

	/**
	 * Attach an observer object
	 *
	 * @param   object  $observer  An observer object to attach
	 *
	 * @return  void
	 */
	public function attach($observer)
	{
		if (! ($observer instanceof JeaGateway))
		{
			return;
		}

		/*
		 * The main difference with the parent method
		 * is to attach several instances of the same
		 * class.
		 */

		$this->_observers[] = $observer;
		$methods = get_class_methods($observer);

		end($this->_observers);
		$key = key($this->_observers);

		foreach ($methods as $method)
		{
			$method = strtolower($method);

			if (! isset($this->_methods[$method]))
			{
				$this->_methods[$method] = array();
			}

			$this->_methods[$method][] = $key;
		}
	}

	/**
	 * Triggers an event by dispatching arguments to all observers that handle
	 * the event and returning their return values.
	 *
	 * @param   string  $event  The event to trigger.
	 * @param   array   $args   An array of arguments.
	 *
	 * @return  array  An array of results from each function call.
	 */
	public function trigger($event, $args = array())
	{
		$result = array();
		$args = (array) $args;
		$event = strtolower($event);

		// Check if any gateways are attached to the event.
		if (!isset($this->_methods[$event]) || empty($this->_methods[$event]))
		{
			// No gateways associated to the event!
			return $result;
		}

		// Loop through all gateways having a method matching our event
		foreach ($this->_methods[$event] as $key)
		{
			// Check if the gateway is present.
			if (!isset($this->_observers[$key]))
			{
				continue;
			}

			if ($this->_observers[$key] instanceof JeaGateway)
			{
				try
				{
					$args['event'] = $event;
					$value = $this->_observers[$key]->update($args);
				}
				catch (Exception $e)
				{
					$application = JFactory::getApplication();
					$gateway = $this->_observers[$key];
					$gateway->log($e->getMessage(), 'err');

					if ($application instanceof JApplicationCli)
					{
						/*
						 * In CLI mode, output the error but don't stop the
						 * execution loop of other gateways
						 */

						$gateway->out('Error [' . $gateway->title . '] : ' . $e->getMessage());
					}
					else
					{
						/*
						 * In AJAX mode, only one gateway is loaded per request,
						 * so we can stop the loop.
						 * Exception will be catched later in a custom Exception handler
						 */
						throw $e;
					}
				}
			}

			if (isset($value))
			{
				$result[] = $value;
			}
		}

		return $result;
	}

	/**
	 * Load JEA gateways
	 *
	 * @param   string  $type  If set, must be 'export' or 'import'
	 *
	 * @return  void
	 */
	public function loadGateways($type = null)
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__jea_gateways');
		$query->where('published=1');

		if (! empty($type))
		{
			$query->where('type=' . $db->Quote($type));
		}

		$query->order('ordering ASC');
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		foreach ($rows as $row)
		{
			$this->loadGateway($row);
		}
	}

	/**
	 * Load one JEA gateway
	 *
	 * @param   $object  $gateway  he row DB gateway
	 *
	 * @return  JeaGateway
	 *
	 * @throws  Exception if gateway cannot be loaded
	 */
	public function loadGateway($gateway)
	{
		$gatewayFile = JPATH_ADMINISTRATOR . '/components/com_jea/gateways/providers/' . $gateway->provider . '/' . $gateway->type . '.php';

		if (JFile::exists($gatewayFile))
		{
			require_once $gatewayFile;
			$className = 'JeaGateway' . ucfirst($gateway->type) . ucfirst($gateway->provider);

			if (class_exists($className))
			{
				$dispatcher = static::getInstance();

				$config = array(
						'id' => $gateway->id,
						'provider' => $gateway->provider,
						'title' => $gateway->title,
						'type' => $gateway->type,
						'params' => new Registry($gateway->params)
				);

				return new $className($dispatcher, $config);
			}
			else
			{
				throw new Exception('Gateway class not found : ' . $className);
			}
		}
		else
		{
			throw new Exception('Gateway file not found : ' . $gatewayFile);
		}
	}
}
