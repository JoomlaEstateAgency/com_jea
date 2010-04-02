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

$previousLabel = JText::_('Previous');
$nextLabel     = JText::_('Next');

$script=<<<EOB

// Override SqueezeBox.showContent()
SqueezeBox.showContent = function() {
	this.window.setStyle('height', 'auto');
	this.fx.content.start(1);
};

var addNavigation = function(content){

    var getButton = function(label, id, fromElement) {
        var button = null;
    	if(fromElement) {
    	    button = new Element('a', {'href': '#'});
            button.addEvent('click', function(e){
                new Event(e).stop();
        		SqueezeBox.fromElement(fromElement);
        	});
        } else {
            button = new Element('span');
        }
        button.setProperty('id', id);
        button.appendText(label);
        return button;
    };
	
	content.setStyle('background', '#000');
	
	var imgSrc = content.getElement('img').src;
	var previousElt = null;
	var nextElt = null;
	var imgLinks = $$('a.jea_modal');
	var imgTitle = null;
	var imgDescription = null;
	imgLinks.each(function(el, count) {
		if(el.href == imgSrc){
		    imgTitle       = el.getElement('img').getProperty('alt');
		    imgDescription = el.getElement('img').getProperty('title');
		    
			if(imgLinks[count-1]){
				previousElt = imgLinks[count-1];
			}
			if(imgLinks[count+1]){
				nextElt = imgLinks[count+1];
			}
		}
	});
	
	var navBlock = new Element('div', {'id': 'jea-squeezeBox-navblock'});
    var previousLink = getButton('< $previousLabel', 'jea-squeezeBox-prev', previousElt);
    var nextLink     = getButton('$nextLabel >', 'jea-squeezeBox-next', nextElt);
    
    if(imgTitle) {
    	var blockTitle = new Element('div', {'id': 'jea-squeezeBox-title'});
    	blockTitle.appendText(imgTitle);
    	content.adopt(blockTitle);
    }
    
    if(imgDescription) {
    	var blockDesc = new Element('div', {'id': 'jea-squeezeBox-description'});
    	blockDesc.appendText(imgDescription);
    	content.adopt(blockDesc);
    }
    
    navBlock.adopt([previousLink,nextLink]);
    content.adopt(navBlock);
}
EOB;

JHTML::_('behavior.modal', 'a.jea_modal', array('onUpdate' => '\addNavigation'));

$document=& JFactory::getDocument();
$document->addScriptDeclaration($script);
$document->addStyleDeclaration("
    
    #jea-squeezeBox-navblock {
    	margin-top: 10px;
    	text-align : center;
    }
    
    #jea-squeezeBox-title {
    	margin-top: 5px;
    	font-weight : bold;
    	color : #fff;
    	text-align : center;
    }
    
    #jea-squeezeBox-description {
    	color : #ddd;
    	font-size: 10px;
    	text-align : center;
    }
    
	#jea-squeezeBox-prev {
    	margin-right: 10px;
    }
    #jea-squeezeBox-next {
    	margin-left: 10px;
    }
	a#jea-squeezeBox-prev, a#jea-squeezeBox-next {
    	color : #fff;
    }
    span#jea-squeezeBox-prev, span#jea-squeezeBox-next {
    	color : #555;
    }
");

?>

<div class="clr" ></div>

<div id="jea-gallery-preview" >
<a class="jea_modal" href="<?php echo $this->main_image['url'] ?>" >
      <img src="<?php echo $this->main_image['preview_url'] ?>" 
      	   id="jea-preview-img"
           alt="<?php echo $this->main_image['title'] ?>" 
           title="<?php echo $this->main_image['description'] ?>" /></a>
</div>

<?php if( !empty($this->secondaries_images)): ?>
<div id="jea-gallery-scroll" >
	<?php foreach($this->secondaries_images as $image) : ?>
	  <a class="jea_modal" href="<?php echo $image['url'] ?>" >
      <img src="<?php echo $image['min_url'] ?>" 
           alt="<?php echo $image['title'] ?>" 
           title="<?php echo $image['description'] ?>" /></a><br />
    <?php endforeach ?>
</div>
<?php endif ?>

<div class="clr" ></div>
