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

$script=<<<EOB
function changeOrdering( order, direction )
{
	var form = document.getElementById('jForm');
	form.filter_order.value = order;
	form.filter_order_Dir.value = direction;
	form.submit();
}
EOB;

$document=& JFactory::getDocument();
$document->addScriptDeclaration($script);

?>


<?php if ( $this->params->get('show_page_title', 0) && $this->params->get('page_title', '') ) : ?>
<h1><?php echo $this->params->get('page_title') ?></h1>
<?php endif ?>


<?php if( JRequest::getVar('task') == 'search') : ?>
<div class="search_parameters">
	<h2><?php echo JText::_('Search parameters') ?> :</h2>
	<?php echo $this->getSearchparameters() ?>
</div>
<?php endif ?>

<?php if( !empty($this->rows) ) : ?>

<form id="jForm" action="<?php echo $this->getViewUrl() ?>" method="post">

	<p class="pagenavigation"><?php echo $this->pagination->getPagesLinks() ?><br />
	<em><?php echo $this->pagination->getPagesCounter(); ?></em></p>
	
	<div class="clr" ></div>
	
	<div id="sort_options">
		<?php echo implode(' | ', $this->sort_links)  ?>
	</div>
	
	<p class="limitbox"><em><?php echo JText::_('Results per page') ?> : </em><?php echo $this->pagination->getLimitBox() ?></p>
	
	
	<div class="clr" ></div>
	
<?php foreach ($this->rows as $k => $row) :?>
	<dl class="jea_item" >
		<dt class="title" >
			<a href="<?php echo $this->getViewUrl ( $row->slug ) ?>" title="<?php echo JText::_('Show detail') ?>" > 
			<strong> 
			<?php if(empty($row->title)):?>
			<?php echo ucfirst( JText::sprintf('PROPERTY TYPE IN TOWN', $this->escape($row->type), $this->escape($row->town) ) ) ?>
			<?php else : echo $this->escape($row->title) ?>
			<?php endif ?>
			</strong> 
			( <?php echo JText::_('Ref' ) . ' : ' . $row->ref ?> )
			</a>
			<?php 
			echo "Date : $row->date_insert <br />";
			echo "hits : $row->hits <br />";
			?>
		</dt>
	
		<?php if ( is_file( JPATH_ROOT.DS.'images'.DS.'com_jea'.DS.'images'.DS.$row->id.DS.'min.jpg' ) ) : ?>
		<dt class="image">
		    <a href="<?php echo $this->getViewUrl ( $row->slug ) ?>" title="<?php echo JText::_('Detail') ?>">
		      <img src="<?php echo JURI::root().'images/com_jea/images/'.$row->id.'/min.jpg' ?>" alt="<?php echo JText::_('Detail') ?>" />
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
		<strong> <?php echo $this->formatPrice( floatval($row->price) , JText::_('Consult us') ) ?></strong>
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
		<a href="<?php echo $this->getViewUrl ( $row->slug ) ?>" title="<?php echo JText::_('Show detail') ?>"> 
		<?php echo JText::_('Detail') ?> </a>
		</dd>
	
		<dd class="clr"></dd>
	
	</dl>
<?php endforeach ?>
	
	<div class="clear">
	  <input type="hidden" id="filter_order" name="filter_order" value="<?php echo $this->order ?>" />
      <input type="hidden" id="filter_order_Dir" name="filter_order_Dir" value="<?php echo $this->order_dir ?>" />
	  <input type="hidden" name="Itemid" value="<?php echo JRequest::getInt('Itemid', 0) ?>" />
	</div>
	
	<p class="pagenavigation"><?php echo $this->pagination->getPagesLinks() ?><br />
	<em><?php echo $this->pagination->getPagesCounter(); ?></em></p>

</form>
<?php else : ?>
	<?php if( JRequest::getVar('task') == 'search') : ?>
		<p><strong><big><?php echo JText::_('No matches found') ?></big></strong></p>
		<p><a href="javascript:window.history.back()" class="jea_return_link" ><?php echo JText::_('Back')?></a></p>
	<?php endif ?>
<?php endif ?>