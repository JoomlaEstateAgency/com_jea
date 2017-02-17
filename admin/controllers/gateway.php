<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
*
* @package     Joomla.Administrator
* @subpackage  com_jea
* @copyright   Copyright (C) 2008 - 2017 PHILIP Sylvain. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controllerform');

require_once JPATH_COMPONENT_ADMINISTRATOR . '/gateways/dispatcher.php';

/**
 * Export controller class.
 * @package     Joomla.Administrator
 * @subpackage  com_jea
*/
class JeaControllerGateway extends JControllerForm
{
    /**
     * @param array $config
     */
    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->view_item .= '&type=' . JFactory::getApplication()->input->getCmd('type');
    }

    public function getLogs()
    {
        $gateway = $this->getGateway();

        /* @var JApplicationWeb  $application */
        $application = JFactory::getApplication();

        $application->setHeader('Content-Type', 'text/plain', true);
        $application->sendHeaders();
        echo $gateway->getLogs();

        exit();
    }

    public function deleteLogs()
    {
        $gateway = $this->getGateway();
        $gateway->deleteLogs();

        $this->getLogs();
    }
    
    public function downloadLogs()
    {
        JFactory::getApplication()->setHeader('Content-Disposition', 'attachment; filename="logs.txt"');
        $this->getLogs();
    }

    /**
     * @return JeaGateway
     */
    protected function getGateway()
    {
        $model      = $this->getModel();
        $item       = $model->getItem();
        $dispatcher = GatewaysEventDispatcher::getInstance();
        return $dispatcher->loadGateway($item);
    }

    public function getModel($name = 'Gateway', $prefix = 'JeaModel', $config = array('ignore_request' => false))
    {
        return parent::getModel($name, $prefix, $config);
    }
}
