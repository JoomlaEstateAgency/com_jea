<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<?php
$rowsCount = count( $this->rows ) ;

//$prix = ($this->cat=='locations')? 'loyer' : 'prix' ; 

//dump($this->params)	;

					

?>


<?php if ( $this->params->get('show_page_title', 0) && $this->params->get('page_title', '') ) : ?>
<h1><?php echo $this->params->get('page_title') ?></h1>
<?php endif ?>

<?php if( !empty($this->rows) ) : ?>

<form id="jForm" action="<?php echo $this->getViewUrl() ?>" method="post">

	<p class="pagenavigation"><?php echo $this->pagination->getPagesLinks() ?><br />
	<em><?php echo $this->pagination->getPagesCounter(); ?></em></p>
	
	<div class="clr" ></div>
	<p><a href="javascript:changeOrdering('price')"><?php echo JText::_('Sort by price') ?></a></p>
	
	<p class="limitbox"><em><?php echo JText::_('Results per page') ?> : </em><?php echo $this->pagination->getLimitBox() ?></p>
	
	
	<div class="clr" ></div>
	
<?php foreach ($this->rows as $k => $row) :?>
	<dl class="jea_item" >
		<dt class="title" >
			<a href="<?php echo $this->getViewUrl ( $row->id ) ?>" title="<?php echo JText::_('Show detail') ?>" > 
			<strong> 
			<?php echo ucfirst( JText::sprintf('PROPERTY TYPE IN TOWN', $this->escape($row->type), $this->escape($row->town) ) ) ?>
			</strong> 
			( <?php echo JText::_('Ref' ) . ' : ' . $row->ref ?> )
			</a>
		</dt>
	
		<?php if ( $img = is_file( JPATH_COMPONENT.'/upload/properties/'.$row->id.'/min.jpg' ) ) : ?>
		<dt class="image">
		    <a href="<?php echo $this->getViewUrl ( $row->id ) ?>" title="<?php echo JText::_('Detail') ?>">
		      <img src="<?php echo JURI::root().'components/com_jea/upload/properties/'.$row->id.'/min.jpg' ?>" alt="<?php echo JText::_('Detail') ?>" />
			</a>
		</dt>
		<?php endif ?>
	
		<dd>
		<?php if ($row->slogan): ?> 
		<span class="slogan">
		    
		    <?php if( $img = is_file(JPATH_COMPONENT.'/upload/slogans/'.$row->slogan_id.'.png') ) : ?>
			<img src="<?php echo JURI::root().'components/com_jea/upload/slogans/'.$row->slogan_id.'.png' ?>" alt="<?php echo $this->escape($row->slogan)  ?>" />
			<?php else : ?>
			<strong><?php echo $this->escape($row->slogan) ?></strong>
			<?php endif ?>
			
		</span>
		<?php endif ?>
			
		<?php echo $this->cat == 'renting' ? JText::_('Renting price') :  JText::_('Selling price') ?> : 
		<strong> <?php echo $this->formatPrice( $row->price , JText::_('Consult us') ) ?></strong>
		<br />
		
		<?php 
		if ($row->living_space) {
		    echo  JText::_('Living space') . ' : <strong>' . $row->living_space . ' ' 
		    	  . $this->params->get('surface_measure') . '</strong>' .PHP_EOL ;
		}?>
		<br />

		<?php
		if ($row->land_space) {
		    echo  JText::_('Land space') . ' : <strong>' . $row->land_space  .' '
		          . $this->params->get('surface_measure'). '</strong>' .PHP_EOL ;
		}		
		
		?>
		
		<?php if ( $row->advantages ) : ?>
		    <br /><strong><?php echo JText::_('Advantages') ?> : </strong>
		    <?php echo $this->getAdvantages( $row->advantages )?>
		<?php endif ?>
		
		<br />
		<a href="<?php echo $this->getViewUrl ( $row->id ) ?>" title="<?php echo JText::_('Show detail') ?>"> 
		<?php echo JText::_('Detail') ?> </a>
		</dd>
	
		<dd class="clr"></dd>
	
	</dl>
<?php endforeach ?>
	
	<div class="clear">&nbsp;<input type="hidden" id="ordering" name="ordering" value="ordering" /></div>
	
	<p class="pagenavigation"><?php echo $this->pagination->getPagesLinks() ?><br />
	<em><?php echo $this->pagination->getPagesCounter(); ?></em></p>


</form>
<?php endif; ?>