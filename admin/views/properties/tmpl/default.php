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

JHtml::_('formbehavior.chosen', 'select');

$rowsCount = count($this->items) ;
$altrow = 1;

$listOrder     = $this->escape($this->state->get('list.ordering'));
$listDirection = $this->escape($this->state->get('list.direction'));
$saveOrder     = $listOrder == 'p.ordering';

$transactionType = $this->state->get('filter.transaction_type');
?>

<form action="<?php echo JRoute::_('index.php?option=com_jea&view=properties') ?>" method="post"
      name="adminForm" id="adminForm">

<?php if ((float) JVERSION < 3): ?>
  <fieldset id="filter-bar">
    <div class="clr"></div>
    <div class="filter-search fltlft">
      <label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
      <input type="text" name="filter_search" id="filter_search"
             value="<?php echo $this->escape($this->state->get('filter.search', '')); ?>"
             title="<?php echo JText::_('COM_JEA_PROPERTIES_SEARCH_FILTER_DESC'); ?>" />
      <button type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
      <button type="button" onclick="document.id('filter_search').value='';this.form.submit();">
      <?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
      </button>
    </div>

    <div class="filter-select fltrt">
      <select name="filter_transaction_type" class="inputbox" onchange="this.form.submit()">
        <option value=""> - <?php echo JText::_('COM_JEA_FIELD_TRANSACTION_TYPE_LABEL')?> - </option>
        <option value="RENTING"<?php if ($transactionType == 'RENTING') echo ' selected="selected"'?>>
          <?php echo JText::_('COM_JEA_OPTION_RENTING')?>
        </option>
        <option value="SELLING"<?php if ($transactionType == 'SELLING') echo ' selected="selected"'?>>
          <?php echo JText::_('COM_JEA_OPTION_SELLING')?>
        </option>
        <?php // TODO: call plugin entry to add more transaction types  ?>
      </select>

      <?php echo JHtml::_('features.types', $this->state->get('filter.type_id', 0), 'filter_type_id', 'onchange="document.adminForm.submit();"' ) ?>
      <?php echo JHtml::_('features.departments', $this->state->get('filter.department_id', 0), 'filter_department_id', 'onchange="document.adminForm.submit();"' ) ?>

      <?php if ($this->params->get('relationship_dpts_towns_area', 0)): ?>
      <?php echo JHtml::_('features.towns', $this->state->get('filter.town_id'), 'filter_town_id', 'onchange="document.adminForm.submit();"', $this->state->get('filter.department_id', 0) ) ?>
      <?php else: ?>
      <?php echo JHtml::_('features.towns', $this->state->get('filter.town_id'), 'filter_town_id', 'onchange="document.adminForm.submit();"' ) ?>
      <?php endif ?>
      <select name="filter_language" class="inputbox" onchange="this.form.submit()">
        <option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE');?></option>
        <?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'));?>
      </select>
    </div>
    <div class="clr"></div>
  </fieldset>
<?php endif ?>

