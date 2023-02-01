<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Site
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2020 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Form\Form;
use Joomla\CMS\Table\Table;

defined('_JEXEC') or die;

// Base this model on the backend version.
require_once JPATH_COMPONENT_ADMINISTRATOR . '/models/property.php';

Table::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');

/**
 * Property form model class.
 *
 * @package     Joomla.Site
 * @subpackage  com_jea
 *
 * @see         JeaModelProperty
 *
 * @since       2.0
 */
class JeaModelForm extends JeaModelProperty
{
	/**
	 * The model (base) name should be the same as parent
	 *
	 * @var string
	 */
	protected $name = 'property';

	/**
	 * Overrides parent method
	 *
	 * @param   array   $data       Data for the form.
	 * @param   boolean $loadData   True if the form is to load its own data (default case), false if not.
	 *
	 * @return  Form|boolean  A JForm object on success, false on failure
	 *
	 * @see JeaModelProperty::getForm()
	 */
	public function getForm($data = array(), $loadData = true)
	{
		Form::addFormPath(JPATH_COMPONENT_ADMINISTRATOR . '/models/forms');
		Form::addFieldPath(JPATH_COMPONENT_ADMINISTRATOR . '/models/fields');

		$form = parent::getForm($data, $loadData);

		return $form;
	}
}
