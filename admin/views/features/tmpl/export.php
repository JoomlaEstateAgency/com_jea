<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 * 
 * @version     $Id$
 * @package		Jea.admin
 * @copyright	Copyright (C) 2008 PHILIP Sylvain. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla Estate Agency is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses.
 * 
 */


// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

JHTML::stylesheet('jea.admin.css', 'media/com_jea/css/');

$config =& JFactory::getConfig();
$tablePrefix = $config->getValue('config.dbprefix');
?>

<form action="index.php?option=com_jea&controller=features" method="post" name="adminForm" id="adminForm">

<fieldset><legend><?php echo JText::_('Choose witch tables to export')?></legend>
<p>
<?php foreach($this->tablesTranslations as $tableName => $tableLabel): ?>
    <input type="checkbox"
           name="export_table[]"
           value="<?php echo $tableName ?>" 
           id="tbl_<?php echo $tableName?>" 
           checked="checked" />
           
    <label for="tbl_<?php echo $tableName ?>"> 
    <?php echo JText::_($tableLabel) . ' ('.$tablePrefix.'jea_'.$tableName.')' ?></label><br />
<?php endforeach ?>
</p>

<p><input type="submit" value="<?php echo JText::_('Export') ?>" /></p>

</fieldset>

<input type="hidden" name="task" value="export" />
<?php echo JHTML::_( 'form.token' ) ?>

</form>
