<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2017 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\Registry\Registry;

jimport('joomla.log.log');
jimport('joomla.filesystem.file');

abstract class JeaGateway extends JEvent
{
    /**
     * A Registry object holding the parameters for the gateway
     *
     * @var    Registry
     */
    public $params = null;

    /**
     * The id of the gateway
     *
     * @var    string
     */
    public $id = null;

    /**
     * The provider of the gateway
     *
     * @var    string
     */
    public $provider = null;

    /**
     * The title of the gateway
     *
     * @var    string
     */
    public $title = null;

    /**
     * The gateway type
     *
     * @var    string
     */
    public $type = null;

    /**
     * The gateway log file
     * 
     * @var string
     */
    protected $_log_file = '';


    /**
     * Constructor
     *
     * @param   object  &$subject  The object to observe
     * @param   array   $config    An optional associative array of configuration settings.
     */
    public function __construct(&$subject, $config = array())
    {
        if (isset($config['params']) && $config['params'] instanceof Registry) {
            $this->params = $config['params'];
        }

        if (isset($config['id']))
        {
            $this->id = $config['id'];
        }

        if (isset($config['provider']))
        {
            $this->provider = $config['provider'];
        }
        
        if (isset($config['title']))
        {
            $this->title = $config['title'];
        }

        if (isset($config['type']))
        {
            $this->type = $config['type'];
        }

        $this->_log_file = $this->provider . '_' . $this->type . '_' . $this->id . '.php';

        parent::__construct($subject);
    }

    /**
     * Write a log message
     *
     * Status codes :
     * 
     * EMERG   = 0;  // Emergency: system is unusable
     * ALERT   = 1;  // Alert: action must be taken immediately
     * CRIT    = 2;  // Critical: critical conditions
     * ERR     = 3;  // Error: error conditions
     * WARN    = 4;  // Warning: warning conditions
     * NOTICE  = 5;  // Notice: normal but significant condition
     * INFO    = 6;  // Informational: informational messages
     * DEBUG   = 7;  // Debug: debug messages
     *
     * @param string $message
     * @param string $status see status codes above
     */
    public function log($message, $status='')
    {
        // A category name
        $cat = $this->provider;

        JLog::addLogger(
            array ('text_file' => $this->_log_file),
            JLog::ALL,
            $cat
        );

        $status = strtoupper($status);
        $levels = array(
            'EMERG'  => JLog::EMERGENCY,
            'ALERT'  => JLog::ALERT,
            'CRIT'   => JLog::CRITICAL,
            'ERR'    => JLog::ERROR,
            'WARN'   => JLog::WARNING,
            'NOTICE' => JLog::NOTICE,
            'INFO'   => JLog::INFO,
            'DEBUG'  => JLog::DEBUG
        );
    
        if (isset($levels[$status])) {
            $status = $levels[$status];
        } else {
            $status = JLog::INFO;
        }
    
        JLog::add($message, $status, $cat);
    }

    public function out($message = '')
    {
        $application = JFactory::getApplication();
        if ($application instanceof JApplicationCli) {
            /* @var JApplicationCli $application */
            $application->out($message);
        }
    }

    /**
     * Get logs
     *
     * @return string
     */
    public function getLogs()
    {
        $file = JFactory::getConfig()->get('log_path') . '/' . $this->_log_file;

        if (JFile::exists($file)) {
            return file_get_contents($file);
        }

        return '';
    }

    /**
     * Delete logs
     *
     * @return bool
     */
    public function deleteLogs()
    {
        $file = JFactory::getConfig()->get('log_path') . '/' . $this->_log_file;

        if (JFile::exists($file)) {
            return JFile::delete($file);
        }

        return false;
    }

}
