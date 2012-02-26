<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @version     $Id$
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2012 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.html.pane');
JHTML::stylesheet('jea.admin.css', 'media/com_jea/css/');
$pane = JPane::getInstance('tabs');
?>


<?php echo $pane->startPane( 'jea_info' ) ?>

<?php echo $pane->startPanel( JText::_( 'About' ), 'pan1' ) ?>
<div style="height: 300px; float: left; margin-right: 50px;">
  <img src="../media/com_jea/images/logo.png" alt="logo.png" />
</div>

<div style="height: 300px">
  <p>
    <strong>Joomla Estate Agency <?php echo $this->getVersion() ?> </strong>
  </p>

  <p>
    <a href="http://joomlacode.org/gf/project/jea/" target="_blank"><?php echo JText::_('Project home') ?></a>
  </p>

  <p>
    <a href="http://joomlacode.org/gf/project/jea/forum/" target="_blank"><?php echo JText::_('Forum') ?></a>
  </p>

  <p>
    <a href="http://joomlacode.org/gf/project/jea/wiki/" target="_blank"><?php echo JText::_('Documentation') ?></a>
  </p>

  <p>
  <?php echo JText::_('Main developer') ?> : <a href="http://www.sphilip.com" target="_blank">Sylvain Philip</a>
  </p>

  <p>
  <?php echo JText::_('Logo') ?> : <a href="http://www.lievregraphiste.com/" target="_blank">Elisa Roche</a>
  </p>
</div>
<?php echo $pane->endPanel() ?>

<?php echo $pane->startPanel( JText::_( 'Licence' ), 'pan2' ) ?>
<div class="file_reader">
  <pre>
  <?php require JPATH_COMPONENT . DS . 'LICENCE.txt' ?>
  </pre>
</div>
<?php echo $pane->endPanel() ?>

<?php echo $pane->startPanel( JText::_( 'Versions' ), 'pan3' ) ?>
<div class="file_reader">
  <pre>
  <?php require JPATH_COMPONENT . DS . 'NEWS.txt' ?>
  </pre>
</div>
<?php echo $pane->endPanel() ?>

<?php echo $pane->endPane() ?>

