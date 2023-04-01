<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Site
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2020 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\Event\Event;

defined('_JEXEC') or die;

/**
 *
 * @var $this JeaViewProperty
 */

$dispatcher = Factory::getApplication()->getDispatcher();
PluginHelper::importPlugin('jea');
?>

<div class="jea-tools">
    <?php if ($this->params->get('show_print_icon')): ?>
      <a href="javascript:window.print()" title="<?php echo Text::_('JGLOBAL_PRINT') ?>"
         class="print-icon">
          <?php echo HTMLHelper::_('image', 'system/printButton.png', Text::_('JGLOBAL_PRINT'), null, true) ?>
      </a>
    <?php endif ?>
  <nav class="prev-next-navigation"><?php echo $this->getPrevNextNavigation() ?></nav>
</div>


<h1><?php echo $this->page_title ?></h1>

<?php if ($this->params->get('show_creation_date', 0)) : ?>
  <p>
    <span
        class="date"><?php echo HTMLHelper::_('date', $this->row->created, Text::_('DATE_FORMAT_LC3')) ?></span>
  </p>
<?php endif ?>

<?php if (!empty($this->row->images)) echo $this->loadTemplate($this->params->get('images_layout', 'squeezebox')) ?>

<h2 class="clr"><?php echo Text::_('COM_JEA_REF') ?>
  : <?php echo $this->escape($this->row->ref) ?></h2>

<div class="jea-col-right">
    <?php if ($this->row->address || $this->row->zip_code || $this->row->town): ?>
      <h3><?php echo Text::_('COM_JEA_FIELD_ADDRESS_LABEL') ?> :</h3>
      <address>
          <?php if ($this->row->address): ?>
              <?php echo $this->escape($this->row->address) ?><br/>
          <?php endif ?>
          <?php if ($this->row->zip_code) echo $this->escape($this->row->zip_code) ?>
          <?php if ($this->row->town) echo $this->escape($this->row->town) ?>
      </address>
    <?php endif ?>

    <?php if ($this->row->area) : ?>
      <p><?php echo Text::_('COM_JEA_FIELD_AREA_LABEL') ?> :
        <strong> <?php echo $this->escape($this->row->area) ?></strong>
      </p>
    <?php endif ?>

    <?php if (!empty($this->row->amenities)): ?>
      <h3><?php echo Text::_('COM_JEA_AMENITIES') ?> :</h3>
        <?php echo HTMLHelper::_('amenities.bindList', $this->row->amenities, 'ul') ?>
    <?php endif ?>
</div>

<?php if (intval($this->row->availability)): ?>
  <p class="availability">
      <?php echo Text::_('COM_JEA_FIELD_PROPERTY_AVAILABILITY_LABEL') ?> :
      <?php echo HTMLHelper::_('date', $this->row->availability, Text::_('DATE_FORMAT_LC3')) ?>
  </p>
<?php endif ?>

<h3><?php echo Text::_('COM_JEA_FINANCIAL_INFORMATIONS') ?> : </h3>

<table class="jea-data">
  <tr>
    <th><?php echo $this->row->transaction_type == 'RENTING' ? Text::_('COM_JEA_FIELD_PRICE_RENT_LABEL') : Text::_('COM_JEA_FIELD_PRICE_LABEL') ?></th>
    <td class="right">
        <?php echo HTMLHelper::_('utility.formatPrice', (float)$this->row->price, Text::_('COM_JEA_CONSULT_US')) ?>
        <?php if ($this->row->transaction_type == 'RENTING' && (float)$this->row->price != 0.0): ?>
          <span
              class="rate_frequency"><?php echo Text::_('COM_JEA_PRICE_PER_FREQUENCY_' . $this->row->rate_frequency) ?></span>
        <?php endif ?>
    </td>
  </tr>

    <?php if ((float)$this->row->charges > 0): ?>
      <tr>
        <th><?php echo Text::_('COM_JEA_FIELD_CHARGES_LABEL') ?></th>
        <td class="right"><?php echo HTMLHelper::_('utility.formatPrice', (float)$this->row->charges) ?></td>
      </tr>
    <?php endif ?>

    <?php if ($this->row->transaction_type == 'RENTING' && (float)$this->row->deposit > 0): ?>
      <tr>
        <th><?php echo Text::_('COM_JEA_FIELD_DEPOSIT_LABEL') ?></th>
        <td class="right"><?php echo HTMLHelper::_('utility.formatPrice', (float)$this->row->deposit) ?></td>
      </tr>
    <?php endif ?>

    <?php if ((float)$this->row->fees > 0): ?>
      <tr>
        <th><?php echo Text::_('COM_JEA_FIELD_FEES_LABEL') ?></th>
        <td class="right"><?php echo HTMLHelper::_('utility.formatPrice', (float)$this->row->fees) ?></td>
      </tr>
    <?php endif ?>
</table>

