<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @version     $Id$
 * @package     Joomla.Site
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2012 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controllerform');

/**
 * Property controller class.
 * @package     Joomla.Site
 * @subpackage  com_jea
 */
class JeaControllerProperty extends JControllerForm
{
    /**
     * The URL view item variable.
     *
     * @var    string
     */
    protected $view_item = 'form';

    /**
     * The URL view list variable.
     *
     * @var    string
     */
    protected $view_list = 'properties';

    /* (non-PHPdoc)
     * @see JControllerForm::allowAdd()
     */
    protected function allowAdd($data = array())
    {
        $user = JFactory::getUser();
        if (!$user->authorise('core.create', 'com_jea')) {
            $app = JFactory::getApplication();
            $uri = JFactory::getURI();
            $return = base64_encode($uri);
            if ($user->get('id')) {
                $this->setMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'warning');
            } else {
                $this->setMessage(JText::_('JGLOBAL_YOU_MUST_LOGIN_FIRST'));
            }
            // Save the data in the session.
            $app->setUserState('com_jea.edit.property.data', $data);
            $this->setRedirect(JRoute::_('index.php?option=com_users&view=login&return='. $return, false));
            return $this->redirect();
        }
        return true;
    }


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

    public function unpublish()
    {
        $this->publish(0);
    }

    public function publish($action=1)
    {
        $id = JFactory::getApplication()->input->get('id', 0, 'int');
        $this->getModel()->publish($id, $action);
        $this->setRedirect(
        JRoute::_('index.php?option=com_jea&view=properties'
        . $this->getRedirectToListAppend(), false)
        );
    }

    public function delete()
    {
        $id = JFactory::getApplication()->input->get('id', 0, 'int');
        if ($this->getModel()->delete($id)) {
            $this->setMessage(JText::_('COM_JEA_SUCCESSFULLY_REMOVED_PROPERTY'));
        }
        $this->setRedirect(
        JRoute::_('index.php?option=com_jea&view=properties'
        . $this->getRedirectToListAppend(), false)
        );
    }

    public function getModel($name = 'form', $prefix = '', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    /* (non-PHPdoc)
     * @see JControllerForm::getRedirectToItemAppend()
     */
    protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
    {
        $tmpl = JRequest::getCmd('tmpl');
        $append = '&layout=edit';

        // Setup redirect info.
        if ($tmpl)
        {
            $append .= '&tmpl=' . $tmpl;
        }

        if ($recordId)
        {
            $append .= '&' . $urlVar . '=' . $recordId;
        }

        return $append;
    }

    /* (non-PHPdoc)
     * @see JControllerForm::getRedirectToListAppend()
     */
    protected function getRedirectToListAppend()
    {
        $tmpl = JRequest::getCmd('tmpl');
        $append = '&layout=manage';

        // Try to redirect to the manage menu item if found
        $app  = JFactory::getApplication();
        $menu = $app->getMenu();
        $activeItem = $menu->getActive();

        if (isset($activeItem->query['layout']) && $activeItem->query['layout'] !='manage' ) {
            $items = $menu->getItems('component', 'com_jea');
            foreach ($items as $item) {
                $layout = isset($item->query['layout']) ? $item->query['layout'] : '';
                if ($layout == 'manage') {
                    $append .= '&Itemid=' . $item->id;
                }
            }
        }


        // Setup redirect info.
        if ($tmpl)
        {
            $append .= '&tmpl=' . $tmpl;
        }

        return $append;
    }

}

