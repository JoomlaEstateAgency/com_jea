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

$useAjax        = $this->params->get('searchform_use_ajax', 0);
$transationType = $this->params->get('searchform_transaction_type');

$showLocalization = $this->params->get('searchform_show_departments')
                  || $this->params->get('searchform_show_towns')
                  || $this->params->get('searchform_show_areas')
                  || $this->params->get('searchform_show_zip_codes');

$showOtherFilters = $this->params->get('searchform_show_number_of_rooms')
                  || $this->params->get('searchform_show_number_of_bedrooms')
                  || $this->params->get('searchform_show_number_of_bathrooms')
                  || $this->params->get('searchform_show_floor')
                  || $this->params->get('searchform_show_hotwatertypes')
                  || $this->params->get('searchform_show_heatingtypes')
                  || $this->params->get('searchform_show_conditions')
                  || $this->params->get('searchform_show_orientation');

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
$ajax = $useAjax? 'true': 'false';
JHTML::script('media/com_jea/js/search.js', true);
$this->document->addScriptDeclaration("
window.addEvent('domready', function() {
    var jeaSearch = new JEASearch('jea-search-form', {fields:$fields, useAJAX:$ajax});
    jeaSearch.refresh();
});");
?>

<?php if ($this->params->get('show_page_heading', 1)) : ?>
  <?php if ($this->params->get('page_heading')) : ?>
  <h1><?php echo $this->escape($this->params->get('page_heading')) ?></h1>
  <?php else: ?>
  <h1><?php echo $this->escape($this->params->get('page_title')) ?></h1>
  <?php endif ?>
<?php endif ?>

<form action="<?php echo JRoute::_('index.php?option=com_jea&task=properties.search') ?>" method="post" id="jea-search-form">

<?php if ($this->params->get('searchform_show_freesearch')): ?>
  <p>
    <label for="jea-search"><?php echo JText::_('COM_JEA_SEARCH_LABEL')?> : </label>
    <input type="text" name="filter_search" id="jea-search" value="<?php echo $states['filter_search'] ?>" />
    <input type="submit" class="button" value="<?php echo JText::_('JSEARCH_FILTER_SUBMIT')?>" />
  </p>
  <hr />
<?php endif ?>

<?php if ($useAjax): ?>
  <div class="jea-counter"><span class="jea-counter-result">0</span> <?php echo JText::_('COM_JEA_FOUND_PROPERTIES')?></div>
<?php endif ?>

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

<?php if ($showLocalization): ?>
  <h2><?php echo JText::_('COM_JEA_LOCALIZATION') ?> :</h2>

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
  </p>

  <?php if ($this->params->get('searchform_show_zip_codes', 1)): ?>
  <p>
    <label for="jea-search-zip-codes"><?php echo JText::_('COM_JEA_SEARCH_ZIP_CODES') ?> : </label>
    <input id="jea-search-zip-codes" type="text" name="filter_zip_codes" size="20" value="<?php echo $states['filter_zip_codes'] ?>" />
    <em><?php echo JText::_('COM_JEA_SEARCH_ZIP_CODES_DESC') ?></em>
  </p>
  <?php endif ?>
<?php endif ?>

<?php if ($this->params->get('searchform_show_budget', 1)): ?>
  <h2><?php echo JText::_('COM_JEA_BUDGET') ?> :</h2>
  <dl class="col-left">
    <dt><label for="jea-search-budget-min"><?php echo JText::_('COM_JEA_MIN') ?> : </label></dt>
    <dd><input id="jea-search-budget-min" type="text" name="filter_budget_min"
               size="5" value="<?php echo $states['filter_budget_min'] ?>" />
    <?php echo $this->params->get('currency_symbol', '&euro;') ?></dd>
  </dl>
  <dl class="col-right">
    <dt><label for="jea-search-budget-max"><?php echo JText::_('COM_JEA_MAX') ?> : </label></dt>
    <dd><input id="jea-search-budget-max" type="text" name="filter_budget_max"
               size="5" value="<?php echo $states['filter_budget_max'] ?>" />
    <?php echo $this->params->get('currency_symbol', '&euro;') ?></dd>
  </dl>
<?php endif ?>

<?php if ($this->params->get('searchform_show_living_space', 1)): ?>
  <h2><?php echo JText::_('COM_JEA_FIELD_LIVING_SPACE_LABEL') ?> :</h2>
  <dl class="col-left">
    <dt><label for="jea-search-living-space-min"><?php echo JText::_('COM_JEA_MIN') ?> : </label></dt>
    <dd><input id="jea-search-living-space-min" type="text" name="filter_living_space_min"
               size="5" value="<?php echo $states['filter_living_space_min'] ?>" />
    <?php echo $this->params->get( 'surface_measure' ) ?></dd>
  </dl>
  <dl class="col-right">
    <dt><label for="jea-search-living-space-max"><?php echo JText::_('COM_JEA_MAX') ?> : </label></dt>
    <dd><input id="jea-search-living-space-max" type="text" name="filter_living_space_max"
               size="5" value="<?php echo $states['filter_living_space_max'] ?>" />
    <?php echo $this->params->get( 'surface_measure' ) ?></dd>
  </dl>
<?php endif ?>

<?php if ($this->params->get('searchform_show_land_space', 1)): ?>
  <h2><?php echo JText::_('COM_JEA_FIELD_LAND_SPACE_LABEL') ?> :</h2>
  <dl class="col-left">
    <dt><label for="jea-search-land-space-min"><?php echo JText::_('COM_JEA_MIN') ?> : </label></dt>
    <dd><input id="jea-search-land-space-min" type="text" name="filter_land_space_min"
               size="5" value="<?php echo $states['filter_land_space_min'] ?>" />
    <?php echo $this->params->get( 'surface_measure' ) ?></dd>
  </dl>
  <dl class="col-right">
    <dt><label for="jea-search-land-space-max"><?php echo JText::_('COM_JEA_MAX') ?> : </label></dt>
    <dd><input id="jea-search-land-space-max" type="text" name="filter_land_space_max"
               size="5" value="<?php echo $states['filter_land_space_max'] ?>" />
    <?php echo $this->params->get( 'surface_measure' ) ?></dd>
  </dl>
<?php endif ?>

<?php if ($showOtherFilters): ?>
  <h2><?php echo JText::_('COM_JEA_SEARCH_OTHER') ?> :</h2>

  <ul class="jea-search-other">
      <?php if ($this->params->get('searchform_show_number_of_rooms', 1)): ?>
    <li>
      <label for="jea-search-rooms"><?php echo JText::_('COM_JEA_NUMBER_OF_ROOMS_MIN') ?> : </label>
      <input id="jea-search-rooms" type="text" name="filter_rooms_min"
             size="2" value="<?php echo $states['filter_rooms_min'] ?>" />
    </li>
    <?php endif?>

    <?php if ($this->params->get('searchform_show_number_of_bedrooms', 1)): ?>
    <li>
      <label for="jea-search-bedrooms"><?php echo JText::_('COM_JEA_NUMBER_OF_BEDROOMS_MIN') ?> : </label>
      <input id="jea-search-bedrooms" type="text" name="filter_bedrooms_min"
             size="2" value="<?php echo $states['filter_bedrooms_min'] ?>" />
    </li>
    <?php endif?>

    <?php if ($this->params->get('searchform_show_number_of_bathrooms', 0)): ?>
    <li>
      <label for="jea-search-bathrooms"><?php echo JText::_('COM_JEA_NUMBER_OF_BATHROOMS_MIN') ?> : </label>
      <input id="jea-search-bathrooms" type="text" name="filter_bathrooms_min"
             size="2" value="<?php echo $states['filter_bathrooms_min'] ?>" />
    </li>
    <?php endif?>

    <?php if ($this->params->get('searchform_show_floor', 1)): ?>
    <li>
      <label for="jea-search-floor"><?php echo JText::_('COM_JEA_FIELD_FLOOR_LABEL') ?> : </label>
      <input id="jea-search-floor" type="text" name="filter_floor" size="2" value="<?php echo $states['filter_floor'] ?>" />
      <em><?php echo JText::_('COM_JEA_SEARCH_FLOOR_DESC') ?></em>
    </li>
    <?php endif?>

    <?php if ($this->params->get('searchform_show_hotwatertypes', 0)): ?>
    <li><?php echo JHtml::_('features.hotwatertypes', $states['filter_hotwatertype'], 'filter_hotwatertype' ) ?></li>
    <?php endif?>

    <?php if ($this->params->get('searchform_show_heatingtypes', 0)): ?>
    <li><?php echo JHtml::_('features.heatingtypes', $states['filter_heatingtype'], 'filter_heatingtype' ) ?></li>
    <?php endif?>

    <?php if ($this->params->get('searchform_show_conditions', 0)): ?>
    <li><?php echo JHtml::_('features.conditions', $states['filter_condition'], 'filter_condition' ) ?></li>
    <?php endif?>

    <?php if ($this->params->get('searchform_show_orientation', 1)): ?>
    <li>
    <?php
        $options = array(
            JHTML::_('select.option', '0',  ' - ' . JText::_('COM_JEA_FIELD_ORIENTATION_LABEL') . ' - ' ),
            JHTML::_('select.option', 'N',  JText::_('COM_JEA_OPTION_NORTH')),
            JHTML::_('select.option', 'NW', JText::_('COM_JEA_OPTION_NORTH_WEST')),
            JHTML::_('select.option', 'NE', JText::_('COM_JEA_OPTION_NORTH_EAST')),
            JHTML::_('select.option', 'NS', JText::_('COM_JEA_OPTION_NORTH_SOUTH')),
            JHTML::_('select.option', 'E',  JText::_('COM_JEA_OPTION_EAST')),
            JHTML::_('select.option', 'EW',  JText::_('COM_JEA_OPTION_EAST_WEST')),
            JHTML::_('select.option', 'W',  JText::_('COM_JEA_OPTION_WEST')),
            JHTML::_('select.option', 'S',  JText::_('COM_JEA_OPTION_SOUTH')),
            JHTML::_('select.option', 'SW', JText::_('COM_JEA_OPTION_SOUTH_WEST')),
            JHTML::_('select.option', 'SE', JText::_('COM_JEA_OPTION_SOUTH_EAST'))
        );
        echo JHTML::_('select.genericlist', $options, 'filter_orientation', 'size="1"', 'value', 'text',  $states['filter_orientation'])
    ?>
    </li>
    <?php endif?>
  </ul>
<?php endif ?>

<?php if ($this->params->get('searchform_show_amenities', 1)): ?>
  <h2><?php echo JText::_('COM_JEA_AMENITIES') ?> :</h2>
  <div class="amenities">
    <?php echo JHtml::_('amenities.checkboxes', $states['filter_amenities'], 'filter_amenities' ) ?>
    <?php // In order to prevent nul post for this field ?>
    <input type="hidden" name="filter_amenities[]" value="0" />
  </div>
<?php endif ?>

<?php if ($useAjax): ?>
  <div class="jea-counter"><span class="jea-counter-result">0</span> <?php echo JText::_('COM_JEA_FOUND_PROPERTIES')?></div>
<?php endif ?>

  <p>
    <input type="reset" class="button" value="<?php echo JText::_('JSEARCH_FILTER_CLEAR') ?>" />
    <input type="submit" class="button" value="<?php echo $useAjax ? JText::_('COM_JEA_LIST_PROPERTIES') : JText::_('JSEARCH_FILTER_SUBMIT')?>" />
  </p>

</form>
