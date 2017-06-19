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

$states = array();
$filters = $this->get('Filters');

foreach ($filters as $name => $defaultValue) {
    $states['filter_'.$name] = $this->state->get('filter.'.$name, $defaultValue);
}

// no direct access
defined('_JEXEC') or die('Restricted access');
?>

<?php if ($states['filter_transaction_type'] == 'RENTING'): ?>
  <strong><?php echo JText::_('COM_JEA_OPTION_RENTING') ?></strong>
<?php elseif ($states['filter_transaction_type'] == 'SELLING'): ?>
  <strong><?php echo JText::_('COM_JEA_OPTION_SELLING') ?></strong>
<?php endif ?>

<?php if ($states['filter_type_id'] > 0): ?>
  <br /><strong><?php echo JText::_('COM_JEA_FIELD_PROPERTY_TYPE_LABEL') ?> : </strong>
  <?php echo $this->getFeatureValue($states['filter_type_id'], 'types') ?>
<?php endif ?>

<?php if ($states['filter_department_id'] > 0): ?>
  <br /><strong><?php echo JText::_('COM_JEA_FIELD_DEPARTMENT_LABEL') ?> : </strong>
  <?php echo $this->getFeatureValue($states['filter_department_id'], 'departments') ?>
<?php endif ?>

<?php if ($states['filter_town_id'] > 0): ?>
  <br /><strong><?php echo JText::_('COM_JEA_FIELD_TOWN_LABEL') ?> : </strong>
  <?php echo $this->getFeatureValue($states['filter_town_id'], 'towns') ?>
<?php endif ?>

<?php if ($states['filter_area_id'] > 0): ?>
  <br /><strong><?php echo JText::_('COM_JEA_FIELD_AREA_LABEL') ?> : </strong>
  <?php echo $this->getFeatureValue($states['filter_area_id'], 'areas') ?>
<?php endif ?>

<?php if (!empty($states['filter_zip_codes'])): ?>
  <br /><strong><?php echo JText::_('COM_JEA_FIELD_ZIP_CODE_LABEL') ?> : </strong>
  <?php echo $this->escape($states['filter_zip_codes']) ?>
<?php endif ?>

<?php if ($states['filter_budget_min'] > 0): ?>
  <br /><strong><?php echo JText::_('COM_JEA_BUDGET_MIN') ?> : </strong>
  <?php echo JHtml::_('utility.formatPrice', $states['filter_budget_min']) ?>
<?php endif ?>

<?php if ($states['filter_budget_max'] > 0): ?>
  <br /><strong><?php echo JText::_('COM_JEA_BUDGET_MAX') ?> : </strong>
  <?php echo JHtml::_('utility.formatPrice', $states['filter_budget_max']) ?>
<?php endif ?>

<?php if ($states['filter_living_space_min'] > 0): ?>
  <br /><strong><?php echo JText::_('COM_JEA_LIVING_SPACE_MIN') ?> : </strong>
  <?php echo JHtml::_('utility.formatSurface', $states['filter_living_space_min']) ?>
<?php endif ?>

<?php if ($states['filter_living_space_max'] > 0): ?>
  <br /><strong><?php echo JText::_('COM_JEA_LIVING_SPACE_MAX') ?> : </strong>
  <?php echo JHtml::_('utility.formatSurface', $states['filter_living_space_max']) ?>
<?php endif ?>

<?php if ($states['filter_land_space_min'] > 0): ?>
  <br /><strong><?php echo JText::_('COM_JEA_LAND_SPACE_MIN') ?> : </strong>
  <?php echo JHtml::_('utility.formatSurface', $states['filter_land_space_min']) ?>
<?php endif ?>

<?php if ($states['filter_land_space_max'] > 0): ?>
  <br /><strong><?php echo JText::_('COM_JEA_LAND_SPACE_MAX') ?> : </strong>
  <?php echo JHtml::_('utility.formatSurface', $states['filter_land_space_max']) ?>
<?php endif ?>

<?php if ($states['filter_rooms_min'] > 0): ?>
  <br /><strong><?php echo JText::_('COM_JEA_NUMBER_OF_ROOMS_MIN') ?> : </strong>
  <?php echo $this->escape($states['filter_rooms_min']) ?>
<?php endif ?>

<?php if ($states['filter_bedrooms_min'] > 0): ?>
  <br /><strong><?php echo JText::_('COM_JEA_NUMBER_OF_BEDROOMS_MIN') ?> : </strong>
  <?php echo $this->escape($states['filter_bedrooms_min']) ?>
<?php endif ?>

<?php if ($states['filter_bathrooms_min'] > 0): ?>
  <br /><strong><?php echo JText::_('COM_JEA_NUMBER_OF_BATHROOMS_MIN') ?> : </strong>
  <?php echo $this->escape($states['filter_bathrooms_min']) ?>
<?php endif ?>

<?php if ($states['filter_floor'] > 0): ?>
  <br /><strong><?php echo JText::_('COM_JEA_FIELD_FLOOR_LABEL') ?> : </strong>
  <?php echo $this->escape($states['filter_floor']) ?>
<?php endif ?>

<?php if ($states['filter_hotwatertype'] > 0): ?>
  <br /><strong><?php echo JText::_('COM_JEA_FIELD_HOTWATERTYPE_LABEL') ?> : </strong>
  <?php echo $this->getFeatureValue($states['filter_hotwatertype'], 'hotwatertypes') ?>
<?php endif ?>

<?php if ($states['filter_heatingtype'] > 0): ?>
  <br /><strong><?php echo JText::_('COM_JEA_FIELD_HEATINGTYPE_LABEL') ?> : </strong>
  <?php echo $this->getFeatureValue($states['filter_heatingtype'], 'heatingtypes') ?>
<?php endif ?>

<?php if ($states['filter_condition'] > 0): ?>
  <br /><strong><?php echo JText::_('COM_JEA_FIELD_CONDITION_LABEL') ?> : </strong>
  <?php echo $this->getFeatureValue($states['filter_condition'], 'conditions') ?>
<?php endif ?>

<?php if (!empty($states['filter_orientation'])): ?>
  <br /><strong><?php echo JText::_('COM_JEA_FIELD_ORIENTATION_LABEL') ?> : </strong>
  <?php switch($states['filter_orientation']) {
      case 'N':
          echo JText::_('COM_JEA_OPTION_NORTH');
          break;
      case 'NW':
          echo JText::_('COM_JEA_OPTION_NORTH_WEST');
          break;
      case 'NE':
          echo JText::_('COM_JEA_OPTION_NORTH_EAST');
          break;
      case 'NS':
          echo JText::_('COM_JEA_OPTION_NORTH_SOUTH');
          break;
      case 'W':
          echo JText::_('COM_JEA_OPTION_WEST');
          break;
      case 'S':
          echo JText::_('COM_JEA_OPTION_SOUTH');
          break;
      case 'SW':
          echo JText::_('COM_JEA_OPTION_SOUTH_WEST');
          break;
      case 'SE':
          echo JText::_('COM_JEA_OPTION_SOUTH_EAST');
          break;
      case 'E':
          echo JText::_('COM_JEA_OPTION_EAST');
          break;
      case 'EW':
          echo JText::_('COM_JEA_OPTION_EAST_WEST');
          break;

  }?>
<?php endif ?>

<?php if (is_array($states['filter_amenities']) && !empty($states['filter_amenities'])): ?>
  <br /><strong><?php echo JText::_('COM_JEA_AMENITIES') ?> : </strong>
  <?php echo JHtml::_('amenities.bindList', $states['filter_amenities']) ?>
<?php endif ?>

