<?php 
/**
 * This file is part of Catalog - Joomla! extension for build commercial catalog
 * 
 * @package     Catalog.admin
 * @copyright   Copyright (C) 2008 PHILIP Sylvain. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 * Catalog is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses.
 * 
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ) ;

?>



<form action="index.php?option=com_jea&controller=properties" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" >

	  <table width="100%" cellspacing="1" class="paramlist admintable" >
	    <tbody>
		<tr>
		  <td width="40%"><label for="title"><?php echo JText::_('Title') ?> : </label></td>
		  <td width="66%">
		  	<input id="title" type="text" name="title" value="<?php echo $this->infos->title ?>" class="inputbox" size="20" />
		  </td>
		</tr>
		
		<tr>
          <td width="40%"><label for="description"><?php echo JText::_('Description') ?> : </label></td>
          <td width="66%">
            <textarea id="description" name="description"  class="inputbox" cols="30" rows="2" ><?php echo $this->infos->description ?></textarea>
          </td>
        </tr>
		</tbody>
	  </table>
	  

  <input type="submit" value="<?php echo JText::_('Save') ?>" />
  <input type="hidden" name="task"  value="saveiptc" />
  <input type="hidden" name="id"    value="<?php echo JRequest::getInt('id') ?>" />
  <input type="hidden" name="image" value="<?php echo JRequest::getVar('image') ?>" />
</form>

