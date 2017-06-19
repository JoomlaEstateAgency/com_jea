<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Site
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2017 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Property controller class.
 *
 * @package     Joomla.Site
 * @subpackage  com_jea
 *
 * @since       2.0
 */
class JeaControllerProperty extends JControllerForm
{
	/**
	 * The URL view item variable.
	 *
	 * @var string
	 */
	protected $view_item = 'form';

	/**
	 * The URL view list variable.
	 *
	 * @var string
	 */
	protected $view_list = 'properties';

	/**
	 * Overrides parent method.
	 *
	 * @param   array  $data  An array of input data.
	 *
	 * @return  boolean
	 *
	 * @see JControllerForm::allowAdd()
	 */
	protected function allowAdd($data = array())
	{
		$user = JFactory::getUser();

		if (!$user->authorise('core.create', 'com_jea'))
		{
			$app = JFactory::getApplication();
			$uri = JFactory::getURI();
			$return = base64_encode($uri);

			if ($user->get('id'))
			{
				$this->setMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'warning');
			}
			else
			{
				$this->setMessage(JText::_('JGLOBAL_YOU_MUST_LOGIN_FIRST'));
			}

			// Save the data in the session.
			$app->setUserState('com_jea.edit.property.data', $data);
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=login&return=' . $return, false));

			return $this->redirect();
		}

		return true;
	}

	/**
	 * Overrides parent method.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key; default is id.
	 *
	 * @return  boolean
	 *
	 * @see JControllerForm::allowEdit()
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		// Initialise variables.
		$recordId = (int) isset($data[$key]) ? $data[$key] : 0;
		$user = JFactory::getUser();
		$asset = 'com_jea.property.' . $recordId;

		// Check general edit permission first.
		if ($user->authorise('core.edit', $asset))
		{
			return true;
		}

		// Fallback on edit.own. First test if the permission is available.
		if ($user->authorise('core.edit.own', $asset))
		{
			// Now test the owner is the user.
			$ownerId = (int) isset($data['created_by']) ? $data['created_by'] : 0;

			if (empty($ownerId) && $recordId)
			{
				// Need to do a lookup from the model.
				$record = $this->getModel()->getItem($recordId);

				if (empty($record))
				{
					return false;
				}

				$ownerId = $record->created_by;
			}

			// If the owner matches 'me' then do the test.
			if ($ownerId == $user->id)
			{
				return true;
			}
		}

		// Since there is no asset tracking, revert to the component permissions.
		return parent::allowEdit($data, $key);
	}

	/**
	 * Unpublish a property
	 *
	 * @return void
	 */
	public function unpublish()
	{
		$this->publish(0);
	}

	/**
	 * Publish/Unpublish a property
	 *
	 * @param   integer  $action  0 -> unpublish, 1 -> publish
	 *
	 * @return  void
	 */
	public function publish($action = 1)
	{
		$id = JFactory::getApplication()->input->get('id', 0, 'int');
		$this->getModel()->publish($id, $action);
		$this->setRedirect(JRoute::_('index.php?option=com_jea&view=properties' . $this->getRedirectToListAppend(), false));
	}

	/**
	 * Delete a property
	 *
	 * @return void
	 */
	public function delete()
	{
		$id = JFactory::getApplication()->input->get('id', 0, 'int');

		if ($this->getModel()->delete($id))
		{
			$this->setMessage(JText::_('COM_JEA_SUCCESSFULLY_REMOVED_PROPERTY'));
		}

		$this->setRedirect(JRoute::_('index.php?option=com_jea&view=properties' . $this->getRedirectToListAppend(), false));
	}

	/**
	 * Overrides parent method.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JeaModelForm|boolean  Model object on success; otherwise false on failure.
	 *
	 * @see JControllerLegacy::getModel()
	 */
	public function getModel($name = 'form', $prefix = '', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

	/**
	 *  Overrides parent method.
	 *
	 * @param   integer  $recordId  The primary key id for the item.
	 * @param   string   $urlVar    The name of the URL variable for the id.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 *
	 * @see JControllerForm::getRedirectToItemAppend()
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		$tmpl = $this->input->getCmd('tmpl');
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

	/**
	 * Overrides parent method.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 *
	 * @see JControllerForm::getRedirectToListAppend()
	 */
	protected function getRedirectToListAppend()
	{
		$tmpl = $this->input->getCmd('tmpl');
		$append = '&layout=manage';

		// Try to redirect to the manage menu item if found
		$app = JFactory::getApplication();
		$menu = $app->getMenu();
		$activeItem = $menu->getActive();

		if (isset($activeItem->query['layout']) && $activeItem->query['layout'] != 'manage')
		{
			$items = $menu->getItems('component', 'com_jea');

			foreach ($items as $item)
			{
				$layout = isset($item->query['layout']) ? $item->query['layout'] : '';

				if ($layout == 'manage')
				{
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
