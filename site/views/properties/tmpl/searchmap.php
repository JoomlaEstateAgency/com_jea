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
defined('_JEXEC') or die('Restricted access');

JHTML::stylesheet('jea.css', 'media/com_jea/css/');

$category = $this->params->get('category', 0);

JHTML::script('search.js', 'media/com_jea/js/', true);
JHTML::script('geoSearch.js', 'media/com_jea/js/');
JHTML::script('geoxml3.js', 'media/com_jea/js/');
JHTML::script('biSlider.js', 'media/com_jea/js/');

$document =& JFactory::getDocument();
$langs  = explode('-', $document->getLanguage());

$lang   = $langs[0];
$region = $langs[1];

$document->addScript('http://maps.google.com/maps/api/js?sensor=false&language=' . $lang
                                                                . '&region=' . $region );
                                                                
$rentingPriceLimit = $this->getFieldLimit('price', 'renting');
$sellingPriceLimit = $this->getFieldLimit('price', 'selling');

$rentingSpaceLimit = $this->getFieldLimit('living_space', 'renting');
$sellingSpaceLimit = $this->getFieldLimit('living_space', 'selling');

$default_area = $this->params->get('default_map_area', $lang);
$currency_symbol = $this->params->get('currency_symbol', '&euro;');
$surface_measure = $this->params->get( 'surface_measure');
$map_width  = $this->params->get('map_width', 0);
$map_height = $this->params->get( 'map_height', 400);

                                                                 
//initialize the form when the page load
$document->addScriptDeclaration("

var rentingMinPrice = {$rentingPriceLimit[0]};
var rentingMaxPrice = {$rentingPriceLimit[1]};
var sellingMinPrice = {$sellingPriceLimit[0]};
var sellingMaxPrice = {$sellingPriceLimit[1]};

var rentingMinSpace = {$rentingSpaceLimit[0]};
var rentingMaxSpace = {$rentingSpaceLimit[1]};
var sellingMinSpace = {$sellingSpaceLimit[0]};
var sellingMaxSpace = {$sellingSpaceLimit[1]};

var minPrice  = 0;
var maxPrice  = 0;
var minSpace  = 0;
var maxSpace  = 0;

function refreshKml() {

	params = {};
	params['cat'] = getCurrentCategory();
	
	var numericFields = [
    	'type_id', 
    	'department_id', 
    	'town_id', 
    	'budget_min', 
    	'budget_max',
    	'living_space_min',
    	'living_space_max',
    	'rooms_min'
	];
	
	numericFields.each(function(field){
	    if($(field)){
	        if($(field).value > 0) {
	            params[field] = $(field).value;
	        }
	    }
	});
	
	$$('.advantage').each(function(item){
          if (item.getElement('input').checked) {
              params[item.getElement('input').name] = item.getElement('input').value;
          }
    });
			
	geoSearch.updateMap(params);
}

function getCurrentCategory() {
    var cat = ''; 
    if($('renting') && $('selling')) {
		cat = $('renting').checked ? 'renting' : 'selling' ;
	} else {
		cat = $('cat').value;
	}
	return cat;
}

function updateCategory() {
    
    if(!$('budget_min') && !$('living_space_min')) {
        // no advanced search
        return;
    }

	var cat = getCurrentCategory();
	
	switch(cat) {
        case 'renting' :
            minPrice = rentingMinPrice;
            maxPrice = rentingMaxPrice;
            minSpace = rentingMinSpace;
            maxSpace = rentingMaxSpace;
            break;
        case 'selling' :
            minPrice = sellingMinPrice;
            maxPrice = sellingMaxPrice;
            minSpace = sellingMinSpace;
            maxSpace = sellingMaxSpace;
            break;
	}

	$('budget_min').value = minPrice;
    $('budget_max').value = maxPrice;
    $('min_price').setHTML(minPrice + ' $currency_symbol');
    $('max_price').setHTML(maxPrice + ' $currency_symbol');
    
    $('living_space_min').value = minSpace;
    $('living_space_max').value = maxSpace;
    $('min_space_value').setHTML(minSpace + ' $surface_measure');
    $('max_space_value').setHTML(maxSpace + ' $surface_measure');
}


window.addEvent('domready', function() {
        
      geoSearch = new JeaGeoSearch('map_canvas', {
          counterElement : 'properties_count',
          defaultArea : '{$default_area}'
      });
      
      updateCategory();
      
      refreshKml();
      
      // refresh AjaxForm
      refreshForm();

      $$('#type_id', '#department_id', '#town_id', '#selling', '#renting').each(function(item){
      		item.addEvent('change', function() {
      		    updateCategory();
      			updateList(this);
      			refreshKml();
      		});
      });
      
      $$('.advantage').each(function(item){
          item.getElement('input').addEvent('change', refreshKml);
      });
      
      // Sliders init
      
      priceSlide = new BiSlider($('price_slider'), $('knob1'), $('knob2'), {
        	steps: 100,
        	onChange: function(steps){
        	    var priceDiff = maxPrice - minPrice;
        	    
        	    $('budget_min').value = Math.round(((priceDiff * steps.minimum ) / 100) + minPrice);
        		$('budget_max').value = Math.round(((priceDiff * steps.maximum ) / 100) + minPrice);
        	    
        		$('min_price').setHTML($('budget_min').value + ' $currency_symbol');
        		$('max_price').setHTML($('budget_max').value + ' $currency_symbol');
        		
        	},
        	onComplete: function(step) {
        	    refreshKml();
        	}
      });
      
      spaceSlide = new BiSlider($('space_slider'), $('knob3'), $('knob4'), {
        	steps: 100,
        	onChange: function(steps){
        	    var spaceDiff = maxSpace - minSpace;
        	    
        	    $('living_space_min').value = Math.round(((spaceDiff * steps.minimum ) / 100) + minSpace);
        		$('living_space_max').value = Math.round(((spaceDiff * steps.maximum ) / 100) + minSpace);

                $('min_space_value').setHTML($('living_space_min').value + ' $surface_measure');
                $('max_space_value').setHTML($('living_space_max').value + ' $surface_measure');
        	},
        	onComplete: function(step) {
        	    refreshKml();
        	}
      });

});");
?>

<?php if ( $this->params->get('show_page_title', 0) && $this->params->get('page_title', '') ) : ?>
<h1><?php echo $this->params->get('page_title') ?></h1>
<?php endif ?>

<form action="<?php echo JRoute::_('&task=search&layout=default') ?>" method="post" id="jea_search_form" enctype="application/x-www-form-urlencoded" >

    <?php if($category == 1): ?>
    <input type="hidden" id="cat" name="cat" value="selling" />
    <?php elseif($category == 2): ?>
    <input type="hidden" id="cat" name="cat" value="renting" />
    <?php else: ?>
	<p>
    <input type="radio" name="cat" id="renting" value="renting" checked="checked" />
    <label for="renting"><?php echo JText::_('Renting') ?></label>
    <input type="radio" name="cat" id="selling" value="selling" />
    <label for="selling"><?php echo JText::_('Selling') ?></label>
    </p>
    <?php endif ?>
    
    <p>
    <?php if ($this->params->get('show_types', 1) == 1):?>
    <select id="type_id" name="type_id" class="inputbox"><option value="0"> </option></select>
    <?php endif ?>
    <?php if ($this->params->get('show_departments', 1) == 1):?>
    <select id="department_id"  name="department_id" class="inputbox" ><option value="0"> </option></select>
    <?php endif ?>
    <?php if ($this->params->get('show_towns', 1) == 1):?>
    <select id="town_id" name="town_id"  class="inputbox"><option value="0"> </option></select>
    <?php endif ?>
    
    <span id="found_properties"><?php echo JText::_('Properties found')?> : <span id="properties_count">0</span></span>
    </p>
    
    <div id="map_canvas" style="width: <?php echo $map_width ? $map_width.'px': '100%'?>; height: <?php echo $map_height.'px'?>"></div>
  	
  	
<?php if ( $this->params->get('advanced_search', 0)): ?>
<div class="clr"></div>

<div class="jea_slider_block"> 
    <h2><?php echo JText::_('Budget') ?></h2>
    <div id="price_slider" class="slider_background">
    	<div id="knob1" class="knob"></div><div id="knob2" class="knob"></div>
    </div>
    <div class="slider_infos">
        <span class="slider_min_value" id="min_price">0</span> <?php echo JText::_('To') ?>    
        <span class="slider_max_value" id="max_price">0</span>
    </div>
    <input id="budget_max" type="hidden" name="budget_max" />
    <input id="budget_min" type="hidden" name="budget_min" />
</div>

<div class="jea_slider_block"> 
    <h2><?php echo JText::_('Living space') ?></h2>
    <div id="space_slider" class="slider_background">
    	<div id="knob3" class="knob"></div><div id="knob4" class="knob"></div>
    </div>
    <div class="slider_infos">
        <span class="slider_min_value" id="min_space_value">0</span> <?php echo JText::_('To') ?>    
        <span class="slider_max_value" id="max_space_value">0</span>
    </div>
    <input id="living_space_min" type="hidden" name="living_space_min" />
    <input id="living_space_max" type="hidden" name="living_space_max" />
</div>

  	
  	<p><?php echo JText::_('Minimum number of rooms') ?>  : <input type="text" name="rooms_min" size="1" />
  	<input type="button" value="<?php echo JText::_('Ok')?>" onclick="refreshKml()">
  	</p>
<div class="clr"></div>
 	
<div id="advantages_list">
  	<div class="clr" ></div>
  	<?php echo $this->getAdvantages('', 'checkbox') ?>
  	<div class="clr" ></div>
</div>
<?php endif ?>

<p>
<input type="submit" class="button" value="<?php echo JText::_('List properties') ?>" />
<input type="hidden" name="Itemid" value="<?php echo JRequest::getInt('Itemid', 0) ?>" />
<?php echo JHTML::_( 'form.token' ) ?>
</p>
  
</form>
