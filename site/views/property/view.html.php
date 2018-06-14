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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

/**
 * Property item view.
 *
 * @package     Joomla.Site
 * @subpackage  com_jea
 *
 * @since       2.0
 */
class JeaViewProperty extends JViewLegacy
{
	/**
	 * Overrides parent method.
	 *
	 * @param   string  $tpl  The name of the template file to parse.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 *
	 * @see     JViewLegacy::display()
	 */
	public function display ($tpl = null)
	{
		$this->state = $this->get('State');
		$item = $this->get('Item');
		$this->params = $this->state->params;

		if (!$item)
		{
			throw new RuntimeException(JText::_('COM_JEA_PROPERTY_NOT_FOUND'));
		}

		$this->row = $item;

		// Increment the hit counter of the property
		$this->getModel()->hit();

		if (empty($item->title))
		{
			$pageTitle = ucfirst(JText::sprintf('COM_JEA_PROPERTY_TYPE_IN_TOWN', $this->escape($item->type), $this->escape($item->town)));
		}
		else
		{
			$pageTitle = $this->escape($item->title);
		}

		$this->page_title = $pageTitle;

		$app = JFactory::getApplication();
		$pathway = $app->getPathway();
		$pathway->addItem($pageTitle);

		$document = JFactory::getDocument();
		$document->setTitle($pageTitle);

		parent::display($tpl);
	}

	/**
	 * Get the previous and next links relative to the property
	 *
	 * @param   string  $previousPrefix  Previous prefix
	 * @param   string  $nextPrefix      Next prefix
	 *
	 * @return  string
	 */
	protected function getPrevNextNavigation ($previousPrefix = '&lt;&lt; ', $nextPrefix = ' &gt;&gt;')
	{
		$res = $this->get('previousAndNext');
		$html = '';
		$previous = $previousPrefix . JText::_('JPREVIOUS');
		$next = JText::_('JNEXT') . $nextPrefix;

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
	 * @param   object  &$item  The property row
	 *
	 * @return  string
	 */
	protected function buildPropertyLink(&$item)
	{
		$slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;

		return JRoute::_('index.php?option=com_jea&view=property&id=' . $slug);
	}

	/**
	 * Display captcha
	 *
	 * @return string the HTML code to dispay captcha
	 */
	protected function displayCaptcha()
	{
		$plugin = JFactory::getConfig()->get('captcha');

		if ($plugin == '0')
		{
			$plugin = 'recaptcha';
		}

		$captcha = JCaptcha::getInstance($plugin, array('namespace' => 'contact'));

		if ($captcha instanceof JCaptcha)
		{
			return $captcha->display('captcha', 'jea-captcha', 'required');
		}

		return '';
	}
}
