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
defined('_JEXEC') or die;

/**
 * @package     Joomla.Site
 * @subpackage  com_jea
 */
abstract class JHtmlAmenities
{
    static protected $amenities = null;

    /**
     * Method to get amenities list values from a list of amenities ids
     * @param mixed $value  string or array of amenities ids
     * @param string $format   The wanted format (ol, li, raw (default))
     * @return  string  HTML for the list.
     */
    static public function bindList($value=0, $format='raw')
    {
        if (is_string($value) && !empty($value)) {
            $ids = explode('-' , $value);
        } elseif (empty($value)) {
            $ids = array();
        } else {
            JArrayHelper::toInteger($value);
            $ids = $value;
        }

        $html = '';
        $amenities = self::getAmenities();
        $items = array();

        foreach ($amenities as $row) {
            if (in_array($row->id, $ids)) {
                if ($format == 'ul'){
                    $items[] = "<li>{$row->value}</li>\n";
                } else {
                    $items[] = $row->value;
                }
                
            }
        }

        if ($format == 'ul'){
            $html = "<ul>\n" . implode("\n", $items) . "</ul>\n";
        } else {
            $html = implode(', ', $items);
        }

        return $html;

    }

    static public function checkboxes($values=array(), $name='amenities')
    {
        $amenities = self::getAmenities();
        $values = (array) $values;
        $html = '';
        if (!empty($amenities)) {
            $html .= "<ul>\n";
            foreach ($amenities as $row) {
                $checked = '';
                $id = 'amenity'. $row->id ;
                if (in_array($row->id, $values)) {
                    $checked = 'checked="checked"';
                }
                // TODO: Solve the problem with the amenities name without []
                $html .= '<li><input name="'. $name.'" id="'
                       . $id . '" type="checkbox" value="'
                       . $row->id .'" '.$checked.' /> '
                       . '<label for="'.$id.'">'. $row->value  . '</label></li>' . "\n" ;
            }
            $html .= "</ul>";
        }
        return $html;
    }

    static public function getAmenities()
    {
        if (self::$amenities === null) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('a.id , a.value');
            $query->from('#__jea_amenities AS a');
            $query->order('a.ordering');
            $db->setQuery($query);
            self::$amenities = $db->loadObjectList();
        }

        return self::$amenities;
    }

}
