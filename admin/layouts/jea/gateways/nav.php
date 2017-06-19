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

$action = $displayData['action'];
$view = $displayData['view'];
?>

<ul class="nav nav-pills">

  <li<?php if ($view == 'console') echo  ' class="active"' ?>>
    <a href="<?php echo JRoute::_('index.php?option=com_jea&view=gateways&layout=' . $action) ?>">
    <span class="icon-play"></span> <?php echo JText::_('COM_JEA_'. strtoupper($action))?></a>
  </li>

  <li<?php if ($view == 'gateways') echo  ' class="active"' ?>>
    <a href="<?php echo JRoute::_('index.php?option=com_jea&view=gateways&filter[type]=' . $action) ?>">
    <span class="icon-list"></span> <?php echo JText::_('COM_JEA_GATEWAYS')?></a>
  </li>

</ul>