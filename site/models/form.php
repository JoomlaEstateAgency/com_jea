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

jimport('joomla.application.component.model');

// Base this model on the backend version.
require_once JPATH_COMPONENT_ADMINISTRATOR.'/models/property.php';

JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.'/tables');

class JeaModelForm extends JeaModelProperty
{
    /**
     * The model (base) name
     * should be the same as parent
     *
     * @var string
     */
    protected $name = 'property';

    /* (non-PHPdoc)
     * @see JeaModelProperty::getForm()
     */
    public function getForm($data = array(), $loadData = true)
    {
        JForm::addFormPath(JPATH_COMPONENT_ADMINISTRATOR . '/models/forms');
        JForm::addFieldPath(JPATH_COMPONENT_ADMINISTRATOR . '/models/fields');

        $form = parent::getForm($data, $loadData);

        return $form;
    }
}
