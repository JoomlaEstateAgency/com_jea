<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2020 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Plugin\PluginHelper;

defined('_JEXEC') or die;

/**
 * @var $this JeaViewForm
 */

HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('behavior.formvalidator');

$this->form->setFieldAttribute('description', 'buttons', 'false');

$user = Factory::getApplication()->getIdentity();
$uri = JUri::getInstance();
?>
<script type="text/javascript">
  Joomla.submitbutton = function (task) {
    if (task == 'property.cancel' || document.formvalidator.isValid(document.getElementById('adminForm'))) {
        <?php // echo $this->form->getField('description')->save() ?>
      Joomla.submitform(task);
    } else {
      alert('<?php echo $this->escape(Text::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
    }
  }
</script>

<div class="edit property<?php echo $this->escape($this->params->get('pageclass_sfx')) ?>">

    <?php if ($this->item->id): ?>
      <p>
        <a href="javascript:Joomla.submitbutton('property.cancel');"><?php echo Text::_('COM_JEA_RETURN_TO_THE_LIST') ?></a>
      </p>
    <?php endif ?>

  <form action="<?php echo (string)$uri ?>" method="post" name="adminForm" id="adminForm"
        class="form-validate" enctype="multipart/form-data">

    <fieldset>
      <legend>
          <?php echo empty($this->item->id) ? Text::_('COM_JEA_NEW_PROPERTY') : Text::sprintf('COM_JEA_EDIT_PROPERTY', $this->item->id) ?>
      </legend>

      <div
          class="formelm"><?php echo $this->form->getLabel('ref') ?><?php echo $this->form->getInput('ref') ?></div>
      <div
          class="formelm"><?php echo $this->form->getLabel('title') ?><?php echo $this->form->getInput('title') ?></div>
      <div
          class="formelm"><?php echo $this->form->getLabel('transaction_type') ?><?php echo $this->form->getInput('transaction_type') ?></div>
      <div
          class="formelm"><?php echo $this->form->getLabel('type_id') ?><?php echo $this->form->getInput('type_id') ?></div>

        <?php echo $this->form->getLabel('description') ?>
        <?php echo $this->form->getInput('description') ?>

      <fieldset>
        <legend><?php echo Text::_('COM_JEA_LOCALIZATION') ?></legend>
          <?php foreach ($this->form->getFieldset('localization') as $field): ?>
            <div class="formelm"><?php echo $field->label . "\n" . $field->input ?></div>
          <?php endforeach ?>
      </fieldset>

      <fieldset>
        <legend><?php echo Text::_('COM_JEA_FINANCIAL_INFORMATIONS') ?></legend>
          <?php foreach ($this->form->getFieldset('financial_informations') as $field): ?>
            <div class="formelm"><?php echo $field->label . "\n" . $field->input ?></div>
          <?php endforeach ?>
      </fieldset>

      <fieldset>
        <legend><?php echo Text::_('COM_JEA_DETAILS') ?></legend>
          <?php foreach ($this->form->getFieldset('details') as $field): ?>
            <div class="formelm"><?php echo $field->label . "\n" . $field->input ?></div>
          <?php endforeach ?>
      </fieldset>

      <div class="clr"></div>

      <fieldset class="amenities">
        <legend><?php echo Text::_('COM_JEA_AMENITIES') ?></legend>
        <div class="clr"></div>
          <?php echo $this->form->getInput('amenities') ?>
        <div class="clr"></div>
      </fieldset>

        <?php if (PluginHelper::isEnabled('jea', 'dpe')): ?>
          <fieldset>
              <?php
              if ($this->item->dpe_energy === null) {
                  $this->item->dpe_energy = '-1';
              }

              if ($this->item->dpe_ges === null) {
                  $this->item->dpe_ges = '-1';
              }

              $energyLabel = Text::_('PLG_JEA_DPE_ENERGY_CONSUMPTION');
              $energyDesc = $energyLabel . '::' . Text::_('PLG_JEA_DPE_ENERGY_CONSUMPTION_DESC');
              $gesLabel = Text::_('PLG_JEA_DPE_EMISSIONS_GES');
              $gesDesc = $gesLabel . '::' . Text::_('PLG_JEA_DPE_EMISSIONS_GES_DESC');
              ?>
            <legend><?php echo Text::_('PLG_JEA_DPE') ?></legend>
            <div class="formelm">
              <label for="dpe_energy" class="hasTip"
                     title="<?php echo $energyDesc ?>"><?php echo $energyLabel ?> : </label>
              <input type="text" name="dpe_energy" id="dpe_energy"
                     value="<?php echo $this->item->dpe_energy ?>" class="numberbox" size="5"/>
            </div>

            <div class="formelm">
              <label for="dpe_ges" class="hasTip"
                     title="<?php echo $gesDesc ?>"><?php echo $gesLabel ?> : </label>
              <input type="text" name="dpe_ges" id="dpe_ges"
                     value="<?php echo $this->item->dpe_ges ?>" class="numberbox" size="5"/>
            </div>
          </fieldset>
        <?php endif ?>

        <?php if ($user->authorise('core.edit.state', 'com_jea')): ?>
          <fieldset>
            <legend><?php echo Text::_('COM_JEA_PUBLICATION_INFO') ?></legend>

            <div class="formelm">
                <?php echo $this->form->getLabel('published') ?><?php echo $this->form->getInput('published') ?>
            </div>

            <div class="formelm">
                <?php echo $this->form->getLabel('language') ?><?php echo $this->form->getInput('language') ?>
            </div>

            <div class="formelm">
                <?php echo $this->form->getLabel('featured') ?><?php echo $this->form->getInput('featured') ?>
            </div>

            <div class="formelm">
                <?php echo $this->form->getLabel('slogan_id') ?><?php echo $this->form->getInput('slogan_id') ?>
            </div>

            <div class="formelm">
                <?php echo $this->form->getLabel('created') ?><?php echo $this->form->getInput('created') ?>
            </div>

            <div class="formelm">
                <?php echo $this->form->getLabel('modified') ?><?php echo $this->form->getInput('modified') ?>
            </div>

            <div class="formelm">
                <?php echo $this->form->getLabel('publish_up') ?><?php echo $this->form->getInput('publish_up') ?>
            </div>

            <div class="formelm">
                <?php echo $this->form->getLabel('publish_down') ?><?php echo $this->form->getInput('publish_down') ?>
            </div>
          </fieldset>
        <?php endif ?>

      <fieldset>
        <legend><?php echo Text::_('COM_JEA_PICTURES') ?></legend>
          <?php LayoutHelper::$defaultBasePath = JPATH_COMPONENT_ADMINISTRATOR . '/layouts'; // Find layout path into JEA admin directory ?>
          <?php echo $this->form->getInput('images') ?>
          <?php LayoutHelper::$defaultBasePath = ''; // Restore default base path ?>
      </fieldset>

      <div class="formelm">
          <?php echo $this->form->getLabel('notes') ?><?php echo $this->form->getInput('notes') ?>
      </div>

    </fieldset>

    <div class="formelm-buttons clr">
      <button type="button"
              onclick="Joomla.submitbutton('property.apply')"><?php echo Text::_('JSAVE') ?></button>
      <button type="button"
              onclick="Joomla.submitbutton('property.cancel')"><?php echo Text::_('JCANCEL') ?></button>
      <input type="hidden" name="task" value=""/>
      <input type="hidden" name="return"
             value="<?php echo Factory::getApplication()->input->getCmd('return') ?>"/>
        <?php echo HTMLHelper::_('form.token') ?>
    </div>
  </form>
</div>
