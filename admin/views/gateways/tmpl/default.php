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

use Joomla\CMS\Application\AdministratorApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

$app = Factory::getApplication();
assert($app instanceof AdministratorApplication);
$am = $app->getDocument()->getWebAssetManager();
$am->useScript('jquery');
$am->useScript('bootstrap.modal');

/**
 * @var $this JeaViewGateways
 */

HTMLHelper::stylesheet('media/com_jea/css/jea.admin.css');
HTMLHelper::_('behavior.multiselect');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirection = $this->escape($this->state->get('list.direction'));

if ($listOrder == 'ordering') {
    $saveOrderingUrl = 'index.php?option=com_jea&task=gateways.saveOrderAjax&tmpl=component';
    HTMLHelper::_('sortablelist.sortable', 'gateways-list', 'adminForm', strtolower($listDirection), $saveOrderingUrl);
}

$script = <<<JS
jQuery(document).ready(function($) {

	$('.show-logs').click( function(e) {
		$('#modal').data('gatewayId', $(this).data('gatewayId'));
		e.preventDefault();
	});

	$('#modal').modal({show: false}).on('shown.bs.modal', function(e) {

		var gatewayId = $(this).data('gatewayId');

		$.get('index.php', {option : 'com_jea', task :'gateway.getLogs', id: gatewayId}, function(response) {
			$('#logs').text(response);
		});

		$(this).find('a').each(function() {
			this.href = this.href.replace(/id=[0-9]*/, 'id=' + gatewayId);
		});

		$('.ajax-refresh-logs').click( function(e) {
			$.get( this.href, {}, function(response) {
				$('#logs').text(response);
			});
			e.preventDefault();
		});

	}).on('hide.bs.modal', function () {
		$('#logs').empty();
	});
});
JS;

$document = Factory::getDocument();
$document->addScriptDeclaration($script);
?>

<?php echo LayoutHelper::render('jea.gateways.nav', array('action' => $this->state->get('filter.type'), 'view' => 'gateways'), JPATH_COMPONENT_ADMINISTRATOR) ?>

<hr/>

<form action="<?php echo Route::_('index.php?option=com_jea&view=gateways') ?>" method="post"
      name="adminForm" id="adminForm">

  <table class="table table-striped" id="gateways-list">
    <thead>
    <tr>
      <th width="1%" class="nowrap center">
          <?php echo HTMLHelper::_('grid.sort', '', 'ordering', $listDirection, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
      </th>

      <th width="1%" class="nowrap">
          <?php echo HTMLHelper::_('grid.checkall') ?>
      </th>

      <th width="1%" class="nowrap">
          <?php echo HTMLHelper::_('grid.sort', 'JSTATUS', 'published', $listDirection, $listOrder) ?>
      </th>

      <th width="86%" class="nowrap">
          <?php echo HTMLHelper::_('grid.sort', 'JGLOBAL_TITLE', 'title', $listDirection, $listOrder) ?>
      </th>

      <th width="5%" class="nowrap center">
          <?php echo Text::_('COM_JEA_LOGS') ?>
      </th>

      <th width="5%" class="nowrap center">
          <?php echo HTMLHelper::_('grid.sort', 'COM_JEA_GATEWAY_FIELD_PROVIDER_LABEL', 'provider', $listDirection, $listOrder) ?>
      </th>

      <th width="1%" class="nowrap hidden-phone">
          <?php echo HTMLHelper::_('grid.sort', 'JGRID_HEADING_ID', 'id', $listDirection, $listOrder); ?>
      </th>
    </tr>
    </thead>

    <tfoot>
    <tr>
      <td colspan="12"></td>
    </tr>
    </tfoot>

    <tbody>
    <?php foreach ($this->items as $i => $item) : ?>
      <tr class="row<?php echo $i % 2 ?>">
        <td width="1%" class="order nowrap center hidden-phone">
            <?php
            $iconClass = '';
            if ($listOrder != 'ordering') $iconClass = ' inactive tip-top hasTooltip" title="' . HTMLHelper::tooltipText('JORDERINGDISABLED');
            ?>
          <span class="sortable-handler<?php echo $iconClass ?>"><span class="icon-menu"></span></span>
            <?php if ($listOrder == 'ordering') : ?>
              <input type="text" style="display:none" name="order[]" size="5"
                      value="<?php echo $item->ordering ?>" class="width-20 text-area-order "/>
            <?php endif ?>
        </td>

        <td class="nowrap center">
            <?php echo HTMLHelper::_('grid.id', $i, $item->id) ?>
        </td>

        <td width="1%" class="nowrap center">
            <?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'gateways.', true, 'cb') ?>
        </td>

        <td width="86%" class="title">
          <a href="<?php echo Route::_('index.php?option=com_jea&task=gateway.edit&type=' . $this->state->get('filter.type') . '&id=' . (int)$item->id) ?>">
              <?php echo $item->title ?></a>
        </td>
        <td width="5%" class="nowrap center">
          <button class="btn btn-info show-logs" data-bs-toggle="modal" data-bs-target="#modal"
                  data-gateway-id="<?php echo $item->id ?>">
              <?php echo Text::_('COM_JEA_LOGS') ?>
          </button>
        </td>
        <td width="5%" class="nowrap center">
            <?php echo $item->provider ?>
        </td>
        <td class="hidden-phone">
            <?php echo (int)$item->id ?>
        </td>
      </tr>
    <?php endforeach ?>
    </tbody>
  </table>
    <?php echo $this->pagination->getListFooter() ?>
  <div>
    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="type" value="<?php echo $this->state->get('filter.type') ?>"/>
    <input type="hidden" name="boxchecked" value="0"/>
    <input type="hidden" name="filter_order" value="<?php echo $listOrder ?>"/>
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirection ?>"/>
      <?php echo HTMLHelper::_('form.token') ?>
  </div>
</form>

<?php ob_start() ?>
<p>
  <a class="ajax-refresh-logs"
     href="<?php echo Route::_('index.php?option=com_jea&task=gateway.getLogs&id=') ?>">
      <?php echo Text::_('JGLOBAL_HELPREFRESH_BUTTON') ?>
  </a> |

  <a class="ajax-refresh-logs"
     href="<?php echo Route::_('index.php?option=com_jea&task=gateway.deleteLogs&id=') ?>"
     id="delete_logs">
      <?php echo Text::_('JACTION_DELETE') ?>
  </a> |

  <a href="<?php echo Route::_('index.php?option=com_jea&task=gateway.downloadLogs&id=') ?>"
     id="download_logs">
      <?php echo Text::_('COM_JEA_DOWNLOAD') ?>
  </a>
</p>

<pre id="logs"></pre>
<?php
$modalBody = ob_get_contents();
ob_end_clean();
echo HTMLHelper::_('bootstrap.renderModal', 'modal', array('title' => Text::_('COM_JEA_LOGS')), $modalBody);
?>
