<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2012 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die();

jimport('joomla.application.component.controlleradmin');

/**
 * Properties controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @since       1.0
 */
class JeaControllerProperties extends JControllerAdmin
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see JControllerLegacy::__construct()
	 */
	public function __construct ($config = array())
	{
		parent::__construct($config);

		$this->registerTask('unfeatured', 'featured');
	}

	/**
	 * Method to toggle the featured setting of a list of properties.
	 *
	 * @return  void
	 */
	public function featured()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$user = JFactory::getUser();
		$ids = JRequest::getVar('cid', array(), '', 'array');
		$values = array(
				'featured' => 1,
				'unfeatured' => 0
		);
		$task = $this->getTask();
		$value = JArrayHelper::getValue($values, $task, 0, 'int');

		// Access checks.
		foreach ($ids as $i => $id)
		{
			if (! $user->authorise('core.edit.state', 'com_jea.property.' . (int) $id))
			{
				// Prune items that you can't change.
				unset($ids[$i]);
				JError::raiseNotice(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
			}
		}

		if (empty($ids))
		{
			JError::raiseWarning(500, JText::_('JERROR_NO_ITEMS_SELECTED'));
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Publish the items.
			if (! $model->featured($ids, $value))
			{
				JError::raiseWarning(500, $model->getError());
			}
		}

		$this->setRedirect('index.php?option=com_jea&view=properties');
	}

	/**
	 * Method to copy a list of properties.
	 *
	 * @return  void
	 */
	public function copy()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$user = JFactory::getUser();
		$ids = JRequest::getVar('cid', array(), '', 'array');

		// Access checks.
		if (! $user->authorise('core.create'))
		{
			JError::raiseNotice(403, JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'));
		}
		elseif (empty($ids))
		{
			JError::raiseWarning(500, JText::_('JERROR_NO_ITEMS_SELECTED'));
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Publish the items.
			if (! $model->copy($ids))
			{
				JError::raiseWarning(500, $model->getError());
			}
		}

		$this->setRedirect('index.php?option=com_jea&view=properties');
	}

	/**
	 * Method to import properties.
	 *
	 * @return  void
	 */
	public function import()
	{
		$app = JFactory::getApplication();

		$model = $this->getModel('Import');
		$type = $app->input->get('type');

		$model->setState('import.type', $type);
		$model->setState('param.jea_version', $app->input->get('jea_version'));
		$model->setState('param.joomla_path', $app->input->get('joomla_path', '', 'string'));

		try
		{
			$model->import();
			$app->enqueueMessage(JText::sprintf('COM_JEA_PROPERTIES_FOUND_TOTAL', $model->total));
			$app->enqueueMessage(JText::sprintf('COM_JEA_PROPERTIES_UPDATED', $model->updated));
			$app->enqueueMessage(JText::sprintf('COM_JEA_PROPERTIES_CREATED', $model->created));
		}
		catch (Exception $e)
		{
			$this->setMessage($e->getMessage(), 'warning');
		}

		$this->setRedirect('index.php?option=com_jea&view=import&layout=' . $type);
	}

	/**
	 * Method to get a JeaModelProperty model object, loading it if required.
	 *
	 * @param   string  $name    The model name.
	 * @param   string  $prefix  The class prefix.
	 * @param   array   $config  Configuration array for model.
	 *
	 * @return  JeaModelProperty|boolean  Model object on success; otherwise false on failure.
	 *
	 * @see JControllerForm::getModel()
	 */
	public function getModel ($name = 'Property', $prefix = 'JeaModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}
}
