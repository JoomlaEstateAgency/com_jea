<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2020 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

FormHelper::loadFieldClass('list');

/**
 * Supports an HTML select list of gateway providers
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @see         ListField
 * @since       2.0
 */
class JFormFieldGatewayproviderlist extends ListField
{
	/**
	 * The form field type.
	 *
	 * @var string
	 */
	protected $type = 'Gatewayproviderlist';

	/**
	 * The provider type (import or export).
	 *
	 * @var string
	 */
	protected $providerType = '';

	/**
	 * Overrides parent method.
	 *
	 * @param   string $name The property name for which to the the value.
	 *
	 * @return  mixed  The property value or null.
	 *
	 * @see ListField::__get()
	 */
	public function __get($name)
	{
		if ($name == 'providerType')
		{
			return $this->$name;
		}

		return parent::__get($name);
	}

	/**
	 * Overrides parent method.
	 *
	 * @param   string $name  The property name for which to the the value.
	 * @param   mixed  $value The value of the property.
	 *
	 * @return  void
	 *
	 * @see ListField::__set()
	 */
	public function __set($name, $value)
	{
		if ($name == 'providerType')
		{
			$this->$name = (string) $value;
		}

		parent::__set($name, $value);
	}

	/**
	 * Overrides parent method.
	 *
	 * @param   SimpleXMLElement $element    The SimpleXMLElement object representing the `<field>` tag for the form field object.
	 * @param   mixed            $value      The form field value to validate.
	 * @param   string           $group      The field name group control value.
	 *
	 * @return  boolean  True on success.
	 *
	 * @see ListField::setup()
	 */
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);

		if ($return)
		{
			$this->providerType = (string) $this->element['provider_type'];
		}

		return $return;
	}

	/**
	 * Overrides parent method.
	 *
	 * @return  array  The field option objects.
	 *
	 * @see ListFieldList::getOptions()
	 */
	protected function getOptions()
	{
		$options = array();

		$path = JPATH_ADMINISTRATOR . '/components/com_jea/gateways/providers';

		$folders = Folder::folders($path);

		$options[] = HTMLHelper::_('select.option', '', Text::alt('JOPTION_DO_NOT_USE', preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)));

		foreach ($folders as $folder)
		{
			if (file_exists($path . '/' . $folder . '/' . $this->providerType . '.xml'))
			{
				$options[] = HTMLHelper::_('select.option', $folder, $folder);
			}
		}

		return $options;
	}
}
