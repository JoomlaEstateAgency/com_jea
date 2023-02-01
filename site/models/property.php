<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Site
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2020 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Captcha\Captcha;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Mail\MailHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\Event\Event;

/**
 * Property model class.
 *
 * @package     Joomla.Site
 * @subpackage  com_jea
 *
 * @see         JModelLegacy
 *
 * @since       2.0
 */
class JeaModelProperty extends JModelLegacy
{
	/**
	 * Overrides parent method
	 *
	 * @return  void
	 *
	 * @see JModelLegacy::populateState()
	 */
	protected function populateState()
	{
		$app = Factory::getApplication();
		$this->setState('property.id', $app->input->get('id', 0, 'int'));

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);

		// Load the contact form informations
		$this->setState('contact.name', $app->getUserStateFromRequest('contact.name', 'name'));
		$this->setState('contact.email', $app->getUserStateFromRequest('contact.email', 'email'));
		$this->setState('contact.telephone', $app->getUserStateFromRequest('contact.telephone', 'telephone'));
		$this->setState('contact.subject', $app->getUserStateFromRequest('contact.subject', 'subject'));
		$this->setState('contact.message', $app->getUserStateFromRequest('contact.message', 'message'));

		$propertyURL = $app->input->get('propertyURL', '', 'base64');
		$this->setState('contact.propertyURL', base64_decode($propertyURL));
	}

	/**
	 * Get the property object
	 *
	 * @return stdClass
	 *
	 * @throws Exception
	 */
	public function getItem()
	{
		static $data;

		if ($data != null)
		{
			return $data;
		}

		$dispatcher = Factory::getApplication()->getDispatcher();

		// Include the jea plugins for the onBeforeLoadProperty event.
		PluginHelper::importPlugin('jea');

		$pk = $this->getState('property.id');

		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('p.*');
		$query->from('#__jea_properties AS p');

		// Join properties types
		$query->select('t.value AS `type`');
		$query->join('LEFT', '#__jea_types AS t ON t.id = p.type_id');

		// Join departments
		$query->select('d.value AS department');
		$query->join('LEFT', '#__jea_departments AS d ON d.id = p.department_id');

		// Join towns
		$query->select('town.value AS town');
		$query->join('LEFT', '#__jea_towns AS town ON town.id = p.town_id');

		// Join areas
		$query->select('area.value AS area');
		$query->join('LEFT', '#__jea_areas AS area ON area.id = p.area_id');

		// Join conditions
		$query->select('c.value AS `condition`');
		$query->join('LEFT', '#__jea_conditions AS c ON c.id = p.condition_id');

		// Join heating types
		$query->select('ht.value AS `heating_type_name`');
		$query->join('LEFT', '#__jea_heatingtypes AS ht ON ht.id = p.heating_type');

		// Join hot water types
		$query->select('hwt.value AS `hot_water_type_name`');
		$query->join('LEFT', '#__jea_hotwatertypes AS hwt ON hwt.id = p.hot_water_type');

		// Join users
		$query->select('u.username AS author');
		$query->join('LEFT', '#__users AS u ON u.id = p.created_by');

		// Join slogans
		$query->select('s.value AS slogan');
		$query->join('LEFT', '#__jea_slogans AS s ON s.id = p.slogan_id');

		$query->where('p.id =' . (int) $pk);
		$query->where('p.published = 1');

		// Filter by access level
		$user = Factory::getApplication()->getIdentity();
		$groups = implode(',', $user->getAuthorisedViewLevels());
		$query->where('p.access IN (' . $groups . ')');

		// Filter by start and end dates.
		$nullDate = $db->Quote($db->getNullDate());
		$nowDate = $db->Quote(Factory::getDate()->toSql());

		$query->where('(p.publish_up = ' . $nullDate . ' OR p.publish_up <= ' . $nowDate . ')');
		$query->where('(p.publish_down = ' . $nullDate . ' OR p.publish_down >= ' . $nowDate . ')');

		$dispatcher->dispatch('onBeforeLoadProperty', new Event('onBeforeLoadProperty', array(&$query, &$this->state)));

		$db->setQuery($query);

		$data = $db->loadObject();

		if ($data == null)
		{
			return false;
		}

		// Convert images field
		$images = json_decode($data->images);

		if (!empty($images) && is_array($images))
		{
			$imagePath = JPATH_ROOT . '/images/com_jea';
			$baseURL = Uri::root(true);

			foreach ($images as $k => $image)
			{
				if (file_exists($imagePath . '/images/' . $data->id . '/' . $image->name))
				{
					$image->URL = $baseURL . '/images/com_jea/images/' . $data->id . '/' . $image->name;

					// Get thumb min URL
					if (file_exists($imagePath . '/thumb-min/' . $data->id . '-' . $image->name))
					{
						// If the thumbnail already exists, display it directly
						$image->minURL = $baseURL . '/images/com_jea/thumb-min/' . $data->id . '-' . $image->name;
					}
					else
					{
						// If the thumbnail doesn't exist, generate it and output it on the fly
						$image->minURL = 'index.php?option=com_jea&task=thumbnail.create&size=min&id=' . $data->id . '&image=' . $image->name;
					}

					// Get thumb medium URL
					if (file_exists($imagePath . '/thumb-medium/' . $data->id . '-' . $image->name))
					{
						// If the thumbnail already exists, display it directly
						$image->mediumURL = $baseURL . '/images/com_jea/thumb-medium/' . $data->id . '-' . $image->name;
					}
					else
					{
						// If the thumbnail doesn't exist, generate it and output it on the fly
						$image->mediumURL = 'index.php?option=com_jea&task=thumbnail.create&size=medium&id=' . $data->id . '&image=' . $image->name;
					}
				}
				else
				{
					unset($images[$k]);
				}
			}

			$data->images = $images;
		}

		return $data;
	}

	/**
	 * Get the previous and next item relative to the current
	 *
	 * @return array
	 */
	public function getPreviousAndNext()
	{
		$item = $this->getItem();

		$properties = JModelLegacy::getInstance('Properties', 'JeaModel');
		$state = $properties->getState();
		$state->set('list.limit', 0);
		$state->set('list.start', 0);
		$items = $properties->getItems();

		$result = array('prev' => null, 'next' => null);

		$currentIndex = 0;

		foreach ($items as $k => $row)
		{
			if ($row->id == $item->id)
			{
				$currentIndex = $k;
			}
		}

		if (isset($items[$currentIndex - 1]))
		{
			$result['prev'] = $items[$currentIndex - 1];
		}

		if (isset($items[$currentIndex + 1]))
		{
			$result['next'] = $items[$currentIndex + 1];
		}

		return $result;
	}

	/**
	 * Increment the hit counter for the property.
	 *
	 * @param   integer $pk Optional primary key of the article to increment.
	 *
	 * @return  boolean True if successful; false otherwise and internal error set.
	 */
	public function hit($pk = 0)
	{
		$pk = empty($pk) ? $this->getState('property.id') : (int) $pk;
		$db = $this->getDbo();
		$db->setQuery('UPDATE #__jea_properties SET hits = hits + 1 WHERE id = ' . (int) $pk);

		try
		{
			$db->execute();
		}
		catch (\RuntimeException $e)
		{
			Log::add($e->getMessage(), Log::ERROR, 'com_jea');

			return false;
		}

		return true;
	}

	/**
	 * Send property contact form
	 *
	 * @return boolean
	 */
	public function sendContactForm()
	{
		$app = Factory::getApplication();

		// Get a JMail instance
		$mailer = Factory::getMailer();
		$params = $app->getParams();

		$defaultFrom = $mailer->From;
		$defaultFromname = $mailer->FromName;

		$data = array(
			'name' => MailHelper::cleanLine($this->getState('contact.name')),
			'email' => MailHelper::cleanAddress($this->getState('contact.email')),
			'telephone' => MailHelper::cleanLine($this->getState('contact.telephone')),
			'subject' => MailHelper::cleanSubject($this->getState('contact.subject')) . ' [' . $defaultFromname . ']',
			'message' => MailHelper::cleanText($this->getState('contact.message')),
			'propertyURL' => $this->getState('contact.propertyURL')
		);

		$dispatcher = Factory::getApplication()->getDispatcher();
		PluginHelper::importPlugin('jea');

		if ($params->get('use_captcha'))
		{
			$plugin = Factory::getApplication()->get('captcha');

			if ($plugin == '0')
			{
				$plugin = 'recaptcha';
			}

			$captcha = Captcha::getInstance($plugin);

			// Test the value.
			if (!$captcha->checkAnswer(''))
			{
				$error = $captcha->getError();

				if ($error instanceof Exception)
				{
					$this->setError($error->getMessage());
				}
				else
				{
					$this->setError($error);
				}
			}
		}

		// Check data
		if (empty($data['name']))
		{
			$this->setError(Text::_('COM_JEA_YOU_MUST_TO_ENTER_YOUR_NAME'));
		}

		if (empty($data['message']))
		{
			$this->setError(Text::_('COM_JEA_YOU_MUST_TO_ENTER_A_MESSAGE'));
		}

		if (!MailHelper::isEmailAddress($data['email']))
		{
			$this->setError(Text::sprintf('COM_JEA_INVALID_EMAIL_ADDRESS', $data['email']));
		}

		$result = $dispatcher->dispatch('onBeforeSendContactForm', new Event('onBeforeSendContactForm', array($data, &$this)));

		if (in_array(false, $result, true))
		{
			return false;
		}

		if ($this->getErrors())
		{
			return false;
		}

		$recipients = array();
		$defaultMail = $params->get('default_mail');
		$agentMail = '';

		if ($params->get('send_form_to_agent') == 1)
		{
			$item = $this->getItem();
			$db = $this->getDbo();
			$q = 'SELECT `email` FROM `#__users` WHERE `id`=' . (int) $item->created_by;
			$db->setQuery($q);
			$agentMail = $db->loadResult();
		}

		if (!empty($defaultMail) && !empty($agentMail))
		{
			$recipients[] = $defaultMail;
			$recipients[] = $agentMail;
		}
		elseif (!empty($defaultMail))
		{
			$recipients[] = $defaultMail;
		}
		elseif (!empty($agentMail))
		{
			$recipients[] = $agentMail;
		}
		else
		{
			// Send to the webmaster email
			$recipients[] = $defaultFrom;
		}

		$body = $data['message'] . "\n";

		if (!empty($data['telephone']))
		{
			$body .= "\n" . Text::_('COM_JEA_TELEPHONE') . ' : ' . $data['telephone'];
		}

		$body .= "\n" . Text::_('COM_JEA_PROPERTY_URL') . ' : ' . $data['propertyURL'];

		$mailer->setBody($body);
		$ret = $mailer->sendMail($data['email'], $data['name'], $recipients, $data['subject'], $body, false);

		if ($ret == true)
		{
			$app->setUserState('contact.name', '');
			$app->setUserState('contact.email', '');
			$app->setUserState('contact.telephone', '');
			$app->setUserState('contact.subject', '');
			$app->setUserState('contact.message', '');

			return true;
		}

		return false;
	}
}
