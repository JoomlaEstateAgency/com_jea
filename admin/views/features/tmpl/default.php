<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @version     $Id$
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2012 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */


// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

JHTML::stylesheet('media/com_jea/css/jea.admin.css');

JHtml::_('behavior.multiselect');
$altrow=1;
?>


<form action="<?php echo JRoute::_('index.php?option=com_jea&view=properties') ?>" method="post"
      name="adminForm" id="adminForm" enctype="multipart/form-data">

  <table class="adminlist">
    <thead>
      <tr>
        <th width="1%">
          <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" 
                 onclick="Joomla.checkAll(this)" />
        </th>
        <th width="60%"><?php echo JText::_('COM_JEA_HEADING_FEATURES_LIST_NAME') ?></th>
        <th width="39%"><?php echo JText::_('COM_JEA_HEADING_FEATURES_IMPORT_CSV') ?></th>
    </thead>

    <tbody>
    <?php foreach ($this->items as $i => $item) : $altrow = ( $altrow == 1 )? 0 : 1 ?>
      <tr class="row<?php echo $altrow ?>">
        <td><?php echo JHtml::_('grid.id', $i, $item); ?></td>
        <td>
          <a href="<?php echo JRoute::_('index.php?option=com_jea&view=featurelist&feature='.$item) ?>">
          <?php echo JText::_(JString::strtoupper("com_jea_list_of_{$item}_title")) ?></a>
        </td>
        <td class="center"><input type="file" name="csv[<?php echo $item ?>]" value="" size="20" /></td>
      </tr>
      <?php endforeach ?>
    </tbody>
  </table>

  <div>
    <input type="hidden" name="task" value="" /> 
    <input type="hidden" name="boxchecked" value="0" />
    <?php echo JHtml::_('form.token') ?>
  </div>
</form>
