<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Site
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2020 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die();

/**
 *
 * @var $this JeaViewProperties
 */

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
HTMLHelper::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/html');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirection = $this->escape($this->state->get('list.direction'));

$user = Factory::getApplication()->getIdentity();
$canDelete = $user->authorise('core.delete', 'com_jea');

$transactionType = $this->state->get('filter.transaction_type');

$script = <<<EOB
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

<?php if ($this->params->get('show_page_heading', 1)) : ?>
    <?php if ($this->params->get('page_heading')) : ?>
    <h1><?php echo $this->escape($this->params->get('page_heading')) ?></h1>
    <?php else: ?>
    <h1><?php echo $this->escape($this->params->get('page_title')) ?></h1>
    <?php endif ?>
<?php endif ?>

<?php if ($user->authorise('core.create', 'com_jea')): ?>
  <p class="jea_add_new">
    <a href="<?php echo Route::_('index.php?option=com_jea&task=property.add') ?>"><?php echo Text::_('COM_JEA_ADD_NEW_PROPERTY') ?></a>
  </p>
<?php endif ?>

<form name="adminForm" id="adminForm" action="<?php echo Route::_('') ?>" method="post">

    <?php if (!empty($this->items)): ?>
      <p class="limitbox">
        <em><?php echo Text::_('COM_JEA_RESULTS_PER_PAGE') ?>
          : </em> <?php echo $this->pagination->getLimitBox() ?>
      </p>
    <?php endif ?>

  <p>
    <select name="filter_transaction_type" class="inputbox" onchange="this.form.submit()">
      <option value=""> - <?php echo Text::_('COM_JEA_FIELD_TRANSACTION_TYPE_LABEL') ?> -</option>
      <option
          value="RENTING" <?php if ($transactionType == 'RENTING') echo ' selected="selected"' ?>>
          <?php echo Text::_('COM_JEA_OPTION_RENTING') ?>
      </option>
      <option value="SELLING"
          <?php if ($transactionType == 'SELLING') echo ' selected="selected"' ?>>
          <?php echo Text::_('COM_JEA_OPTION_SELLING') ?>
      </option>
        <?php // TODO: call plugin entry to add more transaction types  ?>
    </select>

      <?php echo HTMLHelper::_('features.types', $this->state->get('filter.type_id', 0), 'filter_type_id', 'onchange="document.adminForm.submit();"') ?>

    <select name="filter_language" class="inputbox" onchange="this.form.submit()">
      <option value=""><?php echo Text::_('JOPTION_SELECT_LANGUAGE'); ?></option>
        <?php echo HTMLHelper::_('select.options', HTMLHelper::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language')); ?>
    </select>
  </p>

    <?php if (!empty($this->items)): ?>
      <table class="jea_listing">
        <thead>
        <tr>
          <th><?php echo $this->sort('COM_JEA_REF', 'p.ref', $listDirection, $listOrder) ?></th>
          <th><?php echo $this->sort('COM_JEA_FIELD_PROPERTY_TYPE_LABEL', 'type', $listDirection, $listOrder) ?></th>
          <th><?php echo Text::_('COM_JEA_FIELD_ADDRESS_LABEL') ?></th>
          <th><?php echo $this->sort('COM_JEA_FIELD_TOWN_LABEL', 'town', $listDirection, $listOrder) ?></th>
          <th class="right"><?php echo $this->sort('COM_JEA_FIELD_LIVING_SPACE_LABEL', 'living_space', $listDirection, $listOrder) ?></th>
          <th class="right"><?php echo $this->sort('COM_JEA_FIELD_PRICE_LABEL', 'p.price', $listDirection, $listOrder) ?></th>
          <th class="center"><?php echo Text::_('JSTATUS') ?></th>
            <?php if ($canDelete): ?>
              <th class="center"><?php echo Text::_('JACTION_DELETE') ?></th>
            <?php endif ?>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($this->items as $i => $row): ?>
            <?php
            $row->slug = $row->alias ? ($row->id . ':' . $row->alias) : $row->id;
            $canCheckin = $user->authorise('core.manage', 'com_checkin') || $row->checked_out == $user->id || $row->checked_out == 0;
            $canChange = $user->authorise('core.edit.state', 'com_jea.property.' . $row->id) && $canCheckin;
            $canDelete = $user->authorise('core.delete', 'com_jea.property.' . $row->id);
            ?>

          <tr class="row<?php echo $i % 2 ?>">
            <td class="nowrap">
              <a href="<?php echo Route::_('index.php?option=com_jea&task=property.edit&id=' . $row->id) ?>"
                 title="<?php echo Text::_('JACTION_EDIT') ?>">
                  <?php echo $row->ref ?>
              </a>
            </td>
            <td><?php echo $row->type ?></td>
            <td><?php echo $row->address ?></td>
            <td><?php echo $row->town ?></td>
            <td class="right nowrap"><?php echo HTMLHelper::_('utility.formatSurface', (float)$row->living_space, '-') ?></td>
            <td class="right nowrap">
                <?php echo HTMLHelper::_('utility.formatPrice', (float)$row->price, '-') ?>
                <?php if ($row->transaction_type == 'RENTING' && (float)$row->price != 0.0) echo Text::_('COM_JEA_PRICE_PER_FREQUENCY_' . $row->rate_frequency) ?>
            </td>
            <td class="center">
                <?php if ($canChange):
                $task = $row->published ? 'unpublish' : 'publish'; ?>
              <a href="<?php echo Route::_('index.php?option=com_jea&task=property.' . $task . '&id=' . $row->id) ?>">
                  <?php endif ?>

                  <?php if ($row->published): $title = $canChange ? 'JLIB_HTML_UNPUBLISH_ITEM' : 'COM_JEA_PUBLISHED' ?>
                    <img src="<?php echo $this->baseurl . '/media/com_jea/images/published.png' ?>"
                         alt="<?php echo Text::_('COM_JEA_PUBLISHED') ?>"
                         title="<?php echo Text::_($title) ?>"/>

                  <?php else: $title = $canChange ? 'JLIB_HTML_PUBLISH_ITEM' : 'COM_JEA_UNPUBLISHED' ?>
                    <img
                        src="<?php echo $this->baseurl . '/media/com_jea/images/unpublished.png' ?>"
                        alt="<?php echo Text::_('COM_JEA_UNPUBLISHED') ?>"
                        title="<?php echo Text::_($title) ?>"/>
                  <?php endif ?>

                  <?php if ($canChange): ?>
              </a><?php endif ?>
            </td>

              <?php if ($canDelete): ?>
                <td class="center">
                  <a href="<?php echo Route::_('index.php?option=com_jea&task=property.delete&id=' . $row->id) ?>"
                     title="<?php echo Text::_('JACTION_DELETE') ?>"
                     onclick="return confirm('<?php echo Text::_('COM_JEA_MESSAGE_CONFIRM_DELETE') ?>')">
                    <img
                        src="<?php echo $this->baseurl . '/media/com_jea/images/media_trash.png' ?>"
                        alt=""/>
                  </a>
                </td>
              <?php endif ?>
          </tr>
        <?php endforeach ?>
        </tbody>
      </table>
    <?php endif ?>

  <div>
    <input type="hidden" name="filter_order" value="<?php echo $listOrder ?>"/>
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirection ?>"/>
    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="Itemid"
           value="<?php echo Factory::getApplication()->input->getInt('Itemid', 0) ?>"/>
  </div>

  <div class="pagination">
    <p class="counter">
        <?php echo $this->pagination->getPagesCounter() ?>
    </p>
      <?php echo $this->pagination->getPagesLinks() ?>
  </div>

</form>
