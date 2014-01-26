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

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'interface.php';

/**
 * Import model class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 */
class JeaModelImport extends JeaModelInterface
{

    protected $_log_file = 'jea-import.logs.php';

    protected $_auto_deletion = false;

    /**
     * Method to get the form.
     *
     */
    public function getForm()
    {
        // Get the form.
        JForm::addFormPath(JPATH_COMPONENT . '/models/forms');
        $form = JForm::getInstance('com_jea.import-jea', 'import-jea');
        $suggestPath = str_replace(basename(JPATH_ROOT), '', JPATH_ROOT) ;
        $form->setFieldAttribute('joomla_path', 'default', $suggestPath);
        return $form;
    }

    protected function &parse()
    {
        $properties = array();

        if ($this->getState('import.type') == 'jea') {
            $properties =& $this->importFromJEA();
        }

        return $properties;
    }


    protected function &importFromJEA()
    {
        $properties = array();

        $version = (float) $this->getState('param.jea_version', '1.1');
        $path = $this->getState('param.joomla_path');
        $path = rtrim($path, '/');

        if (!JFolder::exists($path)) {
            throw new Exception(JText::sprintf('COM_JEA_DIRECTORY_NOT_FOUND', $path));
        }

        if (!JFile::exists($path.DS.'configuration.php')) {
            throw new Exception(JText::_('COM_JEA_JOOMLA_CONFIGURATION_FILE_NOT_FOUND'));
        }

        // Get The config file
        $confCode = file_get_contents($path.DS.'configuration.php');
        $confCode = str_replace('JConfig', 'JOldConfig', $confCode);
        $tmpConf = JFactory::getConfig()->get('tmp_path') . '/configuration.old.php';
        JFile::write($tmpConf, $confCode);
        require_once $tmpConf;

        $conf = new JOldConfig();
        // remove the conf file :
        JFile::delete($tmpConf);

        $options = array(
            'driver' => $conf->dbtype, 
            'host' => $conf->host, 
            'user' => $conf->user, 
            'password' => $conf->password, 
            'database' => $conf->db, 
            'prefix' => $conf->dbprefix
        );

        $db = JDatabase::getInstance($options);

        $query = $db->getQuery(true);

        $query->select('p.*');
        $query->from('#__jea_properties AS p');

        $query->select('t.value AS `type`');
        $query->join('LEFT', '#__jea_types AS t ON t.id = p.type_id');
        $query->select('d.value AS department');
        $query->join('LEFT', '#__jea_departments AS d ON d.id = p.department_id');
        $query->select('town.value AS town');
        $query->join('LEFT', '#__jea_towns AS town ON town.id = p.town_id');
        $query->select('area.value AS area');
        $query->join('LEFT', '#__jea_areas AS area ON area.id = p.area_id');
        $query->select('c.value AS `condition`');
        $query->join('LEFT', '#__jea_conditions AS c ON c.id = p.condition_id');
        $query->select('ht.value AS `heating_type_name`');
        $query->join('LEFT', '#__jea_heatingtypes AS ht ON ht.id = p.heating_type');
        $query->select('hwt.value AS `hot_water_type_name`');
        $query->join('LEFT', '#__jea_heatingtypes AS hwt ON hwt.id = p.hot_water_type');
        $query->select('u.username AS author, u.email AS email');
        $query->join('LEFT', '#__users AS u ON u.id = p.created_by');
        $query->select('s.value AS slogan');
        $query->join('LEFT', '#__jea_slogans AS s ON s.id = p.slogan_id');
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        if ($version > 1.1) {
            $db->setQuery('SELECT * FROM #__jea_amenities');
        } else {
            $db->setQuery('SELECT * FROM #__jea_advantages');
        }

        $amenities = $db->loadObjectList('id');


        foreach ($rows as $row) {

            $property = new JEAPropertyInterface();

            $property->ref = $row->ref;
            $property->title = $row->title;
            $property->description = $row->description;
            $property->area = $row->area;
            $property->author_email = $row->email;
            $property->author_name = $row->author;
            $property->availability = $row->availability;
            $property->bathrooms = $row->bathrooms;
            $property->transaction_type = $version == 2.0 ? $row->transaction_type : (!empty($row->is_renting) ? 'RENTING' : 'SELLING');
            $property->charges = $row->charges;
            $property->condition = $row->condition;

            $created = $version == 2.0 ? new JDate($row->created) : new JDate($row->date_insert);

            $property->created = $created->toUnix();
            $property->department = $row->department;
            $property->deposit = $row->deposit;
            $property->fees = $row->fees;
            $property->floor = $row->floor;
            $property->heating_type = $row->heating_type_name;
            $property->hot_water_type = $row->hot_water_type_name;
            $property->land_space = $row->land_space;
            $property->latitude = $row->latitude;
            $property->living_space = $row->living_space;
            $property->longitude = $row->longitude;
            $property->price = $row->price;
            $property->rooms = $row->rooms;
            $property->toilets = $row->toilets;
            $property->town = $row->town;
            $property->type = $row->type;
            if ($version == 2.0) {
                $modified = new JDate($row->modified);
                $property->modified = $modified->toUnix();
            } else {
                $property->modified = $property->created;
            }

            $property->zip_code = $row->zip_code;

            $property->address = $version == 2.0 ? $row->address : $row->adress;

            // amenities
            $fieldAmenities = $version == 2.0 ? $row->amenities : $row->advantages;
            $amenities_ids = explode('-', trim($fieldAmenities, '-'));
            foreach ($amenities_ids as $id) {
                if (isset($amenities[$id])) {
                    $property->amenities[] = $amenities[$id]->value ;
                }
            }

            // pictures
            $property->images = $this->getJeaPictures($row, $path .'/images/com_jea/images');

            // Add the alias :
            if ($version >= 1.1) {
                $property->addAdditionalField('alias', $row->alias);
            }

            $properties[$row->ref] = $property;
        }

         
        return $properties;
    }

    function getJeaPictures(&$row, $path)
    {
        $pictures = array();

        if (JFolder::exists($path.'/'.$row->id)) {

            if (isset($row->images)) {
                // a JEA 2.x row
                $list = JFolder::files($path.'/'.$row->id, null, false, true, array(), array('thumb'));
                if (is_array($list)) {
                    return $list;
                }

            } else {

                if (JFile::exists($path.'/'.$row->id . '/main.jpg')) {
                    $pictures[] = $path.'/'.$row->id . '/main.jpg';
                }

                if (JFolder::exists($path.'/'.$row->id .'/secondary')) {
                    $secondaries = JFolder::files($path.'/'.$row->id .'/secondary', null, false, true);
                    foreach($secondaries as $picture) {
                        $pictures[] = $picture;
                    }
                }
            }
        }

        return $pictures;
    }


}


