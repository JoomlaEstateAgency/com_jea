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
 * Default controller class.
 *
 * @package     Joomla.Site
 * @subpackage  com_jea
 *
 * @since       2.0
 */
class JeaControllerDefault extends JControllerLegacy
{
	protected $default_view = 'properties';

	/**
	 * Overrides parent method.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JControllerLegacy.
	 *
	 * @since   3.0
	 */
	public function display($cachable = false, $urlparams = array())
	{
		$layout = JFactory::getApplication()->input->get('layout');

		if ($layout == 'manage' || $layout == 'edit')
		{
			$user = JFactory::getUser();
			$uri = JUri::getInstance();
			$return = base64_encode($uri);
			$access = false;

			if ($layout == 'manage')
			{
				$access = $user->authorise('core.edit.own', 'com_jea');
			}
			elseif ($layout == 'edit')
			{
				$params = JFactory::getApplication()->getParams();

				if ($params->get('login_behavior', 'before') == 'before')
				{
					$access = $user->authorise('core.create', 'com_jea');
				}
				else
				{
					// If the login_behavior is set after save,
					// so all users can see the property form.
					$access = true;
				}
			}

			if (! $access)
			{
				if ($user->id)
				{
					$this->setMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'warning');
				}
				else
				{
					$this->setMessage(JText::_('JGLOBAL_YOU_MUST_LOGIN_FIRST'));
				}

				$this->setRedirect(JRoute::_('index.php?option=com_users&view=login&return=' . $return, false));

				return $this->redirect();
			}
		}

		return parent::display($cachable, $urlparams);
	}

	/**
	 * Send contact form action
	 *
	 * @return void
	 */
	public function sendContactForm()
	{
		// Check for request forgeries
		if (!JSession::checkToken())
		{
			return $this->setRedirect($returnURL, JText::_('JINVALID_TOKEN'), 'warning');
		}

		$model = $this->getModel('Property', 'JeaModel');
		$returnURL = $model->getState('contact.propertyURL');

		if (!$model->sendContactForm())
		{
			$errors = $model->getErrors();
			$msg = '';

			foreach ($errors as $error)
			{
				$msg .= $error . "\n";
			}

			return $this->setRedirect($returnURL, $msg, 'warning');
		}

		$msg = JText::_('COM_JEA_CONTACT_FORM_SUCCESSFULLY_SENT');

		$this->setRedirect($returnURL, $msg);
	}
}
