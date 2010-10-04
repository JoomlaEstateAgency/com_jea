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

<form action="index.php?option=com_jea&controller=features" 
      method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" >

<fieldset><legend><?php echo JText::_('Choose witch tables to update')?></legend>
<table>
	<thead>
    	<tr>
        <th><?php echo JText::_('Table Name')?></th>
        <th><?php echo JText::_('CSV file')?></th>
        </tr>
    </thead>
    <tbody>
<?php foreach($this->tablesTranslations as $tableName => $tableLabel): ?>
    	<tr>
        <td><label for="tbl_<?php echo $tableName ?>"> 
        <?php echo JText::_($tableLabel) . ' ('.$tablePrefix.'jea_'.$tableName.')' ?></label>
        </td>
        <td>
        <input type="file" name="<?php echo $tableName ?>" value="" 
               size="20" id="tbl_<?php echo $tableName ?>" />
        </td>
        </tr>
<?php endforeach ?>
	</tbody>
</table>

<p><input type="submit" value="<?php echo JText::_('Import') ?>" /></p>

</fieldset>

<input type="hidden" name="task" value="import" />
<?php echo JHTML::_( 'form.token' ) ?>

</form>