<h3><?php echo Text::_('COM_JEA_DETAILS') ?> : </h3>
<table class="jea-data">
    <?php if ($this->row->condition): ?>
      <tr>
        <th><?php echo Text::_('COM_JEA_FIELD_CONDITION_LABEL') ?></th>
        <td><?php echo $this->escape($this->row->condition) ?></td>
      </tr>
    <?php endif ?>

    <?php if ($this->row->living_space): ?>
      <tr>
        <th><?php echo Text::_('COM_JEA_FIELD_LIVING_SPACE_LABEL') ?></th>
        <td><?php echo HTMLHelper::_('utility.formatSurface', (float)$this->row->living_space) ?></td>
      </tr>
    <?php endif ?>

    <?php if ($this->row->land_space): ?>
      <tr>
        <th><?php echo Text::_('COM_JEA_FIELD_LAND_SPACE_LABEL') ?></th>
        <td><?php echo HTMLHelper::_('utility.formatSurface', (float)$this->row->land_space) ?></td>
      </tr>
    <?php endif ?>

    <?php if ($this->row->rooms): ?>
      <tr>
        <th><?php echo Text::_('COM_JEA_FIELD_NUMBER_OF_ROOMS_LABEL') ?></th>
        <td><?php echo $this->row->rooms ?></td>
      </tr>
    <?php endif ?>

    <?php if ($this->row->bedrooms): ?>
      <tr>
        <th><?php echo Text::_('COM_JEA_FIELD_NUMBER_OF_BEDROOMS_LABEL') ?></th>
        <td><?php echo $this->row->bedrooms ?></td>
      </tr>
    <?php endif ?>

    <?php if ($this->row->orientation != '0'): ?>
      <tr>
        <th><?php echo Text::_('COM_JEA_FIELD_ORIENTATION_LABEL') ?></th>
        <td>
            <?php
            switch ($this->row->orientation) {
                case 'N':
                    echo Text::_('COM_JEA_OPTION_NORTH');
                    break;
                case 'NW':
                    echo Text::_('COM_JEA_OPTION_NORTH_WEST');
                    break;
                case 'NE':
                    echo Text::_('COM_JEA_OPTION_NORTH_EAST');
                    break;
                case 'NS':
                    echo Text::_('COM_JEA_OPTION_NORTH_SOUTH');
                    break;
                case 'E':
                    echo Text::_('COM_JEA_OPTION_EAST');
                    break;
                case 'EW':
                    echo Text::_('COM_JEA_OPTION_EAST_WEST');
                    break;
                case 'W':
                    echo Text::_('COM_JEA_OPTION_WEST');
                    break;
                case 'S':
                    echo Text::_('COM_JEA_OPTION_SOUTH');
                    break;
                case 'SW':
                    echo Text::_('COM_JEA_OPTION_SOUTH_WEST');
                    break;
                case 'SE':
                    echo Text::_('COM_JEA_OPTION_SOUTH_EAST');
                    break;
            }
            ?>
        </td>
      </tr>
    <?php endif ?>

    <?php if ($this->row->floor): ?>
      <tr>
        <th><?php echo Text::_('COM_JEA_FIELD_FLOOR_LABEL') ?></th>
        <td><?php echo $this->row->floor ?></td>
      </tr>
    <?php endif ?>

    <?php if ($this->row->floors_number): ?>
      <tr>
        <th><?php echo Text::_('COM_JEA_FIELD_FLOORS_NUMBER_LABEL') ?></th>
        <td><?php echo $this->row->floors_number ?></td>
      </tr>
    <?php endif ?>

    <?php if ($this->row->bathrooms): ?>
      <tr>
        <th><?php echo Text::_('COM_JEA_FIELD_NUMBER_OF_BATHROOMS_LABEL') ?></th>
        <td><?php echo $this->row->bathrooms ?></td>
      </tr>
    <?php endif ?>

    <?php if ($this->row->toilets): ?>
      <tr>
        <th><?php echo Text::_('COM_JEA_FIELD_NUMBER_OF_TOILETS_LABEL') ?></th>
        <td><?php echo $this->row->toilets ?></td>
      </tr>
    <?php endif ?>

    <?php if ($this->row->heating_type_name): ?>
      <tr>
        <th><?php echo Text::_('COM_JEA_FIELD_HEATINGTYPE_LABEL') ?></th>
        <td><?php echo $this->escape($this->row->heating_type_name) ?></td>
      </tr>
    <?php endif ?>

    <?php if ($this->row->hot_water_type_name): ?>
      <tr>
        <th><?php echo Text::_('COM_JEA_FIELD_HOTWATERTYPE_LABEL') ?></th>
        <td><?php echo $this->escape($this->row->hot_water_type_name) ?></td>
      </tr>
    <?php endif ?>
</table>

<?php $dispatcher->dispatch('onBeforeShowDescription', new Event('onBeforeShowDescription', array(&$this->row))) ?>

<div class="property-description clr">
    <?php echo $this->row->description ?>
</div>

<?php $dispatcher->dispatch('onAfterShowDescription', new Event('onAfterShowDescription', array(&$this->row))) ?>

<?php if ($this->params->get('show_googlemap')): ?>
  <h3><?php echo Text::_('COM_JEA_GEOLOCALIZATION') ?> :</h3>
    <?php echo $this->loadTemplate('googlemap') ?>
<?php endif ?>

<?php if ($this->params->get('show_contactform')): ?>
    <?php echo $this->loadTemplate('contactform') ?>
<?php endif ?>

<p>
  <a href="<?php echo Route::_('index.php?option=com_jea&view=properties&Itemid=' . $this->itemId) ?>"><?php echo Text::_('COM_JEA_RETURN_TO_THE_LIST') ?></a>
</p>
