<?php // no direct access
defined('_JEXEC') or die('Restricted access');
if(!$this->row->id){
    echo JText::_('This property doesn\'t exists anymore');
    return;
}

require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'library'.DS.'JSON.php';
$serviceJson = new Services_JSON();

JHTML::stylesheet('jea.css', 'media/com_jea/css/');

$secondaries_images = array();

if(!empty($this->main_image['min_url'])) {
    $secondaries_images[] = $this->main_image ;
}

foreach($this->secondaries_images as $image){
    $secondaries_images[] = $image;
}

$js_secondaries_images = $serviceJson->encode($secondaries_images);

$script=<<<EOB

var secondaries_images = $js_secondaries_images;

window.addEvent('domready', function() {
	
	$('snd_imgs').getElements('img').each(function(el){
		el.addEvent('click', function(){
			secondaries_images.each(function(item){
    			if (el.src == item.min_url){
    				$('img_preview').setProperty('src', item.preview_url);
    				$('image_title').empty();
    				$('image_description').empty();
    				$('image_title').appendText(item.title);
    				$('image_description').appendText(item.description);
    			}
			});
		});
	});
});


EOB;

JHTML::_('behavior.mootools');
$document=& JFactory::getDocument();
$document->addScriptDeclaration($script);
?>

<p class="pagenavigation">
  <?php echo $this->getPrevNextItems( $this->row->id ) ?>
</p>



<?php if ( $this->params->get('show_print_icon') || $this->params->get('show_pdf_icon') ): ?>
	<div class="jea_tools">
	<?php if ( $this->params->get('show_pdf_icon') ): ?>
    <a href="<?php echo JRoute::_('&format=pdf') ?>" title="PDF"><?php echo JHTML::_('image.site', 'pdf_button.png') ?></a>     
	<?php endif ?>
	<?php if ( $this->params->get('show_print_icon') ): ?>
	<a href="javascript:window.print()" title="<?php echo JText::_('Print') ?>"><?php echo JHTML::_('image.site', 'printButton.png') ?></a>
    <?php endif ?>
	</div>
<?php endif ?>

<h1> <?php echo $this->page_title ?> </h1>
    
<?php if( !empty($secondaries_images)): ?>
<div class="snd_imgs" id="snd_imgs" >
	<?php foreach($secondaries_images as $image) : ?>
      <img src="<?php echo $image['min_url'] ?>"  
           alt="<?php echo $image['name'] ?>" 
	       title="<?php echo $image['title'] ?>"  /> <br />
    <?php endforeach ?>
</div>
<?php endif ?>

<?php if($img = is_file(JPATH_ROOT.DS.'images'.DS.'com_jea'.DS.'images'.DS.$this->row->id.DS.'min.jpg')) : ?>
	  <div> 
	  <img id="img_preview" src="<?php echo $this->main_image['preview_url'] ?>" alt="preview.jpg"  /> 
	  <div id="image_title" class="jea_image_title"><?php echo $this->main_image['title'] ?></div>
	  <div id="image_description" class="jea_image_desc" ><?php echo $this->main_image['description'] ?></div>
	  </div>
