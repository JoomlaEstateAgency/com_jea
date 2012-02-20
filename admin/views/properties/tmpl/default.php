<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 * 
 * @version     $Id$
 * @package		Jea.admin
 * @copyright	Copyright (C) 2008 PHILIP Sylvain. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla Estate Agency is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses.
 * 
 */


// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

JHTML::stylesheet('media/com_jea/css/jea.admin.css');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

$rowsCount = count($this->items) ;
$altrow = 1;

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirection	= $this->escape($this->state->get('list.direction'));
$saveOrder	= $listOrder == 'p.ordering';

$transactionType = $this->state->get('filter.transaction_type');
?>

<form action="<?php echo JRoute::_('index.php?option=com_jea&view=properties') ?>" method="post" name="adminForm" id="adminForm">

<fieldset id="filter-bar">
  <div class="clr"></div>
    <div class="filter-search fltlft">
      <label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
      <input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('FILTER_SEARCH_DESC'); ?>" />

      <button type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
      <button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
    </div>
    
    <div class="filter-select fltrt">
      <select name="filter_transaction_type" class="inputbox" onchange="this.form.submit()" >
        <option value=""> - <?php echo JText::_('Transaction type')?> - </option>
        <option value="RENTING" <?php if ($transactionType == 'RENTING') echo 'selected="selected"'?>><?php echo JText::_('Renting')?></option>
        <option value="SELLING" <?php if ($transactionType == 'SELLING') echo 'selected="selected"'?>><?php echo JText::_('Selling')?></option>
        <?php // TODO: call plugin entry to add more transaction types  ?>
      </select>
    
      <?php echo JHtml::_('features.types', $this->state->get('filter.type_id'), 'filter_type_id', 'onchange="document.adminForm.submit();"' ) ?>
      <?php echo JHtml::_('features.departments', $this->state->get('filter.department_id'), 'filter_department_id', 'onchange="document.adminForm.submit();"' ) ?>

      <?php if ($this->params->get('relationship_dpts_towns_area', 0)): ?>
      <?php echo JHtml::_('features.towns', $this->state->get('filter.town_id'), 'filter_town_id', 'onchange="document.adminForm.submit();"', $this->state->get('filter.department_id') ) ?>
      <?php else: ?>
      <?php echo JHtml::_('features.towns', $this->state->get('filter.town_id'), 'filter_town_id', 'onchange="document.adminForm.submit();"' ) ?>
      <?php endif ?>
    </div>
    <div class="clr"></div>
</fieldset>

<table class="adminlist">
  <thead>
    <tr>
      <th width="1%">
        <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
      </th>
      <th width="10%">
        <?php echo JHTML::_('grid.sort', 'Reference', 'p.ref', $listDirection , $listOrder ) ?>
      </th>
      <th>
        <?php echo JText::_('Property type') ?>
      </th>
      <th width="27%">
        <?php echo JText::_('Adress') ?>
      </th>
      <th width="10%">
        <?php echo JText::_('Town') ?>
      </th>
      <th width="10%">
        <?php echo JText::_('Department') ?>
      </th>
      <th width="10%">
        <?php echo JHTML::_('grid.sort', 'Price', 'p.price', $listDirection , $listOrder ) ?>
      </th>
      <th width="1%">
        <?php echo JHTML::_('grid.sort', 'JFEATURED', 'p.emphasis', $listDirection , $listOrder ) ?>
      </th>
      <th width="1%">
        <?php echo JHTML::_('grid.sort', 'Published', 'p.published', $listDirection , $listOrder ) ?>
      </th>
      <th width="10%">
        <?php echo JHTML::_('grid.sort', 'JGRID_HEADING_ORDERING', 'p.ordering', $listDirection , $listOrder ) ?>
        <?php if ($saveOrder) :?>
            <?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'properties.saveorder'); ?>
        <?php endif; ?>
      </th>
      <th width="10%">
         <?php echo JHTML::_('grid.sort', 'JGRID_HEADING_CREATED_BY', 'author', $listDirection , $listOrder ) ?>
      </th>
      <th width="5%">
        <?php echo JHTML::_('grid.sort', 'JDATE', 'p.date_insert', $listDirection , $listOrder ) ?>
      </th>
      <th width="1%">
        <?php echo JHTML::_('grid.sort', 'JGLOBAL_HITS', 'p.hits', $listDirection , $listOrder ) ?>
      </th>
      <th width="1%"> 
        <?php echo JHTML::_('grid.sort', 'JGRID_HEADING_ID', 'p.id', $listDirection , $listOrder ) ?>
      </th>
    </tr>
  </thead>

  <tfoot>
    <tr>
      <td colspan="14">
        <?php echo $this->pagination->getListFooter() ?>
      </td>
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
          <?php endif; ?>
          <?php if ($canEdit || $canEditOwn) : ?>
            <a href="<?php echo JRoute::_('index.php?option=com_jea&task=property.edit&id='.(int) $item->id); ?>">
            <?php echo $this->escape($item->ref); ?></a>
          <?php else : ?>
            <?php echo $this->escape($item->ref); ?>
          <?php endif; ?>
      </td>

      <td><?php echo $this->escape( $item->type ) ?></td>
      <td><?php echo $this->escape( $item->address ) ?></td>
      <td><?php echo $this->escape( $item->town ) ?></td>
      <td class="left nowrap"><?php echo $this->escape( $item->department ) ?></td>
      <td class="right" ><?php echo $item->price ?> <?php echo $this->params->get('currency_symbol', '&euro;') ?></td>
      <td class="center">
        <?php echo JHtml::_('contentadministrator.featured', $item->emphasis, $i, $canChange); ?>
      </td>

      <td class="center">
      <?php echo JHTML::_('grid.published', $item, $i, 'publish_g.png') ?>
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
              <?php endif; ?>
            <?php endif; ?>
            <input type="text" name="order[]" size="5" value="<?php echo $item->ordering ?>" <?php if (!$saveOrder) echo 'disabled="disabled"' ?> class="text-area-order" />
          <?php else : ?>
            <?php echo $item->ordering ?>
          <?php endif; ?>
      </td>
      
      <td>
      <?php if ( $this->user->authorize( 'com_users', 'manage' ) ): ?>
                 <a href="<?php echo JRoute::_( 'index.php?option=com_users&task=edit&cid[]='. $item->created_by )  ?>" 
                    title="<?php echo JText::_( 'Edit User' ) ?> "><?php echo $this->escape( $item->author ) ?></a>
            <?php else : echo $this->escape( $item->author ) ?>
      <?php endif ?>
      </td>
      
      <td class="center"><?php echo JHTML::_('date',  $item->date_insert, JText::_('DATE_FORMAT_LC4') ); ?></td>
      <td class="center"><?php echo $item->hits ?></td>
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

</form>
