<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2017 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
$options = getopt('', array('basedir:', 'baseurl:', 'import', 'export'));

if (! isset($options['import']) && ! isset($options['export']))
{
	echo "--import or --export options must be specified.";
	exit(1);
}

if (! isset($options['basedir']))
{
	echo "--basedir option must be set.\n";
	exit(1);
}

if (! is_dir($options['basedir']))
{
	echo "--basedir not found.";
	exit(1);
}

if (isset($options['export']) && ! isset($options['baseurl']))
{
	echo "--baseurl option must be set.\n";
	exit(1);
}

define('JPATH_BASE', $options['basedir']);
define('BASE_URL', isset($options['baseurl'])? $options['baseurl'] : '');
define('_JEXEC', 1);

require_once JPATH_BASE . '/includes/defines.php';
require_once JPATH_LIBRARIES . '/import.legacy.php';
require_once JPATH_LIBRARIES . '/cms.php';

define('JPATH_COMPONENT', JPATH_BASE . '/administrator/components/com_jea');
define('JPATH_COMPONENT_ADMINISTRATOR', JPATH_COMPONENT);

require_once JPATH_COMPONENT_ADMINISTRATOR . '/gateways/dispatcher.php';

// Configure error reporting to maximum for CLI output.
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * A command line cron job to execute gateways import or export tasks
 *
 * @since  3.4
 */
class JeaGateways extends JApplicationCli
{
	/**
	 * Entry point for CLI script
	 *
	 * @return  void
	 */
	public function doExecute()
	{
		JFactory::$application = $this;

		JFactory::getLanguage()->load('com_jea', JPATH_COMPONENT, 'fr-FR', false, false);

		$task = $this->input->getBool('import') ? 'import' : 'export';

		$dispatcher = GatewaysEventDispatcher::getInstance();
		$dispatcher->loadGateways();
		$dispatcher->trigger($task);
	}
}

JApplicationCli::getInstance('JeaGateways')->execute();
