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

jimport('joomla.application.component.modeladmin');

require_once JPATH_COMPONENT.DS.'tables'.DS.'features.php';

/**
 * Feature model class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 */
class JeaModelFeature extends JModelAdmin
{

    /* (non-PHPdoc)
     * @see JModelForm::getForm()
     */
    public function getForm($data = array(), $loadData = true)
    {
        $feature  = $this->getState('feature.name');
        $formFile = $this->getState('feature.form');
        $form     = $this->loadForm('com_jea.feature.'.$feature , $formFile, array('control' => 'jform', 'load_data' => $loadData));

        $form->setFieldAttribute('ordering', 'filter', 'unset');

        if (empty($form)) {
            return false;
        }

        return $form;
    }


    /* (non-PHPdoc)
     * @see JModelAdmin::populateState()
     */
    public function populateState()
    {
        // Be careful to not call parent::populateState() because this will cause an 
        // infinite call of this method in JeaModelFeature::getTable()

        $feature = JRequest::getCmd('feature');
        $this->setState('feature.name', $feature);

        // Retrieve the feature table params
        $xmlPath = JPATH_COMPONENT.'/models/forms/features/';
        $xmlFiles = JFolder::files($xmlPath);

        foreach ($xmlFiles as $filename) {
            if (preg_match('/^[0-9]{2}-([a-z]*).xml/', $filename, $matches)) {
                if ($feature == $matches[1]) {
                    $form = simplexml_load_file($xmlPath.DS.$filename);
                    $this->setState('feature.table', (string) $form['table']);
                    $this->setState('feature.form', $xmlPath.$filename);
                }
            }
        }

        // Get the pk of the record from the request.
        $pk = JRequest::getInt('id');
        $this->setState($this->getName() . '.id', $pk);

    }


    /* (non-PHPdoc)
     * @see JModelForm::loadFormData()
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data. See JControllerForm::save()
        $data = JFactory::getApplication()->getUserState('com_jea.edit.feature.data', array());

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }


    /* (non-PHPdoc)
     * @see JModel::getTable()
     */
    public function getTable($name = 'properties', $prefix = 'Table', $options = array())
    {
        static $table;

        if ($table === null) {
            $tableName = $this->getState('feature.table');
            $db = JFactory::getDbo();
            $table = new FeaturesFactory($db->escape($tableName), 'id', $db);
        }

        return $table;
    }
}


