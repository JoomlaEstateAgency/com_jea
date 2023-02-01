<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Site
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2020 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

/**
 *
 * @var $this JeaViewProperties
 */

HTMLHelper::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/html');
HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');

$useAjax = $this->params->get('searchform_use_ajax', 0);
$transactionType = $this->params->get('searchform_transaction_type');

$showLocalization = $this->params->get('searchform_show_departments') ||
    $this->params->get('searchform_show_towns') ||
    $this->params->get('searchform_show_areas') ||
    $this->params->get('searchform_show_zip_codes');

$showOtherFilters = $this->params->get('searchform_show_number_of_rooms') ||
    $this->params->get('searchform_show_number_of_bedrooms') ||
    $this->params->get('searchform_show_number_of_bathrooms') ||
    $this->params->get('searchform_show_floor') ||
    $this->params->get('searchform_show_hotwatertypes') ||
    $this->params->get('searchform_show_heatingtypes') ||
    $this->params->get('searchform_show_conditions') ||
    $this->params->get('searchform_show_orientation');

$states = array();
$filters = $this->get('Filters');

foreach ($filters as $name => $defaultValue) {
    $states['filter_' . $name] = $this->state->get('filter.' . $name, $defaultValue);
}

$states['filter_transaction_type'] = $transactionType;

$fields = json_encode($states);
$ajax = $useAjax ? 'true' : 'false';

// Include jQuery
HTMLHelper::_('jquery.framework');
HTMLHelper::script('com_jea/jquery-search.js', array('relative' => true));

