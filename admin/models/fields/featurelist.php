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

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Factory;

/**
 * Form Field class for JEA.
 * Provides a list of features
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @see         JFormField
 *
 * @since       2.0
 */
class JFormFieldFeatureList extends ListField
{
	/**
	 * The form field type.
	 *
	 * @var string
	 */
	public $type = 'featureList';

	/**
	 * Method to get the list of features.
	 *
	 * @return  string The field input markup.
	 *
	 * @see     JHtmlFeatures
	 */
	protected function getInput()
	{
		HTMLHelper::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_jea/helpers/html');

		$subtype = (string) $this->element['subtype'];

		$params = array(
			'id' => $this->id,
			'class' => (string) $this->element['class']
		);

		if (isset($this->element['size']))
		{
			$params['size'] = (string) $this->element['size'];
		}

		if (isset($this->element['multiple']))
		{
			$params['multiple'] = (string) $this->element['multiple'];
		}

		if (isset($this->element['onchange']))
		{
			$params['onchange'] = (string) $this->element['onchange'];
		}

		$group = null;

		switch ($this->form->getName())
		{
			case 'com_menus.item':
				$group = 'params';
				break;
			case 'com_jea.properties.filter':
			case 'com_jea.featurelist.filter':
				$group = 'filter';
				break;
		}

		// Verify if some fields have relashionship
		$hasRelationShip = $this->_hasRelationShip();

		switch ($subtype)
		{
			case 'departments':

				if ($hasRelationShip)
				{
					$this->_ajaxUpdateList('department_id', 'town_id', 'get_towns');
				}

				break;
			case 'towns':

				if ($hasRelationShip)
				{
					$this->_ajaxUpdateList('town_id', 'area_id', 'get_areas');

					return HTMLHelper::_('features.towns', $this->value, $this->name, $params, $this->form->getValue('department_id', $group, null));
				}

			case 'areas':

				if ($hasRelationShip)
				{
					return HTMLHelper::_('features.areas', $this->value, $this->name, $params, $this->form->getValue('town_id', $group, null));
				}
		}

		return HTMLHelper::_('features.' . $subtype, $this->value, $this->name, $params);
	}

	/**
	 * Verify relationship component parameter
	 *
	 * @return  boolean
	 */
	private function _hasRelationShip()
	{
		if (isset($this->element['norelation']))
		{
			return false;
		}

		$params = ComponentHelper::getParams('com_jea');

		return (bool) $params->get('relationship_dpts_towns_area', 1);
	}

	/**
	 * Add AJAX behavior
	 *
	 * @param   string $fromId The Element ID where the event come from
	 * @param   string $toId   The target Element ID
	 * @param   string $task   The AJAX controller task
	 *
	 * @return  void
	 */
	private function _ajaxUpdateList($fromId, $toId, $task)
	{
		if (isset($this->element['noajax']))
		{
			return;
		}

		if ($this->form->getName() == 'com_menus.item')
		{
			$fieldTo = $this->form->getField('filter_' . $toId, 'params');
		}
		else
		{
			$fieldTo = $this->form->getField($toId);
		}

		if (!empty($fieldTo->id))
		{
			Factory::getDocument()->addScriptDeclaration(
				"
jQuery(document).ready(function($) {
	$('#{$this->id}').change(function(e) {
		$.ajax({
			dataType: 'json',
			url: 'index.php',
			data: 'option=com_jea&format=json&task=features.{$task}&{$fromId}=' + this.value,
			success: function(response) {
				var first = $('#{$fieldTo->id} option').first().clone();
				$('#{$fieldTo->id}').empty().append(first);
				if (response) {
					$.each(response, function( idx, item ){
				        console.log(item);
				        console.log($('#{$fieldTo->id}'));
						$('#{$fieldTo->id}').append($('<option></option>').text(item.value).attr('value', item.id));
					});
				}
				$('#{$fieldTo->id}').trigger('chosen:updated.chosen'); // Update jQuery choosen
			}
		});
	});
});
				"
			);
		}
	}
}
