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


/**
 * Feature controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 */
class JeaControllerFeature extends JControllerForm
{
    /**
     * The URL view list variable.*
     * @var    string
     */
    protected $view_list = 'featurelist';


    /* (non-PHPdoc)
     * @see JControllerForm::getRedirectToItemAppend()
     */
    protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
    {
        $feature = JRequest::getCmd('feature');
        $append = parent::getRedirectToItemAppend($recordId, $urlVar);

        if ($feature) {
            $append .= '&feature=' . $feature;
        }

        return $append;
    }


    /* (non-PHPdoc)
     * @see JControllerForm::getModel()
     */
    public function getModel($name = 'Feature', $prefix = 'JeaModel', $config = array())
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

}

