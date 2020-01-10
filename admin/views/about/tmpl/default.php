<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2020 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * @var $this JeaViewAbout
 */

JHtml::stylesheet('media/com_jea/css/jea.admin.css');
?>

<?php if (!empty($this->sidebar)) : ?>
<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<?php endif ?>

<div id="j-main-container" class="span10">
	<?php echo JHtml::_('bootstrap.startTabSet', 'aboutTab', array('active' => 'pan1')) ?>

	<?php echo JHtml::_('bootstrap.addTab', 'aboutTab', 'pan1', JText::_('COM_JEA_ABOUT')) ?>
	<div class="row-fluid">
		<div class="span2">
			<img src="../media/com_jea/images/logo.png" alt="logo.png" />
		</div>
		<div class="span10">
			<p>
				<strong>Joomla Estate Agency <?php echo $this->getVersion() ?> </strong>
			</p>
			<p>
				<a href="http://jea.sphilip.com/" target="_blank"><?php echo JText::_('COM_JEA_PROJECT_HOME') ?></a>
			</p>
			<p>
				<a href="http://jea.sphilip.com/forum/" target="_blank"><?php echo JText::_('COM_JEA_FORUM') ?></a>
			</p>
			<p>
				<a href="https://github.com/JoomlaEstateAgency/com_jea/wiki/" target="_blank"><?php echo JText::_('COM_JEA_DOCUMENTATION') ?></a>
			</p>
			<p>
				<?php echo JText::_('COM_JEA_MAIN_DEVELOPER') ?> : <a href="http://www.sphilip.com" target="_blank">Sylvain Philip</a><br />
				<?php echo JText::_('COM_JEA_CREDITS') ?> : <a href="https://twitter.com/#!/phproberto" target="_blank">Roberto Segura</a>
			</p>
		</div>
	</div>
	<?php echo JHtml::_('bootstrap.endTab') ?>

	<?php echo JHtml::_('bootstrap.addTab', 'aboutTab', 'pan2', JText::_('COM_JEA_LICENCE')) ?>
	<pre>
	<?php require JPATH_COMPONENT . '/LICENCE.txt' ?>
	</pre>
	<?php echo JHtml::_('bootstrap.endTab') ?>

	<?php echo JHtml::_('bootstrap.addTab', 'aboutTab', 'pan3', JText::_('COM_JEA_VERSIONS')) ?>
	<pre>
	<?php require JPATH_COMPONENT . '/NEWS.txt' ?>
	</pre>
	<?php echo JHtml::_('bootstrap.endTab') ?>

	<?php echo JHtml::_('bootstrap.endTabSet') ?>
</div>
