<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2017 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

JHtml::stylesheet('media/com_jea/css/jea.admin.css');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

$rowsCount = count($this->items) ;
$altrow = 1;

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirection = $this->escape($this->state->get('list.direction'));
$saveOrder = $listOrder == 'f.ordering';

$filters = $this->state->get('feature.filters', '');
if (!empty($filters)) {
    $filters = explode(',', $filters);
} else {
    $filters = array();
}

?>

<form action="<?php echo JRoute::_('index.php?option=com_jea&view=featurelist') ?>" method="post"
      name="adminForm" id="adminForm">

<?php if ((float) JVERSION < 3): ?>
  <fieldset id="filter-bar">
    <div class="clr"></div>
    <div class="filter-search fltlft">
      <label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
      <input type="text" name="filter_search" id="filter_search"
             value="<?php echo $this->escape($this->state->get('filter.search')); ?>" />
      <button type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
      <button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
    </div>

    <div class="filter-select fltrt">
    <?php
    foreach ($filters as $filter) {
        $filter = explode(':', $filter);
        $filterKey = $filter[0];
        $filterHtml = $filter[1];
        echo JHtml::_($filterHtml, $this->state->get('filter.'.$filterKey), $filterKey, 'onchange="document.adminForm.submit();"' );
    }
    ?>
    <?php if ($this->state->get('language_enabled')): ?>
      <select name="filter_language" class="inputbox" onchange="this.form.submit()">
        <option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE');?></option>
        <?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'));?>
      </select>
    <?php endif; ?>
    </div>
    <div class="clr"></div>
  </fieldset>
<?php endif ?>

  <?php if (!empty( $this->sidebar)) : ?>
  <div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar ?>
    <hr class="hr-condensed" />
    <?php
    foreach ($filters as $filter) {
        $filter = explode(':', $filter);
        $filterKey = $filter[0];
        $filterHtml = $filter[1];
        echo JHtml::_($filterHtml, $this->state->get('filter.'.$filterKey), $filterKey, 'onchange="document.adminForm.submit();"' );
    }
    ?>
    <?php if ($this->state->get('language_enabled')): ?>
    <select name="filter_language" class="inputbox span12 small" onchange="this.form.submit()">
      <option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE');?></option>
      <?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'));?>
    </select>
    <?php endif ?>
  </div>
  <?php endif ?>

  <div id="j-main-container" class="span10">
  <?php if ((float) JVERSION > 3): ?>
    <div id="filter-bar" class="btn-toolbar">
      <div class="filter-search btn-group pull-left">
        <label for="filter_search" class="element-invisible"><?php echo JText::_('JSEARCH_FILTER_LABEL') ?></label>
        <input type="text" name="filter_search"
          placeholder="<?php echo JText::_('COM_JEA_PROPERTIES_SEARCH_FILTER_DESC'); ?>"
          id="filter_search"
          value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
          title="<?php echo JText::_('COM_JEA_PROPERTIES_SEARCH_FILTER_DESC'); ?>" />
      </div>
      <div class="btn-group pull-left hidden-phone">
        <button class="btn tip hasTooltip" type="submit"
          title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>">
          <i class="icon-search"></i>
        </button>
        <button class="btn tip hasTooltip" type="button"
          onclick="document.id('filter_search').value='';this.form.submit();"
          title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>">
          <i class="icon-remove"></i>
        </button>
      </div>
      <div class="btn-group pull-right hidden-phone">
        <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC') ?></label>
        <?php echo $this->pagination->getLimitBox() ?>
      </div>
    </div>
    <?php endif ?>

  <table class="adminlist table table-striped">
    <thead>
      <tr>
        <th width="1%">
          <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
        </th>
        <th width="88%">
          <?php echo JHTML::_('grid.sort', 'COM_JEA_FIELD_'.$this->state->get('feature.name').'_LABEL', 'f.value', $listDirection , $listOrder ) ?>
        </th>
        <th width="10%">
          <?php echo JHTML::_('grid.sort', 'JGRID_HEADING_ORDERING', 'f.ordering', $listDirection , $listOrder ) ?>
          <?php if ($saveOrder) :?>
          <?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'featurelist.saveorder'); ?>
          <?php endif; ?>
        </th>
        <?php if ($this->state->get('language_enabled')): ?>
        <th width="5%">
          <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'language', $listDirection, $listOrder); ?>
        </th>
        <?php endif; ?>
        <th width="1%">
          <?php echo JHTML::_('grid.sort', 'JGRID_HEADING_ID', 'f.id', $listDirection , $listOrder ) ?>
        </th>
      </tr>
    </thead>

    <tfoot>
      <tr>
        <td colspan="<?php echo $this->state->get('language_enabled') ?  5: 4 ?>"><?php echo $this->pagination->getListFooter() ?>
        </td>
      </tr>
    </tfoot>

    <tbody>
    <?php foreach ($this->items as $i => $item) : ?>
    <?php
    $altrow = ( $altrow == 1 )? 0 : 1;
    $canCreate  = $this->user->authorise('core.create');
    $canEdit  = $this->user->authorise('core.edit');
    $canChange  = $this->user->authorise('core.edit.state');
    ?>

      <tr class="row<?php echo $altrow ?>">
        <td><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
        <td>
        <?php if ($canEdit) : ?>
          <a href="<?php echo JRoute::_('index.php?option=com_jea&task=feature.edit&id='.(int) $item->id . '&feature='. $this->state->get('feature.name')); ?>">
          <?php echo $this->escape($item->value) ?> </a>
        <?php else : ?>
          <?php echo $this->escape($item->value) ?>
        <?php endif ?>
        </td>

        <td class="order">
        <?php if ($canChange) : ?>
          <?php if ($saveOrder) :?>
            <?php if ($listDirection == 'asc') : ?>
              <span><?php echo $this->pagination->orderUpIcon($i, true, 'featurelist.orderup', 'JLIB_HTML_MOVE_UP', $saveOrder) ?></span>
              <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'featurelist.orderdown', 'JLIB_HTML_MOVE_DOWN', $saveOrder) ?></span>
            <?php elseif ($listDirection == 'desc') : ?>
              <span><?php echo $this->pagination->orderUpIcon($i, true, 'featurelist.orderdown', 'JLIB_HTML_MOVE_UP', $saveOrder) ?></span>
              <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'featurelist.orderup', 'JLIB_HTML_MOVE_DOWN', $saveOrder) ?></span>
            <?php endif ?>
          <?php endif; ?>
          <input type="text" name="order[]" size="5" value="<?php echo $item->ordering ?>" <?php if (!$saveOrder) echo 'disabled="disabled"' ?> class="text-area-order" />
        <?php else : ?>
          <?php echo $item->ordering ?>
        <?php endif ?>
        </td>
        <?php if ($this->state->get('language_enabled')): ?>
        <td class="center">
        <?php if ($item->language == '*'): ?>
          <?php echo JText::alt('JALL', 'language') ?>
        <?php else: ?>
          <?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED') ?>
        <?php endif ?>
        </td>
        <?php endif; ?>
        <td class="center"><?php echo $item->id ?></td>
      </tr>
      <?php endforeach ?>
    </tbody>
  </table>
  </div>

  <div>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="feature" value="<?php echo $this->state->get('feature.name')?>" />
    <input type="hidden" name="filter_order" value="<?php echo $listOrder ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirection ?>" />
    <?php echo JHtml::_('form.token') ?>
  </div>
</form>
