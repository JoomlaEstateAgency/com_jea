<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 * 
 * @version     0.9 2009-10-14
 * @package		Jea.admin
 * @copyright	Copyright (C) 2008 PHILIP Sylvain. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla Estate Agency is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses.
 * 
 */


// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.html.pane');
JHTML::stylesheet('jea.admin.css', 'media/com_jea/css/');
$pane =& JPane::getInstance('tabs');
?>


<?php echo $pane->startPane( 'jea_info' ) ?>
<?php echo $pane->startPanel( JText::_( 'About' ), 'pan1' ) ?>
      <img src="../media/com_jea/images/logo.png" alt="logo.png" style="float:left;margin-right: 50px" />
      <p>
      	<strong>Joomla Estate Agency <?php echo ComJea::version() ?> </strong>
      </p>
      
      <p style="height:200px">
      	<?php echo JText::_('Main developer') ?> : Sylvain Philip, contact@sphilip.com
      </p>
      
<?php echo $pane->endPanel() ?>
<?php echo $pane->startPanel( JText::_( 'Licence' ), 'pan2' ) ?>


<div class="file_reader">
<pre><?php require JPATH_COMPONENT . DS . 'LICENCE.txt' ?></pre>
</div>

      
<?php echo $pane->endPanel() ?>

<?php echo $pane->startPanel( JText::_( 'Versions' ), 'pan3' ) ?>

<div class="file_reader">
<pre><?php require JPATH_COMPONENT . DS . 'NEWS.txt' ?></pre>
</div>
      
<?php echo $pane->endPanel() ?>


<?php echo $pane->endPane() ?>

