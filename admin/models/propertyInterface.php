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

jimport('joomla.user.user');
jimport('joomla.mail.helper');
jimport('joomla.filesystem.folder');

require_once JPATH_COMPONENT_ADMINISTRATOR . '/tables/property.php';

/**
 * JEAPropertyInterface model class.
 *
 * This class provides an interface between JEA data and third party bridges
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @since       2.0
 */
class JEAPropertyInterface extends JObject
{
	/* These public members concern the interface */

	public $ref = '';

	public $title = '';

	public $type = '';

	public $transaction_type = '';

	/**
	 * Renting or selling price
	 *
	 * @var string
	 */
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

	/**
	 * Timestamp
	 *
	 * @var integer
	 */
	public $created = 0;

	/**
	 * Timestamp
	 *
	 * @var integer
	 */
	public $modified = 0;

	public $images = array();

	public $language = '*';

	/**
	 *  A callback which replace the default implementation to save images.
	 *  This should be a function or a method but not a closure because
	 *  this object needs to be serialised and closures can't be serialized
	 *
	 * @var string|array
	 */
	public $saveImagesCallback = null;

	/**
	 * Fields which are not in the standard JEA interface
	 *
	 * @var array
	 */
	protected $additionnalsFields = array();

	/**
	 * The users array from Joomla
	 *
	 * @var array
	 */
	protected static $users = null;

	/**
	 * The tables data array from JEA
	 *
	 * @var array
	 */
	protected static $tables = null;

	/**
	 * The features data array from JEA
	 *
	 * @var array
	 */
	protected static $features = array();

	/**
	 * Convert Interface data to JEA data
	 *
	 * @return  array representing a JEA propery row
	 */
	protected function toJEAData()
	{
		$data = array(
			'ref' => $this->ref,
			'title' => $this->title,
			'type_id' => self::getFeatureId('types', $this->type, $this->language),
			'price' => floatval($this->price),
			'address' => $this->address,
			'department_id' => self::getFeatureId('departments', $this->department),
			'zip_code' => $this->zip_code,
			'condition_id' => self::getFeatureId('conditions', $this->condition, $this->language),
			'living_space' => floatval($this->living_space),
			'land_space' => floatval($this->land_space),
			'rooms' => intval($this->rooms),
			'bedrooms' => intval($this->bedrooms),
			'charges' => floatval($this->charges),
			'fees' => floatval($this->fees),
			'deposit' => floatval($this->deposit),
			'hot_water_type' => self::getFeatureId('hotwatertypes', $this->hot_water_type, $this->language),
			'heating_type' => self::getFeatureId('heatingtypes', $this->heating_type, $this->language),
			'bathrooms' => intval($this->bathrooms),
			'toilets' => intval($this->toilets),
			'availability' => $this->_convertTimestampToMysqlDate($this->availability, false),
			'floor' => intval($this->floor),
			'floors_number' => (int) $this->floors_number,
			'orientation' => $this->orientation,
			'description' => $this->description,
			'published' => 1,
			'created' => $this->_convertTimestampToMysqlDate($this->created),
			'modified' => $this->_convertTimestampToMysqlDate($this->modified),
			'created_by' => self::getUserId($this->author_email, $this->author_name),
			'latitude' => floatval($this->latitude),
			'longitude' => floatval($this->longitude),
			'language' => $this->language
		);

		$this->transaction_type = strtoupper($this->transaction_type);

		if ($this->transaction_type == 'RENTING' || $this->transaction_type == 'SELLING')
		{
			$data['transaction_type'] = $this->transaction_type;
		}
		else
		{
			$data['transaction_type'] = '0';
		}

		$data['town_id'] = self::getFeatureId('towns', $this->town, null, $data['department_id']);
		$data['area_id'] = self::getFeatureId('areas', $this->area, null, $data['town_id']);

		$orientations = array('0','N','NE','NW','NS','E','EW','W','SW','SE');

		$orientation = strtoupper($this->orientation);

		if (in_array($orientation, $orientations))
		{
			$data['orientation'] = $orientation;
		}
		else
		{
			$data['orientation'] = 'O';
		}

		if (is_array($this->amenities))
		{
			$data['amenities'] = array();

			foreach ($this->amenities as $value)
			{
				$data['amenities'][] = self::getFeatureId('amenities', $value, $this->language);
			}
		}

		$validExtensions = array('jpg', 'JPG', 'jpeg', 'JPEG', 'gif', 'GIF', 'png', 'PNG');
		$data['images'] = array();

		foreach ($this->images as $image)
		{
			if (substr($image, 0, 4) == 'http')
			{
				$uri = new \Joomla\Uri\Uri($image);
				$image = $uri->getPath();
			}

			$image = basename($image);

			if (! empty($image))
			{
				if (in_array(JFile::getExt($image), $validExtensions))
				{
					$img = new stdClass;
					$img->name = $image;
					$img->title = '';
					$img->description = '';
					$data['images'][] = $img;
				}
			}
		}

		return $data;
	}

