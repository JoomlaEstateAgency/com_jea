<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Site
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2020 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Captcha\Captcha;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');

/**
 * Property item view.
 *
 * @package     Joomla.Site
 * @subpackage  com_jea
 *
 * @since       2.0
 */
class JeaViewProperty extends HtmlView
{
	/**
	 * The component parameters
	 *
	 * @var Joomla\Registry\Registry
	 */
	protected $params;

	/**
	 * The model state
	 *
	 * @var JObject
	 */
	protected $state;

	/**
	 * The database record
	 *
	 * @var JObject|boolean
	 */
	protected $row;

	/**
	 * The page title
	 *
	 * @var string
	 */
	protected $page_title = '';

	/**
	 * The current menu item id
	 *
	 * @var integer
	 */
	protected $itemId = 0;

	/**
	 * Overrides parent method.
	 *
	 * @param   string $tpl The name of the template file to parse.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 *
	 * @see     JViewLegacy::display()
	 */
	public function display($tpl = null)
	{
		HTMLHelper::stylesheet('com_jea/jea.css', array('relative' => true));

		$this->state = $this->get('State');
		$item = $this->get('Item');
		$this->params = $this->state->params;
		$this->itemId = Factory::getApplication()->input->getInt('Itemid', 0);

		if (!$item)
		{
			throw new RuntimeException(Text::_('COM_JEA_PROPERTY_NOT_FOUND'));
		}

		$this->row = $item;

		// Increment the hit counter of the property
		$this->getModel()->hit();

		if (empty($item->title))
		{
			$pageTitle = ucfirst(Text::sprintf('COM_JEA_PROPERTY_TYPE_IN_TOWN', $this->escape($item->type), $this->escape($item->town)));
		}
		else
		{
			$pageTitle = $this->escape($item->title);
		}

		$this->page_title = $pageTitle;

		$app = Factory::getApplication();
		$pathway = $app->getPathway();
		$pathway->addItem($pageTitle);

		$document = Factory::getDocument();
		$document->setTitle($pageTitle);

		parent::display($tpl);
	}

	/**
	 * Get the previous and next links relative to the property
	 *
	 * @param   string $previousPrefix  Previous prefix
	 * @param   string $nextPrefix      Next prefix
	 *
	 * @return  string
	 */
	protected function getPrevNextNavigation($previousPrefix = '&lt;&lt; ', $nextPrefix = ' &gt;&gt;')
	{
		$res = $this->get('previousAndNext');
		$html = '';
		$previous = $previousPrefix . Text::_('JPREVIOUS');
		$next = Text::_('JNEXT') . $nextPrefix;

		if ($res['prev'])
		{
			$html .= '<a class="previous" href="' . $this->buildPropertyLink($res['prev']) . '">' . $previous . '</a>';
		}
		else
		{
			$html .= '<span class="previous">' . $previous . '</span>';
		}

		if ($res['next'])
		{
			$html .= '<a class="next" href="' . $this->buildPropertyLink($res['next']) . '">' . $next . '</a>';
		}
		else
		{
			$html .= '<span class="next">' . $next . '</span>';
		}

		return $html;
	}

	/**
	 * Build the property link
	 *
	 * @param   object $item The property row
	 *
	 * @return  string
	 */
	protected function buildPropertyLink(&$item)
	{
		$slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;

		return Route::_('index.php?option=com_jea&view=property&id=' . $slug . '&Itemid=' . $this->itemId);
	}

	/**
	 * Display captcha
	 *
	 * @return string the HTML code to dispay captcha
	 */
	protected function displayCaptcha()
	{
		$plugin = Factory::getApplication()->get('captcha');

		if ($plugin === 0 || $plugin === '0' || $plugin === '' || $plugin === null)
		{
			return 'Error: No captcha found. Please configure a default captcha in your Joomla configuration';
		}

		$captcha = Captcha::getInstance($plugin, array('namespace' => 'contact'));

		if ($captcha instanceof Captcha)
		{
			return $captcha->display('captcha', 'jea-captcha', 'required');
		}

		return '';
	}
}
