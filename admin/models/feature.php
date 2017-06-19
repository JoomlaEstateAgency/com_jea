<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2017 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');

require_once JPATH_COMPONENT . '/tables/features.php';

/**
 * Feature model class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @see         JModelAdmin
 *
 * @since       2.0
 */
class JeaModelFeature extends JModelAdmin
{
	/**
	 * Overrides parent method
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm|boolean  A JForm object on success, false on failure
	 *
	 * @see JModelForm::getForm()
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$feature = $this->getState('feature.name');
		$formFile = $this->getState('feature.form');
		$form = $this->loadForm('com_jea.feature.' . $feature, $formFile, array('control' => 'jform', 'load_data' => $loadData));

		$form->setFieldAttribute('ordering', 'filter', 'unset');

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Overrides parent method.
	 *
	 * @return  void
	 *
	 * @see JModelAdmin::populateState()
	 */
	public function populateState ()
	{
		/*
		 * Be careful to not call parent::populateState() because this will cause an
		 * infinite call of this method in JeaModelFeature::getTable()
		 */
		$input = JFactory::getApplication()->input;
		$feature = $input->getCmd('feature');
		$this->setState('feature.name', $feature);

		// Retrieve the feature table params
		$xmlPath = JPATH_COMPONENT . '/models/forms/features/';
		$xmlFiles = JFolder::files($xmlPath);

		foreach ($xmlFiles as $filename)
		{
			if (preg_match('/^[0-9]{2}-([a-z]*).xml/', $filename, $matches))
			{
				if ($feature == $matches[1])
				{
					$form = simplexml_load_file($xmlPath . '/' . $filename);
					$this->setState('feature.table', (string) $form['table']);
					$this->setState('feature.form', $xmlPath . $filename);
				}
			}
		}

		// Get the pk of the record from the request.
		$pk = $input->getInt('id');
		$this->setState($this->getName() . '.id', $pk);
	}

	/**
	 * Overrides parent method
	 *
	 * @return  array  The default data is an empty array.
	 *
	 * @see JModelForm::loadFormData()
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data. See JControllerForm::save()
		$data = JFactory::getApplication()->getUserState('com_jea.edit.feature.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Overrides parent method
	 *
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 *
	 * @return  JTable  A JTable object
	 *
	 * @see JModel::getTable()
	 */
	public function getTable($name = '', $prefix = 'Table', $options = array())
	{
		static $table;

		if ($table === null)
		{
			$tableName = $this->getState('feature.table');
			$db = JFactory::getDbo();
			$table = new FeaturesFactory($db->escape($tableName), 'id', $db);
		}

		return $table;
	}
}
