<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @version     $Id: edit.php 272 2012-03-10 11:05:08Z ilhooq $
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2012 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.calendar');
JHtml::_('behavior.formvalidation');

JHTML::stylesheet('media/com_jea/css/jea.css');

$this->form->setFieldAttribute('description', 'buttons', 'false');

$user = JFactory::getUser();
?>
<script type="text/javascript">
  Joomla.submitbutton = function(task) {
    if (task == 'property.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
      <?php echo $this->form->getField('description')->save(); ?>
      Joomla.submitform(task);
    } else {
      alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
    }
  }
</script>

<div class="edit property<?php echo $this->escape($this->params->get('pageclass_sfx')) ?>">

<?php if ($this->item->id): ?>
<p><a href="javascript:Joomla.submitbutton('property.cancel');"><?php echo JText::_('COM_JEA_RETURN_TO_THE_LIST')?></a></p>
<?php endif ?>

  <form action="<?php echo JRoute::_('index.php?option=com_jea&id='.(int) $this->item->id) ?>" method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">

    <fieldset>
      <legend>
      <?php echo empty($this->item->id) ? JText::_('COM_JEA_NEW_PROPERTY') : JText::sprintf('COM_JEA_EDIT_PROPERTY', $this->item->id) ?>
      </legend>

      <div class="formelm"><?php echo $this->form->getLabel('ref') ?> <?php echo $this->form->getInput('ref') ?></div>
      <div class="formelm"><?php echo $this->form->getLabel('title') ?> <?php echo $this->form->getInput('title') ?></div>
      <div class="formelm"><?php echo $this->form->getLabel('transaction_type') ?> <?php echo $this->form->getInput('transaction_type') ?></div>
      <div class="formelm"><?php echo $this->form->getLabel('type_id') ?> <?php echo $this->form->getInput('type_id') ?></div>

      <?php echo $this->form->getLabel('description') ?>
      <?php echo $this->form->getInput('description') ?>

      <fieldset>
        <legend><?php echo JText::_('COM_JEA_LOCALIZATION')?></legend>
        <?php foreach ($this->form->getFieldset('localization') as $field): ?>
          <div class="formelm"><?php echo $field->label . "\n" . $field->input ?></div>
        <?php endforeach ?>
      </fieldset>

      <fieldset>
        <legend><?php echo JText::_('COM_JEA_FINANCIAL_INFORMATIONS')?></legend>
        <?php foreach ($this->form->getFieldset('financial_informations') as $field): ?>
          <div class="formelm"><?php echo $field->label . "\n" . $field->input ?></div>
        <?php endforeach ?>
      </fieldset>

      <fieldset>
        <legend><?php echo JText::_('COM_JEA_DETAILS')?></legend>
        <?php foreach ($this->form->getFieldset('details') as $field): ?>
          <div class="formelm"><?php echo $field->label . "\n" . $field->input ?></div>
        <?php endforeach ?>
        <div class="clr"></div>

        <fieldset class="amenities">
          <legend><?php echo JText::_('COM_JEA_AMENITIES')?></legend>
          <div class="clr"></div>
          <?php echo $this->form->getInput('amenities') ?>
          <div class="clr"></div>
        </fieldset>

      </fieldset>

      <?php if ($user->authorise('core.edit.state', 'com_jea')): ?>
      <fieldset>
        <legend><?php echo JText::_('COM_JEA_PUBLICATION_INFO')?></legend>
        <div class="formelm">
        <?php echo $this->form->getLabel('published') ?> <?php echo $this->form->getInput('published') ?>
        </div>
        <div class="formelm">
        <?php echo $this->form->getLabel('language') ?> <?php echo $this->form->getInput('language') ?>
        </div>
        
        <div class="formelm">
        <?php echo $this->form->getLabel('featured') ?> <?php echo $this->form->getInput('featured') ?>
        </div>
        
        <div class="formelm">
        <?php echo $this->form->getLabel('slogan_id') ?> <?php echo $this->form->getInput('slogan_id') ?>
        </div>
        
        <div class="formelm">
        <?php echo $this->form->getLabel('created') ?> <?php echo $this->form->getInput('created') ?>
        </div>

        <div class="formelm">
        <?php echo $this->form->getLabel('modified') ?> <?php echo $this->form->getInput('modified') ?>
        </div>
      </fieldset>
      <?php endif ?>

      <fieldset>
      <legend><?php echo JText::_('COM_JEA_PICTURES')?></legend>
      <?php echo $this->form->getInput('images') ?>
      </fieldset>

      <div class="formelm">
      <?php echo $this->form->getLabel('notes') ?> <?php echo $this->form->getInput('notes') ?>
      </div>

    </fieldset>

    <div class="formelm-buttons clr">
      <button type="button" onclick="Joomla.submitbutton('property.apply')">
        <?php echo JText::_('JSAVE') ?>
      </button>
      <button type="button" onclick="Joomla.submitbutton('property.cancel')">
        <?php echo JText::_('JCANCEL') ?>
      </button>

      <input type="hidden" name="task" value="" />
      <input type="hidden" name="return" value="<?php echo JRequest::getCmd('return') ?>" />
      <?php echo JHtml::_('form.token') ?>
    </div>

  </form>
</div>