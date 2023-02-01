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
 * @var $this JeaViewProperty
 */

$uri = JUri::getInstance();
?>

<form action="<?php echo Route::_('index.php?option=com_jea&task=default.sendContactForm') ?>"
      method="post" id="jea-contact-form">
  <fieldset>
    <legend><?php echo Text::_('COM_JEA_CONTACT_FORM_LEGEND') ?></legend>
    <dl>
      <dt>
        <label for="name"><?php echo Text::_('COM_JEA_NAME') ?> :</label>
      </dt>

      <dd>
        <input type="text" name="name" id="name" size="30"
               value="<?php echo $this->escape($this->state->get('contact.name')) ?>"/>
      </dd>

      <dt>
        <label for="email"><?php echo Text::_('COM_JEA_EMAIL') ?> :</label>
      </dt>

      <dd>
        <input type="text" name="email" id="email" size="30"
               value="<?php echo $this->escape($this->state->get('contact.email')) ?>"/>
      </dd>

      <dt>
        <label for="telephone"><?php echo Text::_('COM_JEA_TELEPHONE') ?> :</label>
      </dt>

      <dd>
        <input type="text" name="telephone" id="telephone" size="30"
               value="<?php echo $this->escape($this->state->get('contact.telephone')) ?>"/>
      </dd>

      <dt>
        <label for="subject"><?php echo Text::_('COM_JEA_SUBJECT') ?> :</label>
      </dt>

      <dd>
        <input type="text" name="subject" id="subject" size="30"
               value="<?php echo Text::_('COM_JEA_REF') ?> : <?php echo $this->escape($this->row->ref) ?>"/>
      </dd>

      <dt>
        <label for="e_message"><?php echo Text::_('COM_JEA_MESSAGE') ?> :</label>
      </dt>

      <dd>
        <textarea name="message" id="e_message" rows="10"
                  cols="40"><?php echo $this->escape($this->state->get('contact.message')) ?></textarea>
      </dd>

        <?php if ($this->params->get('use_captcha')): ?>
          <dd><?php echo $this->displayCaptcha() ?></dd>
        <?php endif ?>

      <dd>
        <input type="hidden" name="id" value="<?php echo $this->row->id ?>"/>
          <?php echo HTMLHelper::_('form.token') ?>
        <input type="hidden" name="propertyURL"
               value="<?php echo base64_encode($uri->toString()) ?>"/>
        <input type="submit" value="<?php echo Text::_('COM_JEA_SEND') ?>"/>
      </dd>
    </dl>
  </fieldset>
</form>
