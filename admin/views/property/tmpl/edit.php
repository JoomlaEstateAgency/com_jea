<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @version     $Id$
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2012 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$dispatcher = JDispatcher::getInstance();
JPluginHelper::importPlugin( 'jea' );

JHTML::stylesheet('media/com_jea/css/jea.admin.css');

// item language
$itemLanguage = $this->item->language;
if (empty($itemLanguage)) {
	$itemLanguage = '*';
}
// item type
$type_id = $this->item->type_id;

$alertBgColor = '#F8E9E9';
$alertBorderColor = '#DE7A7B';

$document = & JFactory::getDocument();
$document->addScriptDeclaration("
	Element.implement({
		flash: function(to,from,reps,prop,dur) {
	
			//defaults
			if(!reps) { reps = 1; }
			if(!prop) { prop = 'background-color'; }
			if(!dur) { dur = 250; }
		
			//create effect
			var effect = new Fx.Tween(this, {
				duration: dur,
				link: 'chain'
			})
		
			//do it!
			for(x = 1; x <= reps; x++)
			{
				effect.start(prop,from,to).start(prop,to,from);
			}
		}
	});
	
	// colors
	var bgColor = '{$alertBgColor}';
	var borderColor = '{$alertBorderColor}';

	function updateFeatures(language) {
		// show field alerts
		document.id('ajaxupdating').setStyle('display','');
		document.id('ajaxupdating').flash('#fff',bgColor,2,'background-color',500);
		$('jform_type_id').setStyle('border','1px solid '+borderColor);
		$('jform_type_id').flash('#fff',bgColor,2,'background-color',500);
		$('jform_condition_id').setStyle('border','1px solid '+borderColor);
		$('jform_condition_id').flash('#fff',bgColor,2,'background-color',500);
		$('jform_heating_type').setStyle('border','1px solid '+borderColor);
		$('jform_heating_type').flash('#fff',bgColor,2,'background-color',500);
		$('jform_hot_water_type').setStyle('border','1px solid '+borderColor);
		$('jform_hot_water_type').flash('#fff',bgColor,2,'background-color',500);
		$('jform_slogan_id').setStyle('border','1px solid '+borderColor);
		$('jform_slogan_id').flash('#fff',bgColor,2,'background-color',500);
		// update dropdowns
		updateFeature('type','jform_type_id',language);
		updateFeature('condition','jform_condition_id',language);
		updateFeature('heatingtype','jform_heating_type',language);
		updateFeature('hotwatertype','jform_hot_water_type',language);
		updateFeature('slogan','jform_slogan_id',language);
	};
	
	function updateFeature(name, fieldId, language) {
		var jSonRequest = new Request.JSON({
			url: 'index.php',
			onSuccess: function(response) {
				var first = document.id(fieldId).getFirst().clone();
				document.id(fieldId).empty();
				document.id(fieldId).adopt(first);
				if (response) {
					response.each(function(item) {
						var option  = new Element('option', {'value' : item.id});
						option.appendText(item.value);
						document.id(fieldId).adopt(option);
					});
				}
			}
		});
		jSonRequest.get({
			'option' : 'com_jea',
			'format' : 'json',
			'task' : 'properties.updateFeature',
			'feature' : name,
			'language' : language
		});
	};
	
	window.addEvent('domready', function() {
		document.id('ajaxupdating').setStyle('display','none');
	});
");
?>
<div id="ajaxupdating" style="padding: 5px; border-radius: 5px; border: 1px solid <?php echo $alertBorderColor; ?>; ">
	<h3><?php echo JText::_('COM_JEA_FEATURES_UPDATED_WARNING')?></h3>
</div>

<form action="<?php echo JRoute::_('index.php?option=com_jea&layout=edit&id='.(int) $this->item->id) ?>" method="post" name="adminForm" id="item-form" class="form-validate" enctype="multipart/form-data">

  <div class="width-60 fltlft">
    <fieldset class="adminform">
      <legend>
      <?php echo empty($this->item->id) ? JText::_('COM_JEA_NEW_PROPERTY') : JText::sprintf('COM_JEA_EDIT_PROPERTY', $this->item->id) ?>
      </legend>

      <ul class="adminformlist">
        <li><?php echo $this->form->getLabel('ref') ?> <?php echo $this->form->getInput('ref') ?></li>
        <li><?php echo $this->form->getLabel('title') ?> <?php echo $this->form->getInput('title') ?></li>
        <li><?php echo $this->form->getLabel('alias') ?> <?php echo $this->form->getInput('alias') ?></li>
        <li><?php echo $this->form->getLabel('transaction_type') ?> <?php echo $this->form->getInput('transaction_type') ?></li>
        <li><?php echo $this->form->getLabel('type_id') ?> <?php echo $this->form->getInput('type_id') ?></li>
      </ul>

      <div class="clr"></div>

      <fieldset>
        <legend><?php echo JText::_('COM_JEA_LOCALIZATION')?></legend>
        <ul class="adminformlist">
        <?php foreach ($this->form->getFieldset('localization') as $field): ?>
          <li><?php echo $field->label . "\n" . $field->input ?></li>
        <?php endforeach ?>
        </ul>
      </fieldset>

      <fieldset>
        <legend><?php echo JText::_('COM_JEA_FINANCIAL_INFORMATIONS')?></legend>
        <ul class="adminformlist">
        <?php foreach ($this->form->getFieldset('financial_informations') as $field): ?>
          <li><?php echo $field->label . "\n" . $field->input ?></li>
        <?php endforeach ?>
        </ul>
      </fieldset>

      <fieldset>
        <legend><?php echo JText::_('COM_JEA_DETAILS')?></legend>
        <ul class="adminformlist">
        <?php foreach ($this->form->getFieldset('details') as $field): ?>
          <li><?php echo $field->label . "\n" . $field->input ?></li>
        <?php endforeach ?>
        </ul>
        <div class="clr"></div>

        <fieldset class="advantages">
          <legend><?php echo JText::_('COM_JEA_AMENITIES')?></legend>
          <div class="clr"></div>
          <?php echo $this->form->getInput('amenities') ?>
          <div class="clr"></div>
        </fieldset>

      </fieldset>

      <div class="clr"></div>
      <?php echo $this->form->getLabel('description') ?>
      <div class="clr"></div>
      <?php echo $this->form->getInput('description') ?>

    </fieldset>
  </div>

  <div class="width-40 fltrt">
  <?php echo JHtml::_('sliders.start', 'property-sliders-'.$this->item->id, array('useCookie'=>1)) ?>

  <?php $dispatcher->trigger('onAfterStartPanels', array(&$this->item)) ?>

  <?php echo JHtml::_('sliders.panel', JText::_('COM_JEA_PUBLICATION_INFO'), 'params-pane') ?>
    <fieldset class="panelform">
      <ul class="adminformlist">
      <?php foreach ($this->form->getFieldset('publication') as $field): ?>
        <li><?php echo $field->label . "\n" . $field->input ?></li>
      <?php endforeach ?>
      </ul>
    </fieldset>

    <?php echo JHtml::_('sliders.panel', JText::_('COM_JEA_PICTURES'), 'picture-pane') ?>
    <fieldset class="panelform">
    <?php echo $this->form->getInput('images') ?>
    </fieldset>
    
    <?php echo JHtml::_('sliders.panel', JText::_('COM_JEA_NOTES'), 'note-pane') ?>
    <fieldset class="panelform">
        <?php echo $this->form->getLabel('notes') ?>
        <div class="clr"></div>
        <?php echo $this->form->getInput('notes') ?>
    </fieldset>

    <?php $dispatcher->trigger('onBeforeEndPanels', array(&$this->item)) ?>

    <?php echo JHtml::_('sliders.end') ?>
  </div>

  <div class="clr"></div>

  <?php if ($this->canDo->get('core.admin')): ?>
  <div class="width-100 fltlft">
  <?php echo JHtml::_('sliders.start', 'permissions-sliders-'.$this->item->id, array('useCookie'=>1)) ?>

  <?php echo JHtml::_('sliders.panel', JText::_('COM_JEA_FIELDSET_RULES'), 'access-rules') ?>
    <fieldset class="panelform">
    <?php echo $this->form->getLabel('rules') ?>
    <?php echo $this->form->getInput('rules') ?>
    </fieldset>

    <?php echo JHtml::_('sliders.end') ?>
  </div>
  <?php endif; ?>

  <div>
    <input type="hidden" name="task" value="" /> <input type="hidden" name="return"
      value="<?php echo JRequest::getCmd('return') ?>" />
      <?php echo JHtml::_('form.token') ?>
  </div>

</form>
