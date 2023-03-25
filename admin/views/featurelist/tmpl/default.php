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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

/**
 * @var $this JeaViewFeaturelist
 */
HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirection = $this->escape($this->state->get('list.direction'));
$saveOrder = $listOrder == 'f.ordering';

if ($saveOrder) {
    $saveOrderingUrl = 'index.php?option=com_jea&task=featurelist.saveOrderAjax&tmpl=component';
    HTMLHelper::_('sortablelist.sortable', 'featureList', 'adminForm', strtolower($listDirection), $saveOrderingUrl);
}

HTMLHelper::stylesheet('media/com_jea/css/jea.admin.css');
?>

<form action="<?php echo Route::_('index.php?option=com_jea&view=featurelist') ?>" method="post"
      name="adminForm" id="adminForm">

<?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)) ?>

<?php if (empty($this->items)) : ?>
  <div class="alert alert-no-items">
      <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
  </div>
<?php else : ?>
  <table class="table table-striped" id="featureList">
    <thead>
    <tr>
      <th width="1%" class="nowrap center hidden-phone">
          <?php echo HTMLHelper::_('searchtools.sort', '', 'f.ordering', $listDirection, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
      </th>
      <th width="1%">
          <?php echo HTMLHelper::_('grid.checkall'); ?>
      </th>
      <th width="88%">
          <?php echo HTMLHelper::_('searchtools.sort', 'COM_JEA_FIELD_' . $this->state->get('feature.name') . '_LABEL', 'f.value', $listDirection, $listOrder) ?>
      </th>
        <?php if ($this->state->get('language_enabled')): ?>
          <th width="5%" class="nowrap">
              <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_LANGUAGE', 'l.title', $listDirection, $listOrder); ?>
          </th>
        <?php endif ?>
      <th width="5%" class="nowrap">
          <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'f.id', $listDirection, $listOrder) ?>
      </th>
    </tr>
    </thead>

    <tfoot>
    <tr>
      <td colspan="<?php echo $this->state->get('language_enabled') ? 5 : 4 ?>"></td>
    </tr>
    </tfoot>

    <tbody>
    <?php foreach ($this->items as $i => $item) : ?>
        <?php
        $canEdit = $this->user->authorise('core.edit');
        $canChange = $this->user->authorise('core.edit.state');
        ?>
      <tr class="row<?php echo $i % 2 ?>">
        <td class="order nowrap center hidden-phone">
            <?php
            $iconClass = '';
            if (!$canChange) {
                $iconClass = ' inactive';
            } elseif (!$saveOrder) {
                $iconClass = ' inactive tip-top hasTooltip" title="' . HTMLHelper::_('tooltipText', 'JORDERINGDISABLED');
            }
            ?>
          <span class="sortable-handler<?php echo $iconClass ?>">
      <span class="icon-menu" aria-hidden="true"></span>
    </span>
            <?php if ($canChange && $saveOrder) : ?>
              <input type="text" style="display:none" name="order[]" size="5"
                      value="<?php echo $item->ordering ?>"
                      class="width-20 text-area-order"/>
            <?php endif ?>
        </td>
        <td class="center">
            <?php echo HTMLHelper::_('grid.id', $i, $item->id) ?>
        </td>
        <td>
            <?php if ($canEdit) : ?>
              <a href="<?php echo Route::_('index.php?option=com_jea&task=feature.edit&id=' . (int)$item->id . '&feature=' . $this->state->get('feature.name')); ?>">
                  <?php echo $this->escape($item->value) ?>
              </a>
            <?php else : ?>
                <?php echo $this->escape($item->value) ?>
            <?php endif ?>
        </td>
          <?php if ($this->state->get('language_enabled')): ?>
            <td>
                <?php if ($item->language == '*'): ?>
                    <?php echo Text::alt('JALL', 'language') ?>
                <?php else: ?>
                    <?php echo $item->language_title ? $this->escape($item->language_title) : Text::_('JUNDEFINED') ?>
                <?php endif ?>
            </td>
          <?php endif ?>
        <td class="center"><?php echo $item->id ?></td>
      </tr>
    <?php endforeach ?>
    </tbody>
  </table>
    <?php echo $this->pagination->getListFooter() ?>
<?php endif ?>

  <div>
    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="boxchecked" value="0"/>
    <input type="hidden" name="feature" value="<?php echo $this->state->get('feature.name') ?>"/>
      <?php echo HTMLHelper::_('form.token') ?>
  </div>
</form>
