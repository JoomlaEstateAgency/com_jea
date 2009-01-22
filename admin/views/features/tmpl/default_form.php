<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 * 
 * @version     O.7 2009-01-22
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
defined( '_JEXEC' ) or die( 'Restricted access' ) ;

JHTML::stylesheet('jea.admin.css', 'media/com_jea/css/');
?>

<script language="javascript" type="text/javascript">

function submitbutton( pressbutton, section ) {

	var form = document.adminForm;
	
	if ( pressbutton == 'apply' || pressbutton == 'save' ) {
	
		if ( form.value.value == "" ) {
			alert( '<?php echo JText::_('You have to write a value') ?>' );
			return;
			
		}
	}	
	
	submitform(pressbutton);
	return;
}

</script>

<form action="index.php?option=com_jea&controller=features" method="post" name="adminForm" id="adminForm">
  

  <table class="adminform"> 
	<tr>
	  <td nowrap="nowrap"><label for="value"><?php echo JText::_('Value') ?> :</label></td>
	  <td width="100%" ><input id="value" type="text" name="value" value="<?php echo $this->escape( $this->row->value ) ?>" class="inputbox" size="50" /></td>
	</tr>
  </table>
  
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="id" value="<?php echo $this->row->id ?>" />
  <?php echo JHTML::_( 'form.token' ) ?>
</form>