<?php if (!empty( $this->sidebar)) : ?>
<div id="j-sidebar-container" class="span2">
  <?php echo $this->sidebar ?>
  <hr />
  <div class="filter-select hidden-phone">
    <h4 class="page-header"><?php echo JText::_('JSEARCH_FILTER_LABEL') ?></h4>
    <select name="filter_transaction_type" class="inputbox span12 small" onchange="this.form.submit()">
      <option value=""> - <?php echo JText::_('COM_JEA_FIELD_TRANSACTION_TYPE_LABEL')?> - </option>
      <option value="RENTING"<?php if ($transactionType == 'RENTING') echo ' selected="selected"'?>>
        <?php echo JText::_('COM_JEA_OPTION_RENTING')?>
      </option>
      <option value="SELLING"<?php if ($transactionType == 'SELLING') echo ' selected="selected"'?>>
        <?php echo JText::_('COM_JEA_OPTION_SELLING')?>
      </option>
      <?php // TODO: call plugin entry to add more transaction types  ?>
    </select>
    <hr class="hr-condensed" />
    <?php echo JHtml::_('features.types', $this->state->get('filter.type_id', 0), 'filter_type_id', 'onchange="document.adminForm.submit();"' ) ?>
    <hr class="hr-condensed" />
    <?php echo JHtml::_('features.departments', $this->state->get('filter.department_id', 0), 'filter_department_id', 'onchange="document.adminForm.submit();"' ) ?>
    <hr class="hr-condensed" />
    <?php if ($this->params->get('relationship_dpts_towns_area', 0)): ?>
    <?php echo JHtml::_('features.towns', $this->state->get('filter.town_id'), 'filter_town_id', 'onchange="document.adminForm.submit();"', $this->state->get('filter.department_id', 0) ) ?>
    <?php else: ?>
    <?php echo JHtml::_('features.towns', $this->state->get('filter.town_id'), 'filter_town_id', 'onchange="document.adminForm.submit();"' ) ?>
    <?php endif ?>
    <hr class="hr-condensed" />
    <select name="filter_language" class="inputbox span12 small" onchange="this.form.submit()">
      <option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE');?></option>
      <?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'));?>
    </select>
  </div>
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
        <th width="1%" class="nowrap">
          <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
        </th>
        <th width="10%" class="nowrap">
          <?php echo JHtml::_('grid.sort', 'COM_JEA_FIELD_REF_LABEL', 'p.ref', $listDirection , $listOrder ) ?>
        </th>
        <th class="nowrap">
          <?php echo JText::_('COM_JEA_FIELD_PROPERTY_TYPE_LABEL') ?>
        </th>
        <th width="27%" class="nowrap">
          <?php echo JText::_('COM_JEA_FIELD_ADDRESS_LABEL') ?>
        </th>
        <th width="10%" class="nowrap">
          <?php echo JText::_('COM_JEA_FIELD_TOWN_LABEL') ?>
        </th>
        <th width="10%" class="nowrap">
          <?php echo JText::_('COM_JEA_FIELD_DEPARTMENT_LABEL') ?>
        </th>
        <th width="10%" class="nowrap">
          <?php echo JHtml::_('grid.sort', 'COM_JEA_FIELD_PRICE_LABEL', 'p.price', $listDirection , $listOrder ) ?>
        </th>
        <th width="1%" class="nowrap">
          <?php echo JHtml::_('grid.sort', 'JFEATURED', 'p.featured', $listDirection , $listOrder ) ?>
        </th>
        <th width="1%" class="nowrap">
          <?php echo JHtml::_('grid.sort', 'JSTATUS', 'p.published', $listDirection , $listOrder ) ?>
        </th>
        <th width="5%" class="nowrap">
          <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ACCESS', 'access_level', $listDirection, $listOrder); ?>
        </th>
        <th width="5%" class="nowrap">
          <?php echo JHTML::_('grid.sort', 'JGRID_HEADING_ORDERING', 'p.ordering', $listDirection , $listOrder ) ?>
          <?php if ($saveOrder) :?>
          <?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'properties.saveorder'); ?>
          <?php endif; ?>
        </th>
        <th width="10%" class="nowrap">
          <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_CREATED_BY', 'author', $listDirection , $listOrder ) ?>
        </th>
        <th width="5%" class="nowrap">
          <?php echo JHtml::_('grid.sort', 'JDATE', 'p.created', $listDirection , $listOrder ) ?>
        </th>
        <th width="1%" class="nowrap">
          <?php echo JHtml::_('grid.sort', 'JGLOBAL_HITS', 'p.hits', $listDirection , $listOrder ) ?>
        </th>
        <th width="5%" class="nowrap">
            <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'language', $listDirection, $listOrder); ?>
        </th>
        <th width="1%" class="nowrap">
          <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'p.id', $listDirection , $listOrder ) ?>
        </th>
      </tr>
    </thead>

    <tfoot>
      <tr>
        <td colspan="16"><?php echo $this->pagination->getListFooter() ?></td>
      </tr>
    </tfoot>

    <tbody>
    <?php foreach ($this->items as $i => $item) : ?>

    <?php
    $altrow = ( $altrow == 1 )? 0 : 1;
    $canCreate  = $this->user->authorise('core.create',   'com_jea.property.'.$item->id);
    $canEdit  = $this->user->authorise('core.edit',     'com_jea.property.'.$item->id);
    $canCheckin = $this->user->authorise('core.manage',   'com_checkin') || $item->checked_out == $this->user->id || $item->checked_out == 0;
    $canEditOwn = $this->user->authorise('core.edit.own',   'com_jea.property.'.$item->id) && $item->created_by == $this->user->id;
    $canChange  = $this->user->authorise('core.edit.state', 'com_jea.property.'.$item->id) && $canCheckin;
    ?>

      <tr class="row<?php echo $altrow ?>">
        <td><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
        <td>
        <?php if ($item->checked_out) : ?>
          <?php echo JHtml::_('jgrid.checkedout', $i, $item->author, $item->checked_out_time, 'properties.', $canCheckin); ?>
        <?php endif ?>
        <?php if ($canEdit || $canEditOwn) : ?>
          <a href="<?php echo JRoute::_('index.php?option=com_jea&task=property.edit&id='.(int) $item->id); ?>">
          <?php echo $this->escape($item->ref); ?> </a>
        <?php else : ?>
          <?php echo $this->escape($item->ref); ?>
        <?php endif ?>
        </td>

        <td><?php echo $this->escape( $item->type ) ?></td>
        <td><?php echo $this->escape( $item->address ) ?></td>
        <td><?php echo $this->escape( $item->town ) ?></td>
        <td class="left nowrap"><?php echo $this->escape( $item->department ) ?></td>
        <td class="right">
          <?php echo $item->price ?> <?php echo $this->params->get('currency_symbol', '&euro;') ?>
          <?php if ($item->transaction_type == 'RENTING') echo JText::_('COM_JEA_PRICE_PER_FREQUENCY_'. $item->rate_frequency) ?>
        </td>
        <td class="center">
          <?php echo JHtml::_('contentadministrator.featured', $item->featured, $i, $canChange) ?>
        </td>
        <td class="center">
          <?php echo JHtml::_('jgrid.published', $item->published, $i, 'properties.', $canChange, 'cb', $item->publish_up, $item->publish_down) ?>
        </td>
        <td class="center">
          <?php echo $this->escape($item->access_level); ?>
        </td>
        <td class="order">
        <?php if ($canChange) : ?>
          <?php if ($saveOrder) :?>
            <?php if ($listDirection == 'asc') : ?>
              <span><?php echo $this->pagination->orderUpIcon($i, true, 'properties.orderup', 'JLIB_HTML_MOVE_UP', $saveOrder) ?></span>
              <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'properties.orderdown', 'JLIB_HTML_MOVE_DOWN', $saveOrder) ?></span>
            <?php elseif ($listDirection == 'desc') : ?>
              <span><?php echo $this->pagination->orderUpIcon($i, true, 'properties.orderdown', 'JLIB_HTML_MOVE_UP', $saveOrder) ?></span>
              <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'properties.orderup', 'JLIB_HTML_MOVE_DOWN', $saveOrder) ?></span>
            <?php endif ?>
          <?php endif ?>
            <input type="text" name="order[]" size="5" value="<?php echo $item->ordering ?>" <?php if (!$saveOrder) echo 'disabled="disabled"' ?> class="text-area-order" />
          <?php else : ?>
            <?php echo $item->ordering ?>
        <?php endif ?>
        </td>
        <td>
        <?php if ( $this->user->authorise( 'com_users', 'manage' ) ): ?>
          <a href="<?php echo JRoute::_( 'index.php?option=com_users&task=user.edit&id='. $item->created_by )  ?>" title="<?php echo JText::_('COM_JEA_EDIT_USER') ?> ">
          <?php echo $this->escape( $item->author ) ?></a>
        <?php else : echo $this->escape( $item->author ) ?>
        <?php endif ?>
        </td>
        <td class="center">
          <?php echo JHtml::_('date',  $item->created, JText::_('DATE_FORMAT_LC4') ) ?>
        </td>
        <td class="center"><?php echo $item->hits ?></td>
        <td class="center">
            <?php if ($item->language=='*'):?>
                <?php echo JText::alt('JALL', 'language'); ?>
            <?php else:?>
                <?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
            <?php endif;?>
        </td>
        <td class="center"><?php echo $item->id ?></td>
      </tr>
      <?php endforeach ?>
    </tbody>
  </table>

  <div>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $listOrder ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirection ?>" />
    <?php echo JHtml::_('form.token') ?>
  </div>
</div>
</form>
