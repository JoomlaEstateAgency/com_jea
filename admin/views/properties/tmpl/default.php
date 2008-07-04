<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 * 
 * @version		0.4 2008-06
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
?>

<form action="index.php?option=com_jea&controller=properties&cat=<?php echo $this->get('category') ?>" method="post" name="adminForm" id="adminForm">

<table class="adminheading">
	<tr>
		<td width="100%">
			<label for="search" ><?php echo JText::_('Find reference') ?></label> : 
			<input type="text" id="search" name="search" size="8" value="<?php echo $this->search ?>" /> 
			<input type="submit" value="ok" />
		</td>
		<td nowrap="nowrap">
			<?php echo JText::_('Filter') ?> : 
			<?php echo  $this->getHtmlList('types', $this->type_id, true ) ?>
			<?php echo  $this->getHtmlList('towns', $this->town_id, true ) ?>
			<?php echo  $this->getHtmlList('departments', $this->department_id, true ) ?>
		</td>
	</tr>
</table>

<table class="adminlist">
	<thead>
		<tr>
			<th style="text-align:left"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo $rowsCount ?>);" /></th>
			
			<th nowrap="nowrap">
				<?php echo JHTML::_('grid.sort', 'Reference', 'ref', $this->order_dir , $this->order ) ?>
			</th>
			
			<th nowrap="nowrap"><?php echo JText::_('Property type') ?></th>
			
			<th nowrap="nowrap"><?php echo JText::_('Adress') ?></th>
			
			<th nowrap="nowrap"><?php echo JText::_('Town') ?></th>
			
			<th nowrap="nowrap"><?php echo JText::_('Department') ?></th>
			
			<th nowrap="nowrap">
				<?php echo JHTML::_('grid.sort', 'Price', 'price', $this->order_dir , $this->order ) ?>
			</th>
			
			<th nowrap="nowrap"><?php echo JText::_('Emphasis') ?></th>
			
			<th nowrap="nowrap">
				<?php echo JHTML::_('grid.sort', 'Published', 'published', $this->order_dir , $this->order ) ?>
			</th>
			
			<th colspan="2" nowrap="nowrap">
				<?php echo JHTML::_('grid.sort', 'Ordering', 'ordering', $this->order_dir , $this->order ) ?>
			</th>
			
			<th nowrap="nowrap">
				<?php echo JHTML::_('grid.sort', 'Date', 'date_insert', $this->order_dir , $this->order ) ?>
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

			<td><?php echo JHTML::_('grid.checkedout', $row, $k ) ?></td>

			<td>
			<?php if ($this->is_checkout($row->checked_out)) : ?>
				<?php echo $this->escape( $row->ref ) ?>
			<?php else : ?>
				<a href="#edit" onclick="return listItemTask('cb<?php echo $k ?>','edit')">
				<?php echo $this->escape( $row->ref ) ?></a>
			<?php endif ?>
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
	<input type="hidden" name="filter_order" value="<?php echo $this->order ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->order_dir ?>" />
	<input type="hidden" name="limitstart" value="<?php echo $this->limitstart ?>" />
	<?php echo JHTML::_( 'form.token' ) ?>
</div>

</form>
