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
class JeaControllerGateway extends JControllerLegacy
{

    public function __construct($config = array())
    {
        parent::__construct($config);
        set_exception_handler(array('JeaControllerGateway', 'error'));
    }

    /**
     * Custom Exception Handler
     * displaying Exception in Json format
     * 
     * @param Exception $e
     */
    public static function error($e)
    {
        $error = array(
            'error' => $e->getmessage(),
            'errorCode' => $e->getCode(),
        );

        echo json_encode($error);
    }

    public function export()
    {
        $this->gatewayExecute('export');
    }

    public function import()
    {
        $this->gatewayExecute('import');
    }

    protected function gatewayExecute($task)
    {
        $model = $this->getModel();
        $gateway = $model->getItem();
        $dispatcher = GatewaysEventDispatcher::getInstance();
        $dispatcher->loadGateway($gateway);
        
        if ($task == 'import') {
            $dispatcher->trigger('activatePersistance');
        }
        
        $responses = $dispatcher->trigger($task);
        echo isset($responses[0]) ? json_encode($responses[0]) : '{}';
    }

    public function getModel($name = 'Gateway', $prefix = 'JeaModel', $config = array('ignore_request' => false))
    {
        return parent::getModel($name, $prefix, $config);
    }
}
