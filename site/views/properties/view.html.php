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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.view');

require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'tables'.DS.'features.php';

class JeaViewProperties extends JView
{

    public function display( $tpl = null )
    {
        $state  = $this->get('State');
        $params = &$state->params;

        $this->assignRef('params', $params);
        $this->assignRef('state', $state);
        $layout = $this->getLayout();

        if ($layout == 'default' || $layout == 'manage') {
            if ($layout == 'manage') {
                $items      = $this->get('MyItems');
            } else {
                $items      = $this->get('Items');
            }

            $pagination = $this->get('Pagination');
            $this->assignRef('items', $items);
            $this->assignRef('pagination', $pagination);
        }

        if ($layout == 'default') {
            $this->prepareSortLinks();

            //add alternate feed link
            if ($this->params->get('show_feed_link', 1) == 1) {
                $link    = '&format=feed&limitstart=';
                $attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
                $this->document->addHeadLink(JRoute::_($link.'&type=rss'), 'alternate', 'rel', $attribs);
                $attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
                $this->document->addHeadLink(JRoute::_($link.'&type=atom'), 'alternate', 'rel', $attribs);
            }
        }

        parent::display($tpl);
    }


    protected function prepareSortLinks()
    {
        $sort_links = array();

        $order = $this->state->get('list.ordering');
        $direction = $this->state->get('list.direction');

        if ($this->params->get('sort_date')) {
            $sort_links[] = $this->sort('COM_JEA_SORT_BY_DATE', 'p.created', $direction , $order);
        }

        if ($this->params->get('sort_price')) {
            $sort_links[] = $this->sort('COM_JEA_SORT_BY_PRICE', 'p.price', $direction , $order);
        }

        if ($this->params->get('sort_livingspace')) {
            $sort_links[] = $this->sort('COM_JEA_SORT_BY_LIVING_SPACE', 'p.living_space', $direction , $order);
        }

        if ($this->params->get('sort_landspace')) {
            $sort_links[] = $this->sort('COM_JEA_SORT_BY_LAND_SPACE', 'p.land_space', $direction , $order);
        }

        if ($this->params->get('sort_hits')) {
            $sort_links[] = $this->sort('COM_JEA_SORT_BY_POPULARITY', 'p.hits', $direction , $order);
        }

        if ($this->params->get('sort_towns')) {
            $sort_links[] = $this->sort('COM_JEA_SORT_BY_TOWN', 'town', $direction , $order);
        }

        if ($this->params->get('sort_departements')) {
            $sort_links[] = $this->sort('COM_JEA_SORT_BY_DEPARTMENT', 'department', $direction , $order);
        }

        if ($this->params->get('sort_areas')) {
            $sort_links[] = $this->sort('COM_JEA_SORT_BY_AREA', 'area', $direction , $order);
        }

        $this->assign( 'sort_links', $sort_links );
    }

    /**
     * @param	string	The link title
     * @param	string	The order field for the column
     * @param	string	The current direction
     * @param	string	The selected ordering
     */
    function sort( $title, $order, $direction = 'asc', $selected = 0 )
    {
        $direction = strtolower( $direction );
        $images    = array( 'sort_asc.png', 'sort_desc.png' );
        $index     = intval( $direction == 'desc' );
        $direction = ($direction == 'desc') ? 'asc' : 'desc';
        $html = '<a href="javascript:changeOrdering(\''.$order.'\',\''.$direction.'\');" >';
        $html .= JText::_( $title );
        if ($order == $selected ) {
            $html .= JHTML::_('image.site', '/media/com_jea/images/'.$images[$index], NULL, NULL);
        }
        $html .= '</a>';
        return $html;
    }




    protected function getFirstImageUrl(&$row)
    {
        $images = json_decode($row->images);
        $image  = null;

        if (!empty($images) && is_array($images)) {

            $image = array_shift($images);
            $imagePath = JPATH_ROOT.DS.'images'.DS.'com_jea';

            if (file_exists($imagePath.DS.'thumb-min'.DS.$row->id.'-'.$image->name)) {
                // If the thumbnail already exists, display it directly
                $baseURL = JURI::root(true);
                return $baseURL.'/images/com_jea/thumb-min/'.$row->id.'-'.$image->name;

            } elseif (file_exists($imagePath.DS.'images'.DS.$row->id.DS.$image->name)) {
                // If the thumbnail doesn't exist, generate it and output it on the fly
                $url = 'index.php?option=com_jea&task=thumbnail.create&size=min&id='
                . $row->id .'&image='.$image->name;

                return JRoute::_($url);
            }
        }

        return '';
    }

    protected function getFeatureValue($featureId=0, $featureTable='')
    {
        $db = JFactory::getDbo();
        $table = new FeaturesFactory('#__jea_'.$db->escape($featureTable), 'id', $db);
        $table->load((int) $featureId);
        return $table->value ;
    }

}
