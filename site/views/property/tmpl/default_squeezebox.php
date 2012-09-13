<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @version     $Id$
 * @package     Joomla.Site
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2012 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

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

JHTML::_('behavior.modal', 'a.jea_modal', array('onOpen' => '\onOpenSqueezebox'));
?>

<div class="clr" ></div>

<div id="jea-gallery-preview" >
<a class="jea_modal" href="<?php echo $mainImage->URL ?>" >
      <img src="<?php echo $mainImage->mediumURL ?>" 
      	   id="jea-preview-img"
           alt="<?php echo $mainImage->title ?>" 
           title="<?php echo $mainImage->description ?>" /></a>
</div>

<?php if( !empty($this->row->images)): ?>
<div id="jea-gallery-scroll" >
	<?php foreach($this->row->images as $image) : ?>
	  <a class="jea_modal" href="<?php echo $image->URL?>" >
      <img src="<?php echo $image->minURL ?>" 
           alt="<?php echo $image->title ?>" 
           title="<?php echo $image->description  ?>" /></a><br />
    <?php endforeach ?>
</div>
<?php endif ?>

<div class="clr" ></div>
