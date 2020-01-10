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

/**
 * @var $this JeaViewGateways
 */

JHtml::stylesheet('media/com_jea/css/jea.admin.css');
?>

<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar?>
</div>

<div id="j-main-container" class="span10">
	<?php echo JLayoutHelper::render('jea.gateways.nav', array('action' => 'export', 'view' => 'console')) ?>
	<?php echo JLayoutHelper::render('jea.gateways.consoles', array('action' => 'export')) ?>
</div>