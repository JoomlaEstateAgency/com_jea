<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2017 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once JPATH_COMPONENT_ADMINISTRATOR . '/gateways/gateway.php';

jimport('joomla.filesystem.folder');

/**
 * The base class for export gateways
 *
 * @since  3.4
 */
abstract class JeaGatewayExport extends JeaGateway
{
	/**
	 * Site base url
	 *
	 * @var string
	 */
	protected $baseUrl = '';

	/**
	 * Constructor
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An optional associative array of configuration settings.
	 */
	public function __construct (&$subject, $config = array())
	{
		$application = JFactory::getApplication();

		if (defined('BASE_URL'))
		{
			$this->baseUrl = BASE_URL;
		}

		if ($application instanceof JApplicationWeb)
		{
			$this->baseUrl = JUri::root();
		}

		parent::__construct($subject, $config);
	}

	/**
	 * This method must be implemented by child class
	 *
	 * @return  array containg export summary data
	 */
	public function export ()
	{
	}

	/**
	 * Get all JEA properties
	 *
	 * @param   boolean  $published  If true, get only published properties
	 *
	 * @return  array
	 */
	protected function getJeaProperties ($published = true)
	{
		$db = JFactory::getDbo();

		$query = 'SELECT p.*, t.value AS town, ht.value AS heating_type'
				. ', hwt.value AS hot_water_type, d.value AS department'
				. ', a.value AS `area`, s.value AS slogan' . ', c.value AS `condition`, type.value AS type' . PHP_EOL
				. 'FROM #__jea_properties AS p' . PHP_EOL
				. 'LEFT JOIN #__jea_towns AS t ON t.id = p.town_id' . PHP_EOL
				. 'LEFT JOIN #__jea_departments AS d ON d.id = p.department_id' . PHP_EOL
				. 'LEFT JOIN #__jea_areas AS a ON a.id = p.area_id' . PHP_EOL
				. 'LEFT JOIN #__jea_heatingtypes AS ht ON ht.id = p.heating_type' . PHP_EOL
				. 'LEFT JOIN #__jea_hotwatertypes AS hwt ON hwt.id = p.heating_type' . PHP_EOL
				. 'LEFT JOIN #__jea_types AS type ON type.id = p.type_id' . PHP_EOL
				. 'LEFT JOIN #__jea_conditions AS c ON c.id = p.condition_id' . PHP_EOL
				. 'LEFT JOIN #__jea_slogans AS s ON s.id = p.slogan_id' . PHP_EOL;

		if ($published)
		{
			$query .= 'WHERE p.published = 1';
		}

		$db->setQuery($query);
		$properties = $db->loadAssocList();

		$db->setQuery('SELECT `id`, `value` FROM #__jea_amenities');
		$amenities = $db->loadObjectList('id');

		$unsets = array(
			'asset_id',
			'town_id',
			'area_id',
			'department_id',
			'slogan_id',
			'published',
			'access',
			'publish_up',
			'publish_down',
			'checked_out',
			'checked_out_time',
			'created_by',
			'hits'
		);

		foreach ($properties as &$property)
		{
			foreach ($unsets as $key)
			{
				if (isset($property[$key]))
				{
					unset($property[$key]);
				}
			}

			$exp = explode('-', $property['amenities']);
			$tmp = array();

			foreach ($exp as $id)
			{
				if (isset($amenities[$id]))
				{
					$tmp[$id] = $amenities[$id]->value;
				}
			}

			$property['amenities'] = $tmp;
			$property['images'] = $this->getImages((object) $property);
		}

		return $properties;
	}

	/**
	 * Get pictures of a property
	 *
	 * @param   object  $row  The property DB row
	 *
	 * @return  array
	 */
	private function getImages ($row)
	{
		$result = array();
		$images = json_decode($row->images);

		if (empty($images) && ! is_array($images))
		{
			return $result;
		}

		$imgDir = 'images/com_jea/images/' . $row->id;

		if (! JFolder::exists(JPATH_ROOT . '/' . $imgDir))
		{
			return $result;
		}

		foreach ($images as $image)
		{
			$path = JPATH_ROOT . '/' . $imgDir . '/' . $image->name;

			if (JFile::exists($path))
			{
				$result[] = array(
						'path' => $path,
						'url' => $this->baseUrl . $imgDir . '/' . $image->name,
						'name' => $image->name,
						'title' => $image->title,
						'description' => $image->description
				);
			}
		}

		return $result;
	}
}
