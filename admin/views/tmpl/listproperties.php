<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 * 
 * @version		0.1 2008-02-26
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

$rowsCount = count( $this->rows ) ;
$altrow = 1;
//TODO:utiliser JHTML::_('grid.sort' pour trier les listes
?>

<script language="javascript" type="text/javascript">

function ordering(field){
	var form = document.adminForm;
	form.ordering.value = field;
	form.submit();
}


function submitbutton(pressbutton, section) {
	var form = document.adminForm;
	
	if (pressbutton == 'new' || pressbutton == 'edit' ) {
		form.hidemainmenu.value = "1";
	}
	
	submitform( pressbutton );
	return;
}

function findref() {
	var form = document.adminForm;
	if (form.find_ref.value !=''){
		form.submit();
	}
}

</script>

<form action="index.php?option=com_jea&controller=properties&cat=<?php echo $this->cat ?>" method="post" name="adminForm" id="adminForm">

<table class="adminheading">
	<tr>
		<td width="100%">
			<label for="find_ref" ><?php echo JText::_('Find reference') ?></label> : 
			<input type="text" id="find_ref" name="find_ref" size="8" value="" /> 
			<input type="button" value="ok" onclick="findref()" />
		</td>
		<td nowrap="nowrap">
			<?php echo JText::_('Filter') ?> : 
			<?php echo  $this->lists['types'] ?>
			<?php echo  $this->lists['towns'] ?>
			<?php echo  $this->lists['departments'] ?>
		</td>
	</tr>
</table>

<table class="adminlist">
	<thead>
		<tr>
			<th style="text-align:left"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo $rowsCount ?>);" /></th>
			
			<th nowrap="nowrap">
				<a href="javascript:ordering('ref')" title="<?php echo JText::_('Click to sort this column') ?>">
				<?php echo JText::_('Reference') ?></a>
			</th>
			
			<th nowrap="nowrap"><?php echo JText::_('Property type') ?></th>
			
			<th nowrap="nowrap"><?php echo JText::_('Adress') ?></th>
			
			<th nowrap="nowrap"><?php echo JText::_('Town') ?></th>
			
			<th nowrap="nowrap"><?php echo JText::_('Department') ?></th>
			
			<th nowrap="nowrap">
				<a href="javascript:ordering('price')" title="<?php echo JText::_('Click to sort this column') ?>">
				<?php echo ($this->cat=='renting')? JText::_('Rent') : JText::_('Price') ?></a>
			</th>
			
			<th nowrap="nowrap"><?php echo JText::_('Emphasis') ?></th>
			
			<th nowrap="nowrap">
				<a href="javascript:products_filter('published')" title="<?php echo JText::_('Click to sort this column') ?>" ><?php echo JText::_('Published') ?></a>
			</th>
			
			<th colspan="2" nowrap="nowrap">
				<a href="javascript:ordering('ordering')" title="<?php echo JText::_('Click to sort this column') ?>">
				<?php echo JText::_('Ordering') ?></a>
			</th>
			
			<th nowrap="nowrap">
				<a href="javascript:ordering('date_insert DESC')" title="<?php echo JText::_('Click to sort this column') ?>">
				<?php echo JText::_('Date') ?></a>
			</th>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<td colspan="12">
				<del class="container">
					<div class="pagination">
						<div class="limit">
							<?php echo JText::_('Items per page')?> :&nbsp;&nbsp;
							<?php echo $this->pagination->getLimitBox() ?>&nbsp;&nbsp;
						</div>
						<?php echo $this->pagination->getPagesLinks() ?>
						<div class="limit"><?php echo $this->pagination->getPagesCounter() ?></div>
					</div>
				</del>
			</td>
		</tr>
	</tfoot>

	<tbody>

	<?php foreach ($this->rows as $k => $row) :?>

	<?php $altrow = ( $altrow == 1 )? 0 : 1; ?>

		<tr class="row<?php echo $altrow ?>">

			<td><?php echo JHTML::_('grid.id',   $k, $row->id ) ?></td>

			<td>
				<a href="#edit" onclick="return listItemTask('cb<?php echo $k ?>','edit')">
				<?php echo $this->escape( $row->ref ) ?></a>
			</td>

			<td><?php echo $this->escape( $row->type ) ?></td>
			<td><?php echo $this->escape( $row->adress ) ?></td>
			<td><?php echo $this->escape( $row->town ) ?></td>
			<td><?php echo $this->escape( $row->department ) ?></td>
			<td><?php echo $row->price ?> <?php echo $this->params->get('currency_symbol', '&euro;') ?></td>
			<td align="center">
				<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $k ?>','emphasize')">
				<img src="images/<?php echo ( $row->emphasis ) ? 'tick.png' : 'publish_x.png';?>"
				width="16" height="16" border="0" alt="<?php echo $row->emphasis ? JText::_('Yes') : JText::_('No') ?>" />
				</a>
			</td>

			<td align="center"><?php echo JHTML::_('grid.published', $row, $k, 'publish_g.png') ?></td>

			<td align="center"><?php echo $this->pagination->orderUpIcon( $k ) ?></td>

			<td align="center"><?php echo $this->pagination->orderDownIcon( $k, $rowsCount ) ?></td>
			
			<td><?php echo JHTML::_('date',  $row->date_insert, JText::_('DATE_FORMAT_LC4') ); ?></td>
		</tr>

		<?php endforeach ?>

	</tbody>

</table>

<div>
	<input type="hidden" name="task" value="" /> 
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="ordering" value="ordering" />
	<input type="hidden" name="limitstart" value="<?php echo $this->offset ?>" />
	<input type="hidden" name="hidemainmenu" value="0" />
</div>

</form>
