<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Site
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2020 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;

/**
 *
 * @var $this JeaViewProperty
 */

if (!is_array($this->row->images)) {
    return;
}

HTMLHelper::stylesheet('com_jea/magnific-popup.css', array('relative' => true));
HTMLHelper::_('jquery.framework');
HTMLHelper::script('com_jea/jquery.magnific-popup.min.js', array('relative' => true));

$mainImage = $this->row->images[0];

$script = <<<JS
jQuery(function($) {
	$('.jea-thumbnails').on('click', function(e) {
		e.preventDefault();
		$('#jea-preview-img').attr('src', $(this).attr('rel'));
		$('#jea-preview-img').parent().attr('href', $(this).attr('href'));
		$('#jea-preview-title').empty().text($(this).find('img').attr('alt'));
		$('#jea-preview-description').empty().text($(this).find('img').attr('title'));

	});

	$('.modal').magnificPopup({
		type: 'image'
	});

	if ($('#jea-gallery-scroll').hasClass('vertical') &&  $(window).width() > 1200) {
		$('#jea-preview-img').on('load', function() {
			$('#jea-gallery-scroll').css('height', $(this).height());
		});
	}
});
JS;

$this->document->addScriptDeclaration($script);
$gallery_orientation = $this->params->get('gallery_orientation', 'vertical');

?>

<div id="jea-gallery" class="<?php echo $gallery_orientation ?>">

  <div id="jea-gallery-preview" class="<?php echo $gallery_orientation ?>">
    <a href="<?php echo $mainImage->URL ?>" class="modal">
      <img src="<?php echo $mainImage->mediumURL ?>" id="jea-preview-img"
           alt="<?php echo $mainImage->title ?>" title="<?php echo $mainImage->description ?>"/>
    </a>
    <div id="jea-preview-title"><?php echo $mainImage->title ?></div>
    <div id="jea-preview-description"><?php echo $mainImage->description ?></div>
  </div>

    <?php if (!empty($this->row->images)): ?>
      <div id="jea-gallery-scroll" class="<?php echo $gallery_orientation ?>">
          <?php foreach ($this->row->images as $image) : ?>
            <a class="jea-thumbnails" rel="<?php echo $image->mediumURL ?>"
               href="<?php echo $image->URL ?>">
              <img src="<?php echo $image->minURL ?>" alt="<?php echo $image->title ?>"
                   title="<?php echo $image->description ?>"/></a>
          <?php endforeach ?>
      </div>
    <?php endif ?>

</div>