	/**
	 * Add a custom field to the interface
	 *
	 * @param   string  $fieldName  The custom field name
	 * @param   string  $value      The custom field value
	 *
	 * @return  void
	 */
	public function addAdditionalField($fieldName = '', $value = '')
	{
		$this->additionnalsFields[$fieldName] = $value;
	}

	/**
	 * Save the property
	 *
	 * @param   string   $provider   A provider name
	 * @param   number   $id         The property id
	 * @param   boolean  $forceUTF8  To force string to be converted into UTF-8
	 *
	 * @return  boolean return true if property was saved
	 */
	public function save($provider = '', $id = 0, $forceUTF8 = false)
	{
		$db = JFactory::getDbo();
		$property = new TableProperty($db);
		$isNew = true;
		$dispatcher = JDispatcher::getInstance();

		// Include the jea plugins for the on save events.
		JPluginHelper::importPlugin('jea');

		if (! empty($id))
		{
			$property->load($id);
			$isNew = false;
		}

		// Prepare data
		$data = $this->toJEAData();

		foreach ($this->additionnalsFields as $fieldName => $value)
		{
			$data[$fieldName] = $value;
		}

		if (! empty($provider))
		{
			$data['provider'] = $provider;
		}

		if ($forceUTF8)
		{
			foreach ($data as $k => $v)
			{
				switch ($k)
				{
					case 'title':
					case 'description':
					case 'address':
						$data[$k] = utf8_encode($v);
				}
			}
		}

		$property->bind($data);
		$property->check();

		// Check override created_by
		if (! empty($data['created_by']))
		{
			$property->created_by = $data['created_by'];
		}

		// Trigger the onContentBeforeSave event.
		$result = $dispatcher->trigger('onBeforeSaveProperty', array('com_jea.propertyInterface', &$property, $isNew));

		if (in_array(false, $result, true))
		{
			$this->_errors = $property->getError();

			return false;
		}

		$property->store();

		// Trigger the onContentAfterSave event.
		$dispatcher->trigger('onAfterSaveProperty', array('com_jea.propertyInterface', &$property, $isNew));

		$errors = $property->getErrors();

		if (! empty($errors))
		{
			$this->_errors = $errors;

			return false;
		}

		// Save images
		if (!empty($this->images) && $this->saveImagesCallback === null)
		{
			$imgDir = JPATH_ROOT . '/images/com_jea/images/' . $property->id;

			if (! JFolder::exists($imgDir))
			{
				JFolder::create($imgDir);
			}

			$validExtensions = array('jpg', 'JPG', 'jpeg', 'JPEG', 'gif', 'GIF', 'png', 'PNG');

			foreach ($this->images as $image)
			{
				$basename = basename($image);

				if (substr($image, 0, 4) == 'http')
				{
					$uri = new \Joomla\Uri\Uri($image);
					$basename = basename($uri->getPath());
				}

				if (!in_array(JFile::getExt($basename), $validExtensions))
				{
					// Not a valid Extension
					continue;
				}

				if (substr($image, 0, 4) != 'http' && file_exists($image))
				{
					JFile::copy($image, $imgDir . '/' . $basename);
				}

				if (substr($image, 0, 4) == 'http')
				{
					if (JFile::exists($imgDir . '/' . $basename))
					{
						$localtime  = $this->getLastModified($imgDir . '/' . $basename);
						$remotetime = $this->getLastModified($image);

						if ($remotetime <= $localtime)
						{
							JLog::add(
								sprintf(
									"File %s is up to date. [local time: %u - remote time: %u]",
									$imgDir . '/' . $basename,
									$localtime,
									$remotetime
								),
								JLog::DEBUG,
								'jea'
							);

							continue;
						}
					}

					$this->downloadImage($image, $imgDir . '/' . $basename);
				}
			}
		}
		elseif (!empty($this->images) && is_callable($this->saveImagesCallback))
		{
			call_user_func_array($this->saveImagesCallback, array($this->images, $property));
		}

		return true;
	}

