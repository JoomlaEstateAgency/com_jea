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
 * Gateway controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @since       3.4
 */
class JeaControllerGateway extends JControllerForm
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see JControllerLegacy::__construct()
	 */
	public function __construct ($config = array())
	{
		parent::__construct($config);
		$this->view_item .= '&type=' . JFactory::getApplication()->input->getCmd('type');
	}

	/**
	 * Output current gateway logs
	 *
	 * @return void
	 */
	public function getLogs()
	{
		$gateway = $this->getGateway();

		/* @var JApplicationWeb  $application */
		$application = JFactory::getApplication();

		$application->setHeader('Content-Type', 'text/plain', true);
		$application->sendHeaders();
		echo $gateway->getLogs();

		$application->close();
	}

	/**
	 * Delete current gateway logs
	 *
	 * @return void
	 */
	public function deleteLogs()
	{
		$gateway = $this->getGateway();
		$gateway->deleteLogs();

		$this->getLogs();
	}

	/**
	 * Serve current gateway logs
	 *
	 * @return void
	 */
	public function downloadLogs()
	{
		JFactory::getApplication()->setHeader('Content-Disposition', 'attachment; filename="logs.txt"');
		$this->getLogs();
	}

	/**
	 * Return the current gateway
	 *
	 * @return JeaGateway
	 */
	protected function getGateway()
	{
		$model = $this->getModel('Gateway', 'JeaModel', array('ignore_request' => false));
		$item = $model->getItem();
		$dispatcher = GatewaysEventDispatcher::getInstance();

		return $dispatcher->loadGateway($item);
	}
}
