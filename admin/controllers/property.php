<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @version     $Id$
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2012 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controllerform');

// TODO: implement access right management
// TODO: implement publish down date management
/**
 * Property controller class.
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 */
class JeaControllerProperty extends JControllerForm
{

    /* (non-PHPdoc)
     * @see JControllerForm::allowEdit()
     */
    protected function allowEdit($data = array(), $key = 'id')
    {
        $user = JFactory::getUser();
        $assetName = isset($data[$key]) ? 'com_jea.property.' . (int) $data[$key] : 'com_jea';
        return $user->authorise('core.edit', $assetName) ||
               $user->authorise('core.edit.own', $assetName);
    }
}
