<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2020 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\String\StringHelper;

defined('_JEXEC') or die;

/**
 * @var $this JeaViewFeatures
 */

HTMLHelper::stylesheet('media/com_jea/css/jea.admin.css');
HTMLHelper::_('behavior.multiselect');
$count = 0;
?>

<form action="<?php echo Route::_('index.php?option=com_jea&view=properties') ?>" method="post"
      name="adminForm"
      id="adminForm" enctype="multipart/form-data">

  <div class="row">

    <div id="j-sidebar-container" class="col-md-2">
        <?php echo $this->sidebar; ?>
    </div>

    <div id="j-sidebar-container" class="col-md-10">
      <table class="table table-striped">
        <thead>
        <tr>
          <th width="1%" class="center">
              <?php echo HTMLHelper::_('grid.checkall') ?>
          </th>
          <th width="60%">
              <?php echo Text::_('COM_JEA_HEADING_FEATURES_LIST_NAME') ?>
          </th>
          <th width="39%" class="center">
              <?php echo Text::_('COM_JEA_HEADING_FEATURES_IMPORT_CSV') ?>
          </th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($this->items as $i => $item) : $count++ ?>
          <tr class="row<?php echo $count % 2 ?>">
            <td>
                <?php echo HTMLHelper::_('grid.id', $i, $item->name) ?>
            </td>

            <td>
              <a href="<?php echo Route::_('index.php?option=com_jea&view=featurelist&feature=' . $item->name) ?>">
                  <?php echo Text::_(StringHelper::strtoupper("com_jea_list_of_{$item->name}_title")) ?>
              </a>
            </td>

            <td class="center">
              <input type="file" name="csv[<?php echo $item->name ?>]" value="" size="20"/>
            </td>
          </tr>
        <?php endforeach ?>
        </tbody>
      </table>

      <div>
        <input type="hidden" name="task" value=""/>
        <input type="hidden" name="boxchecked" value="0"/>
          <?php echo HTMLHelper::_('form.token') ?>
      </div>
    </div>

  </div>
</form>
