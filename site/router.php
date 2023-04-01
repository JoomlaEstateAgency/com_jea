<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Site
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2020 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;

defined('_JEXEC') or die;

/**
 * JEA router functions
 *
 * @param   array $query An array of URL arguments
 *
 * @return  array The URL arguments to use to assemble the subsequent URL.
 *
 * @deprecated  4.0  Use Class based routers instead
 */
function jeaBuildRoute(&$query)
{
	$segments = array();

	if (isset($query['view']))
	{
		unset($query['view']);
	}

	if (isset($query['layout']))
	{
		unset($query['layout']);
	}

	if (isset($query['id']))
	{
		$segments[] = $query['id'];
		unset($query['id']);
	}

	return $segments;
}

/**
 * Parse the segments of a URL.
 *
 * @param   array $segments The segments of the URL to parse.
 *
 * @return  array  The URL attributes to be used by the application.
 *
 * @deprecated  4.0  Use Class based routers instead
 */
function jeaParseRoute(&$segments)
{
	$vars = array();

	// Get the active menu item
	$app = Factory::getApplication();
	assert($app instanceof \Joomla\CMS\Application\SiteApplication);

	$menu = $app->getMenu();
	assert($menu instanceof \Joomla\CMS\Menu\SiteMenu);

	$item = $menu->getActive();

	// Count route segments
	$count = count($segments);

	// Standard routing for property
	if ($count == 1 && !isset($item))
	{
		// $vars['option'] = 'com_jea';
		$vars['view'] = 'property';
		$vars['id'] = $segments[0];
		unset($segments[0]);

		return $vars;
	}

	if ($item->query['view'] == 'properties')
	{
		$layout = isset($item->query['layout']) ? $item->query['layout'] : 'default';

		switch ($layout)
		{
			case 'default':
			case 'search':
			case 'searchmap':
				$vars['view'] = 'properties';
				$vars['layout'] = $layout;

				if ($count == 1)
				{
					$vars['view'] = 'property';

					// If there is only one, then it points to a property detail
					if (is_numeric($segments[0]))
					{
						$vars['id'] = (int) $segments[0];
					}
					elseif (strpos($segments[0], ':') !== false)
					{
						$exp = explode(':', $segments[0], 2);
						$vars['id'] = (int) $exp[0];
					}

					unset($segments[0]);
				}
				break;
			case 'manage':
				$vars['view'] = 'properties';
				$vars['layout'] = 'manage';

				if ($count == 1 && is_numeric($segments[0]))
				{
					$vars['view'] = 'form';
					$vars['layout'] = 'edit';
					$vars['id'] = (int) $segments[0];
					unset($segments[0]);
				}
				elseif ($count > 0 && $segments[0] == 'edit')
				{
					$vars['view'] = 'form';
					$vars['layout'] = 'edit';

					if ($count == 2)
					{
						$vars['id'] = (int) $segments[1];
						unset($segments[1]);
					}

					unset($segments[0]);
				}

				break;
		}
	}
	elseif ($item->query['view'] == 'form')
	{
		$vars['view'] = 'form';
		$vars['layout'] = 'edit';

		if ($count > 0)
		{
			if ($segments[0] == 'edit' && $count == 2)
			{
				$vars['id'] = (int) $segments[1];
				unset($segments[1]);
			}
			elseif ($segments[0] == 'manage')
			{
				$vars['view'] = 'properties';
				$vars['layout'] = 'manage';
			}

			unset($segments[0]);
		}
	}

	return $vars;
}
