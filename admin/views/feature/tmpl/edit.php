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
use Joomla\CMS\Router\Route;

/**
 * @var $this JeaViewFeature
 */
$app = Factory::getApplication();
HTMLHelper::_('formbehavior.chosen', 'select');
HTMLHelper::stylesheet('media/com_jea/css/jea.admin.css');

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate');
?>
<form
    action="<?php echo Route::_('index.php?option=com_jea&layout=edit&id=' . (int)$this->item->id) ?>"
    method="post" name="adminForm" id="adminForm" class="form-validate">

  <div class="form-horizontal">
      <?php foreach ($this->form->getFieldset('feature') as $field): ?>
          <?php echo $field->renderField() ?>
      <?php endforeach ?>
  </div>

  <div>
    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="feature" value="<?php echo $this->state->get('feature.name') ?>"/>
    <input type="hidden" name="return" value="<?php echo $app->input->getCmd('return') ?>"/>
      <?php echo HTMLHelper::_('form.token') ?>
  </div>
</form>
