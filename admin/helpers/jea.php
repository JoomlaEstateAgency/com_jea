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

        JSubMenuHelper::addEntry(
            JText::_('COM_JEA_TOOLS'), 
            'index.php?option=com_jea&view=tools',
            $viewName == 'tools'
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
    
    /**
     * Gets the list of tools icons.
     *
     */
    public static function getToolsIcons()
    {
        $db = JFactory::getDbo();
        
        $query = $db->getQuery(true);
        $query->select(array('link', 'title AS text', 'icon AS image', 'access'));
        $query->from('#__jea_tools');
        $query->order('id ASC');
        $db->setQuery($query);
        $buttons = $db->loadAssocList();
        
        foreach ($buttons as &$button) {
            if (!empty($button['access'])) {
                $button['access'] = json_decode($button['access']);
            }
        }
        
        return $buttons;

        /*
        $buttons = array(
            array(
                'link' => JRoute::_('index.php?option=com_jea&view=import&layout=jea'),
                'image' => 'header/icon-48-config.png',
                'text' => JText::_('Import from JEA'),
                'access' => array('core.manage', 'com_jea', 'core.create', 'com_jea')
            ),

             array(
                'link' => JRoute::_('index.php?option=com_jea&view=import&layout=csv'),
                'image' => 'header/icon-48-config.png',
                'text' => JText::_('Import from CSV'),
                'access' => array('core.manage', 'com_jea', 'core.create', 'com_jea')
            ),
        );

        // Include buttons defined by published jea plugins
        JPluginHelper::importPlugin('jea');
        $app = JFactory::getApplication();
        $result = (array) $app->triggerEvent('onGetToolsIcons');

        foreach ($result as $response) {
            foreach ($response as $icon) {
                $default = array(
                    'link' => null,
                    'image' => 'header/icon-48-config.png',
                    'text' => null,
                    'access' => true
                );
                $icon = array_merge($default, $icon);
                if (!is_null($icon['link']) && !is_null($icon['text'])) {
                    $buttons[] = $icon;
                }
            }
        }
        */
        
    }
    
    public static function getFeatures()
    {
    	$xmlPath = JPATH_COMPONENT.'/models/forms/features/';
    	$xmlFiles = JFolder::files($xmlPath);
    
    	$featuresLocalized = array();
    	foreach ($xmlFiles as $key => $filename) {
    		if (preg_match('/^[0-9]{2}-([a-z]*).xml/', $filename, $matches)) {
    			$form = simplexml_load_file($xmlPath.DS.$filename);
    			// generate object
    			$feature = new stdClass();
    			$feature->id = $key;
    			$feature->name = $matches[1];
    			$feature->table = (string) $form['table'];
    			$feature->language = false;
    			// Check if this feature uses language
    			$lang = $form->xpath("//field[@name='language']");
    			if (!empty($lang)) {
    				$feature->language = true;
    			}
    			$featuresLocalized[$matches[1]] = $feature;
    
    		}
    	}
    	return $featuresLocalized;
    }
}
