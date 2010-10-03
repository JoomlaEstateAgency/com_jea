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
defined( '_JEXEC' ) or die( 'Restricted access' ) ;

JFilterOutput::objectHTMLSafe( $this->row, ENT_QUOTES, array('ref', 'adress') );
JHTML::_( 'behavior.calendar' );
JHTML::_( 'behavior.modal' );

jimport( 'joomla.html.pane' );
$pane =& JPane::getInstance('sliders', array('startOffset'=> $this->sliderOffset, 'startTransition'=>0));

$editor =& JFactory::getEditor();

JHTML::stylesheet('jea.admin.css', 'media/com_jea/css/');

// Ajax behavior on towns and departments


$script=<<<EOB

function updateCoordinates(content)
{
	url = 'index.php?option=com_jea&controller=ajax&task=getCoordinates&id='+$('adminForm').id.value
	var jSonRequest = new Json.Remote(url, {onComplete: function(result){
        
		text = '[ Long : '+ result.longitude + ', lat: '+ result.latitude + ' ]';
		$('coordinates').setText(text);
		
    }}).send();
}

var townOptionsCallback = {
	onComplete: function(response){
		var first = $('area_id').getFirst().clone();
	    $('area_id').empty();
	    $('area_id').appendChild(first);
	    
	    response.each(function(item){
	    	var option  = new Element('option', {'value' : item.id});
	    	option.appendText(item.value);
	    	$('area_id').appendChild(option);
	    });
	}
};

var deptOptionsCallback = {
	onComplete: function(response){
		var first = $('town_id').getFirst().clone();
	    $('town_id').empty();
	    $('town_id').appendChild(first);
	    
	    if(response){
		    response.each(function(item){
		    	var option  = new Element('option', {'value' : item.id});
		    	option.appendText(item.value);
		    	$('town_id').appendChild(option);
		    });
	    }
	}
};

EOB;

$document =& JFactory::getDocument();
$document->addScriptDeclaration($script);

