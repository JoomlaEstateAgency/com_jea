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

$mainImage = array_shift($this->row->images);



$previousLabel = JText::_('JPREVIOUS');
$nextLabel     = JText::_('JNEXT');

$script=<<<EOB
	var previousLabel = '$previousLabel';
	var nextLabel = '$nextLabel';
EOB;


$this->document->addScriptDeclaration($script)
               ->addScript(JURI::root(true).'/media/com_jea/js/jea-squeezebox.js');

JHtml::_('behavior.modal', 'a.jea_modal', array('onOpen' => '\onOpenSqueezebox'));

$gallery_orientation = $this->params->get('gallery_orientation', 'vertical');
$img_width = $this->params->get('thumb_medium_width', 400);
$img_height = $this->params->get('thumb_medium_height', 400);
?>

<div class="clr" ></div>

<div id="jea-gallery-preview" class="<?php echo $gallery_orientation ?>">
<a class="jea_modal" href="<?php echo $mainImage->URL ?>" >
      <img src="<?php echo $mainImage->mediumURL ?>"
      	   id="jea-preview-img"
           alt="<?php echo $mainImage->title ?>"
           title="<?php echo $mainImage->description ?>" /></a>
</div>

<?php if( !empty($this->row->images)): ?>
<div id="jea-gallery-scroll"
     class="<?php echo $gallery_orientation ?>"
     style="<?php echo $gallery_orientation == 'horizontal' ? 'width:'.$img_width.'px' : 'max-height:'.$img_height.'px' ?>">
    <?php foreach($this->row->images as $image) : ?>
      <a class="jea_modal" href="<?php echo $image->URL?>" >
      <img src="<?php echo $image->minURL ?>"
           alt="<?php echo $image->title ?>"
           title="<?php echo $image->description  ?>" /></a><br />
    <?php endforeach ?>
</div>
<?php endif ?>

<div class="clr" ></div>
