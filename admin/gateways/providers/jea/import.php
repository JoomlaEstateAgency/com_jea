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

require_once JPATH_COMPONENT_ADMINISTRATOR . '/gateways/import.php';
require_once JPATH_COMPONENT_ADMINISTRATOR . '/models/propertyInterface.php';

/**
 * The import class for JEA gateway provider
 *
 * @since  3.4
 */
class JeaGatewayImportJea extends JeaGatewayImport
{
	/**
	 * The gateway parser method.
	 *
	 * @return JEAPropertyInterface[]
	 */
	protected function &parse()
	{
		/* @var JEAPropertyInterface[] $properties */
		$properties = array();
		$importDir = $this->params->get('import_directory');

		if (! JFolder::exists($importDir))
		{
			// Maybe a relative path to Joomla ?
			if (! JFolder::exists(JPATH_ROOT . '/' . trim($importDir, '/')))
			{
				throw new Exception(JText::sprintf('COM_JEA_GATEWAY_ERROR_IMPORT_DIRECTORY_NOT_FOUND', $importDir));
			}

			$importDir = JPATH_ROOT . '/' . trim($importDir, '/');
		}

		$tmpDirs = $this->extractZips($importDir);

		if (empty($tmpDirs))
		{
			return $properties;
		}

		foreach ($tmpDirs as $dir)
		{
			$xmlFiles = JFolder::files($dir, '.(xml|XML)$', false, true);

			if (empty($xmlFiles))
			{
				continue;
			}

			$xmlFile = array_shift($xmlFiles);
			$this->parseXML($properties, $xmlFile);
		}

		return $properties;
	}

	/**
	 * Extract Zip files and return extracting directories
	 *
	 * @param   string  $importDir  The directory where to find zip files
	 *
	 * @return  array
	 */
	protected function extractZips($importDir)
	{
		$tmpDirs = array();
		$tmpPath = JFactory::getConfig()->get('tmp_path');

		// Find zip files
		$zips = JFolder::files($importDir, '.(zip|ZIP)$', false, true);

		if (empty($zips))
		{
			throw new Exception(JText::sprintf('COM_JEA_GATEWAY_ERROR_IMPORT_NO_ZIP_FOUND', $importDir));
		}

		// Extract zips
		foreach ($zips as $zipfile)
		{
			$tmpDir = $tmpPath . '/' . basename($zipfile);

			if (! JFolder::create($tmpDir))
			{
				throw new Exception(JText::sprintf('COM_JEA_ERROR_CANNOT_CREATE_DIR', $tmpDir));
			}

			$tmpDirs[] = $tmpDir;

			if (! JArchive::extract($zipfile, $tmpDir))
			{
				throw new Exception(JText::sprintf('COM_JEA_GATEWAY_ERROR_CANNOT_EXTRACT_ZIP', $zipfile));
			}
		}

		return $tmpDirs;
	}

	/**
	 * Xml parser
	 *
	 * @param   array   &$properties  Will be filled with JEAPropertyInterface instances
	 * @param   string  $xmlFile      The xml file path
	 *
	 * @return  void
	 *
	 * @throws Exception if xml cannot be parsed
	 */
	protected function parseXML(&$properties, $xmlFile = '')
	{
		$xml = $this->parseXmlFile($xmlFile);

		// Check root tag
		if ($xml->getName() != 'jea')
		{
			throw new Exception("$xmlFile is not an export from JEA");
		}

		$children = $xml->children();

		foreach ($children as $child)
		{
			if ($child->getName() != 'property')
			{
				continue;
			}

			$fields = $child->children();
			$property = new JEAPropertyInterface;

			$ref = (string) $child->ref;

			// Ref must be unique
			if (isset($properties[$ref]))
			{
				$ref .= '-' . (string) $child->id;
			}

			foreach ($fields as $field)
			{
				$fieldName = $field->getName();

				if ($fieldName == 'id')
				{
					continue;
				}

				if ($fieldName == 'images')
				{
					$images = $field->children();

					foreach ($images as $image)
					{
						if (JFile::exists($currentDirectory . '/' . $image->name))
						{
							$property->images[] = $currentDirectory . '/' . $image->name;
						}
					}

					continue;
				}

				if ($fieldName == 'amenities')
				{
					$amenities = $field->children();

					foreach ($amenities as $amenity)
					{
						$property->amenities[] = (string) $amenity;
					}

					continue;
				}

				$value = (string) $field;

				if ($fieldName == 'ref')
				{
					$value = $ref;
				}

				// Filter values
				if (ctype_digit($value))
				{
					$value = (int) $value;
				}

				if (property_exists($property, $fieldName))
				{
					$property->$fieldName = $value;
				}
				else
				{
					$property->addAdditionalField($fieldName, $value);
				}
			}

			$properties[$ref] = $property;
		}
	}
}
