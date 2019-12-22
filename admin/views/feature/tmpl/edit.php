<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2019 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * @var $this JeaViewFeature
 */
$app = JFactory::getApplication();
JHtml::_('formbehavior.chosen', 'select');
JHtml::stylesheet('media/com_jea/css/jea.admin.css');
?>
<form action="<?php echo JRoute::_('index.php?option=com_jea&layout=edit&id='.(int) $this->item->id) ?>"
	method="post" name="adminForm" id="adminForm" class="form-validate">

	<div class="form-horizontal">
	<?php foreach ($this->form->getFieldset('feature') as $field): ?>
		<?php echo $field->renderField() ?>
	<?php endforeach ?>
	</div>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="feature" value="<?php echo $this->state->get('feature.name')?>" />
		<input type="hidden" name="return" value="<?php echo $app->input->getCmd('return') ?>" />
		<?php echo JHtml::_('form.token') ?>
	</div>
</form>