<?php endif ?>

 <div class="clr" >&nbsp;</div>
  
 <h2 ><?php echo JText::_('Ref')?> : <?php echo $this->escape($this->row->ref) ?></h2>
 
 <div class="clr" >&nbsp;</div>
  	    
   <div class="item_second_column">
      <h3><?php echo JText::_('Adress') ?> :</h3>
      <strong>
      <?php if($this->row->adress) echo $this->escape( $this->row->adress ).", <br /> \n" ?>
      <?php if ($this->row->zip_code) echo $this->escape( $this->row->zip_code ) ?> 
      <?php if ($this->row->town) echo strtoupper( $this->escape($this->row->town) )."<br /> \n" ?> 
      </strong> 
      <?php if ($this->row->area) 
              echo JText::_('Area') . ' : <strong>'
				   .$this->escape( $this->row->area ). "</strong>\n" ?>
      
     <?php if ( $this->row->advantages ) : ?>
     <h3><?php echo JText::_('Advantages')?></h3>
     <?php echo $this->getAdvantages( $this->row->advantages , 'ul' ) ?>
     <?php endif  ?>
   </div>
    

   	<?php if (intval($this->row->availability)): ?>
   	<p><em><?php echo JText::_('Availability date') ?> : <?php echo $this->row->availability ?></em> </p>
   	<?php endif  ?>
   	
   	
      
     <table>
      
      <tr>
   
         <td><?php echo $this->row->is_renting ?  JText::_('Renting price') : JText::_('Selling price') ?></td>

         <td> : <strong><?php echo $this->formatPrice( floatval($this->row->price) , JText::_('Consult us') ) ?></strong></td>
      </tr>
      
   <?php if ( $this->row->charges ): ?> 
   <tr>
     <td><?php echo JText::_('Charges') ?></td>
     <td> : <strong><?php echo $this->formatPrice( floatval($this->row->charges), JText::_('Consult us') ) ?></strong></td>
   </tr>
   <?php endif  ?>
   
   <?php if ($this->row->fees): ?> 
   <tr>
     <td><?php echo JText::_('Fees') ?></td>
     <td> : <strong><?php echo $this->formatPrice( floatval($this->row->fees), JText::_('Consult us') ) ?></strong></td>
   </tr>
      <?php endif  ?>
      
  </table>
  
  <h3><?php echo JText::_('Description') ?> :</h3>
      <?php if ($this->row->condition): ?>
     <p><strong><?php echo ucfirst($this->escape($this->row->condition)) ?></strong></p>
      <?php endif  ?>
      
     <p>
		<?php 
		if ($this->row->living_space) {
		    echo  JText::_( 'Living space' ) . ' : <strong>' . $this->row->living_space . ' ' 
		    	  . $this->params->get( 'surface_measure' ) . '</strong>' .PHP_EOL ;
		}?>
		<br />

		<?php
		if ($this->row->land_space) {
		    echo  JText::_( 'Land space' ) . ' : <strong>' . $this->row->land_space  .' '
		          . $this->params->get('surface_measure'). '</strong>' .PHP_EOL ;
		}?>
        <br />
        
        <?php if ( $this->row->rooms ): ?>
        <?php echo JText::_('Number of rooms') ?> : <strong><?php echo $this->row->rooms ?></strong> <br />
        <?php endif  ?>
         
        <?php if ( $this->row->floor ): ?>
        <?php echo JText::_('Number of floors') ?> : <strong><?php echo $this->row->floor ?></strong> <br />
        <?php endif  ?>
         
        <?php if ( $this->row->bathrooms ): ?>
        <?php echo JText::_('Number of bathrooms') ?> : <strong><?php echo $this->row->bathrooms ?></strong> <br />
        <?php endif  ?>
        
        <?php if ($this->row->toilets): ?>
        <?php echo JText::_('Number of toilets') ?> : <strong><?php echo $this->row->toilets ?></strong>
        <?php endif  ?>
        
    </p>

    <p>
     <?php if ( $this->row->hot_water_type ): ?>
     <?php echo JText::_('Hot water type') ?> : <strong><?php echo ucfirst($this->escape( $this->row->hot_water )) ?></strong><br />
     <?php endif  ?>
     
     <?php if ( $this->row->heating_type ): ?>
     <?php echo JText::_('Heating type') ?> : <strong><?php echo ucfirst($this->escape( $this->row->heating )) ?></strong>
     <?php endif  ?>
     </p>

     
 <div class="clr" >&nbsp;</div>
          
 <div class="item_description" > 
 <?php echo $this->row->description ?> 
 </div>

<?php if ( $this->params->get('show_googlemap') ): $this->activateGoogleMap($this->row, 'map_canvas') ?>
<h3><?php echo JText::_('Property geolocalization') ?> :</h3>
<div id="map_canvas" style="width: 500px; height: 300px"></div>
<?php endif  ?>

<?php if ( $this->params->get('show_contactform') ): ?>
    
<form action="<?php echo $this->getViewUrl ($this->row->id,'&task=sendmail' ) ?>" method="post" enctype="application/x-www-form-urlencoded">

	<fieldset><legend><?php echo JText::_('FORMCONTACTLEGEND') ?></legend>
		<p><label for="name"><?php echo JText::_('Name') ?> :</label><br />
		   <input type="text" name="name" id="name" value="<?php echo $this->escape(JRequest::getVar('name', '')) ?>" size="40" />
		</p>
		
		<p><label for="email"><?php echo JText::_('Email') ?> :</label><br />
		   <input type="text" name="email" id="email" value="<?php echo $this->escape(JRequest::getVar('email', '')) ?>" size="40" />
		</p>
		
		<p><label for="subject"><?php echo JText::_('Subject') ?> :</label><br />
		   <input type="text" name="subject" id="subject" value="Ref : <?php echo $this->escape( $this->row->ref ) ?>" size="40" />
		</p>
		
		<p><label for="e_message"><?php echo JText::_('Message') ?> :</label><br /> 
		   <textarea name="e_message" id="e_message" rows="10" cols="40"><?php echo $this->escape(JRequest::getVar('e_message', '')) ?></textarea>
		</p>
		<p>
		<input type="hidden" name="created_by" value="<?php echo $this->row->created_by ?>" />
		<input type="submit" value="<?php echo JText::_('Send') ?>" />
		
		</p>
	
	</fieldset>
</form>  
<?php endif  ?>

<p><a href="javascript:window.history.back()" class="jea_return_link" ><?php echo JText::_('Back')?></a></p>
