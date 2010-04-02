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

$this->initGallery($this->row->id);
array_unshift($this->secondaries_images, $this->main_image);

$script=<<<EOB
function updatePreviewInfos(){
	var imgTitle       = $('jea-preview-img').getProperty('alt');
	var imgDescription = $('jea-preview-img').getProperty('title');
	$('jea-preview-title').empty();
	$('jea-preview-description').empty();
	if(imgTitle) {
		$('jea-preview-title').appendText(imgTitle);
    }
    if(imgDescription) {
		$('jea-preview-description').appendText(imgDescription);
    }
};

window.addEvent('domready', function() {
	$$('a.jea-thumbnails').each(function(el) {
		el.addEvent('click', function(e) {
    		new Event(e).stop();

    		if($('jea-preview-img')){
    			$('jea-preview-img').setProperties({
    				'alt'  : this.getElement('img').getProperty('alt'),
    				'title': this.getElement('img').getProperty('title'),
    				'src' : this.getProperty('href')
    			});
				updatePreviewInfos();
			}
		});
	});
});
EOB;

JHTML::_('behavior.mootools');
$document=& JFactory::getDocument();
$document->addScriptDeclaration($script);

?>

<div class="clr" ></div>

<?php if(!empty($this->main_image['preview_url'])) : ?>
<div id="jea-gallery-preview" >
	<img src="<?php echo $this->main_image['preview_url'] ?>" 
      	 id="jea-preview-img"
         alt="<?php echo $this->main_image['title'] ?>" 
         title="<?php echo $this->main_image['description'] ?>" />
    <div id="jea-preview-title"><?php echo $this->main_image['title'] ?></div>
	<div id="jea-preview-description"><?php echo $this->main_image['description'] ?></div>
</div>
<?php endif ?>

<?php if( !empty($this->secondaries_images)): ?>
<div id="jea-gallery-scroll" >
	<?php foreach($this->secondaries_images as $image) : ?>
	  <a class="jea-thumbnails" href="<?php echo $image['preview_url'] ?>" >
      <img src="<?php echo $image['min_url'] ?>" 
           alt="<?php echo $image['title'] ?>" 
           title="<?php echo $image['description'] ?>" /></a><br />
    <?php endforeach ?>
</div>
<?php endif ?>

<div class="clr" ></div>
