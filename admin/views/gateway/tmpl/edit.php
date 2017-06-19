<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2017 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
// JHtml::stylesheet('media/com_jea/css/jea.admin.css');
?>

<form action="" method="post" id="adminForm" class="form-validate">

  <div class="form-horizontal">

    <div class="control-group">
      <div class="control-label"><?php echo $this->form->getLabel('title') ?></div>
      <div class="controls"><div class="input-append"><?php echo $this->form->getInput('title') ?></div></div>
    </div>
   <div class="control-group">
      <div class="control-label"><?php echo $this->form->getLabel('provider') ?></div>
      <div class="controls"><div class="input-append"><?php echo $this->form->getInput('provider') ?></div></div>
    </div>
    <div class="control-group">
      <div class="control-label"><?php echo $this->form->getLabel('published') ?></div>
      <div class="controls"><div class="input-append"><?php echo $this->form->getInput('published') ?></div></div>
    </div>
  </div>

  <fieldset>
    <legend><?php echo JText::_('COM_JEA_GATEWAY_PARAMS')?></legend>
    <?php if (!empty($this->item->id)): ?>
    <div class="form-horizontal">
        <?php foreach ($this->form->getGroup('params') as $field): ?>
          <div class="control-group">
            <div class="control-label"><?php echo $field->label ?></div>
            <div class="controls">
              <div class="input-append"><?php echo $field->input ?></div>
            </div>
          </div>
        <?php endforeach ?>
    </div>
    <?php else : ?>
    <p><?php echo JText::_('COM_JEA_GATEWAY_PARAMS_APPEAR_AFTER_SAVE')?></p>
    <?php endif?>
  </fieldset>

  <div>
    <input type="hidden" name="task" value="" />
    <?php echo $this->form->getInput('id') ?>
    <?php echo $this->form->getInput('type') ?>
    <?php echo JHtml::_('form.token') ?>
  </div>

</form>
