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
        // TODO: implement
    }

    public function publish()
    {
        // TODO: implement
    }

    public function delete()
    {
        // TODO: implement
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

    protected function getRedirectToListAppend()
    {
        $tmpl = JRequest::getCmd('tmpl');
        $append = '&layout=manage';

        // Setup redirect info.
        if ($tmpl)
        {
            $append .= '&tmpl=' . $tmpl;
        }

        return $append;
    }
    
	

}

