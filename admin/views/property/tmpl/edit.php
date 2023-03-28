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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\Event\Event;

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
HTMLHelper::_('formbehavior.chosen', 'select');
?>
<div id="ajaxupdating">
  <h3><?php echo Text::_('COM_JEA_FEATURES_UPDATED_WARNING') ?></h3>
</div>

<form
    action="<?php echo Route::_('index.php?option=com_jea&layout=edit&id=' . (int)$this->item->id) ?>"
    method="post" id="adminForm"
    class="form-validate" enctype="multipart/form-data">

  <div class="card">
    <div class="card-body">
      <div class="form-inline form-inline-header">
          <?php echo $this->form->renderField('title') ?>
          <?php echo $this->form->renderField('ref') ?>
          <?php echo $this->form->renderField('alias') ?>
      </div>
      <div class="row">
        <div class="col-8">
          <fieldset class="adminform">
              <?php echo $this->form->renderField('transaction_type') ?>
              <?php echo $this->form->renderField('type_id') ?>
              <?php echo $this->form->renderField('description') ?>
          </fieldset>
        </div>
        <div class="col-4">
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
    </div>
  </div>

  <div class="card mt-4">
    <div class="card-header">
      <h2><?php echo Text::_('COM_JEA_FINANCIAL_INFORMATIONS') ?></h2>
    </div>
    <div class="card-body">
        <?php foreach ($this->form->getFieldset('financial_informations') as $field) echo $field->renderField() ?>
    </div>
  </div>

  <div class="card mt-4">
    <div class="card-header">
      <h2><?php echo Text::_('COM_JEA_LOCALIZATION') ?></h2>
    </div>
    <div class="card-body">
        <?php foreach ($this->form->getFieldset('localization') as $field) echo $field->renderField() ?>
    </div>
  </div>

  <div class="card mt-4">
    <div class="card-header">
      <h2><?php echo Text::_('COM_JEA_DETAILS') ?></h2>
    </div>
    <div class="card-body">
        <?php foreach ($this->form->getFieldset('details') as $field) echo $field->renderField() ?>
    </div>
  </div>

  <div class="card mt-4">
    <div class="card-header">
      <h2><?php echo Text::_('COM_JEA_AMENITIES') ?></h2>
    </div>
    <div class="card-body">
        <?php echo $this->form->getInput('amenities') ?>
    </div>
  </div>

  <div class="card mt-4">
    <div class="card-header">
      <h2><?php echo Text::_('COM_JEA_PUBLICATION_INFO') ?></h2>
    </div>
    <div class="card-body">
        <?php foreach ($this->form->getFieldset('publication') as $field) echo $field->renderField() ?>
    </div>
  </div>

  <?php if ($this->canDo->get('core.admin')) : ?>
    <div class="card mt-4">
      <div class="card-header">
        <h2><?php echo Text::_('COM_JEA_FIELDSET_RULES') ?></h2>
      </div>
      <div class="card-body">
          <?php echo $this->form->getInput('rules') ?>
      </div>
    </div>
  <?php endif ?>

  <div>
    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="return"
           value="<?php echo Factory::getApplication()->input->getCmd('return') ?>"/>
      <?php echo HTMLHelper::_('form.token') ?>
  </div>
</form>
