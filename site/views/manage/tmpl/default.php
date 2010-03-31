<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @version     $Id$
 * @package     Jea.site
 * @copyright	Copyright (C) 2008 PHILIP Sylvain. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla Estate Agency is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses.
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

JHTML::stylesheet('jea.css', 'media/com_jea/css/');
$rowsCount = count( $this->rows );
$altrow = 1;
?>


<?php if ( $this->params->get('show_page_title', 0) && $this->params->get('page_title', '') ) : ?>
<h1><?php echo $this->params->get('page_title') ?></h1>
<?php endif ?>

<p class="jea_add_new"><a href="<?php echo JRoute::_('&layout=form') ?>"><?php echo JText::_('Add new property' )?></a></p>

<?php if( !empty($this->rows) ) : ?>

<form name="adminForm" id="adminForm" action="<?php echo JRoute::_('') ?>" method="post">

	<p class="pagenavigation"><?php echo $this->pagination->getPagesLinks() ?><br />
	<em><?php echo $this->pagination->getPagesCounter(); ?></em></p>
	
	<p class="limitbox"><em><?php echo JText::_('Results per page') ?> : </em><?php echo $this->pagination->getLimitBox() ?></p>
	
	<p>
    	<select name="cat" onchange="this.form.submit()" class="inputbox" size="1">
    	   <option value="-1">--<?php echo JText::_('Filter') ?>--</option>
    	   <option value="0" <?php if($this->cat == 0) echo 'selected="selected"' ?>><?php echo JText::_('Selling') ?></option>
    	   <option value="1" <?php if($this->cat == 1) echo 'selected="selected"' ?>><?php echo JText::_('Renting') ?></option>
    	</select>
	</p>
	
	<table class="jea_listing" >
		<thead>
		<tr>
			<th class="ref"><?php echo JHTML::_('grid.sort', 'Ref', 'ref', $this->order_dir , $this->order ) ?></th>
			<th class="type"><?php echo JHTML::_('grid.sort', 'Type', 'type', $this->order_dir , $this->order ) ?></th>
			<th class="adress"><?php echo JText::_('Adress' )?></th>
			<th class="town"><?php echo JHTML::_('grid.sort', 'Town', 'town', $this->order_dir , $this->order ) ?></th>
			<th class="living_space number"><?php echo JHTML::_('grid.sort', 'Living space', 'living_space', $this->order_dir , $this->order ) ?></th>
			<th class="land_space number"><?php echo JHTML::_('grid.sort', 'Land space', 'land_space', $this->order_dir , $this->order ) ?></th>
			<th class="price number"><?php echo JHTML::_('grid.sort', 'renting' ? JText::_('Renting price') :  JText::_('Selling price'), 'price', $this->order_dir , $this->order ) ?></th>
			<th class="published"><?php echo JText::_('Published' )?></th>
			<th class="edit"><?php echo JText::_('Delete' )?></th>
		</tr>
		</thead>
		
		<tbody>
<?php foreach ($this->rows as $k => $row): $altrow = $altrow ? 0 : 1 ?>

		<tr class="row<?php echo $altrow ?>" >
			<td class="ref"><a href="<?php echo JRoute::_( 'index.php?option=com_jea&view=manage&layout=form&id='.$row->slug ) ?>" title="<?php echo JText::_('Edit') ?>" > 
			<?php echo $row->ref ?></a></td>
			<td class="type"><?php echo $row->type ?></td>
			<td class="adress"><?php echo $row->adress ?></td>
			<td class="town"><?php echo $row->town ?></td>
			<td class="living_space number"><?php echo $this->formatNumber( floatval( $row->living_space), 2 ) . ' ' . $this->params->get('surface_measure') ?></td>
			<td class="land_space number"><?php echo $this->formatNumber( floatval( $row->land_space ), 2 ). ' ' . $this->params->get('surface_measure') ?></td>
			<td class="price number"><?php echo $this->formatPrice( floatval($row->price) , JText::_('Consult us') ) ?></td>
			<td class="published"><?php echo $row->published ? JHTML::_('image.site', 'publish_g.png', '/administrator/images/') : JHTML::_('image.site', 'publish_r.png', '/administrator/images/') ?></td>
			<td class="delete">
			<a href="<?php echo JRoute::_( 'index.php?option=com_jea&task=delete&id='.$row->id ) ?>" 
			   title="<?php echo JText::_('Delete') ?>"
			   onclick="return confirm('<?php echo JText::_('Are you sure you want to delete this item?') ?>')">
			<?php echo JHTML::_('image.site', 'media_trash.png', '/media/com_jea/images/') ?></a>
			</td>
		</tr>
<?php endforeach ?>
		</tbody>
	
	</table>
	
	 <div>
	  <input type="hidden" name="filter_order" value="<?php echo $this->order ?>" />
      <input type="hidden" name="filter_order_Dir" value="<?php echo $this->order_dir ?>" />
	  <input type="hidden" name="Itemid" value="<?php echo JRequest::getInt('Itemid', 0) ?>" />
	</div>
	
	<p class="pagenavigation"><?php echo $this->pagination->getPagesLinks() ?><br />
	<em><?php echo $this->pagination->getPagesCounter(); ?></em></p>

</form>

<?php endif ?>