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
JHTML::_('behavior.tooltip');
?>

<?php if (!empty( $this->sidebar)) : ?>
<div id="j-sidebar-container" class="span2">
  <?php echo $this->sidebar ?>
</div>
<?php endif ?>

<div id="j-main-container" class="span10">
<p><?php echo JText::_('COM_JEA_IMPORT_FROM_JEA_DESC')?></p>

<form action="<?php echo JRoute::_('index.php?option=com_jea&task=properties.import') ?>" method="post" name="adminForm" id="adminForm" enctype="application/x-www-form-urlencoded">
  <div class="width-100">
    <fieldset class="adminform">
      <legend>
      <?php echo JText::_('COM_JEA_IMPORT_PARAMETERS') ?>
      </legend>
      
      <ul class="adminformlist">
        <li><?php echo $this->form->getLabel('jea_version') ?> <?php echo $this->form->getInput('jea_version') ?></li>
        <li><?php echo $this->form->getLabel('joomla_path') ?> <?php echo $this->form->getInput('joomla_path') ?></li>
      </ul>
      
      <div class="clr"></div>
      <p>
      <input type="hidden" name="type" value="jea" />
      <input type="submit" value="<?php echo JText::_('COM_JEA_START_IMPORT') ?>" />
      </p>

    </fieldset>
  </div>
</form>
</div>