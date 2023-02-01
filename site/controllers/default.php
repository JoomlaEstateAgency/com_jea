<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Site
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2020 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

/**
 * Default controller class.
 *
 * @package     Joomla.Site
 * @subpackage  com_jea
 *
 * @since       2.0
 */
class JeaControllerDefault extends BaseController
{
	/**
	 * The default view for the display method.
	 *
	 * @var   string
	 */
	protected $default_view = 'properties';

	/**
	 * Overrides parent method.
	 *
	 * @param   boolean $cachable    If true, the view output will be cached
	 * @param   array   $urlparams   An array of safe URL parameters and their variable types, for valid values see {@link InputFilter::clean()}.
	 *
	 * @return  BaseController.
	 *
	 * @since   3.0
	 */
	public function display($cachable = false, $urlparams = array())
	{
		$layout = Factory::getApplication()->input->get('layout');

		if ($layout == 'manage' || $layout == 'edit')
		{
			$user = Factory::getApplication()->getIdentity();
			$uri = Uri::getInstance();
			$return = base64_encode($uri);
			$access = false;

			if ($layout == 'manage')
			{
				$access = $user->authorise('core.edit.own', 'com_jea');
			}
			elseif ($layout == 'edit')
			{
				$params = Factory::getApplication()->getParams();

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

			if (!$access)
			{
				if ($user->id)
				{
					$this->setMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'warning');
				}
				else
				{
					$this->setMessage(Text::_('JGLOBAL_YOU_MUST_LOGIN_FIRST'));
				}

				return $this->setRedirect(Route::_('index.php?option=com_users&view=login&return=' . $return, false));
			}
		}

		return parent::display($cachable, $urlparams);
	}

	/**
	 * Send contact form action
	 *
	 * @return BaseController
	 */
	public function sendContactForm()
	{
		$model = $this->getModel('Property', 'JeaModel');
		$returnURL = $model->getState('contact.propertyURL');

		// Check for request forgeries
		if (!Session::checkToken())
		{
			return $this->setRedirect($returnURL, Text::_('JINVALID_TOKEN'), 'warning');
		}

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

		$msg = Text::_('COM_JEA_CONTACT_FORM_SUCCESSFULLY_SENT');

		return $this->setRedirect($returnURL, $msg);
	}
}
