<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @version     $Id$
 * @package     Joomla.Site
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2012 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

JHTML::stylesheet('media/com_jea/css/jea.css');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/html');
$rowsCount = count($this->items);
$altrow = 1;

$listOrder      = $this->escape($this->state->get('list.ordering'));
$listDirection  = $this->escape($this->state->get('list.direction'));

$user = JFactory::getUser();
$canDelete  = $user->authorise('core.delete',     'com_jea');

$transactionType = $this->state->get('filter.transaction_type');

$script=<<<EOB
function changeOrdering( order, direction )
{
	var form = document.getElementById('adminForm');
	form.filter_order.value = order;
	form.filter_order_Dir.value = direction;
	form.submit();
}
EOB;

$this->document->addScriptDeclaration($script);
?>


<?php if ($this->params->get('show_page_title', 0) && $this->params->get('page_title', '')): ?>
<h1><?php echo $this->params->get('page_title') ?></h1>
<?php endif ?>

<?php if ($user->authorise('core.create', 'com_jea')): ?>
<p class="jea_add_new"><a href="<?php echo JRoute::_('index.php?option=com_jea&task=property.add') ?>"><?php echo JText::_('Add new property' )?></a></p>
<?php endif ?>

<form name="adminForm" id="adminForm" action="<?php echo JRoute::_('') ?>" method="post">

<?php if (!empty($this->items)): ?>  
  <p class="limitbox"><em><?php echo JText::_('Results per page') ?> : </em><?php echo $this->pagination->getLimitBox() ?></p>
<?php endif ?>

  <p>
      <select name="filter_transaction_type" class="inputbox" onchange="this.form.submit()">
        <option value=""> - <?php echo JText::_('Transaction type')?> - </option>
        <option value="RENTING"<?php if ($transactionType == 'RENTING') echo ' selected="selected"'?>>
          <?php echo JText::_('Renting')?>
        </option>
        <option value="SELLING"<?php if ($transactionType == 'SELLING') echo ' selected="selected"'?>>
          <?php echo JText::_('Selling')?>
        </option>
        <?php // TODO: call plugin entry to add more transaction types  ?>
      </select>

      <?php echo JHtml::_('features.types', $this->state->get('filter.type_id', 0), 'filter_type_id', 'onchange="document.adminForm.submit();"' ) ?>
    <select name="filter_language" class="inputbox" onchange="this.form.submit()">
    <option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE');?></option>
    <?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'));?>
    </select>
  </p>
  
<?php if (!empty($this->items)): ?>  
  <table class="jea_listing" >
    <thead>
    <tr>
      <th><?php echo $this->sort('Ref', 'p.ref', $listDirection , $listOrder) ?></th>
      <th><?php echo $this->sort('Type', 'type', $listDirection , $listOrder) ?></th>
      <th><?php echo JText::_('Address' )?></th>
      <th><?php echo $this->sort('Town', 'town', $listDirection , $listOrder) ?></th>
      <th class="right"><?php echo $this->sort('Living space', 'living_space', $listDirection , $listOrder) ?></th>
      <th class="right"><?php echo $this->sort('Price', 'p.price', $listDirection , $listOrder) ?></th>
      <th class="center"><?php echo JText::_('State' )?></th>
      <?php if ($canDelete): ?>
      <th class="center"><?php echo JText::_('Delete' )?></th>
      <?php endif ?>
    </tr>
    </thead>

    <tbody>
<?php foreach ($this->items as $k => $row): ?>
    <?php
    $altrow = ( $altrow == 1 )? 0 : 1;
    $row->slug = $row->alias ? ($row->id . ':' . $row->alias) : $row->id;
    $canEdit    = $user->authorise('core.edit',       'com_jea.property.'.$row->id);
    $canCheckin = $user->authorise('core.manage',     'com_checkin') || $row->checked_out == $user->id || $row->checked_out == 0;
    $canEditOwn = $user->authorise('core.edit.own',   'com_jea.property.'.$row->id) && $row->created_by == $user->id;
    $canChange  = $user->authorise('core.edit.state', 'com_jea.property.'.$row->id) && $canCheckin;
    $canDelete  = $user->authorise('core.delete',     'com_jea.property.'.$row->id);
    ?>

    <tr class="row<?php echo $altrow ?>" >
      <td class="nowrap"><a href="<?php echo JRoute::_( 'index.php?option=com_jea&task=property.edit&id='.$row->slug ) ?>" title="<?php echo JText::_('Edit') ?>" > 
      <?php echo $row->ref ?></a></td>
      <td><?php echo $row->type ?></td>
      <td><?php echo $row->address ?></td>
      <td><?php echo $row->town ?></td>
      <td class="right nowrap"><?php echo JHtml::_('utility.formatSurface', (float) $row->living_space , '-' ) ?></td>
      <td class="right nowrap"><?php echo JHtml::_('utility.formatPrice', (float) $row->price, '-') ?></td>
      <td class="center">

      <?php if ($canChange): $task = $row->published ? 'unpublish' : 'publish'; ?>
      <a href="<?php echo JRoute::_( 'index.php?option=com_jea&task=property.'.$task.'&id='.$row->id ) ?>" >
      <?php endif ?>

      <?php if ($row->published): $title = $canChange ? 'COM_JEA_UNPUBLISH_ITEM' : 'COM_JEA_PUBLISHED';  ?>
          <?php echo JHTML::_('image.site', 'published.png', '/media/com_jea/images/', null, '', JText::_('Published'),  array('title' => JText::_($title))) ?>
      <?php else: $title = $canChange ? 'COM_JEA_PUBLISH_ITEM' : 'COM_JEA_UNPUBLISHED';?>
          <?php echo JHTML::_('image.site', 'unpublished.png', '/media/com_jea/images/', null, '', JText::_('Unpublished'),  array('title' => JText::_($title))) ?>
      <?php endif?>
      
      <?php if ($canChange): ?>
      </a>
      <?php endif ?>

      </td>
      <?php if ($canDelete): ?>
      <td class="center">
        <a href="<?php echo JRoute::_( 'index.php?option=com_jea&task=property.delete&id='.$row->id ) ?>" 
           title="<?php echo JText::_('Delete') ?>"
           onclick="return confirm('<?php echo JText::_('Are you sure you want to delete this item?') ?>')">
         <?php echo JHTML::_('image.site', 'media_trash.png', '/media/com_jea/images/') ?></a>
      </td>
      <?php endif ?>
    </tr>
<?php endforeach ?>
    </tbody>
  </table>
<?php endif ?>
  
   <div>
    <input type="hidden" name="filter_order" value="<?php echo $listOrder ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirection ?>" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="Itemid" value="<?php echo JRequest::getInt('Itemid', 0) ?>" />
  </div>

  <div class="pagination">
      <p class="counter"><?php echo $this->pagination->getPagesCounter() ?></p>
      <?php echo $this->pagination->getPagesLinks() ?>
  </div>
</form>

