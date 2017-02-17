<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2016 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


/**
 * Gateways list controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 */
class JeaControllerGateways extends JControllerAdmin
{

    public function export()
    {
        $this->gatewaysExecute('export');
    }

    public function import()
    {
        $this->gatewaysExecute('import');
    }

    protected function gatewaysExecute($task)
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $application = JFactory::getApplication();
        $application->setHeader('Content-Type', 'text/plain', true);
        $application->sendHeaders();

        $interpreter = JFactory::getApplication()->input->getString('php_interpreter', 'php');

        if (preg_match('/^([a-zA-Z0-9-_.\/]+)/', $interpreter, $matches) !== false) {
            $interpreter = $matches[1];
        }

        if (strpos($interpreter, 'php') === false) {
            echo "PHP interpreter must contains 'php' in its name";
            exit();
        }

        $command = ($task == 'export' ?
        $interpreter . ' ' . JPATH_COMPONENT_ADMINISTRATOR . '/cli/gateways.php --export --basedir="' . JPATH_ROOT . '" --baseurl="' . JUri::root() . '"' :
        $interpreter . ' ' . JPATH_COMPONENT_ADMINISTRATOR . '/cli/gateways.php --import --basedir="' . JPATH_ROOT . '"');

        echo "> $command\n\n";

        $output = array();
        $return = 0;
        $lastLine = exec($command, $output, $return);

        if ($return > 0) {
            echo "Error\n";
        }

        foreach ($output as $line) {
            echo "$line\n";
        }

        exit();
    }
    
    /* (non-PHPdoc)
     * @see JController::getModel()
     */
    public function getModel($name = 'Gateway', $prefix = 'JeaModel', $config = array())
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

}

