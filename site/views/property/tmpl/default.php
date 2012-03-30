<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @version     $Id$
 * @package     Joomla.Site
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2012 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */


// no direct access
defined('_JEXEC') or die('Restricted access');

if (empty($this->row->id)) {
    echo JText::_('This property doesn\'t exists anymore');
    return;
}



JHTML::stylesheet('media/com_jea/css/jea.css');
$dispatcher = JDispatcher::getInstance();
JPluginHelper::importPlugin('jea');
?>

<p class="prev-next-navigation">
<?php echo $this->getPrevNextNavigation() ?>
</p>

<?php if ($this->params->get('show_print_icon')): ?>
<div class="jea_tools">
  <!-- <a href="javascript:window.print()" title="<?php echo JText::_('Print') ?>"><?php echo JHTML::_('image.site', 'printButton.png') ?></a> -->
</div>
<?php endif ?>

<h1><?php echo $this->page_title ?></h1>

<?php if ( $this->params->get('show_creation_date', 0) ) : ?>
<p>
  <span class="date"> <?php echo JHTML::_('date',  $this->row->created, JText::_('DATE_FORMAT_LC3') ); ?></span>
</p>
<?php endif ?>

<?php if(!empty($this->row->images)): ?>
<div id="jea-gallery">
<?php echo $this->loadTemplate('squeezebox') ?>
</div>
<?php endif ?>

<h2><?php echo JText::_('COM_JEA_REF')?> : <?php echo $this->escape($this->row->ref) ?></h2>

<div class="clr">&nbsp;</div>

<div class="item_second_column">
  <h3><?php echo JText::_('COM_JEA_FIELD_ADDRESS_LABEL') ?>:</h3>
  <strong> <?php if($this->row->address) echo $this->escape( $this->row->address ).", <br /> \n" ?>
  <?php if ($this->row->zip_code) echo $this->escape( $this->row->zip_code ) ?> 
  <?php if ($this->row->town) echo strtoupper( $this->escape($this->row->town) )."<br /> \n" ?>
  </strong>
  <?php if ($this->row->area)
  echo JText::_('COM_JEA_FIELD_AREA_LABEL') . ' : <strong>'   .$this->escape( $this->row->area ). "</strong>\n" ?>

  <?php if (!empty($this->row->amenities)) : ?>
  <h3><?php echo JText::_('COM_JEA_AMENITIES')?></h3>
  <?php echo JHtml::_('amenities.bindList', $this->row->amenities, 'ul') ?>
  <?php endif  ?>
</div>

  <?php if (intval($this->row->availability)): ?>
<p>
  <em><?php echo JText::_('COM_JEA_FIELD_PROPERTY_AVAILABILITY_LABEL') ?> : <?php echo $this->row->availability ?> </em>
</p>
  <?php endif  ?>

<table>
  <tr>
    <td><?php echo $this->row->transaction_type == 'RENTING' ? JText::_('COM_JEA_FIELD_PRICE_RENT_LABEL') :  JText::_('COM_JEA_FIELD_PRICE_LABEL') ?></td>
    <td>: <strong><?php echo JHtml::_('utility.formatPrice', (float) $this->row->price, JText::_('Consult us')) ?></strong>
    <?php if ($this->row->transaction_type == 'RENTING' && (float)$this->row->price != 0.0) echo JText::_('COM_JEA_PRICE_PER_FREQUENCY_'. $this->row->rate_frequency) ?>
    </td>
  </tr>

  <?php if ($this->row->charges): ?>
  <tr>
    <td><?php echo JText::_('COM_JEA_FIELD_CHARGES_LABEL') ?></td>
    <td>: <strong><?php echo JHtml::_('utility.formatPrice', (float) $this->row->charges) ?></strong></td>
  </tr>
  <?php endif  ?>

  <?php if ( $this->row->is_renting &&  floatval($this->row->deposit) > 0 ): ?>
  <tr>
    <td><?php echo JText::_('COM_JEA_FIELD_DEPOSIT_LABEL') ?></td>
    <td>: <strong><?php echo JHtml::_('utility.formatPrice', (float) $this->row->deposit) ?></strong></td>
  </tr>
  <?php endif  ?>

  <?php if ($this->row->fees): ?>
  <tr>
    <td><?php echo JText::_('COM_JEA_FIELD_FEES_LABEL') ?></td>
    <td>: <strong><?php echo JHtml::_('utility.formatPrice', (float) $this->row->fees) ?></strong></td>
  </tr>
  <?php endif  ?>
</table>

<h3><?php echo JText::_('COM_JEA_DETAILS') ?> : </h3>
<?php if ($this->row->condition): ?>
<p><strong><?php echo ucfirst($this->escape($this->row->condition)) ?> </strong></p>
<?php endif  ?>

