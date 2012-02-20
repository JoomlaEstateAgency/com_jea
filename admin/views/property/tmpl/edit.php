<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright	Copyright (C) 2008 PHILIP Sylvain. All rights reserved.
 * @license		GNU/GPL, see LICENSE.txt
 * Joomla Estate Agency is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses.
 *
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

JHTML::stylesheet('media/com_jea/css/jea.admin.css');
?>

<form action="<?php echo JRoute::_('index.php?option=com_jea&layout=edit&id='.(int) $this->item->id) ?>" method="post" name="adminForm" id="item-form" class="form-validate" enctype="multipart/form-data">

  <div class="width-60 fltlft">
    <fieldset class="adminform">
      <legend>
      <?php echo empty($this->item->id) ? JText::_('COM_JEA_NEW_ARTICLE') : JText::sprintf('COM_JEA_EDIT_ARTICLE', $this->item->id) ?>
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
        <legend><?php echo JText::_('Localization')?></legend>
        <ul class="adminformlist">
        <?php foreach ($this->form->getFieldset('localization') as $field): ?>
          <li><?php echo $field->label . "\n" . $field->input ?></li>
        <?php endforeach ?>
        </ul>
      </fieldset>

      <fieldset>
        <legend><?php echo JText::_('Financial informations')?></legend>
        <ul class="adminformlist">
        <?php foreach ($this->form->getFieldset('financial_informations') as $field): ?>
          <li><?php echo $field->label . "\n" . $field->input ?></li>
        <?php endforeach ?>
        </ul>
      </fieldset>

      <fieldset>
        <legend><?php echo JText::_('Details')?></legend>
        <ul class="adminformlist">
        <?php foreach ($this->form->getFieldset('details') as $field): ?>
          <li><?php echo $field->label . "\n" . $field->input ?></li>
        <?php endforeach ?>
        </ul>
        <div class="clr"></div>

        <fieldset class="advantages">
          <legend><?php echo JText::_('Advantages')?></legend>
          <div class="clr"></div>
          <?php echo $this->form->getInput('advantages') ?>
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

  <?php echo JHtml::_('sliders.panel', JText::_('Publication info'), 'params-pane') ?>
    <fieldset class="panelform">
      <ul class="adminformlist">
      <?php foreach ($this->form->getFieldset('publication') as $field): ?>
        <li><?php echo $field->label . "\n" . $field->input ?></li>
      <?php endforeach ?>
      </ul>
    </fieldset>

    <?php echo JHtml::_('sliders.panel', JText::_('Pictures'), 'picture-pane') ?>
    <fieldset class="panelform">
    <?php echo $this->form->getInput('images') ?>
    </fieldset>
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
