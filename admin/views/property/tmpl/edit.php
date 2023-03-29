<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2020 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Event\Event;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Plugin\PluginHelper;

/**
 * @var $this JeaViewProperty
 */

$wa = $this->document->getWebAssetManager();
$wa->useScript('form.validate');
$dispatcher = Factory::getApplication()->getDispatcher();
PluginHelper::importPlugin('jea');

HTMLHelper::stylesheet('media/com_jea/css/jea.admin.css');
HTMLHelper::script('media/com_jea/js/property.form.js');

HTMLHelper::_('bootstrap.framework');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('formbehavior.chosen', '#localization select');
?>
<div id="ajaxupdating">
  <h3><?php echo Text::_('COM_JEA_FEATURES_UPDATED_WARNING') ?></h3>
</div>

<form
    action="<?php echo Route::_('index.php?option=com_jea&layout=edit&id=' . (int)$this->item->id) ?>"
    method="post" id="adminForm"
    class="form-validate" enctype="multipart/form-data">

  <?php echo LayoutHelper::render('joomla.edit.title_alias', $this) ?>

  <div class="main-card">
    <?php echo HTMLHelper::_('uitab.startTabSet', 'property-tabs', ['active' => 'general-tab', 'recall' => true, 'breakpoint' => 768]) ?>
    <?php echo HTMLHelper::_('uitab.addTab', 'property-tabs', 'general-tab', Text::_('COM_JEA_CONFIG_GENERAL')) ?>
      <div class="row">
        <div class="col-lg-9">
          <fieldset class="adminform">
            <?php echo $this->form->renderField('ref') ?>
            <?php echo $this->form->renderField('transaction_type') ?>
            <?php echo $this->form->renderField('type_id') ?>
            <?php echo $this->form->renderField('description') ?>
          </fieldset>
        </div>
        <div class="col-lg-3">
          <fieldset class="form-vertical">
            <?php echo $this->form->renderField('published') ?>
            <?php echo $this->form->renderField('featured') ?>
            <?php echo $this->form->renderField('access') ?>
            <?php echo $this->form->renderField('language') ?>
            <?php echo $this->form->renderField('slogan_id') ?>
          </fieldset>

          <?php echo HTMLHelper::_('bootstrap.startAccordion', 'property-sliders', ['active' => 'picture-pane']) ?>
            <?php $dispatcher->dispatch('onAfterStartPanels', new Event('onAfterStartPanels', array(&$this->item))) ?>

            <?php echo HTMLHelper::_('bootstrap.addSlide', 'property-sliders', Text::_('COM_JEA_PICTURES'), 'picture-pane') ?>
              <fieldset>
                <?php echo $this->form->getInput('images') ?>
              </fieldset>
            <?php echo HTMLHelper::_('bootstrap.endSlide') ?>

            <?php echo HTMLHelper::_('bootstrap.addSlide', 'property-sliders', Text::_('COM_JEA_NOTES'), 'note-pane') ?>
              <fieldset class="form-vertical panelform">
              <?php echo $this->form->renderField('notes') ?>
              </fieldset>
            <?php echo HTMLHelper::_('bootstrap.endSlide') ?>

            <?php $dispatcher->dispatch('onBeforeEndPanels', new Event('onBeforeEndPanels', array(&$this->item))) ?>
          <?php echo HTMLHelper::_('bootstrap.endAccordion') ?>
        </div>
      </div>
    <?php echo HTMLHelper::_('uitab.endTab') ?>

    <?php echo HTMLHelper::_('uitab.addTab', 'property-tabs', 'financial-tab', Text::_('COM_JEA_FINANCIAL_INFORMATIONS')) ?>
      <?php foreach ($this->form->getFieldset('financial_informations') as $field) echo $field->renderField() ?>
    <?php echo HTMLHelper::_('uitab.endTab') ?>

    <?php echo HTMLHelper::_('uitab.addTab', 'property-tabs', 'localization-tab', Text::_('COM_JEA_LOCALIZATION')) ?>
      <div id="localization">
          <?php foreach ($this->form->getFieldset('localization') as $field) echo $field->renderField() ?>
      </div>
    <?php echo HTMLHelper::_('uitab.endTab') ?>

    <?php echo HTMLHelper::_('uitab.addTab', 'property-tabs', 'details-tab', Text::_('COM_JEA_DETAILS')) ?>
      <?php foreach ($this->form->getFieldset('details') as $field) echo $field->renderField() ?>
      <fieldset class="options-form">
        <legend><?php echo Text::_('COM_JEA_AMENITIES') ?></legend>
        <?php echo $this->form->getInput('amenities') ?>
      </fieldset>
    <?php echo HTMLHelper::_('uitab.endTab') ?>

    <?php echo HTMLHelper::_('uitab.addTab', 'property-tabs', 'info-tab', Text::_('COM_JEA_PUBLICATION_INFO')) ?>
      <?php foreach ($this->form->getFieldset('publication') as $field) echo $field->renderField() ?>
    <?php echo HTMLHelper::_('uitab.endTab') ?>

    <?php if ($this->canDo->get('core.admin')) : ?>
      <?php echo HTMLHelper::_('uitab.addTab', 'property-tabs', 'rules-tab', Text::_('COM_JEA_FIELDSET_RULES')) ?>
        <?php echo $this->form->getInput('rules') ?>
      <?php echo HTMLHelper::_('uitab.endTab') ?>
    <?php endif ?>

    <?php echo HTMLHelper::_('uitab.endTabSet') ?>
  </div>

  <div>
    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="return"
           value="<?php echo Factory::getApplication()->input->getCmd('return') ?>"/>
      <?php echo HTMLHelper::_('form.token') ?>
  </div>
</form>
