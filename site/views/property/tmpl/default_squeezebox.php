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
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

/**
 *
 * @var $this JeaViewProperty
 */

if (!is_array($this->row->images)) {
    return;
}

$mainImage = array_shift($this->row->images);

$previousLabel = Text::_('JPREVIOUS');
$nextLabel = Text::_('JNEXT');

$script = <<<EOB
var previousLabel = '$previousLabel';
var nextLabel = '$nextLabel';

window.addEvent('domready', function(){
	if (document.id('jea-gallery-scroll').hasClass('vertical')) {
		var winSize = window.getSize();

		if (winSize.x > 1200) {
			document.id('jea-preview-img').addEvent('load', function() {
				var imgSize = this.getSize();
				document.id('jea-gallery-scroll').setStyle('height', imgSize.y);
			});
		}
	}
});
EOB;

$this->document->addScriptDeclaration($script);

HTMLHelper::script('com_jea/jea-squeezebox.js', array('relative' => true));

HTMLHelper::_('behavior.modal', 'a.jea_modal', array('onOpen' => '\onOpenSqueezebox'));

$gallery_orientation = $this->params->get('gallery_orientation', 'vertical');
?>

<div id="jea-gallery" class="<?php echo $gallery_orientation ?>">

  <div id="jea-gallery-preview" class="<?php echo $gallery_orientation ?>">
    <a class="jea_modal" href="<?php echo $mainImage->URL ?>"><img
          src="<?php echo $mainImage->mediumURL ?>" id="jea-preview-img"
          alt="<?php echo $mainImage->title ?>" title="<?php echo $mainImage->description ?>"/></a>
  </div>

    <?php if (!empty($this->row->images)): ?>
      <div id="jea-gallery-scroll" class="<?php echo $gallery_orientation ?>">
          <?php foreach ($this->row->images as $image) : ?>
            <a class="jea_modal" href="<?php echo $image->URL ?>"><img
                  src="<?php echo $image->minURL ?>" alt="<?php echo $image->title ?>"
                  title="<?php echo $image->description ?>"/></a>
          <?php endforeach ?>
      </div>
    <?php endif ?>

</div>
