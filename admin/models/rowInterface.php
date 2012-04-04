<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @version     $Id$
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2012 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

jimport('joomla.user.user');
jimport('joomla.mail.helper');

require_once JPATH_COMPONENT . '/tables/properties.php';

/**
 * RowInterface model class.
 * 
 * This class provides an interface between JEA data and third party bridges
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 */
class JEARowInterface extends JObject
{

    // These public members concern the interface
    public $ref = '';
    public $title = '';
    public $type = '';
    public $transaction_type = ''; // Renting or selling
    public $price = '';
    public $address = '';
    public $town = '';
    public $area = '';
    public $zip_code = '';
    public $department = '';
    public $condition = '';
    public $living_space = '';
    public $land_space = '';
    public $rooms = '';
    public $bedrooms = '';
    public $charges = '';
    public $fees = '';
    public $deposit = '';
    public $hot_water_type = '';
    public $heating_type = '';
    public $bathrooms = '';
    public $toilets = '';
    public $availability = '';
    public $floor = 0;
    public $floors_number = 0;
    public $orientation = '0';
    public $amenities = array();
    public $description = '';
    public $author_name = '';
    public $author_email = '';
    public $latitude = 0;
    public $longitude = 0;
    public $created = 0; // timestamp
    public $modified = 0; // timestamp
    public $images = array();

    // Fields which are not in the standard JEA interface
    protected $_additionnalsFields = array();

    /**
     * The users array from Joomla
     * @var array
     */
    protected static $_users = null;

    /**
     * The tables data array from JEA
     * @var array
     */
    protected static $_tables = null;

    /**
     * The features data array from JEA
     * @var array
     */
    protected static $_features = array();


    function toJEAData()
    {
        $data = array(
            'ref' => $this->ref,
            'title' => $this->title,
            'type_id' => JEARowInterface::getFeatureId('types', $this->type),
            'price' => floatval($this->price),
            'address' => $this->address,
            'department_id' => JEARowInterface::getFeatureId('departments', $this->department),
            'zip_code' => $this->zip_code,
            'condition_id' => JEARowInterface::getFeatureId('conditions', $this->condition),
            'living_space' => floatval($this->living_space),
            'land_space' => floatval($this->land_space),
            'rooms' => intval($this->rooms),
            'bedrooms' => intval($this->bedrooms),
            'charges' => floatval($this->charges),
            'fees' => floatval($this->fees),
            'deposit' => floatval($this->deposit),
            'hot_water_type' => JEARowInterface::getFeatureId('hotwatertypes', $this->hot_water_type),
            'heating_type' => JEARowInterface::getFeatureId('heatingtypes', $this->heating_type),
            'bathrooms' => intval($this->bathrooms),
            'toilets' => intval($this->toilets),
            'availability' => $this->_convertTimestampToMysqlDate($this->availability, false),
            'floor' => intval($this->floor),
            'floors_number' => (int) $this->floors_number,
            'description' => $this->description,
            'published' => 1,
            'created' => $this->_convertTimestampToMysqlDate($this->created),
            'modified' => $this->_convertTimestampToMysqlDate($this->modified),
            'created_by' => JEARowInterface::getUserId($this->author_email, $this->author_name),
            'latitude' => floatval($this->latitude),
            'longitude' => floatval($this->longitude)
        );

        $this->transaction_type = strtoupper($this->transaction_type);
        if ($this->transaction_type == 'RENTING' || $this->transaction_type == 'SELLING') {
            $data['transaction_type'] = $this->transaction_type;
        } else {
            $data['transaction_type'] = '0';
        }

        $data['town_id'] = JEARowInterface::getFeatureId('towns', $this->town, $data['department_id']);
        $data['area_id'] = JEARowInterface::getFeatureId('areas', $this->area, $data['town_id']);

        $orientations = array('0', 'N', 'NE', 'NW', 'E', 'W', 'SW', 'SE');
        $orientation = strtoupper($this->orientation);
        if (in_array($orientation, $orientations)) {
            $data['orientation'] = $orientation;
        } else {
            $data['orientation'] = 'O';
        }

        if (is_array($this->amenities)) {
            $data['amenities'] = array();
            foreach ($this->amenities as $value) {
                $data['amenities'][] = JEARowInterface::getFeatureId('amenities', $value);
            }
        }

        $validExtensions = array('jpg','JPG','jpeg','JPEG','gif','GIF','png','PNG') ;
        $data['images'] = array();
        foreach ($this->images as $image) {
            $image = basename($image);
            if (!empty($image)) {
                if (in_array(JFile::getExt($image), $validExtensions)) {
                     $img = new stdClass();
                     $img->name = $image;
                     $img->title = '';
                     $img->description = '';
                     $data['images'][] = $img;
                }
            }
        }

        return $data;
    }

    public function addAdditionalField($fieldName='', $value='')
    {
        $this->_additionnalsFields[$fieldName] = $value;
    }


