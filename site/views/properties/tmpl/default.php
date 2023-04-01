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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

/**
 *
 * @var $this JeaViewProperties
 */

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');

$script = <<<EOB
function changeOrdering( order, direction )
{
	var form = document.getElementById('jForm');
	form.filter_order.value = order;
	form.filter_order_Dir.value = direction;
	form.submit();
}
EOB;

$this->document->addScriptDeclaration($script);

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirection = $this->escape($this->state->get('list.direction'));

?>

<div class="jea-properties<?php echo $this->escape($this->params->get('pageclass_sfx')) ?>">

    <?php if ($this->params->get('show_page_heading', 1)) : ?>
        <?php if ($this->params->get('page_heading')) : ?>
        <h1><?php echo $this->escape($this->params->get('page_heading')) ?></h1>
        <?php else: ?>
        <h1><?php echo $this->escape($this->params->get('page_title')) ?></h1>
        <?php endif ?>
    <?php endif ?>

    <?php if ($this->state->get('searchcontext') === true): ?>
      <div class="search_parameters">
        <h2><?php echo Text::_('COM_JEA_SEARCH_PARAMETERS_TITLE') ?> :</h2>
          <?php echo $this->loadTemplate('remind') ?>
      </div>
    <?php endif ?>

    <?php if (!empty($this->items)): ?>
      <form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()) ?>" id="jForm"
            method="post">

        <p class="sort-options">
            <?php echo implode(' | ', $this->sort_links) ?>
        </p>

        <p class="limitbox">
          <em><?php echo Text::_('COM_JEA_RESULTS_PER_PAGE') ?> : </em>
            <?php echo $this->pagination->getLimitBox() ?>
        </p>

        <div class="jea-items">
            <?php foreach ($this->items as $row): ?>
                <?php
                $row->slug = $row->alias ? ($row->id . ':' . $row->alias) : $row->id;
                $itemUrl = Route::_('index.php?option=com_jea&view=property&id=' . $row->slug . '&Itemid=' . $this->itemId);
                ?>
              <dl class="jea_item">
                <dt class="title">
                  <a href="<?php echo $itemUrl ?>"
                     title="<?php echo Text::_('COM_JEA_DETAIL') ?>"><strong>
                          <?php if (empty($row->title)): ?>
                              <?php echo ucfirst(Text::sprintf('COM_JEA_PROPERTY_TYPE_IN_TOWN', $this->escape($row->type), $this->escape($row->town))) ?>
                          <?php else : echo $this->escape($row->title) ?>
                          <?php endif ?></strong>
                    ( <?php echo Text::_('COM_JEA_REF') . ' : ' . $row->ref ?>)
                  </a>

                    <?php if ($this->params->get('show_creation_date', 0)): ?>
                      <span
                          class="date"><?php echo HTMLHelper::_('date', $row->created, Text::_('DATE_FORMAT_LC3')) ?></span>
                    <?php endif ?>
                </dt>

                  <?php if ($imgUrl = $this->getFirstImageUrl($row)): ?>
                    <dt class="image">
                      <a href="<?php echo $itemUrl ?>"
                         title="<?php echo Text::_('COM_JEA_DETAIL') ?>">
                        <img src="<?php echo $imgUrl ?>"
                             alt="<?php echo Text::_('COM_JEA_DETAIL') ?>"/>
                      </a>
                    </dt>
                  <?php endif ?>

                <dd>
                    <?php if ($row->slogan): ?>
                      <span class="slogan"><?php echo $this->escape($row->slogan) ?> </span>
                    <?php endif ?>

                    <?php echo $row->transaction_type == 'RENTING' ? Text::_('COM_JEA_FIELD_PRICE_RENT_LABEL') : Text::_('COM_JEA_FIELD_PRICE_LABEL') ?>
                  :
                  <strong> <?php echo HTMLHelper::_('utility.formatPrice', (float)$row->price, Text::_('COM_JEA_CONSULT_US')) ?> </strong>
                    <?php if ($row->transaction_type == 'RENTING' && (float)$row->price != 0.0) echo Text::_('COM_JEA_PRICE_PER_FREQUENCY_' . $row->rate_frequency) ?>

                    <?php if (!empty($row->living_space)): ?>
                      <br/><?php echo Text::_('COM_JEA_FIELD_LIVING_SPACE_LABEL') ?> :
                      <strong><?php echo HTMLHelper::_('utility.formatSurface', (float)$row->living_space, '-') ?></strong>
                    <?php endif ?>

                    <?php if (!empty($row->land_space)): ?>
                      <br/><?php echo Text::_('COM_JEA_FIELD_LAND_SPACE_LABEL') ?> :
                      <strong><?php echo HTMLHelper::_('utility.formatSurface', (float)$row->land_space, '-') ?></strong>
                    <?php endif ?>

                    <?php if (!empty($row->amenities)) : ?>
                      <br/> <strong><?php echo Text::_('COM_JEA_AMENITIES') ?> :</strong>
                        <?php echo HTMLHelper::_('amenities.bindList', $row->amenities) ?>
                    <?php endif ?>

                  <br/>
                  <a href="<?php echo $itemUrl ?>"
                     title="<?php echo Text::_('COM_JEA_DETAIL') ?>">
                      <?php echo Text::_('COM_JEA_DETAIL') ?></a>
                </dd>
              </dl>
            <?php endforeach ?>
        </div>

        <div>
          <input type="hidden" id="filter_order" name="filter_order"
                 value="<?php echo $listOrder ?>"/>
          <input type="hidden" id="filter_order_Dir" name="filter_order_Dir"
                 value="<?php echo $listDirection ?>"/>
        </div>

        <div class="pagination">
          <p class="counter"><?php echo $this->pagination->getPagesCounter() ?></p>
            <?php echo $this->pagination->getPagesLinks() ?>
        </div>
      </form>

    <?php elseif ($this->state->get('searchcontext') === true) : ?>

      <hr/>
      <h2><?php echo Text::_('COM_JEA_SEARCH_NO_MATCH_FOUND') ?></h2>

      <p>
        <a href="<?php echo Route::_('index.php?option=com_jea&view=properties&layout=search') ?>">
            <?php echo Text::_('COM_JEA_MODIFY_SEARCH') ?>
        </a>
      </p>

    <?php endif ?>

</div>
