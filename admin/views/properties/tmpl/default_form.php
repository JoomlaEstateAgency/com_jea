<?php 
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 * 
 * @version		0.4 2008-06
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

JFilterOutput::objectHTMLSafe( $this->row );
JHTML::_( 'behavior.calendar' );

jimport( 'joomla.html.pane' );
$pane =& JPane::getInstance('sliders');

$editor =& JFactory::getEditor();

JHTML::stylesheet('jea.admin.css', 'media/com_jea/css/');
?>

<script language="javascript" type="text/javascript">

function submitbutton( pressbutton, section ) {
	var form = document.adminForm;
	if (pressbutton == 'apply' || pressbutton == 'save') {
		if ( form.ref.value == "" ) {
			alert( "<?php echo JText::_('Property must have a reference') ?>" );
			return;
		} else if ( form.type_id.value == "0" ) {
			alert( "<?php echo JText::_('Select a type of property') ?>" );
			return;
		}
	}
	
	<?php echo $editor->save( 'description' ) ?>
	submitform(pressbutton);
	return;
}

</script>

<form action="index.php?option=com_jea&controller=properties&cat=<?php echo $this->get('category') ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" >
  
  <table cellspacing="0" cellpadding="0" border="0" width="100%" id="jea-edit" >
  <tr>
  <td valign="top">
	  <table class="adminform">
		<tr>
		  <td nowrap="nowrap"><label for="ref"><?php echo JText::_('Reference') ?> : </label></td>
		  <td width="100%">
		  	<input id="ref" type="text" name="ref" value="<?php echo $this->escape( $this->row->ref ) ?>" class="inputbox" />
		  </td>
		</tr>
        
		<tr>
		  <td nowrap="nowrap"><label for="type_id"><?php echo JText::_('Property type') ?> :</label></td>
		  <td width="100%" ><?php echo  $this->getHtmlList('types', $this->row->type_id) ?></td>
		</tr>
		
		<tr>
		  <td nowrap="nowrap"><label for="adress"><?php echo JText::_('Adress') ?> :</label></td>
		  <td width="100%" >
		  	<input id="adress" type="text" name="adress" value="<?php echo $this->escape( $this->row->adress ) ?>" class="inputbox" size="70" />
		  </td>
		</tr>		
		
		<tr>
		  <td nowrap="nowrap"><label for="zip_code"><?php echo JText::_('Zip code') ?> :</label></td>
		  <td width="100%" >
			  <input id="zip_code" type="text" name="zip_code" size="5" value="<?php echo $this->row->zip_code ?>" class="inputbox" />
			  <span style="margin-left:25px">
		      <?php echo $this->getHtmlList('towns', $this->row->town_id) ?>
			  <?php echo $this->getHtmlList('areas', $this->row->area_id) ?>
			  <?php echo $this->getHtmlList('departments', $this->row->department_id) ?>
			  </span>
		  </td>
		</tr>
		
		<tr>
		  <td nowrap="nowrap"><?php echo JText::_('Published') ?> : </td>
		  <td><?php echo JHTML::_('select.booleanlist',  'published' , 'class="inputbox"' , $this->row->published ) ; ?></td>
		</tr>
		
		<tr>
		  <td nowrap="nowrap"><label for="emphasis"><?php echo JText::_('Emphasis') ?> : </label></td>
		  <td width="100%" >
		  	<input type="checkbox" value="1" id="emphasis" name="emphasis"  <?php if ( $this->row->emphasis ) echo 'checked="checked"' ?> />
		  </td>
		</tr>
		
		<tr>
		  <td valign="top" colspan="2"><?php echo JText::_('Description') ?> :</td>
		</tr>
		<tr>
		  <td colspan="2" style="vertical-align:top">
		  <?php
				// parameters : areaname, content, width, height, cols, rows, show buttons
				echo $editor->display( 'description',  $this->row->description , '100%', '400', '75', '20', false ) ;
		  ?>
		  
		  </td>
		</tr>
	  </table>
  </td>
  
  <td valign="top" width="330" nowrap="nowrap" style="padding: 7px 0 0 5px" >
  <?php echo $pane->startPane("content-pane") ?>
  
  <?php echo $pane->startPanel( JText::_('Specifications') , "params-pane" ) ?>
    <table>
	  <tr>
	    <th colspan="2" style="text" ><?php echo JText::_('Price') ?></th>
	  </tr>
		
	  <tr>
		  <td nowrap="nowrap" class="label">
		  	<label for="price"><?php echo  $this->row->is_renting ? JText::_('Rent') : JText::_('price') ?> :</label>
		  </td>
		  <td width="100%">
			  <input id="price" type="text" name="price" value="<?php echo $this->row->price ?>" class="numberbox" /> 
			  <?php echo $this->params->get('currency_symbol', '&euro;') ?> 
		  </td>
	  </tr>
		
	  <tr>
		  <td nowrap="nowrap" class="label"><label for="charges"><?php echo JText::_('Charges') ?> :</label></td>
		  <td width="100%">
		  	<input id="charges" type="text" name="charges" value="<?php echo $this->row->charges ?>" class="numberbox" /> 
		  	<?php echo $this->params->get('currency_symbol', '&euro;') ?> 
		  </td>
		</tr>
		
		<tr>
		  <td nowrap="nowrap" class="label"><label for="fees"><?php echo JText::_('Fees') ?> :</label></td>
		  <td class="spacer_bottom" >
		  	<input id="fees" type="text" name="fees" value="<?php echo $this->row->fees ?>" class="numberbox" /> 
		  	<?php echo $this->params->get('currency_symbol', '&euro;') ?> 
		  </td>
		</tr>	  
	  
	  <tr>
	    <th colspan="2"><?php echo JText::_('Description') ?></th>
	  </tr>
	  
		<tr>
		  <td nowrap="nowrap" class="label">
		  	<label for="condition_id"><?php echo JText::_('General condition') ?> :</label>
		  </td>
		  <td width="100%"><?php echo  $this->getHtmlList('conditions', $this->row->condition_id ) ?></td>
		</tr>
		
		<tr>
		  <td nowrap="nowrap" class="label">
		  	<label for="living_space"><?php echo JText::_('Living space') ?> :</label>
		  </td>
		  <td width="100%">
	      	<input id="living_space" type="text" name="living_space" value="<?php echo $this->row->living_space ?>" class="numberbox" size="7" /> 
			<?php echo $this->params->get('surface_measure', 'M&sup2;') ?> 
		  </td>
		</tr>
		
		<tr>
		  <td nowrap="nowrap" class="label"><label for="land_space"><?php echo JText::_('Land space') ?> :</label></td>
		  <td width="100%">
		  	<input id="land_space" type="text" name="land_space" value="<?php echo $this->row->land_space ?>" class="numberbox" size="7" /> 
		  	<?php echo $this->params->get('surface_measure', 'M&sup2;') ?> 
		  </td>
		</tr>
		
		<tr>
		  <td nowrap="nowrap" class="label"><label for="floor"><?php echo JText::_('Floors') ?> :</label></td>
		  <td width="100%">
		  	<input id="floor" type="text" name="floor" value="<?php echo $this->row->floor ?>" class="numberbox" size="3" />
		  </td>
		</tr>
		
		<tr>
		  <td nowrap="nowrap" class="label"><label for="rooms"><?php echo JText::_('Number of rooms') ?> :</label></td>
		  <td width="100%"><input id="rooms" type="text" name="rooms" value="<?php echo $this->row->rooms ?>" class="numberbox" size="3" /> </td>
		</tr>
		
		<tr>
		  <td nowrap="nowrap" class="label"><label for="bathrooms"><?php echo JText::_('Number of bathrooms') ?> :</label></td>
		  <td width="100%"><input id="bathrooms" type="text" name="bathrooms" value="<?php echo $this->row->bathrooms ?>" class="numberbox" size="3" /> </td>
		</tr>
		
		<tr>
		  <td nowrap="nowrap" class="label"><label for="toilets"><?php echo JText::_('Number of toilets') ?> :</label></td>
		  <td><input id="toilets" type="text" name="toilets" value="<?php echo $this->row->toilets ?>" class="numberbox" size="3" /> </td>
		</tr>	
	  			
	    <tr>
		  <td nowrap="nowrap" class="label"><label for="hot_water_type" ><?php echo JText::_('Hot water type') ?> :</label></td>
		  <td width="100%"><?php echo $this->getHtmlList('hotwatertypes', $this->row->hot_water_type) ?></td>
	    </tr>		
			
	    <tr>
		  <td nowrap="nowrap" class="label"><label for="heating_type"><?php echo JText::_('Heating type') ?> :</label></td>
		  <td class="spacer_bottom" ><?php echo $this->getHtmlList('heatingtypes', $this->row->heating_type) ?></td>
		</tr>
    
    <tr>
	    <th colspan="2"><?php echo JText::_('Advantages') ?></th>
	</tr>
	
	<tr>
	   <td colspan="2" >
	   <?php echo $this->getAdvantagesRadioList() ?>
	   </td>
	</tr>
    
  </table> 
  <?php echo  $pane->endPanel() ?>
  <?php echo $pane->startPanel( JText::_('Pictures') ,'picture-pane') ?>
    <table>
	<tr>
	  <th><?php echo JText::_('Property pictures') ?></th>
	</tr>
	<tr>
	  <td>
	  <fieldset><legend style="font-size:11px"><?php echo JText::_('Main property picture') ?></legend>
		  <input type="file" name="main_image" value=""  size="30"/> <input class="button" type="button" value="<?php echo JText::_('Send') ?>" onclick="submitbutton('apply')" />
			<?php if (!empty($this->main_image)) : ?>
			<fieldset style="margin-top:10px;">
				<img src="<?php echo $this->main_image['min_url'] ?>" alt="preview.jpg" title="<?php echo $this->main_image['width'].'X'.$this->main_image['height'].'px - '.$this->main_image['weight'].' ko' ?>" />
				<a href="<?php echo $this->main_image['delete_url'] ?>"><?php echo JText::_('Delete') ?></a>
			</fieldset>
			<?php endif ?>
	  </fieldset>
	  <fieldset><legend style="font-size:11px"><?php echo JText::_('Secondaries property pictures') ?></legend>
		  <input type="file" name="second_image" value=""  size="30"/> <input class="button" type="button" value="<?php echo JText::_('Send') ?>" onclick="submitbutton('apply')" />
		  <div style="height:200px; overflow:auto;">
		  <?php foreach($this->secondaries_images as $image) : ?>
			<fieldset style="margin-top:10px;">
			<img src="<?php echo $image['min_url'] ?>" alt="<?php echo $image['name'] ?>" title="<?php echo $image['width'].'X'.$image['height'].'px - '.$image['weight'].' ko' ?>" />
			<a href="<?php echo $image['delete_url'] ?>"><?php echo JText::_('Delete') ?></a>
			</fieldset>
		  <?php endforeach ?>
		  </div>
	  </fieldset>
	  
	  </td>
	</tr>
    </table>
  <?php echo  $pane->endPanel() ?>
  <?php echo $pane->startPanel( JText::_('Miscellaneous') , "miscellaneous-pane" ) ?>
    <table>
	  <tr>
	    <th colspan="2"><?php echo JText::_('Miscellaneous informations') ?></th>
	  </tr>
	  <tr>
		  <td nowrap="nowrap"><label for="slogan_id"><?php echo JText::_('Slogan') ?> :</label></td>
		  <td width="100%"><?php echo $this->getHtmlList('slogans', $this->row->slogan_id ) ?></td>
	  </tr>
      <tr>
		<td><label for="availability"><?php echo JText::_('Property availability') ?> : </label></td>
		<td><input type="text" class="text_area" id="availability" name="availability" value="<?php echo $this->row->availability ?>" />
		<input type="reset" class="button" value="..." onclick="return showCalendar('availability', '%Y-%m-%d');" />
		</td>
	  </tr>
	  <tr>
	    <td><label for="created_by"><?php echo JText::_('Author') ?> :</label></td>
	    <td><?php echo JHTML::_('list.users', 'created_by', $this->row->created_by ) ?></td>
	  </tr>
	</table>
  <?php echo  $pane->endPanel() ?>
  <?php echo $pane->endPane() ?>
  </td>
  
  </tr>
  </table>
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="id" value="<?php echo $this->row->id ?>" />
  <?php echo JHTML::_( 'form.token' ) ?>
</form>

