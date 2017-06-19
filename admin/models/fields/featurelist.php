<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2017 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

JHtml::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_jea/helpers/html');

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
class JFormFieldFeatureList extends JFormField
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
	protected function getInput ()
	{
		$subtype = (string) $this->element['subtype'];

		$size = (string) $this->element['size'];
		$multiple = (string) $this->element['multiple'];

		$params = array(
				'id' => $this->id,
				'class' => (string) $this->element['class']
		);

		if (! empty($size))
		{
			$params['size'] = $size;
		}

		if (! empty($multiple))
		{
			$params['multiple'] = 'multiple';
		}

		$group = null;

		if ($this->form->getName() == 'com_menus.item')
		{
			$group = 'params';
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

					return JHtml::_('features.towns', $this->value, $this->name, $params, $this->form->getValue('department_id', $group, null));
				}

			case 'areas':

				if ($hasRelationShip)
				{
					return JHtml::_('features.areas', $this->value, $this->name, $params, $this->form->getValue('town_id', $group, null));
				}
		}

		return JHtml::_('features.' . $subtype, $this->value, $this->name, $params);
	}

	/**
	 * Verify relationship component parameter
	 *
	 * @return  boolean
	 */
	private function _hasRelationShip ()
	{
		$params = JComponentHelper::getParams('com_jea');

		return (bool) $params->get('relationship_dpts_towns_area', 1);
	}

	/**
	 * Add AJAX behavior
	 *
	 * @param   string  $fromId  The Element ID where the event come from
	 * @param   string  $toId    The target Element ID
	 * @param   string  $task    The AJAX controller task
	 *
	 * @return  void
	 */
	private function _ajaxUpdateList ($fromId, $toId, $task)
	{
		if ($this->form->getName() == 'com_menus.item')
		{
			$fieldTo = $this->form->getField($toId, 'params');
		}
		else
		{
			$fieldTo = $this->form->getField($toId);
		}

		if (! empty($fieldTo->id))
		{
			JFactory::getDocument()->addScriptDeclaration(
			"window.addEvent('domready', function() {
                    document.id('{$this->id}').addEvent('change', function() {
                        var jSonRequest = new Request.JSON({
                            url: 'index.php',
                            onSuccess: function(response) {
                                var first = document.id('{$fieldTo->id}').getFirst().clone();
                                document.id('{$fieldTo->id}').empty();
                                document.id('{$fieldTo->id}').adopt(first);
                                if (response) {
                                    response.each(function(item) {
                                        var option  = new Element('option', {'value' : item.id});
                                        option.appendText(item.value);
                                        document.id('{$fieldTo->id}').adopt(option);
                                    });
                                }
                            }
                         });
                         jSonRequest.get({
                             'option' : 'com_jea',
                             'format' : 'json',
                             'task' : 'features.{$task}',
                             {$fromId} : this.value
                         });
                     });
                });
            "
			);
		}
	}
}
