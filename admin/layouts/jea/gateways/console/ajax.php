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
use Joomla\CMS\Language\Text;

/* @var $displayData array */

require_once JPATH_COMPONENT_ADMINISTRATOR . '/gateways/dispatcher.php';

HTMLHelper::script('media/com_jea/js/console.js');

Text::script('COM_JEA_GATEWAY_IMPORT_TIME_REMAINING', true);
Text::script('COM_JEA_GATEWAY_IMPORT_TIME_ELAPSED', true);

$action = $displayData['action'];

$dispatcher = GatewaysEventDispatcher::getInstance();
$dispatcher->loadGateways($action);
$dispatcher->trigger('initWebConsole');

$script = <<<JS
function GatewaysActionDispatcher() {

	this.queue = []

	this.register = function(action) {
		this.queue.push(action)
	}

	this.nextAction = function()
	{
		if (this.queue.length > 0) {
			var nextAction = this.queue.shift()
			nextAction()
		}
	}
}

jQuery(document).ready(function($) {
	var dispatcher = new GatewaysActionDispatcher();
	
	$(this).on('gatewayActionDone', function(e) {
		$('#console').append($('<br>'));
		if (dispatcher.queue.length == 0) {
			$('#ajax-launch').toggleClass('active');
		} else {
			dispatcher.nextAction();
		}
	});

	$('#ajax-launch').on('click', function(e) {
		$(this).toggleClass('active');
		$('#console').empty();
		$(document).trigger('registerGatewayAction', [$('#console').console(), dispatcher]);
		dispatcher.nextAction();
	});
});
JS;

$document = Factory::getDocument();
$document->addScriptDeclaration($script);
?>

<button id="ajax-launch" class="btn btn-success has-spinner">
  <span class="spinner"><i class="jea-icon-spin icon-refresh"></i></span>
    <?php echo Text::_('COM_JEA_' . strtoupper($action) . '_LAUNCH') ?>
</button>

<div id="console" class="console"></div>






