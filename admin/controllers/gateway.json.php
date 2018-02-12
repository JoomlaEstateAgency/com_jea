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

require_once JPATH_COMPONENT_ADMINISTRATOR . '/gateways/dispatcher.php';

/**
 * Gateway controller class for AJAX requests.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @since       3.4
 */
class JeaControllerGateway extends JControllerLegacy
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see JControllerLegacy::__construct()
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		set_exception_handler(array('JeaControllerGateway', 'error'));
	}

	/**
	 * Custom Exception Handler
	 * displaying Exception in Json format
	 *
	 * @param   Exception  $e  An error exception
	 *
	 * @return  void
	 */
	public static function error($e)
	{
		$error = array(
			'error' => $e->getmessage(),
			'errorCode' => $e->getCode(),
			'trace' => $e->getTraceAsString(),
		);

		echo json_encode($error);
	}

	/**
	 * Ask the gateway to execute export
	 *
	 * @return  void
	 */
	public function export()
	{
		$this->gatewayExecute('export');
	}

	/**
	 * Ask the gateway to execute import
	 *
	 * @return  void
	 */
	public function import()
	{
		$this->gatewayExecute('import');
	}

	/**
	 * Ask the gateway to execute action
	 *
	 * @param   string  $task  Action to execute
	 *
	 * @return  void
	 */
	protected function gatewayExecute($task)
	{
		$model = $this->getModel('Gateway', 'JeaModel');
		$gateway = $model->getItem();
		$dispatcher = GatewaysEventDispatcher::getInstance();
		$dispatcher->loadGateway($gateway);

		if ($task == 'import')
		{
			$dispatcher->trigger('activatePersistance');
		}

		$responses = $dispatcher->trigger($task);

		echo isset($responses[0]) ? json_encode($responses[0]) : '{}';
	}
}
