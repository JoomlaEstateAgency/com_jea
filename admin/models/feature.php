<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @version     $Id: property.php 258 2012-02-20 00:54:35Z ilhooq $
 * @package		Jea.admin
 * @copyright	Copyright (C) 2008 PHILIP Sylvain. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla Estate Agency is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses.
 *
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.modeladmin');

require_once JPATH_COMPONENT.DS.'tables'.DS.'features.php';

class JeaModelFeature extends JModelAdmin
{
    /**
     * Method to get the record form.
     *
     * @param	array	$data		Data for the form.
     * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
     *
     * @return	mixed	A JForm object on success, false on failure
     * @since	1.6
     */
    public function getForm($data = array(), $loadData = true)
    {
        
        $feature = $this->getState('feature.name');
        $formFile = $this->getState('feature.form');
        $form = $this->loadForm('com_jea.feature.'.$feature , $formFile, array('control' => 'jform', 'load_data' => $loadData));
        
        $form->setFieldAttribute('ordering', 'filter', 'unset');

        if (empty($form)) {
            return false;
        }

        return $form;
    }
    
    
    public function populateState()
    {
        $feature = JRequest::getCmd('feature');
        $this->setState('feature.name', $feature);
        
         // Retrieve the feature table params
        $xmlPath = JPATH_COMPONENT.'/models/forms/features/';
        $xmlFiles = JFolder::files($xmlPath);
        
        foreach($xmlFiles as $filename) {
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
    

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return	mixed	The data for the form.
     * @since	1.6
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

	
	/**
	 * Proxy for getTable.
	 *
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 *
	 * @return  JTable  A JTable object
	 *
	 */
	public function getTable($name = 'properties', $prefix = 'Table', $options = array())
	{
		// return parent::getTable($name, $prefix, $options);
		static $table;
		if ($table === null) {
		    $tableName = $this->getState('feature.table');
	        $db = JFactory::getDbo();
            $table = new FeaturesFactory($db->escape($tableName), 'id', $db);
		}
		
		return $table;
	}
     
}


