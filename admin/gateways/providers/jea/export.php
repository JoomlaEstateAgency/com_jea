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

require_once JPATH_COMPONENT_ADMINISTRATOR . '/gateways/export.php';

jimport('joomla.filesystem.archive');
jimport('joomla.client.ftp');

/**
 * The export class for JEA gateway provider
 *
 * @since  3.4
 */
class JeaGatewayExportJea extends JeaGatewayExport
{
	/**
	 * The directory where to write data
	 *
	 * @var string
	 */
	protected $exportDirectory = '';

	/**
	 * Get the export directory path
	 *
	 * @throws Exception if directory is not found
	 *
	 * @return string The export dir
	 */
	protected function getExportDirectory()
	{
		if (empty($this->exportDirectory))
		{
			$dir = $this->params->get('export_directory');

			if (! JFolder::exists($dir))
			{
				// Try to create dir into joomla root dir
				$dir = JPATH_ROOT . '/' . trim($dir, '/');

				if (JFolder::exists(basename($dir)) && ! JFolder::create($dir))
				{
					throw new Exception(JText::sprintf('COM_JEA_GATEWAY_ERROR_EXPORT_DIRECTORY_CANNOT_BE_CREATED', $dir));
				}
			}

			$this->exportDirectory = $dir;
		}

		return $this->exportDirectory;
	}

	/**
	 * Create the zip file
	 *
	 * @param   string  $filename  The zipfile path to create.
	 * @param   array   &$files    A set of files to be included into the zipfile.
	 *
	 * @throws  Exception if zip file cannot be created
	 * @return  void
	 */
	protected function createZip ($filename, &$files)
	{
		$zipAdapter = JArchive::getAdapter('zip');

		if (! @$zipAdapter->create($filename, $files))
		{
			throw new Exception(JText::sprintf('COM_JEA_GATEWAY_ERROR_ZIP_CREATION', $filename));
		}

		$this->log("Zip creation : $filename");
	}

	/**
	 * Build XML
	 *
	 * @param   string|array  &$data        The data to convert
	 * @param   string        $elementName  The element name
	 *
	 * @return  DOMDocument|DOMElement
	 */
	protected function buildXMl(&$data, $elementName = '')
	{
		static $doc;

		$rootName = 'jea';

		if ($elementName == $rootName)
		{
			$doc = new DOMDocument('1.0', 'utf-8');
		}

		$element = $doc->createElement($elementName);

		if (is_array($data))
		{
			foreach ($data as $key => $value)
			{
				if (is_int($key))
				{
					$childsName = array(
							$rootName => 'property',
							'amenities' => 'amenity',
							'images' => 'image'
					);

					if (! isset($childsName[$elementName]))
					{
						continue;
					}

					$key = $childsName[$elementName];
				}

				$child = $this->buildXMl($value, $key);
				$element->appendChild($child);
			}
		}
		else
		{
			$text = ctype_alnum($data) ? $doc->createTextNode((string) $data) : $doc->createCDATASection((string) $data);
			$element->appendChild($text);
		}

		if ($elementName == $rootName)
		{
			$doc->appendChild($element);

			return $doc;
		}

		return $element;
	}

	/**
	 * Send File over FTP
	 *
	 * @param   string  $file  The file to send
	 *
	 * @throws Exception if FTP transfer fails
	 * @return  void
	 */
	protected function ftpSend($file)
	{
		$ftpClient = new JClientFtp(array('timeout' => 5, 'type' => FTP_BINARY));

		if (! $ftpClient->connect($this->params->get('ftp_host')))
		{
			throw new Exception(JText::sprintf('COM_JEA_GATEWAY_ERROR_FTP_UNABLE_TO_CONNECT_TO_HOST', $this->params->get('ftp_host')));
		}

		if (! $ftpClient->login($this->params->get('ftp_username'), $this->params->get('ftp_password')))
		{
			throw new Exception(JText::_('COM_JEA_GATEWAY_ERROR_FTP_UNABLE_TO_LOGIN'));
		}

		if (! $ftpClient->store($file))
		{
			throw new Exception(JText::_('COM_JEA_GATEWAY_ERROR_FTP_UNABLE_TO_SEND_FILE'));
		}

		$ftpClient->quit();
	}

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
		JText::script('COM_JEA_EXPORT_START_MESSAGE', true);
		JText::script('COM_JEA_EXPORT_END_MESSAGE', true);
		JText::script('COM_JEA_GATEWAY_FTP_TRANSFERT_SUCCESS', true);
		JText::script('COM_JEA_GATEWAY_DOWNLOAD_ZIP', true);

		$document = JFactory::getDocument();
		$script = "jQuery(document).on('registerGatewayAction', function(event, webConsole, dispatcher) {"
				. "    dispatcher.register(function() {"
				. "        JeaGateway.startExport($this->id, '$title', webConsole);"
				. "    });"
				. "});";
		$document->addScriptDeclaration($script);
	}

	/**
	 * The export event handler
	 *
	 * @return void
	 */
	public function export()
	{
		$dir = $this->getExportDirectory();
		$xmlFile = $dir . '/export.xml';
		$zipName = $this->params->get('zip_name', 'jea_export_{{date}}.zip');
		$zipName = str_replace('{{date}}', date('Y-m-d-H:i:s'), $zipName);
		$zipFile = $dir . '/' . $zipName;
		$zipUrl = rtrim($this->_baseUrl, '/') . str_replace(JPATH_ROOT, '', $dir) . '/' . $zipName;

		$files = array();

		$properties = $this->getJeaProperties();

		$exportImages = $this->params->get('export_images');

		foreach ($properties as &$property)
		{
			foreach ($property['images'] as &$image)
			{
				if ($exportImages == 'file')
				{
					$name = $property['id'] . '_' . basename($image['path']);

					$files[] = array(
						'data' => file_get_contents($image['path']),
						'name' => $name
					);

					$image['name'] = $name;
					unset($image['url']);
				}

				unset($image['path']);
			}
		}

		// Init $xmlFile before DOMDocument::save()
		JFile::write($xmlFile, '');

		$xml = $this->buildXMl($properties, 'jea');
		$xml->formatOutput = true;
		$xml->save($xmlFile);

		$files[] = array(
			'data' => file_get_contents($xmlFile),
			'name' => 'export.xml'
		);

		$this->createZip($zipFile, $files);

		$response = array(
			'exported_properties' => count($properties),
			'zip_url' => $zipUrl,
			'ftp_sent' => false
		);

		$message = JText::_('COM_JEA_EXPORT_END_MESSAGE');
		$message = str_replace(
			array('{title}', '{count}'),
			array($this->title, $response['exported_properties']),
			$message
		);

		if ($this->params->get('send_by_ftp') == '1')
		{
			$this->ftpSend($zipFile);
			$response['ftp_sent'] = true;
			$message .= ' ' . JText::_('COM_JEA_GATEWAY_FTP_TRANSFERT_SUCCESS');
		}

		$this->out($message);
		$this->log($message);

		return $response;
	}
}
