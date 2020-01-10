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

/**
 * @var $this JeaViewProperty
 */

$dispatcher = JDispatcher::getInstance();
JPluginHelper::importPlugin( 'jea' );

JHtml::_('behavior.framework');
JHtml::stylesheet('media/com_jea/css/jea.admin.css');
JHtml::script('media/com_jea/js/property.form.js');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tabstate');
JHtml::_('formbehavior.chosen', 'select');
?>
<div id="ajaxupdating">
	<h3><?php echo JText::_('COM_JEA_FEATURES_UPDATED_WARNING')?></h3>
</div>

<form action="<?php echo JRoute::_('index.php?option=com_jea&layout=edit&id='.(int) $this->item->id) ?>" method="post" id="adminForm"
	class="form-validate" enctype="multipart/form-data">

	<div class="form-inline form-inline-header">
		<?php echo $this->form->renderField('title') ?>
		<?php echo $this->form->renderField('ref') ?>
		<?php echo $this->form->renderField('alias') ?>
	</div>

	<div class="form-horizontal">

		<?php echo JHtml::_('bootstrap.startTabSet', 'propertyTab', array('active' => 'general')) ?>

		<?php echo JHtml::_('bootstrap.addTab', 'propertyTab', 'general', JText::_('COM_JEA_CONFIG_GENERAL')) ?>
		<div class="row-fluid">
			<div class="span8">
				<fieldset class="adminform">
					<?php echo $this->form->renderField('transaction_type') ?>
					<?php echo $this->form->renderField('type_id') ?>
					<?php echo $this->form->renderField('description') ?>
				</fieldset>
			</div>
			<div class="span4">
				<fieldset class="form-vertical">
					<?php echo $this->form->renderField('published') ?>
					<?php echo $this->form->renderField('featured') ?>
					<?php echo $this->form->renderField('access') ?>
					<?php echo $this->form->renderField('language') ?>
					<?php echo $this->form->renderField('slogan_id') ?>
				</fieldset>

				<?php echo JHtml::_('sliders.start', 'property-sliders', array('useCookie'=>1)) ?>
				<?php $dispatcher->trigger('onAfterStartPanels', array(&$this->item)) ?>

				<?php echo JHtml::_('sliders.panel', JText::_('COM_JEA_PICTURES'), 'picture-pane') ?>
				<fieldset>
					<?php echo $this->form->getInput('images') ?>
				</fieldset>

				<?php echo JHtml::_('sliders.panel', JText::_('COM_JEA_NOTES'), 'note-pane') ?>
				<fieldset class="form-vertical panelform">
					<?php echo $this->form->renderField('notes') ?>

				</fieldset>
				<?php $dispatcher->trigger('onBeforeEndPanels', array(&$this->item)) ?>
				<?php echo JHtml::_('sliders.end') ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab') ?>

		<?php echo JHtml::_('bootstrap.addTab', 'propertyTab', 'details', JText::_('COM_JEA_DETAILS')) ?>
			<div class="row-fluid">
				<div class="span4">
					<fieldset>
						<legend><?php echo JText::_('COM_JEA_FINANCIAL_INFORMATIONS')?></legend>
						<?php foreach ($this->form->getFieldset('financial_informations') as $field) echo $field->renderField() ?>
					</fieldset>
					<fieldset class="advantages">
						<legend><?php echo JText::_('COM_JEA_AMENITIES')?></legend>
						<?php echo $this->form->getInput('amenities') ?>
					</fieldset>
				</div>

				<div class="span4">
					<fieldset>
						<legend><?php echo JText::_('COM_JEA_LOCALIZATION')?></legend>
						<?php foreach ($this->form->getFieldset('localization') as $field) echo $field->renderField() ?>
					</fieldset>
				</div>

				<div class="span4">
					<fieldset>
						<?php foreach ($this->form->getFieldset('details') as $field) echo $field->renderField() ?>
					</fieldset>
				</div>
			</div>
		<?php echo JHtml::_('bootstrap.endTab') ?>

		<?php echo JHtml::_('bootstrap.addTab', 'propertyTab', 'publication', JText::_('COM_JEA_PUBLICATION_INFO')) ?>
			<?php foreach ($this->form->getFieldset('publication') as $field) echo $field->renderField() ?>
		<?php echo JHtml::_('bootstrap.endTab') ?>

		<?php if ($this->canDo->get('core.admin')): ?>
		<?php echo JHtml::_('bootstrap.addTab', 'propertyTab', 'permissions', JText::_('COM_JEA_FIELDSET_RULES')) ?>
			<?php echo $this->form->getInput('rules') ?>
		<?php echo JHtml::_('bootstrap.endTab') ?>
		<?php endif ?>

		<?php echo JHtml::_('bootstrap.endTabSet'); ?>

	</div>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="return" value="<?php echo JFactory::getApplication()->input->getCmd('return') ?>" />
		<?php echo JHtml::_('form.token') ?>
	</div>
</form>
