<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2020 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/* @var $displayData array */

$uploadNumber = (int)$displayData['uploadNumber'];
$images = $displayData['images'];
$name = $displayData['name'];

HTMLHelper::script('media/com_jea/js/admin/gallery.js');
?>

<p>
    <?php for ($i = 0; $i < $uploadNumber; $i++): ?>
      <input type="file" name="newimages[]" value="" size="30" class="fltnone"/>
      <br/>
    <?php endfor ?>
</p>

<?php if (!extension_loaded('gd')): // Alert & return if GD library for PHP is not enabled  ?>
  <div class="alert alert-warning">
    <strong>WARNING: </strong>The <a href="http://php.net/manual/en/book.image.php" target="_blank">
      GD library for PHP</a> was not found. Ensure to install it.
  </div>
    <?php return ?>
<?php endif ?>

<ul class="gallery">

    <?php foreach ($images as $k => $image): ?>

      <li class="item-<?php echo $k ?>">
          <?php
          if (isset($image->error)) {
              echo $image->error;
              continue;
          }
          ?>

        <a href="<?php echo $image->url ?>" title="Zoom" class="imgLink modal"
           rel="{handler: 'image'}">
          <img src="<?php echo $image->thumbUrl ?>" alt="<?php echo $image->name ?>"/>
        </a>
        <div class="imgInfos">
            <?php echo $image->name ?><br/>
            <?php echo Text::_('COM_JEA_WIDTH') ?> : <?php echo $image->width ?> px<br/>
            <?php echo Text::_('COM_JEA_HEIGHT') ?> : <?php echo $image->height ?> px<br/>
        </div>

        <div class="imgTools">
          <a class="img-move-up" title="<?php echo Text::_('JLIB_HTML_MOVE_UP') ?>">
              <?php echo HTMLHelper::image('media/com_jea/images/sort_asc.png', "Move up") ?>
          </a>
          <a class="img-move-down" title="<?php echo Text::_('JLIB_HTML_MOVE_DOWN') ?>">
              <?php echo HTMLHelper::image('media/com_jea/images/sort_desc.png', "Move down") ?>
          </a>
          <a class="delete-img" title="<?php echo Text::_('JACTION_DELETE') ?>">
              <?php echo HTMLHelper::image('media/com_jea/images/media_trash.png', "Delete") ?>
          </a>
        </div>

        <div class="clearfix"></div>

        <div class="control-group">
          <div class="control-label">
            <label
                for="<?php echo $name . $k ?>title"> <?php echo Text::_('JGLOBAL_TITLE') ?></label>
          </div>
          <div class="controls">
            <input id="<?php echo $name . $k ?>title"
                   type="text"
                   name="<?php echo $name ?>[<?php echo $k ?>][title]"
                   value="<?php echo $image->title ?>"
                   size="20"
            />
          </div>
        </div>

        <div class="control-group">
          <div class="control-label">
            <label
                for="<?php echo $name . $k ?>desc"><?php echo Text::_('JGLOBAL_DESCRIPTION') ?></label>
          </div>
          <div class="controls">
            <input id="<?php echo $name . $k ?>desc"
                   type="text"
                   name="<?php echo $name ?>[<?php echo $k ?>][description]"
                   value="<?php echo $image->description ?>"
                   size="40"
            />
            <input type="hidden" name="<?php echo $name ?>[<?php echo $k ?>][name]"
                   value="<?php echo $image->name ?>"/>
          </div>
        </div>
      </li>

    <?php endforeach ?>
</ul>
