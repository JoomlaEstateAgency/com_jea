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
	 * InitWebConsole event handler
	 *
	 * @return void
	 */
	public function initWebConsole()
	{
		JHtml::script('media/com_jea/js/gateway-jea.js', true);
		$title = addslashes($this->title);

		// Register script messages
		JText::script('COM_JEA_IMPORT_START_MESSAGE', true);
		JText::script('COM_JEA_IMPORT_END_MESSAGE', true);
		JText::script('COM_JEA_GATEWAY_PROPERTIES_FOUND', true);
		JText::script('COM_JEA_GATEWAY_PROPERTIES_CREATED', true);
		JText::script('COM_JEA_GATEWAY_PROPERTIES_UPDATED', true);
		JText::script('COM_JEA_GATEWAY_PROPERTIES_DELETED', true);

		$script = "jQuery(document).on('registerGatewayAction', function(event, webConsole, dispatcher) {"
				. "    dispatcher.register(function() {"
				. "        JeaGateway.startImport($this->id, '$title', webConsole);"
				. "    });"
				. "});";

		$document = JFactory::getDocument();
		$document->addScriptDeclaration($script);
	}

	/**
	 * The gateway parser method.
	 *
	 * @return JEAPropertyInterface[]
	 */
	protected function &parse()
	{
		/* @var JEARowInterface[] $properties */
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
		libxml_use_internal_errors(true);
		$xml = simplexml_load_file($xmlFile, 'SimpleXMLElement', LIBXML_PARSEHUGE);
		$currentDirectory = dirname($xmlFile);

		if (! $xml)
		{
			$msg = "Cannot load : $xmlFile. ";
			$errors = libxml_get_errors();

			foreach ($errors as $error)
			{
				switch ($error->level)
				{
					case LIBXML_ERR_WARNING:
						$msg .= "Warning $error->code: ";
						break;
					case LIBXML_ERR_ERROR:
						$msg .= "Err Error $error->code: ";
						break;
					case LIBXML_ERR_FATAL:
						$msg .= "Fatal Error $error->code: ";
						break;
				}

				$msg .= trim($error->message) . " -  Line: $error->line" . " -  Column: $error->column";

				if ($error->file)
				{
					$msg .= "  File: $error->file";
				}
			}

			libxml_clear_errors();
			throw new Exception($msg, $error->code);
		}

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
