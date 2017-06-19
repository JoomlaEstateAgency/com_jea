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

/**
 * Gateways controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @since       3.4
 */
class JeaControllerGateways extends JControllerAdmin
{
	/**
	 * Ask the gateways to execute their export handlers
	 *
	 * @return  void
	 */
	public function export()
	{
		$this->gatewaysExecute('export');
	}

	/**
	 * Ask the gateways to execute their import handlers
	 *
	 * @return  void
	 */
	public function import()
	{
		$this->gatewaysExecute('import');
	}

	/**
	 * Ask the gateways to execute their action handlers
	 *
	 * @param   string  $task  Action to execute
	 *
	 * @return  void
	 */
	protected function gatewaysExecute($task)
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$application = JFactory::getApplication();
		$application->setHeader('Content-Type', 'text/plain', true);
		$application->sendHeaders();

		$interpreter = JFactory::getApplication()->input->getString('php_interpreter', 'php');

		if (preg_match('/^([a-zA-Z0-9-_.\/]+)/', $interpreter, $matches) !== false)
		{
			$interpreter = $matches[1];
		}

		if (strpos($interpreter, 'php') === false)
		{
			echo "PHP interpreter must contains 'php' in its name";
			$application->close();
		}

		$command = ($task == 'export' ? $interpreter . ' '
				. JPATH_COMPONENT_ADMINISTRATOR . '/cli/gateways.php --export --basedir="' . JPATH_ROOT . '" --baseurl="' . JUri::root() . '"' :
				$interpreter . ' ' . JPATH_COMPONENT_ADMINISTRATOR . '/cli/gateways.php --import --basedir="' . JPATH_ROOT . '"');

		echo "> $command\n\n";

		$output = array();
		$return = 0;

		exec($command, $output, $return);

		if ($return > 0)
		{
			echo "Error\n";
		}

		foreach ($output as $line)
		{
			echo "$line\n";
		}

		$application->close();
	}

	/**
	 * Method to get a JeaModelGateway model object, loading it if required.
	 *
	 * @param   string  $name    The model name.
	 * @param   string  $prefix  The class prefix.
	 * @param   array   $config  Configuration array for model.
	 *
	 * @return  JeaModelGateway|boolean  Model object on success; otherwise false on failure.
	 *
	 * @see JControllerForm::getModel()
	 */
	public function getModel ($name = 'Gateway', $prefix = 'JeaModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}
}
