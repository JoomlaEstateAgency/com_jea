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

JHtml::stylesheet('media/com_jea/css/jea.admin.css');

JHtml::_('behavior.multiselect');
$altrow=1;
?>

<form action="<?php echo JRoute::_('index.php?option=com_jea&view=properties') ?>" method="post"
      name="adminForm" id="adminForm" enctype="multipart/form-data">

<?php if (!empty( $this->sidebar)) : ?>
    <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
    </div>
<?php endif ?>

<div id="j-main-container" <?php if (!empty( $this->sidebar)) echo 'class="span10"' ?>>

  <table class="adminlist table table-striped">
    <thead>
      <tr>
        <th width="1%">
          <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>"
                 onclick="Joomla.checkAll(this)" />
        </th>
        <th width="60%"><?php echo JText::_('COM_JEA_HEADING_FEATURES_LIST_NAME') ?></th>
        <th width="39%" class="center"><?php echo JText::_('COM_JEA_HEADING_FEATURES_IMPORT_CSV') ?></th>
    </thead>

    <tbody>
    <?php foreach ($this->items as $i => $item) : $altrow = ( $altrow == 1 )? 0 : 1 ?>
      <tr class="row<?php echo $altrow ?>">
        <td><?php echo JHtml::_('grid.id', $i, $item->name); ?></td>
        <td>
          <a href="<?php echo JRoute::_('index.php?option=com_jea&view=featurelist&feature='.$item->name) ?>">
          <?php echo JText::_(JString::strtoupper("com_jea_list_of_{$item->name}_title")) ?></a>
        </td>
        <td class="center"><input type="file" name="csv[<?php echo $item->name ?>]" value="" size="20" /></td>
      </tr>
      <?php endforeach ?>
    </tbody>
  </table>

  <div>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <?php echo JHtml::_('form.token') ?>
  </div>

</div>
</form>


