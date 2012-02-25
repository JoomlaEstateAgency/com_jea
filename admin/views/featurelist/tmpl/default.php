<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 * 
 * @version     $Id: default.php 258 2012-02-20 00:54:35Z ilhooq $
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

$filters = $this->state->get('feature.filters', '');
if (!empty($filters)) {
    $filters = explode(',', $filters);
} else {
    $filters = array();
}

?>

<form action="<?php echo JRoute::_('index.php?option=com_jea&view=featurelist') ?>" method="post" name="adminForm" id="adminForm">

<fieldset id="filter-bar">
  <div class="clr"></div>
    <div class="filter-search fltlft">
      <label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
      <input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('FILTER_SEARCH_DESC'); ?>" />

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
    </div>
    <div class="clr"></div>
</fieldset>

<table class="adminlist">
  <thead>
    <tr>
      <th width="1%">
        <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
      </th>
      <th width="88%">
        <?php echo JHTML::_('grid.sort', 'COM_JEA_'.$this->state->get('feature.name'), 'f.value', $listDirection , $listOrder ) ?>
      </th>
      <th width="10%">
        <?php echo JHTML::_('grid.sort', 'JGRID_HEADING_ORDERING', 'f.ordering', $listDirection , $listOrder ) ?>
        <?php if ($saveOrder) :?>
            <?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'properties.saveorder'); ?>
        <?php endif; ?>
      </th>
      <th width="1%"> 
        <?php echo JHTML::_('grid.sort', 'JGRID_HEADING_ID', 'f.id', $listDirection , $listOrder ) ?>
      </th>
    </tr>
  </thead>

  <tfoot>
    <tr>
      <td colspan="4">
        <?php echo $this->pagination->getListFooter() ?>
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
            <?php echo $this->escape($item->value); ?></a>
          <?php else : ?>
            <?php echo $this->escape($item->value); ?>
          <?php endif; ?>
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
              <?php endif; ?>
            <?php endif; ?>
            <input type="text" name="order[]" size="5" value="<?php echo $item->ordering ?>" <?php if (!$saveOrder) echo 'disabled="disabled"' ?> class="text-area-order" />
          <?php else : ?>
            <?php echo $item->ordering ?>
          <?php endif ?>
      </td>

      <td class="center"><?php echo $item->id ?></td>
    </tr>

<?php endforeach ?>

  </tbody>

</table>


<div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="feature" value="<?php echo $this->state->get('feature.name')?>" /> 
	<input type="hidden" name="filter_order" value="<?php echo $listOrder ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirection ?>" />
	<?php echo JHtml::_('form.token') ?>
</div>

</form>
