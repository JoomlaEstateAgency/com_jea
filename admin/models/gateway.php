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
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\Model\AdminModel;

require_once JPATH_COMPONENT_ADMINISTRATOR . '/gateways/dispatcher.php';

/**
 * Gateway model class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @see         AdminModel
 *
 * @since       3.4
 */
class JeaModelGateway extends AdminModel
{
	/**
	 * Overrides parent method
	 *
	 * @param   array   $data     Data for the form.
	 * @param   boolean $loadData True if the form is to load its own data (default case), false if not.
	 *
	 * @return  Form|boolean  A JForm object on success, false on failure
	 *
	 * @see AdminModel::getForm()
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$app = Factory::getApplication();
		$type = $app->getUserStateFromRequest('com_jea.gateway.type', 'type', '', 'cmd');

		// @var $form JForm

		$form = $this->loadForm('com_jea.' . $type, $type, array('control' => 'jform', 'load_data' => false));

		if (empty($form))
		{
			return false;
		}

		$item = $this->getItem($app->input->getInt('id', 0));

		// Load gateway params
		if ($item->id)
		{
			$formConfigFile = JPATH_COMPONENT_ADMINISTRATOR . '/gateways/providers/' . $item->provider . '/' . $item->type . '.xml';

			if (File::exists($formConfigFile))
			{
				// Try to load provider language file
				Factory::getApplication()->getLanguage()->load($item->provider, JPATH_COMPONENT, null, false, false);

				$gatewayForm = $this->loadForm('com_jea.' . $item->type . '.' . $item->provider, $formConfigFile, array('load_data' => false));
				$form->load($gatewayForm->getXml());
			}

			$dispatcher = GatewaysEventDispatcher::getInstance();
			$dispatcher->loadGateway($item);
			$dispatcher->trigger('onPrepareForm', array('form' => $form));

			$data = $this->loadFormData();
			$form->bind($data);
		}

		return $form;
	}

	/**
	 * Overrides parent method
	 *
	 * @return  array  The default data is an empty array.
	 *
	 * @see AdminModel::loadFormData()
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data. See JControllerForm::save()
		$data = Factory::getApplication()->getUserState('com_jea.edit.gateway.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Overrides parent method
	 *
	 * @param   array $data The form data.
	 *
	 * @return  boolean  True on success, False on error.
	 *
	 * @see AdminModel::save()
	 */
	public function save($data)
	{
		if (isset($data['params']) && is_array($data['params']))
		{
			$data['params'] = json_encode($data['params']);
		}

		return parent::save($data);
	}
}
