<?php // no direct access
defined('_JEXEC') or die('Restricted access');

JHTML::stylesheet('jea.css', 'media/com_jea/css/');
$rowsCount = count( $this->rows );
$altrow = 1;
?>


<?php if ( $this->params->get('show_page_title', 0) && $this->params->get('page_title', '') ) : ?>
<h1><?php echo $this->params->get('page_title') ?></h1>
<?php endif ?>



<?php if( !empty($this->rows) ) : ?>

<form id="jForm" action="<?php echo $this->getViewUrl() ?>" method="post">

	<p class="pagenavigation"><?php echo $this->pagination->getPagesLinks() ?><br />
	<em><?php echo $this->pagination->getPagesCounter(); ?></em></p>
	
	<div class="clr" ></div>
	
	<div id="sort_options">
		<?php if ( $this->params->get('sort_price') ): ?>
		<a href="javascript:changeOrdering('price')"><?php echo JText::_('Sort by price') ?></a><br />
		<?php endif ?>
		<?php if ( $this->params->get('sort_livingspace') ): ?>
		<a href="javascript:changeOrdering('living_space')"><?php echo JText::_('Sort by living space') ?></a><br />
		<?php endif ?>
		<?php if ( $this->params->get('sort_landspace') ): ?>
		<a href="javascript:changeOrdering('land_space')"><?php echo JText::_('Sort by land space') ?></a><br />
		<?php endif ?>
	</div>
	
	<p class="limitbox"><em><?php echo JText::_('Results per page') ?> : </em><?php echo $this->pagination->getLimitBox() ?></p>
	
	
	<table class="jea_listing" >
		<thead>
		<tr>
			<th class="ref"><?php echo JText::_('Ref' )?></th>
			<th class="type"><?php echo JText::_('Type' )?></th>
			<th class="adress"><?php echo JText::_('Adress' )?></th>
			<th class="town"><?php echo JText::_('Town' )?></th>
			<th class="living_space number"><?php echo JText::_('Living space' )?></th>
			<th class="land_space number"><?php echo JText::_('Land space' )?></th>
			<th class="price number"><?php echo $this->cat == 'renting' ? JText::_('Renting price') :  JText::_('Selling price') ?></th>
			<th class="published"><?php echo JText::_('Published' )?></th>
			<th class="edit"><?php echo JText::_('Edit' )?></th>
		</tr>
		</thead>
		
		<tbody>
<?php foreach ($this->rows as $k => $row): ?>
<?php
    $altrow = ( $altrow == 1 )? 0 : 1;
    $canAccess = $this->access->canEdit ||( $this->access->canEditOwn && ( $row->created_by == $this->user->get('id')) ) ;
    if(!$canAccess){
        continue;
    }
?>
		<tr class="row<?php echo $altrow ?>" >
			<td class="ref"><?php echo $row->ref ?></td>
			<td class="type"><?php echo $row->type ?></td>
			<td class="adress"><?php echo $row->adress ?></td>
			<td class="town"><?php echo $row->town ?></td>
			<td class="living_space number"><?php echo $this->formatNumber( floatval( $row->living_space), 2 ) . ' ' . $this->params->get('surface_measure') ?></td>
			<td class="land_space number"><?php echo $this->formatNumber( floatval( $row->land_space ), 2 ). ' ' . $this->params->get('surface_measure') ?></td>
			<td class="price number"><?php echo $this->formatPrice( floatval($row->price) , JText::_('Consult us') ) ?></td>
			<td class="published"><?php echo $this->row->published ?></td>
			<td class="edit">
			<a href="<?php echo JRoute::_( 'index.php?option=com_jea&view=properties&layout=form&id='.$row->id ) ?>" title="<?php echo JText::_('Edit') ?>" > 
				<?php echo JText::_('Edit') ?>
			</a>
			</td>
		</tr>
<?php endforeach ?>
		</tbody>
	
	</table>
	
	 <div>
	  <input type="hidden" id="filter_order" name="filter_order" value="<?php echo $this->order ?>" />
	  <input type="hidden" name="Itemid" value="<?php echo JRequest::getInt('Itemid', 0) ?>" />
	</div>
	
	<p class="pagenavigation"><?php echo $this->pagination->getPagesLinks() ?><br />
	<em><?php echo $this->pagination->getPagesCounter(); ?></em></p>

</form>

<?php endif ?>