<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Site
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2020 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 *
 * @var $this JeaViewProperties
 */

JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/html');
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

$transactionType = $this->params->get('searchform_transaction_type');
$Itemid = JFactory::getApplication()->input->getInt('Itemid', 0);

$states = array();
$filters = $this->get('Filters');

foreach ($filters as $name => $defaultValue)
{
	$states['filter_' . $name] = $this->state->get('filter.' . $name, $defaultValue);
}

$states['filter_transaction_type'] = $transactionType;

$fields = json_encode($states);

$langs = explode('-', $this->document->getLanguage());
$lang = $langs[0];
$region = $langs[1];

$this->document->addScript(
	'https://maps.google.com/maps/api/js?key=' . $this->params->get('googlemap_api_key') . '&amp;language=' . $lang . '&amp;region=' . $region
);

// Include jQuery
JHtml::_('jquery.framework');
JHTML::script('com_jea/jquery-search.js', array('relative' => true));
JHtml::script('com_jea/geoxml3.js', array('relative' => true));
JHTML::script('com_jea/jquery-geoSearch.js', array('relative' => true));
JHTML::script('com_jea/jquery-ui-draggable.min.js', array('relative' => true));
JHTML::script('com_jea/jquery-biSlider.js', array('relative' => true));

$model = $this->getModel();

$fieldsLimit = json_encode(
	array(
		'RENTING' => array(
			'price' => $model->getFieldLimit('price', 'RENTING'),
			'surface' => $model->getFieldLimit('living_space', 'RENTING')
		),
		'SELLING' => array(
			'price' => $model->getFieldLimit('price', 'SELLING'),
			'surface' => $model->getFieldLimit('living_space', 'SELLING')
		)
	)
);

$default_area = $this->params->get('searchform_default_map_area', $lang);
$currency_symbol = $this->params->get('currency_symbol', '€');
$surface_measure = $this->params->get('surface_measure');
$map_width = $this->params->get('searchform_map_width', 0);
$map_height = $this->params->get('searchform_map_height', 400);

$script =<<<JS
jQuery(function($) {

	var minPrice = 0;
	var maxPrice = 0;
	var minSpace = 0;
	var maxSpace = 0;

	function updateSliders()
	{
		if(!$('#price_slider') && !$('#space_slider')) {
			return;
		}

		var transaction_type = 'SELLING';

		var transTypes = $('#jea-search-form').find('[name=\"filter_transaction_type\"]');

		jQuery.each(transTypes, function(idx, item){
			if ($(item).prop('checked')) {
				transaction_type = $(item).val();
			}
		})

		var fieldsLimit = $fieldsLimit;
		minPrice = fieldsLimit[transaction_type].price[0];
		maxPrice = fieldsLimit[transaction_type].price[1];
		minSpace = fieldsLimit[transaction_type].surface[0];
		maxSpace = fieldsLimit[transaction_type].surface[1];

		$('#budget_min').val(minPrice);
		$('#budget_max').val(maxPrice);
		$('#min_price_value').text(minPrice + ' $currency_symbol');
		$('#max_price_value').text(maxPrice + ' $currency_symbol');

		$('#living_space_min').val(minSpace);
		$('#living_space_max').val(maxSpace);
		$('#min_space_value').text(minSpace + ' $surface_measure');
		$('#max_space_value').text(maxSpace + ' $surface_measure');
	}

	var jeaSearch = new JEASearch('#jea-search-form', {fields:$fields, useAJAX:true, transactionType:'$transactionType'});
	jeaSearch.refresh();

	geoSearch = new JEAGeoSearch('map_canvas', {
		counterElement : 'properties_count',
		defaultArea : '{$default_area}',
		form : 'jea-search-form',
		Itemid : {$Itemid}
	});

	updateSliders();
	geoSearch.refresh();
	jeaSearch.refresh();

	$('#jea-search-form').on('reset', function(){
		jeaSearch.reset();
		updateSliders();
		geoSearch.refresh();
	});

	$('#filter_type_id, #filter_department_id, #filter_town_id, #filter_area_id, #rooms_min, .amenities input').on('change', function() {
		geoSearch.refresh();
	});

	$('#jea-search-selling, #jea-search-renting').on('change', function() {
		updateSliders();
		geoSearch.refresh();
	});

	$('#price_slider').bislider({
		steps:100,
		onChange: function(steps) {
			var priceDiff = maxPrice - minPrice;
			$('#budget_min').val(Math.round(((priceDiff * steps.minimum ) / 100) + minPrice));
			$('#budget_max').val(Math.round(((priceDiff * steps.maximum ) / 100) + minPrice));

			$('#min_price_value').text($('#budget_min').val() + ' $currency_symbol');
			$('#max_price_value').text($('#budget_max').val() + ' $currency_symbol');
		},
		onComplete: function(step) {
			geoSearch.refresh();
		}
	})

	$('#space_slider').bislider({
		steps:100,
		onChange: function(steps) {
			var spaceDiff = maxSpace - minSpace;
			$('#living_space_min').val(Math.round(((spaceDiff * steps.minimum ) / 100) + minSpace));
			$('#living_space_max').val(Math.round(((spaceDiff * steps.maximum ) / 100) + minSpace));

			$('#min_space_value').text($('#living_space_min').val() + ' $surface_measure');
			$('#max_space_value').text($('#living_space_max').val() + ' $surface_measure');
		},
		onComplete: function(step) {
			geoSearch.refresh();
		}
	})
});
JS;

$this->document->addScriptDeclaration($script);
?>

<?php if ($this->params->get('show_page_heading', 1)) : ?>
<?php if ($this->params->get('page_heading')) : ?>
<h1><?php echo $this->escape($this->params->get('page_heading')) ?></h1>
<?php else: ?>
<h1><?php echo $this->escape($this->params->get('page_title')) ?></h1>
<?php endif ?>
<?php endif ?>

<form action="<?php echo JRoute::_('index.php?option=com_jea&task=properties.search') ?>" method="post" id="jea-search-form">
	<p>
		<?php echo JHtml::_('features.types', $this->state->get('filter.type_id', 0), 'filter_type_id') ?>

		<?php if ($transactionType == 'RENTING'): ?>
		<input type="hidden" name="filter_transaction_type" value="RENTING" />
		<?php elseif($transactionType == 'SELLING'): ?>
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
	<?php endif ?>

	<?php if ($this->params->get('searchform_show_living_space', 1)): ?>
	<div class="jea_slider_block">
		<h2><?php echo JText::_('COM_JEA_FIELD_LIVING_SPACE_LABEL') ?></h2>
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
		<?php // In order to prevent null post for this field ?>
		<input type="hidden" name="filter_amenities[]" value="0" />
	</div>
	<?php endif; ?>

	<p>
		<input type="reset" class="button" value="<?php echo JText::_('JSEARCH_FILTER_CLEAR') ?>" />
		<input type="submit" class="button" value="<?php echo JText::_('COM_JEA_LIST_PROPERTIES') ?>" />
	</p>
</form>
