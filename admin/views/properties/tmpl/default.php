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
use Joomla\CMS\Button\FeaturedButton;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

/**
 * @var $this JeaViewProperties
 */
HTMLHelper::stylesheet('media/com_jea/css/jea.admin.css');

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirection = $this->escape($this->state->get('list.direction'));

$saveOrder = $listOrder == 'p.ordering';

if ($saveOrder) {
    $saveOrderingUrl = 'index.php?option=com_jea&task=properties.saveOrderAjax&tmpl=component';
    HTMLHelper::_('sortablelist.sortable', 'propertiesList', 'adminForm', strtolower($listDirection), $saveOrderingUrl);
}
?>

<form action="<?php echo Route::_('index.php?option=com_jea&view=properties') ?>" method="post"
      name="adminForm" id="adminForm">

  <div class="row">

    <div id="j-sidebar-container" class="col-md-2">
        <?php echo $this->sidebar ?>
    </div>

    <div id="j-sidebar-container" class="col-md-10">
        <?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)) ?>

        <?php if (empty($this->items)) : ?>
          <div class="alert alert-no-items">
              <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
          </div>
        <?php else : ?>
          <table class="table table-striped" id="propertiesList">
            <thead>
            <tr>
              <th width="1%" class="nowrap center hidden-phone">
                  <?php echo HTMLHelper::_('searchtools.sort', '', 'p.ordering', $listDirection, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
              </th>
              <th width="1%" class="center">
                  <?php echo HTMLHelper::_('grid.checkall') ?>
              </th>
              <th width="10%" class="nowrap">
                  <?php echo HTMLHelper::_('searchtools.sort', 'COM_JEA_FIELD_REF_LABEL', 'p.ref', $listDirection, $listOrder) ?>
              </th>
              <th class="nowrap">
                  <?php echo Text::_('COM_JEA_FIELD_PROPERTY_TYPE_LABEL') ?>
              </th>
              <th width="27%" class="nowrap">
                  <?php echo Text::_('COM_JEA_FIELD_ADDRESS_LABEL') ?>
              </th>
              <th width="10%" class="nowrap">
                  <?php echo Text::_('COM_JEA_FIELD_TOWN_LABEL') ?>
              </th>
              <th width="10%" class="nowrap">
                  <?php echo Text::_('COM_JEA_FIELD_DEPARTMENT_LABEL') ?>
              </th>
              <th width="10%" class="nowrap">
                  <?php echo HTMLHelper::_('searchtools.sort', 'COM_JEA_FIELD_PRICE_LABEL', 'p.price', $listDirection, $listOrder) ?>
              </th>
              <th width="1%" class="nowrap">
                  <?php echo HTMLHelper::_('searchtools.sort', 'JFEATURED', 'p.featured', $listDirection, $listOrder) ?>
              </th>
              <th width="1%" class="nowrap">
                  <?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'p.published', $listDirection, $listOrder) ?>
              </th>
              <th width="5%" class="nowrap">
                  <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ACCESS', 'access_level', $listDirection, $listOrder); ?>
              </th>
              <th width="10%" class="nowrap">
                  <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_CREATED_BY', 'author', $listDirection, $listOrder) ?>
              </th>
              <th width="5%" class="nowrap">
                  <?php echo HTMLHelper::_('searchtools.sort', 'JDATE', 'p.created', $listDirection, $listOrder) ?>
              </th>
              <th width="1%" class="nowrap">
                  <?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_HITS', 'p.hits', $listDirection, $listOrder) ?>
              </th>
              <th width="5%" class="nowrap">
                  <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_LANGUAGE', 'language', $listDirection, $listOrder); ?>
              </th>
              <th width="1%" class="nowrap">
                  <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'p.id', $listDirection, $listOrder) ?>
              </th>
            </tr>
            </thead>

            <tfoot>
            <tr>
              <td colspan="16"></td>
            </tr>
            </tfoot>

            <tbody>
            <?php foreach ($this->items as $i => $item) : ?>
                <?php
                $canEdit = $this->user->authorise('core.edit', 'com_jea.property.' . $item->id);
                $canCheckin = $this->user->authorise('core.manage', 'com_checkin') || $item->checked_out == $this->user->id || $item->checked_out == 0;
                $canEditOwn = $this->user->authorise('core.edit.own', 'com_jea.property.' . $item->id) && $item->created_by == $this->user->id;
                $canChange = $this->user->authorise('core.edit.state', 'com_jea.property.' . $item->id) && $canCheckin;
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
                             value="<?php echo $item->ordering; ?>"
                             class="width-20 text-area-order"/>
                    <?php endif; ?>
                </td>
                <td class="center">
                    <?php echo HTMLHelper::_('grid.id', $i, $item->id) ?>
                </td>
                <td class="has-context">
                    <?php if ($item->checked_out) : ?>
                        <?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->author, $item->checked_out_time, 'properties.', $canCheckin); ?>
                    <?php endif ?>
                    <?php if ($canEdit || $canEditOwn) : ?>
                      <a href="<?php echo Route::_('index.php?option=com_jea&task=property.edit&id=' . (int)$item->id); ?>">
                          <?php echo $this->escape($item->ref); ?>
                      </a>
                    <?php else : ?>
                        <?php echo $this->escape($item->ref); ?>
                    <?php endif ?>
                </td>
                <td>
                    <?php echo $this->escape($item->type) ?>
                </td>
                <td>
                    <?php echo $this->escape($item->address) ?>
                </td>
                <td>
                    <?php echo $this->escape($item->town) ?>
                </td>
                <td class="left nowrap">
                    <?php echo $this->escape($item->department) ?>
                </td>
                <td class="right">
                    <?php echo $item->price ?><?php echo $this->params->get('currency_symbol', '&euro;') ?>
                    <?php if ($item->transaction_type == 'RENTING') echo Text::_('COM_JEA_PRICE_PER_FREQUENCY_' . $item->rate_frequency) ?>
                </td>
                <td class="center">
                    <?php
                    $options = [
                        'task_prefix' => 'properties.',
                        'disabled' => !$canChange,
                        'id' => 'featured-' . $item->id
                    ];
                    echo (new FeaturedButton())->render((int) $item->featured, $i, $options); ?>
                </td>
                <td class="center">
                    <?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'properties.', $canChange, 'cb', $item->publish_up, $item->publish_down) ?>
                </td>
                <td class="center">
                    <?php echo $this->escape($item->access_level); ?>
                </td>
                <td>
                    <?php if ($this->user->authorise('com_users', 'manage')): ?>
                      <a href="<?php echo Route::_('index.php?option=com_users&task=user.edit&id=' . $item->created_by) ?>"
                         title="<?php echo Text::_('COM_JEA_EDIT_USER') ?> ">
                          <?php echo $this->escape($item->author) ?>
                      </a>
                    <?php else : echo $this->escape($item->author) ?>
                    <?php endif ?>
                </td>
                <td class="center">
                    <?php echo HTMLHelper::_('date', $item->created, Text::_('DATE_FORMAT_LC4')) ?>
                </td>
                <td class="center"><?php echo $item->hits ?></td>
                <td class="center">
                    <?php if ($item->language == '*'): ?>
                        <?php echo Text::alt('JALL', 'language') ?>
                    <?php else: ?>
                        <?php echo $item->language_title ? $this->escape($item->language_title) : Text::_('JUNDEFINED') ?>
                    <?php endif ?>
                </td>
                <td class="center">
                    <?php echo $item->id ?>
                </td>
              </tr>
            <?php endforeach ?>
            </tbody>
          </table>

            <?php echo $this->pagination->getListFooter() ?>

        <?php endif ?>

      <div>
        <input type="hidden" name="task" value=""/>
        <input type="hidden" name="boxchecked" value="0"/>
          <?php echo HTMLHelper::_('form.token') ?>
      </div>
    </div>

  </div>
</form>
