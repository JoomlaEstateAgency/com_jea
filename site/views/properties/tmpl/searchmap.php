<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Site
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2017 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::stylesheet('media/com_jea/css/jea.css');
JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/html');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

$transationType = $this->params->get('searchform_transaction_type');
$Itemid = JFactory::getApplication()->input->getInt('Itemid', 0);

$states = array();
$filters = $this->get('Filters');

foreach ($filters as $name => $defaultValue) {
    $states['filter_'.$name] = $this->state->get('filter.'.$name, $defaultValue);
}
if (empty($transationType) && empty($states['filter_transaction_type'])) {
    // Set SELLING as default transaction_type state
    $states['filter_transaction_type'] = 'SELLING';
} elseif (!empty($transationType) && empty($states['filter_transaction_type'])) {
    $states['filter_transaction_type'] = $transationType;
}

$fields = json_encode($states);
// Load the Mootools More framework if not already inclued
JHtml::_('behavior.framework', true);
JHtml::script('media/com_jea/js/search.js', true);
JHtml::script('media/com_jea/js/geoSearch.js');
JHtml::script('media/com_jea/js/geoxml3.js');
JHtml::script('media/com_jea/js/biSlider.js');

$langs  = explode('-', $this->document->getLanguage());
$lang   = $langs[0];
$region = $langs[1];

$this->document->addScript('http://maps.google.com/maps/api/js?key=' . $this->params->get('googlemap_api_key') . '&amp;language=' . $lang
. '&amp;region=' . $region );

$model = $this->getModel();

$fieldsLimit = json_encode(array(
    'RENTING' => array(
        'price'   => $model->getFieldLimit('price', 'RENTING'),
        'surface' => $model->getFieldLimit('living_space', 'RENTING')
        ),
    'SELLING' => array(
        'price'   => $model->getFieldLimit('price', 'SELLING'),
        'surface' => $model->getFieldLimit('living_space', 'SELLING')
     ),
));

$default_area = $this->params->get('searchform_default_map_area', $lang);
$currency_symbol = $this->params->get('currency_symbol', '€');
$surface_measure = $this->params->get( 'surface_measure');
$map_width  = $this->params->get('searchform_map_width', 0);
$map_height = $this->params->get( 'searchform_map_height', 400);

