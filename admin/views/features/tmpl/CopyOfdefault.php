<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 * 
 * @version     $Id: default.php 145 2010-03-31 10:03:47Z ilhooq $
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

JHTML::stylesheet('jea.admin.css', 'media/com_jea/css/');
$rowsCount = count( $this->rows ) ;
$altrow = 1;
?>

<form action="index.php?option=com_jea&controller=features" method="post" name="adminForm" id="adminForm">

<table class="adminheading">
	<tr>
		<td width="100%" align="right">
		<?php echo JText::_('Change table') ?> : 
		</td>
		<td nowrap="nowrap">
			<?php echo $this->selectTableList ?>
		</td>
	</tr>
</table>

<table class="adminlist">
	<thead>
		<tr>
			<th width="1%" class="title">#</th>
			<th width="2%">
			<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows ) ?>);" />
			</th>
			
			<th nowrap="nowrap" width="100%" ><?php echo JText::_('Value') ?></th>

			<th colspan="2" nowrap="nowrap"><?php echo JText::_('Ordering') ?></th>
			
			<!--  <th nowrap="nowrap" width="20%" ><?php // echo JText::_('Type') ?></th>-->
			
		</tr>
	</thead>

	<tfoot>
		<tr>
			<td colspan="5">
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

<?php foreach ( $this->rows as $k => $row ) :?>

<?php $altrow = ( $altrow == 1 )? 0 : 1; ?>

		<tr class="row<?php echo $altrow ?>">
		    <td><?php echo $k ?></td>
			<td><?php echo JHTML::_('grid.id', $k, $row->id ) ?></td>
			<td nowrap="nowrap"><a href="#edit" onclick="return listItemTask('cb<?php echo $k ?>','edit')"><?php echo $this->escape( $row->value ) ?></a></td>
			<td align="center"><?php echo $this->pagination->orderUpIcon( $k ) ?></td>
			<td align="center"><?php echo $this->pagination->orderDownIcon( $k, $rowsCount ) ?></td>
		</tr>
		
<?php endforeach ?>

	</tbody>

</table>

<div>
	<input type="hidden" name="task" value="" /> 
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="limitstart" value="<?php echo $this->limitstart ?>" />
	<?php echo JHTML::_( 'form.token' ) ?>
</div>

</form>
