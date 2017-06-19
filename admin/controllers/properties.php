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

use Joomla\Utilities\ArrayHelper;

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
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$user = JFactory::getUser();
		$ids = $this->input->get('cid', array(), 'array');
		$values = array(
				'featured' => 1,
				'unfeatured' => 0
		);
		$task = $this->getTask();
		$value = ArrayHelper::getValue($values, $task, 0, 'int');

		// Access checks.
		foreach ($ids as $i => $id)
		{
			if (!$user->authorise('core.edit.state', 'com_jea.property.' . (int) $id))
			{
				// Prune items that you can't change.
				unset($ids[$i]);
				$this->setMessage(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), 'warning');
			}
		}

		if (empty($ids))
		{
			$this->setMessage(JText::_('JERROR_NO_ITEMS_SELECTED'), 'warning');
		}
		else
		{
			$model = $this->getModel();

			try
			{
				$model->featured($ids, $value);
			}
			catch (\Exception $e)
			{
				$this->setMessage($e->getMessage(), 'error');
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
		JSession::checkToken()or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$user = JFactory::getUser();
		$ids = $this->input->get('cid', array(), 'array');

		// Access checks.
		if (! $user->authorise('core.create'))
		{
			$this->setMessage(JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'), 'warning');
		}
		elseif (empty($ids))
		{
			$this->setMessage(JText::_('JERROR_NO_ITEMS_SELECTED'), 'warning');
		}
		else
		{
			$model = $this->getModel();

			try
			{
				$model->copy($ids);
			}
			catch (\Exception $e)
			{
				$this->setMessage($e->getMessage(), 'error');
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
