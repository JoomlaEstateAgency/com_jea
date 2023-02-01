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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

/**
 * Property form view.
 *
 * @package     Joomla.Site
 * @subpackage  com_jea
 *
 * @since       2.0
 */
class JeaViewForm extends HtmlView
{
	/**
	 * The form object
	 *
	 * @var Form
	 */
	protected $form;

	/**
	 * The database record
	 *
	 * @var JObject|boolean
	 */
	protected $item;

	/**
	 * The model state
	 *
	 * @var JObject
	 */
	protected $state;

	/**
	 * The component parameters
	 *
	 * @var Joomla\Registry\Registry
	 */
	protected $params;

	/**
	 * Overrides parent method.
	 *
	 * @param   string $tpl The name of the template file to parse.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 *
	 * @see     JViewLegacy::display()
	 */
	public function display($tpl = null)
	{
		HTMLHelper::stylesheet('com_jea/jea.css', array('relative' => true));

		$app = Factory::getApplication();
		$user = $app->getIdentity();

		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->state = $this->get('State');
		$this->params = $app->getParams();

		$authorised = false;

		if (empty($this->item->id))
		{
			if (!$user->id)
			{
				// When user is not authenticated
				if ($this->params->get('login_behavior') == 'before')
				{
					$return = base64_encode(JUri::getInstance());
					$message = Text::_('JGLOBAL_YOU_MUST_LOGIN_FIRST');
					$redirect = Route::_('index.php?option=com_users&view=login&return=' . $return, false);
					$app->redirect($redirect, $message);
				}
				else
				{
					// The user should be redirected on the login form after the form submission.
					$authorised = true;
				}
			}
			else
			{
				$authorised = $user->authorise('core.create', 'com_jea');
			}
		}
		else
		{
			$asset = 'com_jea.property.' . $this->item->id;

			// Check general edit permission first.
			if ($user->authorise('core.edit', $asset))
			{
				$authorised = true;
			}
			elseif ($user->authorise('core.edit.own', $asset) && $this->item->created_by == $user->id)
			{
				$authorised = true;
			}
		}

		if (!$authorised)
		{
			throw new RuntimeException(Text::_('JERROR_ALERTNOAUTHOR'));
		}

		parent::display($tpl);
	}
}