if($this->params->get('relationship_dpts_towns_area', 0)) {
    $document->addScriptDeclaration("
    window.addEvent('domready', function() {
    	
    	
    	$('department_id').addEvent('change', function(){
    		var url = 'index.php?option=com_jea&controller=ajax&task=get_towns'
    		    + '&department_id=' + this.value;
    		var jSonRequest = new Json.Remote( url , deptOptionsCallback );
    		jSonRequest.send();
    	});
    	
    	$('town_id').addEvent('change', function(){
    		var url = 'index.php?option=com_jea&controller=ajax&task=get_areas'
    		    + '&town_id=' + this.value;
    		var jSonRequest = new Json.Remote( url , townOptionsCallback );
    		jSonRequest.send();
    	});
    });");
}

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

	$('content-pane').getElements('h3.title').each(function(item, count){
		if(item.hasClass('jpane-toggler-down')){
			form.sliderOffset.value = count;
		}
	});
	
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
		  <td nowrap="nowrap"><label for="title"><?php echo JText::_('Title') ?> : </label></td>
		  <td width="100%">
		  	<input id="title" type="text" name="title" value="<?php echo $this->row->title ?>" class="inputbox" size="40" />
		  </td>
		</tr>
		
		<tr>
		  <td nowrap="nowrap"><label for="alias"><?php echo JText::_('Alias') ?> : </label></td>
		  <td width="100%">
		  	<input id="alias" type="text" name="alias" value="<?php echo $this->escape( $this->row->alias ) ?>" class="inputbox" size="40" />
		  </td>
		</tr>
        
		<tr>
		  <td nowrap="nowrap"><label for="type_id"><?php echo JText::_('Property type') ?> :</label></td>
		  <td width="100%" ><?php echo  $this->getHtmlList('types', $this->row->type_id) ?></td>
		</tr>
		
		<tr>
		  <td nowrap="nowrap"><label for="adress"><?php echo JText::_('Adress') ?> :</label></td>
		  <td width="100%" >
		  	<input id="adress" type="text" name="adress" value="<?php echo $this->escape( $this->row->adress ) ?>" class="inputbox" size="70" style="margin-right: 10px" />
		  	<?php if(!empty($this->row->id)): ?>
		  	<a class="modal" rel="{handler: 'iframe', size: {x: 600, y: 500}, onClose:updateCoordinates}" 
		  	   href="index.php?option=com_jea&controller=properties&task=geolocalization&tmpl=component&id=<?php echo $this->row->id?>">
		  	<?php echo JText::_('Geolocalization')?></a> 
		  	<span id="coordinates">[ Long : <?php echo $this->row->longitude ?>, lat: <?php echo $this->row->latitude ?> ]</span>
		  	<?php endif ?>
		  </td>
		</tr>		
		
		<tr>
		  <td nowrap="nowrap"><label for="zip_code"><?php echo JText::_('Zip code') ?> :</label></td>
		  <td width="100%" >
			  <input id="zip_code" type="text" name="zip_code" size="5" value="<?php echo $this->row->zip_code ?>" class="inputbox" />
			  <span style="margin-left:25px">
			  <?php echo $this->getHtmlList('departments', $this->row->department_id) ?>
			  <?php if($this->params->get('relationship_dpts_towns_area', 0)): ?>
        		  <?php echo $this->getTownsList($this->row->town_id, $this->row->department_id) ?>
        		  <?php echo $this->getAreasList($this->row->area_id, $this->row->town_id) ?>
    		  <?php else :?>
        		  <?php echo $this->getHtmlList('towns', $this->row->town_id) ?>
        		  <?php echo $this->getHtmlList('areas', $this->row->area_id) ?>
    		  <?php endif ?>
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
  
  <?php if(!empty($this->row->id)): ?>
  <table width="100%" style="border: 1px dashed silver; padding: 5px; margin-bottom: 10px;">
  	<tr>
  		<td><strong><?php echo JText::_( 'Property ID' ) ?>:</strong></td>
  		<td><?php echo $this->row->id ?></td>
  	</tr>
  	<tr>
  		<td><strong><?php echo JText::_( 'State' ) ?>:</strong></td>
  		<td><?php echo $this->row->published == 1 ? JText::_( 'Published' ) : JText::_( 'Unpublished' ) ?></td>
  	</tr>
  	<tr>
  		<td><strong><?php echo JText::_( 'hits' ); ?>:</strong></td>
  		<td><?php echo intval($this->row->hits) ?>
  		<?php if(!empty($this->row->hits)): ?>
			<input name="reset_hits" type="button" class="button" value="<?php echo JText::_( 'Reset' ); ?>" onclick="submitbutton('resethits');" />
		<?php endif ?>
  		</td>
  	</tr>
  	<tr>
  		<td><strong><?php echo JText::_( 'Created' ) ?>:</strong></td>
  		<td><?php echo JHTML::_('date',  $this->row->date_insert,  JText::_('DATE_FORMAT_LC2') ) ?></td>
  	</tr>
  </table>
  <?php endif ?>
  
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
		
	  <?php if ($this->row->is_renting): ?> 
	  <tr>
		  <td nowrap="nowrap" class="label"><label for="deposit"><?php echo JText::_('Deposit') ?> :</label></td>
		  <td>
		  	<input id="deposit" type="text" name="deposit" value="<?php echo $this->row->deposit ?>" class="numberbox" /> 
		  	<?php echo $this->params->get('currency_symbol', '&euro;') ?> 
		  </td>
	  </tr>
	  <?php endif ?>
		
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
				<a class="modal" rel="{handler: 'iframe', size: {x: 400, y: 150}}" href="<?php echo $this->main_image['iptc_url'] ?>" >
				<img src="<?php echo $this->main_image['min_url'] ?>" alt="preview.jpg" title="<?php echo $this->main_image['width'].'X'.$this->main_image['height'].'px - '.$this->main_image['weight'].' ko' ?>" />
				</a>
				<a href="<?php echo $this->main_image['delete_url'] ?>"><?php echo JText::_('Delete') ?></a>
			</fieldset>
			<?php endif ?>
	  </fieldset>
	  <fieldset><legend style="font-size:11px"><?php echo JText::_('Secondaries property pictures') ?></legend>
	  	  <?php for($i=0; $i < $this->params->get('secondaries_img_upload_number', 3); $i++): ?>
		      <input type="file" name="second_image[]" value=""  size="30"/> <br />
		  <?php endfor ?>
		      <input class="button" type="button" value="<?php echo JText::_('Send') ?>" onclick="submitbutton('apply')" />
		  <div style="height:200px; overflow:auto;">
		  <?php foreach($this->secondaries_images as $image) : ?>
			<fieldset style="margin-top:10px;">
			<a class="modal" rel="{handler: 'iframe', size: {x: 400, y: 150}}" href="<?php echo $image['iptc_url'] ?>" >
			<img src="<?php echo $image['min_url'] ?>" alt="<?php echo $image['name'] ?>" title="<?php echo $image['width'].'X'.$image['height'].'px - '.$image['weight'].' ko' ?>" />
			</a>
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
	    <td><?php echo JHTML::_('list.users', 'created_by', $this->row->created_by, 1, null, 'name', 0 ) ?></td>
	  </tr>
	</table>
  <?php echo  $pane->endPanel() ?>
  <?php echo $pane->endPane() ?>
  </td>
  
  </tr>
  </table>
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="id" value="<?php echo $this->row->id ?>" />
  <input type="hidden" name="sliderOffset" value="0" />
  <?php echo JHTML::_( 'form.token' ) ?>
</form>

