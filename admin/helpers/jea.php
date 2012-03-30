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
defined('_JEXEC') or die;

/**
 * @package		Joomla.Administrator
 * @subpackage	com_jea
 */

class JeaHelper
{

    /**
     * Configure the Linkbar.
     *
     * @param   string  $viewName  The name of the active view.
     *
     * @return  void
     */
    public static function addSubmenu($viewName)
    {
        JSubMenuHelper::addEntry(
            JText::_('COM_JEA_PROPERTIES_MANAGEMENT'), 
            'index.php?option=com_jea&view=properties',
            $viewName == 'properties'
        );
        JSubMenuHelper::addEntry(
            JText::_('COM_JEA_FEATURES_MANAGEMENT'), 
            'index.php?option=com_jea&view=features',
            $viewName == 'features'
        );
    }

    /**
     * Gets a list of the actions that can be performed.
     *
     * @param  int    The property ID.
     * @return  JObject
     */
    public static function getActions($propertyId = 0)
    {
        $user   = JFactory::getUser();
        $result = new JObject;

        if (empty($propertyId)) {
            $assetName = 'com_jea';
        }  else {
            $assetName = 'com_jea.property.'.(int) $propertyId;
        }

        $actions = array(
            'core.admin',
            'core.manage',
            'core.create',
            'core.edit',
            'core.edit.own',
            'core.edit.state',
            'core.delete'
        );

        foreach ($actions as $action) {
            $result->set($action,	$user->authorise($action, $assetName));
        }

        return $result;
    }
}