<p>
  <?php if ($this->row->living_space): ?>
  <?php echo  JText::_( 'COM_JEA_FIELD_LIVING_SPACE_LABEL' ) ?> : 
  <strong><?php echo JHtml::_('utility.formatSurface', (float) $this->row->living_space ) ?></strong>
  <br />
  <?php endif ?>

  <?php if ($this->row->land_space): ?>
  <?php echo  JText::_( 'COM_JEA_FIELD_LAND_SPACE_LABEL' ) ?> : 
  <strong><?php echo JHtml::_('utility.formatSurface', (float) $this->row->land_space ) ?></strong>
  <br />
  <?php endif ?>

  <?php if ( $this->row->rooms ): ?>
  <?php echo JText::_('COM_JEA_FIELD_NUMBER_OF_ROOMS_LABEL') ?> : 
  <strong><?php echo $this->row->rooms ?> </strong> <br />
  <?php endif  ?>
  
  <?php if ( $this->row->bedrooms ): ?>
  <?php echo JText::_('COM_JEA_FIELD_NUMBER_OF_BEDROOMS_LABEL') ?> : 
  <strong><?php echo $this->row->bedrooms ?> </strong> <br />
  <?php endif  ?>

  <?php if ( $this->row->floor ): ?>
  <?php echo JText::_('COM_JEA_FIELD_FLOOR_LABEL') ?> : 
  <strong><?php echo $this->row->floor ?> </strong> <br />
  <?php endif  ?>
  
  <?php if ( $this->row->floors_number ): ?>
  <?php echo JText::_('COM_JEA_FIELD_FLOORS_NUMBER_LABEL') ?> : 
  <strong><?php echo $this->row->floors_number ?> </strong> <br />
  <?php endif  ?>

  <?php if ( $this->row->bathrooms ): ?>
  <?php echo JText::_('COM_JEA_FIELD_NUMBER_OF_BATHROOMS_LABEL') ?> : 
  <strong><?php echo $this->row->bathrooms ?> </strong> <br />
  <?php endif  ?>

  <?php if ($this->row->toilets): ?>
  <?php echo JText::_('COM_JEA_FIELD_NUMBER_OF_TOILETS_LABEL') ?> : 
  <strong><?php echo $this->row->toilets ?> </strong>
  <?php endif  ?>
</p>

<p>
<?php if ( $this->row->heating_type_name ): ?>
<?php echo JText::_('COM_JEA_FIELD_HEATINGTYPE_LABEL') ?>
  : <strong><?php echo ucfirst($this->escape( $this->row->heating_type_name )) ?> </strong><br />
  <?php endif  ?>

  <?php if ( $this->row->hot_water_type_name ): ?>
  <?php echo JText::_('COM_JEA_FIELD_HOTWATERTYPE_LABEL') ?>
  : <strong><?php echo ucfirst($this->escape( $this->row->hot_water_type_name )) ?> </strong>
  <?php endif  ?>
</p>


<div class="clr">&nbsp;</div>

  <?php $dispatcher->trigger('onBeforeShowDescription', array(&$this->row)) ?>

<div class="item_description">
<?php echo $this->row->description ?>
</div>

<?php $dispatcher->trigger('onAfterShowDescription', array(&$this->row)) ?>

<?php if ( $this->params->get('show_googlemap') ): ?>
<h3>
<?php echo JText::_('COM_JEA_GEOLOCALIZATION') ?> :
</h3>
<?php echo $this->loadTemplate('googlemap') ?>
<?php endif ?>

<?php if ( $this->params->get('show_contactform') ): // TODO: sendmail implementation ?>

<form action="<?php echo JRoute::_('index.php?option=com_jea&task=property.sendmail') ?>" method="post" enctype="application/x-www-form-urlencoded">

  <fieldset>
    <legend>
    <?php echo JText::_('COM_JEA_CONTACT_FORM_LEGEND') ?>
    </legend>
    <p>
      <label for="name"><?php echo JText::_('COM_JEA_NAME') ?> :</label><br />
      <input type="text" name="name" id="name" size="40" value="<?php echo $this->escape(JRequest::getVar('name', '')) ?>" />
    </p>

    <p>
      <label for="email"><?php echo JText::_('COM_JEA_EMAIL') ?> :</label><br />
      <input type="text" name="email" id="email" size="40" value="<?php echo $this->escape(JRequest::getVar('email', '')) ?>" />
    </p>

    <p>
      <label for="telephone"><?php echo JText::_('COM_JEA_TELEPHONE') ?> :</label><br />
      <input type="text"name="telephone" id="telephone" size="40" value="<?php echo $this->escape(JRequest::getVar('telephone', '')) ?>" />
    </p>

    <p>
      <label for="subject"><?php echo JText::_('COM_JEA_SUBJECT') ?> :</label><br />
      <input type="text" name="subject" id="subject" value="Ref : <?php echo $this->escape( $this->row->ref ) ?>" size="40" />
    </p>

    <p>
      <label for="e_message"><?php echo JText::_('COM_JEA_MESSAGE') ?> :</label><br />
      <textarea name="e_message" id="e_message" rows="10" cols="40"><?php echo $this->escape(JRequest::getVar('message', '')) ?></textarea>
    </p>
    <?php $dispatcher->trigger('onFormCaptchaDisplay'); // TODO: Captcha integration ?>
    <p>
      <input type="hidden" name="created_by" value="<?php echo $this->row->created_by ?>" />
      <?php echo JHTML::_( 'form.token' ) ?>
      <input type="submit" value="<?php echo JText::_('COM_JEA_SEND') ?>" />
    </p>
  </fieldset>
</form>
      <?php endif  ?>

<p>
  <a href="javascript:window.history.back()" class="jea_return_link"><?php echo JText::_('COM_JEA_RETURN_TO_THE_LIST')?> </a>
</p>
