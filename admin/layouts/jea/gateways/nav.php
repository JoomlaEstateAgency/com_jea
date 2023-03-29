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

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

/* @var $displayData array */

$action = $displayData['action'];
$view = $displayData['view'];
?>

<ul class="nav nav-pills mb-4">
  <li class="nav-item">
    <a class="nav-link<?php if ($view == 'console') echo ' active' ?>"
       href="<?php echo Route::_('index.php?option=com_jea&view=gateways&layout=' . $action) ?>">
      <span class="icon-play"></span> <?php echo Text::_('COM_JEA_' . strtoupper($action)) ?></a>
  </li>

  <li class="nav-item">
    <a class="nav-link<?php if ($view == 'gateways') echo ' active' ?>"
       href="<?php echo Route::_('index.php?option=com_jea&view=gateways&filter[type]=' . $action) ?>">
      <span class="icon-list"></span> <?php echo Text::_('COM_JEA_GATEWAYS') ?></a>
  </li>
</ul>
