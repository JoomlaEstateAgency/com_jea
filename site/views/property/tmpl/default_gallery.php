<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Site
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2017 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

if (!is_array($this->row->images)) {
    return ;
}
JHtml::_('behavior.modal');

$mainImage = $this->row->images[0];

$script=<<<EOB
function updatePreviewInfos(){
    var imgTitle       = document.id('jea-preview-img').getProperty('alt');
    var imgDescription = document.id('jea-preview-img').getProperty('title');
    document.id('jea-preview-title').empty();
    document.id('jea-preview-description').empty();
    if (imgTitle) {
        document.id('jea-preview-title').set('text', imgTitle);
    }
    if(imgDescription) {
        document.id('jea-preview-description').set('text', imgDescription);
    }
};

window.addEvent('domready', function() {
    $$('a.jea-thumbnails').each(function(el) {
        el.addEvent('click', function(e) {
            new Event(e).stop();
            if(document.id('jea-preview-img')){
                document.id('jea-preview-img').set({
                    'alt'  : this.getElement('img').getProperty('alt'),
                    'title': this.getElement('img').getProperty('title'),
                    'src'  :  this.getProperty('rel')
                });
                document.id('jea-preview-img').getParent().set('href', this.getProperty('href'));
                updatePreviewInfos();
            }
        });
    });
});
EOB;

JHtml::_('behavior.framework');
$this->document->addScriptDeclaration($script);
$gallery_orientation = $this->params->get('gallery_orientation', 'vertical');
$img_width = $this->params->get('thumb_medium_width', 400);
$img_height = $this->params->get('thumb_medium_height', 400);
?>

<div class="clr" ></div>

<div id="jea-gallery-preview" class="<?php echo $gallery_orientation ?>">
<a href="<?php echo $mainImage->URL ?>" class="modal">
      <img src="<?php echo $mainImage->mediumURL ?>"
           id="jea-preview-img"
           alt="<?php echo $mainImage->title ?>"
           title="<?php echo $mainImage->description ?>" /></a>
  <div id="jea-preview-title"><?php echo $mainImage->title ?></div>
  <div id="jea-preview-description"><?php echo $mainImage->description ?></div>
</div>

<?php if( !empty($this->row->images)): ?>
<div id="jea-gallery-scroll"
     class="<?php echo $gallery_orientation ?>"
     style="<?php echo $gallery_orientation == 'horizontal' ? 'width:'.$img_width.'px' : 'max-height:'.$img_height.'px' ?>">
     <?php foreach($this->row->images as $image) : ?>
     <a class="jea-thumbnails" rel="<?php echo $image->mediumURL?>" href="<?php echo $image->URL?>" >
     <img src="<?php echo $image->minURL ?>"
           alt="<?php echo $image->title ?>"
           title="<?php echo $image->description  ?>" /></a><br />
    <?php endforeach ?>
</div>
<?php endif ?>

<div class="clr" ></div>
