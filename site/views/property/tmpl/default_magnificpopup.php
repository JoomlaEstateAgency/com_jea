<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Site
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2019 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 *
 * @var $this JeaViewProperty
 */

if (!is_array($this->row->images))
{
	return;
}

$mainImage = $this->row->images[0];

$previousLabel = JText::_('JPREVIOUS');
$nextLabel = JText::_('JNEXT');

JHtml::stylesheet('com_jea/magnific-popup.css', array('relative' => true));

JHtml::_('jquery.framework');
JHtml::script('com_jea/jquery.magnific-popup.min.js', array('relative' => true));

$script = <<<EOB

jQuery(function($) {

	var previousLabel = '$previousLabel';
	var nextLabel = '$nextLabel';

	$('#jea-gallery-preview a').on('click', function(e) {
		e.preventDefault();
		$('.popup-gallery a:first').trigger('click');
	});

	$('.popup-gallery').magnificPopup({
		delegate: 'a',
		type: 'image',
		tLoading: 'Loading image #%curr%...',
		mainClass: 'mfp-img-mobile',
		gallery: {
			enabled: true,
			navigateByImgClick: true,
			preload: [0,1] // Will preload 0 - before current, and 1 after the current image
		},
		image: {
			tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
			titleSrc: function(item) {
				var title = item.el.attr('title');
				var description = item.img.attr('alt');
				return description ? title + ' / ' + description : title;
			}
		}
	});
});
EOB;

$this->document->addScriptDeclaration($script);

$gallery_orientation = $this->params->get('gallery_orientation', 'vertical');
$img_width = $this->params->get('thumb_medium_width', 400);
$img_height = $this->params->get('thumb_medium_height', 400);
?>

<div class="clr"></div>

<div id="jea-gallery-preview" class="<?php echo $gallery_orientation ?>">
	<a href="<?php echo $mainImage->URL ?>" title="<?php echo $mainImage->title ?>"><img src="<?php echo $mainImage->mediumURL ?>" id="jea-preview-img"
		alt="<?php echo $mainImage->description ?>" /></a>
</div>

<?php if( !empty($this->row->images)): ?>
<div id="jea-gallery-scroll" class="popup-gallery <?php echo $gallery_orientation ?>"
	style="<?php echo $gallery_orientation == 'horizontal' ? 'width:'.$img_width.'px' : 'max-height:'.$img_height.'px' ?>">
	<?php foreach($this->row->images as $image) : ?>
	<a href="<?php echo $image->URL?>" title="<?php echo $image->title ?>">
	<img src="<?php echo $image->minURL ?>" alt="<?php echo $image->description ?>" /></a><br />
	<?php endforeach ?>
</div>
<?php endif ?>

<div class="clr"></div>
