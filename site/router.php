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

function JeaBuildRoute(&$query)
{
    $segments = array();

    if(isset($query['view'])) {
        unset( $query['view'] );
    }

    if (isset($query['layout'])) {
        $segments[] = $query['layout'];
        unset( $query['layout'] );
    }

    if(isset($query['id'])) {
        $segments[] = $query['id'];
        unset( $query['id'] );
    };

    return $segments;
}

function JeaParseRoute($segments)
{
    $vars = array();
    // var_dump($segments);
    // exit();

    //Get the active menu item
    $app  = JFactory::getApplication();
    $menu = $app->getMenu();
    $item = $menu->getActive();

    // Count route segments
    $count = count($segments);

    //Standard routing for property
    if(!isset($item))
    {
        $vars['view']  = 'properties';
        $vars['id']    = $segments[$count-1];
        return $vars;
    }

    if ($count == 1 && is_numeric($segments[0])) {
        // Should be a JEA property id
        $vars['id'] = $segments[0];
    }

    if ($item->query['view'] == 'properties') {

        $layout = isset($item->query['layout']) ? $item->query['layout'] : 'default';

        switch($layout) {
            case 'default' :
                if ($count == 1) {
                    // if there is only one segment, then it points to a property detail
                    if (strpos($segments[0], ':') === false) {
                       $id = (int) $segments[0];
                    } else {
                       $exp = explode(':', $segments[0], 2);
                       $id = (int) $exp[0];
                    }

                    $vars['view']  = 'property';
                    $vars['id'] = $id;
                }
                break;

            case 'manage' :
                $vars['view']  = 'properties';
                $vars['layout']  = 'manage';

                if ($count > 0 && $segments[0] == 'edit') {
                    $vars['view']  = 'form';
                    $vars['layout']  = 'edit';
                    if ($count == 2) {
                        $vars['id'] = (int) $segments[1];
                    }
                }
                break;
        }
    }

    return $vars;
}