$script = <<<JS
jQuery(function($) {
	var jeaSearch = new JEASearch('#jea-search-form', {fields:$fields, useAJAX:$ajax, transactionType:'$transactionType'});
	jeaSearch.refresh();
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

<form action="<?php echo Route::_('index.php?option=com_jea&view=properties&task=properties.search') ?>"
      method="post" id="jea-search-form">

    <?php if ($this->params->get('searchform_show_freesearch')): ?>
      <p>
        <label for="jea-search"><?php echo Text::_('COM_JEA_SEARCH_LABEL') ?> : </label>
        <input type="text" name="filter_search" id="jea-search"
               value="<?php echo $states['filter_search'] ?>"/>
        <input type="submit" class="button"
               value="<?php echo Text::_('JSEARCH_FILTER_SUBMIT') ?>"/>
      </p>

      <hr/>
    <?php endif ?>

    <?php if ($useAjax): ?>
      <div class="jea-counter">
        <span class="jea-counter-result">0</span> <?php echo Text::_('COM_JEA_FOUND_PROPERTIES') ?>
      </div>
    <?php endif ?>

  <p>
      <?php echo HTMLHelper::_('features.types', $this->state->get('filter.type_id', 0), 'filter_type_id') ?>

      <?php if ($transactionType == 'RENTING'): ?>
        <input type="hidden" name="filter_transaction_type" value="RENTING"/>
      <?php elseif ($transactionType == 'SELLING'): ?>
        <input type="hidden" name="filter_transaction_type" value="SELLING"/>
      <?php else: ?>
        <input type="radio" name="filter_transaction_type" id="jea-search-selling" value="SELLING"
            <?php if ($states['filter_transaction_type'] == 'SELLING') echo 'checked="checked"' ?> />

        <label for="jea-search-selling"><?php echo Text::_('COM_JEA_OPTION_SELLING') ?></label>

        <input type="radio" name="filter_transaction_type" id="jea-search-renting" value="RENTING"
            <?php if ($states['filter_transaction_type'] == 'RENTING') echo 'checked="checked"' ?> />
        <label for="jea-search-renting"><?php echo Text::_('COM_JEA_OPTION_RENTING') ?></label>
      <?php endif ?>
  </p>

    <?php if ($showLocalization): ?>
      <h2><?php echo Text::_('COM_JEA_LOCALIZATION') ?> :</h2>

      <p>
          <?php if ($this->params->get('searchform_show_departments', 1)): ?>
              <?php echo HTMLHelper::_('features.departments', $states['filter_department_id'], 'filter_department_id') ?>
          <?php endif ?>

          <?php if ($this->params->get('searchform_show_towns', 1)): ?>
              <?php echo HTMLHelper::_('features.towns', $states['filter_town_id'], 'filter_town_id') ?>
          <?php endif ?>

          <?php if ($this->params->get('searchform_show_areas', 1)): ?>
              <?php echo HTMLHelper::_('features.areas', $states['filter_area_id'], 'filter_area_id') ?>
          <?php endif ?>
      </p>

        <?php if ($this->params->get('searchform_show_zip_codes', 1)): ?>
        <p>
          <label for="jea-search-zip-codes"><?php echo Text::_('COM_JEA_SEARCH_ZIP_CODES') ?>
            : </label>
          <input id="jea-search-zip-codes" type="text" name="filter_zip_codes" size="20"
                 value="<?php echo $states['filter_zip_codes'] ?>"/>
          <em><?php echo Text::_('COM_JEA_SEARCH_ZIP_CODES_DESC') ?></em>
        </p>
        <?php endif ?>

    <?php endif ?>

    <?php if ($this->params->get('searchform_show_budget', 1)): ?>
      <h2><?php echo Text::_('COM_JEA_BUDGET') ?> :</h2>
      <dl class="col-left">
        <dt>
          <label for="jea-search-budget-min"><?php echo Text::_('COM_JEA_MIN') ?> : </label>
        </dt>
        <dd>
          <input id="jea-search-budget-min" type="text" name="filter_budget_min" size="5"
                 value="<?php echo $states['filter_budget_min'] ?>"/>
            <?php echo $this->params->get('currency_symbol', '&euro;') ?>
        </dd>
      </dl>
      <dl class="col-right">
        <dt>
          <label for="jea-search-budget-max"><?php echo Text::_('COM_JEA_MAX') ?> : </label>
        </dt>
        <dd>
          <input id="jea-search-budget-max" type="text" name="filter_budget_max" size="5"
                 value="<?php echo $states['filter_budget_max'] ?>"/>
            <?php echo $this->params->get('currency_symbol', '&euro;') ?>
        </dd>
      </dl>
    <?php endif ?>

    <?php if ($this->params->get('searchform_show_living_space', 1)): ?>
      <h2><?php echo Text::_('COM_JEA_FIELD_LIVING_SPACE_LABEL') ?> :</h2>

      <dl class="col-left">
        <dt>
          <label for="jea-search-living-space-min"><?php echo Text::_('COM_JEA_MIN') ?> : </label>
        </dt>
        <dd>
          <input id="jea-search-living-space-min" type="text" name="filter_living_space_min"
                 size="5"
                 value="<?php echo $states['filter_living_space_min'] ?>"/>
            <?php echo $this->params->get('surface_measure') ?>
        </dd>
      </dl>
      <dl class="col-right">
        <dt>
          <label for="jea-search-living-space-max"><?php echo Text::_('COM_JEA_MAX') ?> : </label>
        </dt>
        <dd>
          <input id="jea-search-living-space-max" type="text" name="filter_living_space_max"
                 size="5"
                 value="<?php echo $states['filter_living_space_max'] ?>"/>
            <?php echo $this->params->get('surface_measure') ?>
        </dd>
      </dl>
    <?php endif ?>

    <?php if ($this->params->get('searchform_show_land_space', 1)): ?>
      <h2><?php echo Text::_('COM_JEA_FIELD_LAND_SPACE_LABEL') ?> :</h2>
      <dl class="col-left">
        <dt>
          <label for="jea-search-land-space-min"><?php echo Text::_('COM_JEA_MIN') ?> : </label>
        </dt>
        <dd>
          <input id="jea-search-land-space-min" type="text" name="filter_land_space_min" size="5"
                 value="<?php echo $states['filter_land_space_min'] ?>"/>
            <?php echo $this->params->get('surface_measure') ?>
        </dd>
      </dl>
      <dl class="col-right">
        <dt>
          <label for="jea-search-land-space-max"><?php echo Text::_('COM_JEA_MAX') ?> : </label>
        </dt>
        <dd>
          <input id="jea-search-land-space-max" type="text" name="filter_land_space_max" size="5"
                 value="<?php echo $states['filter_land_space_max'] ?>"/>
            <?php echo $this->params->get('surface_measure') ?>
        </dd>
      </dl>
    <?php endif ?>

    <?php if ($showOtherFilters): ?>
      <h2><?php echo Text::_('COM_JEA_SEARCH_OTHER') ?> :</h2>

      <ul class="jea-search-other">
          <?php if ($this->params->get('searchform_show_number_of_rooms', 1)): ?>
            <li>
              <label for="jea-search-rooms"><?php echo Text::_('COM_JEA_NUMBER_OF_ROOMS_MIN') ?>
                : </label>
              <input id="jea-search-rooms" type="text" name="filter_rooms_min" size="2"
                     value="<?php echo $states['filter_rooms_min'] ?>"/>
            </li>
          <?php endif ?>

          <?php if ($this->params->get('searchform_show_number_of_bedrooms', 1)): ?>
            <li>
              <label
                  for="jea-search-bedrooms"><?php echo Text::_('COM_JEA_NUMBER_OF_BEDROOMS_MIN') ?>
                : </label>
              <input id="jea-search-bedrooms" type="text" name="filter_bedrooms_min" size="2"
                     value="<?php echo $states['filter_bedrooms_min'] ?>"/>
            </li>
          <?php endif ?>

          <?php if ($this->params->get('searchform_show_number_of_bathrooms', 0)): ?>
            <li>
              <label
                  for="jea-search-bathrooms"><?php echo Text::_('COM_JEA_NUMBER_OF_BATHROOMS_MIN') ?>
                : </label>
              <input id="jea-search-bathrooms" type="text" name="filter_bathrooms_min" size="2"
                     value="<?php echo $states['filter_bathrooms_min'] ?>"/>
            </li>
          <?php endif ?>

          <?php if ($this->params->get('searchform_show_floor', 1)): ?>
            <li>
              <label for="jea-search-floor"><?php echo Text::_('COM_JEA_FIELD_FLOOR_LABEL') ?>
                : </label>
              <input id="jea-search-floor" type="text" name="filter_floor" size="2"
                     value="<?php echo $states['filter_floor'] ?>"/>
              <em><?php echo Text::_('COM_JEA_SEARCH_FLOOR_DESC') ?></em>
            </li>
          <?php endif ?>

          <?php if ($this->params->get('searchform_show_hotwatertypes', 0)): ?>
            <li><?php echo HTMLHelper::_('features.hotwatertypes', $states['filter_hotwatertype'], 'filter_hotwatertype') ?></li>
          <?php endif ?>

          <?php if ($this->params->get('searchform_show_heatingtypes', 0)): ?>
            <li><?php echo HTMLHelper::_('features.heatingtypes', $states['filter_heatingtype'], 'filter_heatingtype') ?></li>
          <?php endif ?>

          <?php if ($this->params->get('searchform_show_conditions', 0)): ?>
            <li><?php echo HTMLHelper::_('features.conditions', $states['filter_condition'], 'filter_condition') ?></li>
          <?php endif ?>

          <?php if ($this->params->get('searchform_show_orientation', 1)): ?>
            <li>
                <?php
                $options = array(
                    HTMLHelper::_('select.option', '0', ' - ' . Text::_('COM_JEA_FIELD_ORIENTATION_LABEL') . ' - '),
                    HTMLHelper::_('select.option', 'N', Text::_('COM_JEA_OPTION_NORTH')),
                    HTMLHelper::_('select.option', 'NW', Text::_('COM_JEA_OPTION_NORTH_WEST')),
                    HTMLHelper::_('select.option', 'NE', Text::_('COM_JEA_OPTION_NORTH_EAST')),
                    HTMLHelper::_('select.option', 'NS', Text::_('COM_JEA_OPTION_NORTH_SOUTH')),
                    HTMLHelper::_('select.option', 'E', Text::_('COM_JEA_OPTION_EAST')),
                    HTMLHelper::_('select.option', 'EW', Text::_('COM_JEA_OPTION_EAST_WEST')),
                    HTMLHelper::_('select.option', 'W', Text::_('COM_JEA_OPTION_WEST')),
                    HTMLHelper::_('select.option', 'S', Text::_('COM_JEA_OPTION_SOUTH')),
                    HTMLHelper::_('select.option', 'SW', Text::_('COM_JEA_OPTION_SOUTH_WEST')),
                    HTMLHelper::_('select.option', 'SE', Text::_('COM_JEA_OPTION_SOUTH_EAST'))
                );

                echo HTMLHelper::_('select.genericlist', $options, 'filter_orientation', 'size="1"', 'value', 'text', $states['filter_orientation'])
                ?>
            </li>
          <?php endif ?>
      </ul>
    <?php endif ?>

    <?php if ($this->params->get('searchform_show_amenities', 1)): ?>
      <h2><?php echo Text::_('COM_JEA_AMENITIES') ?> :</h2>
      <div class="amenities">
          <?php echo HTMLHelper::_('amenities.checkboxes', $states['filter_amenities'], 'filter_amenities') ?>
          <?php // In order to prevent nul post for this field ?>
        <input type="hidden" name="filter_amenities[]" value="0"/>
      </div>
    <?php endif ?>

    <?php if ($useAjax): ?>
      <div class="jea-counter">
        <span class="jea-counter-result">0</span> <?php echo Text::_('COM_JEA_FOUND_PROPERTIES') ?>
      </div>
    <?php endif ?>

  <p>
    <input type="reset" class="button" value="<?php echo Text::_('JSEARCH_FILTER_CLEAR') ?>"/>
    <input type="submit" class="button"
           value="<?php echo $useAjax ? Text::_('COM_JEA_LIST_PROPERTIES') : Text::_('JSEARCH_FILTER_SUBMIT') ?>"/>
  </p>
</form>
