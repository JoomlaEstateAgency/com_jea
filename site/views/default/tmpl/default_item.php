<?php // no direct access
defined('_JEXEC') or die('Restricted access');
JHTML::stylesheet('jea.css', 'components/com_jea/medias/css/');
?>

<p class="pagenavigation">
  <?php echo $this->getPrevNextItems( $this->row->id ) ?>
</p>



<?php if ( $this->params->get('show_print_icon') ): ?>
		<div class="jea_tools">
		<a href="javascript:window.print()" title="<?php echo JText::_('Print') ?>"><?php echo JHTML::_('image.site', 'printButton.png') ?></a><br />
		</div>
<?php endif ?>

<h1> <?php echo $this->page_title ?> </h1>
    
<?php if( !empty($this->secondaries_images)): ?>
<div class="snd_imgs" >
	  <img src="<?php echo $this->main_image['min_url'] ?>" alt="<?php echo $this->main_image['name'] ?>" 
	  title="<?php echo JText::_('Enlarge')?>" onclick="swapImage('<?php echo $this->main_image['preview_url'] ?>')" /> <br />
	<?php foreach($this->secondaries_images as $image) : ?>
      <img src="<?php echo $image['min_url'] ?>"  alt="<?php echo $image['name'] ?>" 
	  title="<?php echo JText::_('Enlarge')?>" onclick="swapImage('<?php echo $image['preview_url'] ?>')" /> <br />
    <?php endforeach ?>
</div>
<?php endif ?>

<?php if($img = is_file(JPATH_COMPONENT_SITE.DS.'upload'.DS.'properties'.DS.$this->row->id.DS.'min.jpg')) : ?>
	  <div> <img id="img_preview" src="<?php echo $this->main_image['preview_url'] ?>" alt="preview.jpg"  /> </div>
<?php endif ?>

 <div class="clr" >&nbsp;</div>
 
 <div class="jea_toolbar">
   <a href="#" title="<?php //echo _CMN_PDF ?>"><?php //echo $pdf_image ?> </a> 
   <a href="#" title="<?php //echo _CMN_PRINT ?>"><?php //echo $print_image  ?> </a>
  </div>
  
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
     <?php //TODO: Rajouter surface tout court (ex: garage) ?>
     
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

<?php if ( $this->params->get('show_contactform') ): ?>
    
<form action="<?php echo $this->getViewUrl ($this->row->id,'&task=sendmail' ) ?>" method="post" enctype="application/x-www-form-urlencoded">

	<fieldset><legend><?php echo JText::_('FORMCONTACTLEGEND') ?></legend>
		<p><label for="name"><?php echo JText::_('Name') ?> :</label><br />
		   <input type="text" name="name" id="name" value="<?php echo JRequest::getVar('name', '') ?>" size="40" />
		</p>
		
		<p><label for="email"><?php echo JText::_('Email') ?> :</label><br />
		   <input type="text" name="email" id="email" value="<?php echo JRequest::getVar('email', '') ?>" size="40" />
		</p>
		
		<p><label for="subject"><?php echo JText::_('Subject') ?> :</label><br />
		   <input type="text" name="subject" id="subject" value="Ref : <?php echo $this->escape( $this->row->ref ) ?>" size="40" />
		</p>
		
		<p><label for="e_message"><?php echo JText::_('Message') ?> :</label><br /> 
		   <textarea name="e_message" id="e_message" rows="10" cols="40"><?php echo JRequest::getVar('e_message', '') ?></textarea>
		</p>
		<p><input type="submit" value="<?php echo JText::_('Send') ?>" /></p>
	
	</fieldset>
</form>  
<?php endif  ?>

<p><a href="javascript:window.history.back()" class="jea_return_link" ><?php echo JText::_('Back')?></a></p>
