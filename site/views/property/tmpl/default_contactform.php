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
$uri = JFactory::getURI();
?>


<form action="<?php echo JRoute::_('index.php?option=com_jea&task=default.sendContactForm') ?>" method="post" enctype="application/x-www-form-urlencoded">

  <fieldset>
    <legend><?php echo JText::_('COM_JEA_CONTACT_FORM_LEGEND') ?></legend>
    <p>
      <label for="name"><?php echo JText::_('COM_JEA_NAME') ?> :</label><br />
      <input type="text" name="name" id="name" size="40" value="<?php echo $this->escape($this->state->get('contact.name')) ?>" />
    </p>

    <p>
      <label for="email"><?php echo JText::_('COM_JEA_EMAIL') ?> :</label><br />
      <input type="text" name="email" id="email" size="40" value="<?php echo $this->escape($this->state->get('contact.email')) ?>" />
    </p>

    <p>
      <label for="telephone"><?php echo JText::_('COM_JEA_TELEPHONE') ?> :</label><br />
      <input type="text"name="telephone" id="telephone" size="40" value="<?php echo $this->escape($this->state->get('contact.telephone')) ?>" />
    </p>

    <p>
      <label for="subject"><?php echo JText::_('COM_JEA_SUBJECT') ?> :</label><br />
      <input type="text" name="subject" id="subject" value="<?php echo JText::_('COM_JEA_REF') ?> : <?php echo $this->escape($this->row->ref) ?>" size="40" />
    </p>

    <p>
      <label for="message"><?php echo JText::_('COM_JEA_MESSAGE') ?> :</label><br />
      <textarea name="message" id="e_message" rows="10" cols="40"><?php echo $this->escape($this->state->get('contact.message')) ?></textarea>
    </p>
    
    <?php if ($this->params->get('use_captcha')) echo $this->displayCaptcha() ?>
    <p>
      <input type="hidden" name="id" value="<?php echo $this->row->id ?>" />
      <?php echo JHTML::_( 'form.token' ) ?>
      <input type="hidden" name="propertyURL" value="<?php echo base64_encode($uri->toString())?>" />
      <input type="submit" value="<?php echo JText::_('COM_JEA_SEND') ?>" />
    </p>
  </fieldset>
</form>
