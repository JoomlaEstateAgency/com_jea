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

jimport('joomla.application.component.controllerform');

/**
 * Export controller class.
 * @package     Joomla.Administrator
 * @subpackage  com_jea
*/
class JeaControllerExport extends JControllerForm
{
    public function getModel($name = 'Export', $prefix = 'JeaModel', $config = array('ignore_request' => false))
    {
        return parent::getModel($name, $prefix, $config);
    }
}
