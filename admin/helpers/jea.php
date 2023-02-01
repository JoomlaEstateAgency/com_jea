<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2020 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\Helpers\Sidebar;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseDriver;

/**
 * Jea Helper class
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @since       2.0
 */
class JeaHelper
{
    /**
     * Configure the Linkbar.
     *
     * @param string $viewName The name of the active view.
     *
     * @return  void
     */
    public static function addSubmenu($viewName)
    {
        $db = Factory::getContainer()->get(DatabaseDriver::class);
        $query = $db->getQuery(true);
        $query->select('m.*')
            ->from('#__menu AS m')
            ->innerJoin('#__menu AS m2 ON m.parent_id = m2.id')
            ->where("m2.link='index.php?option=com_jea'")
            ->order('id ASC');

        $db->setQuery($query);
        $items = $db->loadObjectList();

        foreach ($items as $item) {
            $active = false;

            switch ($item->title) {
                case 'com_jea_properties':
                    $item->title = 'COM_JEA_PROPERTIES_MANAGEMENT';
                    break;
                case 'com_jea_features':
                    $item->title = 'COM_JEA_FEATURES_MANAGEMENT';
                    break;
            }

            $matches = array();

            if (preg_match('#&view=([a-z]+)#', $item->link, $matches)) {
                $active = $matches[1] == $viewName;
            }

            Sidebar::addEntry(Text::_($item->title), $item->link, $active);
        }

        Sidebar::addEntry(
            Text::_('COM_JEA_ABOUT'),
            'index.php?option=com_jea&view=about',
            $viewName == 'about'
        );
    }

    /**
     * Gets a list of actions that can be performed.
     *
     * @param int $propertyId The property ID.
     *
     * @return  Jobject
     */
    public static function getActions($propertyId = 0)
    {
        $user = Factory::getApplication()->getIdentity();
        $result = new JObject;

        if (empty($propertyId)) {
            $assetName = 'com_jea';
        } else {
            $assetName = 'com_jea.property.' . (int)$propertyId;
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
            $result->set($action, $user->authorise($action, $assetName));
        }

        return $result;
    }

    /**
     * Gets the list of tools icons.
     *
     * @return array  A button list
     */
    public static function getToolsIcons()
    {
        $db = Factory::getContainer()->get(DatabaseDriver::class);

        $query = $db->getQuery(true);
        $query->select(array('link', 'title AS text', 'icon AS image', 'access'));
        $query->from('#__jea_tools');
        $query->order('id ASC');
        $db->setQuery($query);
        $buttons = $db->loadAssocList();

        foreach ($buttons as &$button) {
            $button['text'] = Text::_($button['text']);

            if (!empty($button['access'])) {
                $button['access'] = json_decode($button['access']);
            }
        }

        return $buttons;
    }
}