//initialize the form when the page load
$this->document->addScriptDeclaration("

var minPrice = 0;
var maxPrice = 0;
var minSpace = 0;
var maxSpace = 0;

function updateSliders()
{
    if(!document.id('price_slider') && !document.id('space_slider')) {
        return;
    }
    var transaction_type = 'SELLING';
    var form = document.id('jea-search-form');

	var transTypes = document.getElements('[name=filter_transaction_type]');
	transTypes.each(function(item) {
		if (item.get('checked')) {
			transaction_type = item.get('value');
		}
	});

    var fieldsLimit = $fieldsLimit;
    minPrice = fieldsLimit[transaction_type].price[0];
    maxPrice = fieldsLimit[transaction_type].price[1];
    minSpace = fieldsLimit[transaction_type].surface[0];
    maxSpace = fieldsLimit[transaction_type].surface[1];

    if (document.id('budget_min') && document.id('budget_max')) {
        document.id('budget_min').set('value',minPrice);
        document.id('budget_max').set('value',maxPrice);
        document.id('min_price_value').set('text', minPrice + ' $currency_symbol');
        document.id('max_price_value').set('text', maxPrice + ' $currency_symbol');
    }

    if (document.id('living_space_min') && document.id('living_space_max')) {
        document.id('living_space_min').set('value',minSpace);
        document.id('living_space_max').set('value',maxSpace);
        document.id('min_space_value').set('text', minSpace + ' $surface_measure');
        document.id('max_space_value').set('text', maxSpace + ' $surface_measure');
    }
}


window.addEvent('domready', function() {

    var jeaSearch = new JEASearch('jea-search-form', {fields:$fields, useAJAX:true});

    geoSearch = new JEAGeoSearch('map_canvas', {
        counterElement : 'properties_count',
        defaultArea : '{$default_area}',
        form : 'jea-search-form',
        Itemid : {$Itemid}
    });

    updateSliders();
    geoSearch.refresh();
    jeaSearch.refresh();

    $$('#filter_type_id, #filter_department_id, #filter_town_id, #filter_area_id, #rooms_min, .amenities input').each(function(item) {
        item.addEvent('change', function() {
          geoSearch.refresh();
        });
    });

    $$('#jea-search-selling, #jea-search-renting').each(function(item) {
        item.addEvent('change', function() {
          updateSliders();
          geoSearch.refresh();
        });
    });

});");

// budget slider js
if ($this->params->get('searchform_show_budget', 1)) {
    $this->document->addScriptDeclaration("
            window.addEvent('domready', function() {
                // price slider init
                priceSlide = new BiSlider('price_slider', 'knob1', 'knob2', {
                    steps: 100,
                    onChange: function(steps){
                        var priceDiff = maxPrice - minPrice;

                        document.id('budget_min').set('value', Math.round(((priceDiff * steps.minimum ) / 100) + minPrice));
                        document.id('budget_max').set('value', Math.round(((priceDiff * steps.maximum ) / 100) + minPrice));

                        document.id('min_price_value').set('text', document.id('budget_min').get('value') + ' $currency_symbol');
                        document.id('max_price_value').set('text', document.id('budget_max').get('value') + ' $currency_symbol');

                    },
                    onComplete: function(step) {
                        geoSearch.refresh();
                    }
                });
            });
    ");
}

// living space slider js
if ($this->params->get('searchform_show_living_space', 1)) {
    $this->document->addScriptDeclaration("
            window.addEvent('domready', function() {
                // price slider init
                spaceSlide = new BiSlider('space_slider', 'knob3', 'knob4', {
                    steps: 100,
                    onChange: function(steps){
                        var spaceDiff = maxSpace - minSpace;

                        document.id('living_space_min').set('value', Math.round(((spaceDiff * steps.minimum ) / 100) + minSpace));
                        document.id('living_space_max').set('value', Math.round(((spaceDiff * steps.maximum ) / 100) + minSpace));

                        document.id('min_space_value').set('text',document.id('living_space_min').get('value') + ' $surface_measure');
                        document.id('max_space_value').set('text',document.id('living_space_max').get('value') + ' $surface_measure');
                    },
                    onComplete: function(step) {
                        geoSearch.refresh();
                    }
                });
            });
    ");
}
?>

<?php if ($this->params->get('show_page_heading', 1)) : ?>
  <?php if ($this->params->get('page_heading')) : ?>
  <h1><?php echo $this->escape($this->params->get('page_heading')) ?></h1>
  <?php else: ?>
  <h1><?php echo $this->escape($this->params->get('page_title')) ?></h1>
  <?php endif ?>
<?php endif ?>

<form action="<?php echo JRoute::_('index.php?option=com_jea&task=properties.search') ?>" method="post" id="jea-search-form" enctype="application/x-www-form-urlencoded">

  <p>
  <?php echo JHtml::_('features.types', $this->state->get('filter.type_id', 0), 'filter_type_id') ?>

  <?php if ($transationType == 'RENTING'): ?>
    <input type="hidden" name="filter_transaction_type" value="RENTING" />
  <?php elseif($transationType == 'SELLING'): ?>
    <input type="hidden" name="filter_transaction_type" value="SELLING" />
  <?php else: ?>
    <input type="radio" name="filter_transaction_type" id="jea-search-selling" value="SELLING"
           <?php if ($states['filter_transaction_type'] == 'SELLING') echo 'checked="checked"' ?> />
    <label for="jea-search-selling"><?php echo JText::_('COM_JEA_OPTION_SELLING') ?></label>

    <input type="radio" name="filter_transaction_type" id="jea-search-renting" value="RENTING"
           <?php if ($states['filter_transaction_type'] == 'RENTING') echo 'checked="checked"' ?> />
    <label for="jea-search-renting"><?php echo JText::_('COM_JEA_OPTION_RENTING') ?></label>
  <?php endif ?>
  </p>

  <p>
    <?php if ($this->params->get('searchform_show_departments', 1)): ?>
    <?php echo JHtml::_('features.departments', $states['filter_department_id'], 'filter_department_id' ) ?>
    <?php endif ?>

    <?php if ($this->params->get('searchform_show_towns', 1)): ?>
    <?php echo JHtml::_('features.towns', $states['filter_town_id'], 'filter_town_id' ) ?>
    <?php endif ?>

    <?php if ($this->params->get('searchform_show_areas', 1)): ?>
    <?php echo JHtml::_('features.areas', $states['filter_area_id'], 'filter_area_id' ) ?>
    <?php endif ?>
    <span id="found_properties"><?php echo JText::_('COM_JEA_FOUND_PROPERTIES')?> : <span id="properties_count">0</span></span>
  </p>

  <div id="map_canvas" style="width: <?php echo $map_width ? $map_width.'px': '100%'?>; height: <?php echo $map_height.'px'?>"></div>

  <div class="clr"></div>

  <?php if ($this->params->get('searchform_show_budget', 1)): ?>
  <div class="jea_slider_block">
    <h2><?php echo JText::_('COM_JEA_BUDGET') ?></h2>
    <div id="price_slider" class="slider_background">
      <div id="knob1" class="knob"></div>
      <div id="knob2" class="knob"></div>
    </div>
    <div class="slider_infos">
      <span class="slider_min_value" id="min_price_value">0</span>
      <?php echo JText::_('COM_JEA_TO') ?>
      <span class="slider_max_value" id="max_price_value">0</span>
    </div>
    <input id="budget_max" type="hidden" name="filter_budget_max" />
    <input id="budget_min" type="hidden" name="filter_budget_min" />
  </div>
  <?php endif; ?>

  <?php if ($this->params->get('searchform_show_living_space', 1)): ?>
  <div class="jea_slider_block">
    <h2>
    <?php echo JText::_('COM_JEA_FIELD_LIVING_SPACE_LABEL') ?>
    </h2>
    <div id="space_slider" class="slider_background">
      <div id="knob3" class="knob"></div>
      <div id="knob4" class="knob"></div>
    </div>
    <div class="slider_infos">
      <span class="slider_min_value" id="min_space_value">0</span>
      <?php echo JText::_('COM_JEA_TO') ?>
      <span class="slider_max_value" id="max_space_value">0</span>
    </div>
    <input id="living_space_min" type="hidden" name="filter_living_space_min" />
    <input id="living_space_max" type="hidden" name="filter_living_space_max" />
  </div>
  <?php endif; ?>

  <?php if ($this->params->get('searchform_show_number_of_rooms', 1)): ?>
  <p>
  <?php echo JText::_('COM_JEA_NUMBER_OF_ROOMS_MIN') ?>:
      <input type="text" id="rooms_min" name="filter_rooms_min" size="1" />
  </p>
  <?php endif; ?>

  <div class="clr"></div>

  <?php if ($this->params->get('searchform_show_amenities', 1)): ?>
  <div class="amenities">
    <?php echo JHtml::_('amenities.checkboxes', $states['filter_amenities'], 'filter_amenities' ) ?>
    <?php // In order to prevent nul post for this field ?>
    <input type="hidden" name="filter_amenities[]" value="0" />
  </div>
  <?php endif; ?>

  <p>
    <input type="submit" class="button" value="<?php echo JText::_('COM_JEA_LIST_PROPERTIES') ?>" />
  </p>

</form>
