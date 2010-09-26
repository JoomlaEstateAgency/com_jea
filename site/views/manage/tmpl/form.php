<?php 
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @version     $Id$
 * @package     Jea.site
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
JHTML::_( 'behavior.calendar' );
JHTML::_( 'behavior.modal' );

JFilterOutput::objectHTMLSafe( $this->row );
$editor =& JFactory::getEditor();

// Ajax behavior on towns and departments


$ajaxScript=<<<EOB

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

window.addEvent('domready', function() {
	
	
	$('department_id').addEvent('change', function(){
		var url = 'index.php?option=com_jea&controller=frontajax&task=get_towns'
		    + '&department_id=' + this.value;
		var jSonRequest = new Json.Remote( url , deptOptionsCallback );
		jSonRequest.send();
	});
	
	$('town_id').addEvent('change', function(){
		var url = 'index.php?option=com_jea&controller=frontajax&task=get_areas'
		    + '&town_id=' + this.value;
		var jSonRequest = new Json.Remote( url , townOptionsCallback );
		jSonRequest.send();
	});
});

EOB;

$document=& JFactory::getDocument();

if($this->params->get('relationship_dpts_towns_area', 0)) {
    $document->addScriptDeclaration($ajaxScript);
}

$document->addScriptDeclaration( '
function checkForm() {
    var form = document.jeaForm;
    
    if ( form.ref.value == "" ) {
        alert( "' . JText::_('Property must have a reference') . '" );
        return false;
    } else if ( form.type_id.value == "0" ) {
        alert( "' . JText::_('Select a type of property') . '" );
        return false;
    }
    
    ' . $editor->save( 'description' ) .' 
    return true;
}');
    
?>


<h1><?php echo $this->page_title ?></h1>

<form action="<?php echo JRoute::_('&task=save') ?>" method="post" name="jeaForm" id="jeaForm" enctype="multipart/form-data" onsubmit="return checkForm()" >

      <table class="adminform">
        <tr>
          <td nowrap="nowrap"><label for="ref"><?php echo JText::_('Reference') ?> : </label></td>
          <td width="100%">
            <input id="ref" type="text" name="ref" value="<?php echo $this->escape( $this->row->ref ) ?>" class="inputbox" />
           
            <?php echo JHTML::_('select.booleanlist',  'is_renting' , 'class="inputbox"' , $this->row->is_renting, 'Renting', 'Selling' ) ; ?>
          </td>
        </tr>
        
        <tr>
          <td nowrap="nowrap"><label for="title"><?php echo JText::_('Title') ?> : </label></td>
          <td width="100%">
            <input id="title" type="text" name="title" value="<?php echo $this->escape( $this->row->title ) ?>" class="inputbox" size="45" />
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
          <td valign="top" colspan="2"><?php echo JText::_('Description') ?> :</td>
        </tr>
        <tr>
          <td colspan="2" style="vertical-align:top">
          <?php
                // parameters : areaname, content, width, height, cols, rows, show buttons
                echo $editor->display( 'description',  $this->row->description , '100%', '300', '75', '20', false ) ;
          ?>
          
          </td>
        </tr>
      </table>
      
      
    <fieldset><legend><?php echo JText::_('Price') ?></legend>
    <table>
    
      <tr>
          <td nowrap="nowrap" class="label">
            <label for="price"><?php echo  $this->row->is_renting ? JText::_('Renting price') : JText::_('Selling price') ?> :</label>
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
        
      </table>   
      </fieldset>
      
      <fieldset><legend><?php echo JText::_('Description') ?></legend>
      <table>
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
    </table>
    </fieldset>
    
    <fieldset><legend><?php echo JText::_('Advantages') ?></legend>
    <?php echo $this->getAdvantagesRadioList() ?>
    </fieldset>


  <fieldset><legend><?php echo JText::_('Main property picture') ?></legend>
      <input type="file" name="main_image" value=""  size="30"/> <input class="button" type="button" value="<?php echo JText::_('Send') ?>" onclick="if (checkForm()) this.form.submit()" />
        <?php if (!empty($this->main_image)) : ?>
        <fieldset style="margin-top:10px;">
        	<a class="modal" rel="{handler: 'iframe', size: {x: 400, y: 150}}" href="<?php echo $this->main_image['iptc_url'] ?>" >
            <img src="<?php echo $this->main_image['min_url'] ?>" alt="preview.jpg" title="<?php echo $this->main_image['width'].'X'.$this->main_image['height'].'px - '.$this->main_image['weight'].' ko' ?>" />
            </a>
            <a href="<?php echo $this->main_image['delete_url'] ?>"><?php echo JText::_('Delete') ?></a>
        </fieldset>
        <?php endif ?>
  </fieldset>
  
  <fieldset><legend><?php echo JText::_('Secondaries property pictures') ?></legend>
      <input type="file" name="second_image" value=""  size="30"/> <input class="button" type="button" value="<?php echo JText::_('Send') ?>" onclick="if (checkForm()) this.form.submit()" />
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
    
  <fieldset><legend><?php echo JText::_('Miscellaneous informations') ?></legend>
    <table>
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
    </table>
  </fieldset>
  
  <p style="margin-top:20px">
      <input type="hidden" name="id" value="<?php echo $this->row->id ?>" />
      <?php echo JHTML::_( 'form.token' ) ?>
      <input type="submit" value="<?php echo JText::_('Save') ?>" />
  </p>
  
</form>