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
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.model');

class JeaModelProperty extends JModel
{

    /**
     * Model context string.
     *
     * @var  string
     */
    protected $_context = 'com_jea.property';


    /* (non-PHPdoc)
     * @see JModel::populateState()
     */
    protected function populateState()
    {
        $app = JFactory::getApplication('site');
        $this->setState('property.id', $app->input->get('id', 0, 'int'));

        // $offset = JRequest::getUInt('limitstart');
        // $this->setState('list.offset', $offset);

        // Load the parameters.
        $params = $app->getParams();
        $this->setState('params', $params);
    }


    /**
     * Get the property object
     * @throws Exception
     * @return stdClass
     */
    public function getItem()
    {
        static $data;

        if ($data != null) {
            return $data;
        }

        $pk = $this->getState('property.id');

        $db        = $this->getDbo();
        $query    = $db->getQuery(true);

        $query->select('p.*');
        $query->from('#__jea_properties AS p');

        // Join properties types
        $query->select('t.value AS `type`');
        $query->join('LEFT', '#__jea_types AS t ON t.id = p.type_id');

        // Join departments
        $query->select('d.value AS department');
        $query->join('LEFT', '#__jea_departments AS d ON d.id = p.department_id');

        // Join towns
        $query->select('town.value AS town');
        $query->join('LEFT', '#__jea_towns AS town ON town.id = p.town_id');

        // Join areas
        $query->select('area.value AS area');
        $query->join('LEFT', '#__jea_areas AS area ON area.id = p.area_id');

        // Join conditions
        $query->select('c.value AS `condition`');
        $query->join('LEFT', '#__jea_conditions AS c ON c.id = p.condition_id');
        
        // Join heating types
        $query->select('ht.value AS `heating_type_name`');
        $query->join('LEFT', '#__jea_heatingtypes AS ht ON ht.id = p.heating_type');
        
        // Join hot water types
        $query->select('hwt.value AS `hot_water_type_name`');
        $query->join('LEFT', '#__jea_heatingtypes AS hwt ON hwt.id = p.hot_water_type');

        // Join users
        $query->select('u.username AS author');
        $query->join('LEFT', '#__users AS u ON u.id = p.created_by');

        // Join slogans
        $query->select('s.value AS slogan');
        $query->join('LEFT', '#__jea_slogans AS s ON s.id = p.slogan_id');

        $query->where('p.id ='.(int) $pk);

        $db->setQuery($query);

        $data = $db->loadObject();

        if ($error = $db->getErrorMsg()) {
            throw new Exception($error);
        }

        // convert images field
        $images = json_decode($data->images);

        if (!empty($images) && is_array($images)) {

            $imagePath = JPATH_ROOT.DS.'images'.DS.'com_jea';
            $baseURL = JURI::root(true);

            foreach ($images as $k => $image) {

                if (file_exists($imagePath.DS.'images'.DS.$data->id.DS.$image->name)) {

                    $image->URL = $baseURL.'/images/com_jea/images/'.$data->id.'/'.$image->name;

                    // get thumb min URL
                    if (file_exists($imagePath.DS.'thumb-min'.DS.$data->id.'-'.$image->name)) {
                        // If the thumbnail already exists, display it directly
                        $image->minURL = $baseURL.'/images/com_jea/thumb-min/'.$data->id.'-'.$image->name;

                    } else {
                        // If the thumbnail doesn't exist, generate it and output it on the fly
                        $image->minURL = 'index.php?option=com_jea&task=thumbnail.create&size=min&id='
                        . $data->id .'&image='.$image->name;
                    }

                    // get thumb medium URL
                    if (file_exists($imagePath.DS.'thumb-medium'.DS.$data->id.'-'.$image->name)) {
                        // If the thumbnail already exists, display it directly
                        $image->mediumURL = $baseURL.'/images/com_jea/thumb-medium/'.$data->id.'-'.$image->name;

                    } else {
                        // If the thumbnail doesn't exist, generate it and output it on the fly
                        $image->mediumURL = 'index.php?option=com_jea&task=thumbnail.create&size=medium&id='
                        . $data->id .'&image='.$image->name;
                    }

                } else {
                    unset($images[$k]);
                }
            }

            $data->images = $images;
        }

        return $data;

    }
    
    /**
     * Get the previous and next item relative to the current
     * @return array
     */
    public function getPreviousAndNext()
    {
        $item = $this->getItem();
        $properties = JModel::getInstance('Properties', 'JeaModel');

        // Deactivate pagination
        $properties->setState('list.start', 0);
        $properties->setState('list.limit', 0);
        $items = $properties->getItems();

        $result= array(
            'prev' => null,
            'next' => null
        );

        $currentIndex = 0;
        foreach($items as $k => $row){
            if ($row->id == $item->id) {
                $currentIndex = $k;
            }
        }
        if ( isset($items[$currentIndex-1]) ) $result['prev_item'] = $items[$currentIndex-1] ;
        if ( isset($items[$currentIndex+1]) ) $result['next_item'] = $items[$currentIndex+1] ;

        return $result;
    }

    /**
     * Increment the hit counter for the property.
     *
     * @param   int  Optional primary key of the article to increment.*
     * @return  boolean  True if successful; false otherwise and internal error set.
     */
    public function hit($pk = 0)
    {
        // Initialise variables.
        $pk = (!empty($pk)) ? $pk :  $this->getState('property.id');
        $db = $this->getDbo();
        $db->setQuery(
            'UPDATE #__jea_properties' .
             ' SET hits = hits + 1' .
             ' WHERE id = '.(int) $pk
        );

        if (!$db->query()) {
            $this->setError($db->getErrorMsg());
            return false;
        }

        return true;
    }
}