    public function save($bridge_code='', $id=0, $forceUTF8=false)
    {
        $db = JFactory::getDbo();
        $jeaPropertiesTable = new TableProperties($db);

        if (!empty($id)) {
            $jeaPropertiesTable->load($id);
        }
        // Prepare data
        $data = $this->toJEAData();

        foreach ($this->_additionnalsFields as $fieldName => $value) {
            $data[$fieldName] = $value;
        }

        if (!empty($bridge_code)) {
            $data['bridge_code'] = $bridge_code ;
        }

        if ($forceUTF8) {
            foreach ($data as $k => $v) {
                switch($k) {
                    case 'title':
                    case 'description':
                    case 'address':
                        $data[$k] = utf8_encode($v);
                }
            }
        }

        $jeaPropertiesTable->bind($data);
        $jeaPropertiesTable->check();
        $jeaPropertiesTable->store();

        $errors = $jeaPropertiesTable->getErrors();

        if(!empty($errors)) {
            $this->_errors = $errors;
            return false;
        }

        // Save images
        if (!empty($this->images)) {
            $imgDir = JPATH_ROOT.'/images/com_jea/images/'.$jeaPropertiesTable->id;
    
            if (!JFolder::exists($imgDir)) {
                JFolder::create($imgDir);
            }
            
            $validExtensions = array('jpg','JPG','jpeg','JPEG','gif','GIF','png','PNG') ;
    
            foreach ($this->images as $image) {
                $basename = basename($image);
                if (in_array(JFile::getExt($basename), $validExtensions)) {
                    if (substr($image, 0, 7) == 'http://') {
                        $this->downloadImage($image, $imgDir.'/'.$basename);
                    } else {
                        JFile::copy($image, $imgDir.'/'.$basename);
                    }
                }
            }
        }
        return true;

    }


    protected function downloadImage($url='', $dest='')
    {
        if (empty($url) || empty($dest)) {
            return false;
        }

        $buffer = '';
        $allow_url_fopen = (bool) ini_get('allow_url_fopen');

        if ($allow_url_fopen) {

            $buffer = file_get_contents($url);

        } elseif (function_exists('curl_init')) {

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $buffer = curl_exec($ch);
            curl_close($ch);
        }

        return JFile::write($dest, $buffer);
    }


    /**
     * Get Feature id related to its value
     *
     * @param string $tableName
     * @param string $fieldValue
     * @param int $parentId
     * @return int
     */
    public static function getFeatureId($tableName='', $fieldValue='', $parentId=0)
    {
        $fieldValue = trim($fieldValue);
        $id = 0;
        $r = self::_getJeaRowIfExists($tableName,'value', $fieldValue);

        static $tablesOrdering = array();

        if ($r === false && !empty($fieldValue) && !isset(self::$_features[$tableName][$fieldValue])) {
            $db = JFactory::getDbo();

            if (!isset($tablesOrdering[$tableName])) {
                $db->setQuery('SELECT MAX(ordering) FROM #__jea_' . $tableName) ;
                $tablesOrdering[$tableName] = intval($db->loadResult());
            }

            $maxord = $tablesOrdering[$tableName] += 1;
            $query = $db->getQuery(true);
            $query->insert('#__jea_' . $tableName);

            $columns = array('value', 'ordering');
            $values  = $db->quote($fieldValue) . ','. $maxord ;

            if ($tableName == 'towns') {
                $columns[] = 'department_id';
                $values .= ',' . (int) $parentId;
            } elseif ($tableName == 'areas') {
                $columns[] = 'town_id';
                $values .= ',' . (int) $parentId;
            }

            $query->columns($columns);
            $query->values($values);
            $db->setQuery($query);
            $db->query();
            $id = $db->insertid();
            self::$_features[$tableName][$fieldValue] = $id;

        } elseif (isset(self::$_features[$tableName][$fieldValue])) {

            $id = self::$_features[$tableName][$fieldValue] ;
                
        } elseif (is_object($r)) {

            $id = $r->id;
        }

        return $id;
    }


    protected static function _getJeaRowIfExists($tableName='', $fieldName='', $fieldValue='')
    {
        if (self::$_tables == null) {
            $db = JFactory::getDbo();
            self::$_tables = array(
                'amenities' => array(),
                'areas' => array(),
                'conditions' => array(),
                'departments' => array(),
                'heatingtypes' => array(),
                'hotwatertypes' => array(),
                'properties' => array(),
                'slogans' => array(),
                'towns' => array(),
                'types' => array()
            );

            foreach (self::$_tables as $tableName => $value){
                // Get all JEA datas
                $db->setQuery('SELECT * FROM #__jea_'.$tableName);
                self::$_tables[$tableName] = $db->loadObjectList('id');
            }
        }


        if (empty(self::$_tables[$tableName]) || empty($fieldName) || empty($fieldValue)){
            return false;
        }

        foreach (self::$_tables[$tableName] as $row){
            if (!isset($row->$fieldName)){
                return false;
            }
            if ($row->$fieldName == $fieldValue){
                return $row;
            }
        }
        return false;
    }

    public static function getUserId($email='', $name='')
    {
        if (self::$_users == null) {
            $db = JFactory::getDbo();
            $db->setQuery('SELECT `id`, `email` FROM `#__users`');
            $rows = $db->loadObjectList();

            foreach ($rows as $row) {
                self::$_users[$row->email] = $row->id;
            }
        }

        if (isset(self::$_users[$email])) {
            return self::$_users[$email];

        } else {
            $id = self::_createUser($email, $name);
            if ($id != false) {
                self::$_users[$email] = $id;
            }
        }

        return 0;
    }


    protected static function _createUser($email='', $name='')
    {
        if (!JMailHelper::isEmailAddress($email)) {
            return false;
        }

        $splitMail = explode('@', $email);
        $user = new JUser();

        $params = array(
            'username' => $splitMail[0],
            'name'     => $name,
            'email'     => $email,
            'block'    => 0,
            'sendEmail'=> 0
        );

        $user->bind($params);

        if (true === $user->save()) {
            return $user->id;
        }

        return false;
    }


    /**
     * Convert Unix timestamp to MYSQL date
     *
     * @param int $timestamp
     * @param boolean $datetime If true return MYSQL DATETIME else return MYSQL DATE
     * @return string
     */
    protected function _convertTimestampToMysqlDate($timestamp, $datetime=true)
    {
        if (is_int($timestamp) && $timestamp > 0) {
            if ($datetime) {
                return date('Y-m-d H:i:s', $timestamp);
            }
            return date('Y-m-d', $timestamp);
        }
         
        return '';
    }


}