	/**
	 * Download an image
	 *
	 * @param   string  $url   The image URL
	 * @param   string  $dest  The destination directory
	 *
	 * @return  boolean
	 */
	protected function downloadImage ($url = '', $dest = '')
	{
		JLog::add("Download Image : $url", JLog::DEBUG, 'jea');

		if (empty($url) || empty($dest))
		{
			return false;
		}

		$buffer = '';
		$allow_url_fopen = (bool) ini_get('allow_url_fopen');

		if ($allow_url_fopen)
		{
			$buffer = file_get_contents($url);
		}
		elseif (function_exists('curl_init'))
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);

			// Don't check SSL certificate
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$buffer = curl_exec($ch);
			curl_close($ch);
		}

		return JFile::write($dest, $buffer);
	}

	/**
	 * Get Last modified time as Unix timestamp
	 *
	 * @param   string  $file  A local or remote file
	 *
	 * @throws  RuntimeException
	 * @return  integer Unix timestamp
	 */
	public function getLastModified($file)
	{
		if (substr($file, 0, 4) != 'http' && file_exists($file))
		{
			$stat = stat($file);

			return $stat['mtime'];
		}

		$allow_url_fopen = (bool) ini_get('allow_url_fopen');
		$headers = array();

		if ($allow_url_fopen)
		{
			$headers = get_headers($file);
		}
		elseif (function_exists('curl_init'))
		{
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $file);

			// Don't check SSL certificate
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_FILETIME, true);
			curl_setopt($curl, CURLOPT_NOBODY, true);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, true);
			$out = curl_exec($curl);
			curl_close($curl);
			$headers = explode("\n", $out);
		}

		if (empty($headers))
		{
			throw new RuntimeException("Cannot get HTTP headers for $file");
		}

		foreach ($headers as $header)
		{
			if (strpos($header, 'Last-Modified') !== false)
			{
				if (preg_match('/:\s?(.*)$/m', $header, $matches) !== false)
				{
					$matches[1];
					JLog::add(sprintf("Last-Modified: %s - Time: %u", $matches[1], strtotime($matches[1])), JLog::DEBUG, 'jea');

					return strtotime($matches[1]);
				}
			}
		}

		return 0;
	}

	/**
	 * Get Feature id related to its value
	 *
	 * @param   string   $tableName   The feature table name
	 * @param   string   $fieldValue  The value to store
	 * @param   string   $language    The language code
	 * @param   integer  $parentId    An optional parent id
	 *
	 * @return  integer
	 */
	public static function getFeatureId ($tableName = '', $fieldValue = '', $language = null, $parentId = 0)
	{
		$fieldValue = trim($fieldValue);
		$id = 0;
		$r = self::_getJeaRowIfExists($tableName, 'value', $fieldValue);

		static $tablesOrdering = array();

		if ($r === false && ! empty($fieldValue) && ! isset(self::$features[$tableName][$fieldValue]))
		{
			$db = JFactory::getDbo();

			if (! isset($tablesOrdering[$tableName]))
			{
				$db->setQuery('SELECT MAX(ordering) FROM #__jea_' . $tableName);
				$tablesOrdering[$tableName] = intval($db->loadResult());
			}

			$maxord = $tablesOrdering[$tableName] += 1;
			$query = $db->getQuery(true);
			$query->insert('#__jea_' . $tableName);

			$columns = array('value', 'ordering');

			$values = $db->quote($fieldValue) . ',' . $maxord;

			if ($tableName == 'towns')
			{
				$columns[] = 'department_id';
				$values .= ',' . (int) $parentId;
			}
			elseif ($tableName == 'areas')
			{
				$columns[] = 'town_id';
				$values .= ',' . (int) $parentId;
			}

			if ($language != null)
			{
				$columns[] = 'language';
				$values .= ',' . $query->q($language);
			}

			$query->columns($columns);
			$query->values($values);
			$db->setQuery($query);
			$db->query();
			$id = $db->insertid();

			self::$features[$tableName][$fieldValue] = $id;
		}
		elseif (isset(self::$features[$tableName][$fieldValue]))
		{
			$id = self::$features[$tableName][$fieldValue];
		}
		elseif (is_object($r))
		{
			$id = $r->id;
		}

		return $id;
	}

	/**
	 * Return a feature row if already extis in the database
	 *
	 * @param   string  $tableName   The feature table name
	 * @param   string  $fieldName   The feature field name
	 * @param   string  $fieldValue  The feature field value
	 *
	 * @return  boolean|object The feature row object or false if feature not found
	 */
	protected static function _getJeaRowIfExists ($tableName = '', $fieldName = '', $fieldValue = '')
	{
		if (self::$tables == null)
		{
			$db = JFactory::getDbo();

			self::$tables = array(
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

			foreach (self::$tables as $tableName => $value)
			{
				// Get all JEA datas
				$db->setQuery('SELECT * FROM #__jea_' . $tableName);

				self::$tables[$tableName] = $db->loadObjectList('id');
			}
		}

		if (empty(self::$tables[$tableName]) || empty($fieldName) || empty($fieldValue))
		{
			return false;
		}

		foreach (self::$tables[$tableName] as $row)
		{
			if (! isset($row->$fieldName))
			{
				return false;
			}

			if ($row->$fieldName == $fieldValue)
			{
				return $row;
			}
		}

		return false;
	}

	/**
	 * Get an user. Try to create an user if not found.
	 *
	 * @param   string  $email  The user email
	 * @param   string  $name   The user name
	 *
	 * @return  integer Return the user id. Return 0 if the user cannot be created.
	 */
	public static function getUserId ($email = '', $name = '')
	{
		if (self::$users == null)
		{
			$db = JFactory::getDbo();
			$db->setQuery('SELECT `id`, `email` FROM `#__users`');
			$rows = $db->loadObjectList();

			foreach ($rows as $row)
			{
				self::$users[$row->email] = $row->id;
			}
		}

		if (isset(self::$users[$email]))
		{
			return self::$users[$email];
		}
		else
		{
			$id = self::_createUser($email, $name);

			if ($id != false)
			{
				self::$users[$email] = $id;

				return $id;
			}
		}

		return 0;
	}

	/**
	 * Create an user
	 *
	 * @param   string  $email  The user email
	 * @param   string  $name   The user name
	 *
	 * @return  boolean|number return the user id or false if the user cannot be created
	 */
	protected static function _createUser ($email = '', $name = '')
	{
		if (!JMailHelper::isEmailAddress($email))
		{
			return false;
		}

		$splitMail = explode('@', $email);
		$user = new JUser;

		$params = array(
			'username' => $splitMail[0] . uniqid(),
			'name' => $name,
			'email' => $email,
			'block' => 0,
			'sendEmail' => 0
		);

		$user->bind($params);

		if (true === $user->save())
		{
			return $user->id;
		}

		return false;
	}

	/**
	 * Convert Unix timestamp to MYSQL date
	 *
	 * @param   integer  $timestamp  An UNIX timestamp
	 * @param   boolean  $datetime   If true return MYSQL DATETIME else return MYSQL DATE
	 *
	 * @return  string
	 */
	protected function _convertTimestampToMysqlDate($timestamp, $datetime = true)
	{
		if (is_int($timestamp) && $timestamp > 0)
		{
			if ($datetime)
			{
				return date('Y-m-d H:i:s', $timestamp);
			}

			return date('Y-m-d', $timestamp);
		}

		return '';
	}
}
