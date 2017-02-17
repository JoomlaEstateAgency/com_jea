<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2017 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

require_once JPATH_COMPONENT_ADMINISTRATOR . '/gateways/export.php';

jimport('joomla.filesystem.archive');
jimport('joomla.client.ftp');


class JeaGatewayExportJea extends JeaGatewayExport
{
    /**
     * Data array of properties
     * 
     * @var array
     */
    protected $_data = array();

    /**
     * The directory where to write data 
     * 
     * @var string
     */
    protected $_exportDirectory='';


    protected function getExportDirectory()
    {
        if (empty($this->_exportDirectory)) {


            $dir = $this->params->get('export_directory');

            if (!JFolder::exists($dir)) {

                // Try to create dir into joomla root dir
                $dir = JPATH_ROOT . '/' . trim($dir, '/');

                if (JFolder::exists(basename($dir)) && !JFolder::create($dir)) {
                    throw new Exception(JText::sprintf('COM_JEA_GATEWAY_ERROR_EXPORT_DIRECTORY_CANNOT_BE_CREATED', $dir));
                }
            }

            $this->_exportDirectory = $dir;


        }

        return $this->_exportDirectory;
    }

    /**
     * Create the zip file
     *
     * @return void
     * @throws Exception if zip file cannot be created
     */
    protected function createZip($filename, &$files)
    {
        $zipAdapter = JArchive::getAdapter('zip');

        if (!@$zipAdapter->create($filename, $files)) {
            throw new Exception(JText::sprintf('COM_JEA_GATEWAY_ERROR_ZIP_CREATION', $filename));
        }

        $this->log("Zip creation : $filename");
    }


    /**
     * Build XML
     *
     * @param mixed $data            
     * @param string $elementName            
     * @return DOMDocument|DOMElement
     */
    protected function buildXMl(&$data, $elementName = '')
    {
        static $doc;

        $rootName = 'jea';

        if ($elementName == $rootName) {
            $doc = new DOMDocument('1.0', 'utf-8');
        }

        $element = $doc->createElement($elementName);

        if (is_array($data)) {

            foreach ($data as $key => $value) {

                if (is_int($key)) {

                    $childsName = array (
                        $rootName   => 'property',
                        'amenities' => 'amenity',
                        'images'    => 'image'
                    );

                    if (!isset($childsName[$elementName])) {
                        continue;
                    }

                    $key = $childsName[$elementName];
                }

                $child = $this->buildXMl($value, $key);
                $element->appendChild($child);
            }

        } else {

            $text = ctype_alnum($data) ? 
                    $doc->createTextNode((string) $data) : 
                    $doc->createCDATASection((string) $data);

            $element->appendChild($text);
        }

        if ($elementName == $rootName) {
            $doc->appendChild($element);
            return $doc;
        }

        return $element;
    }

    /**
     * Send File to FTP
     *
     * @var $file string the file path to send
     * @return void
     * @throws Exception si le fichier zip n'a pas été trouvé ou si le transfert à échoué
     */
    protected function ftpSend($file)
    {
        $ftpClient = new JClientFtp(array(
            'timeout' => 5,
            'type' => FTP_BINARY
        ));

        if (!$ftpClient->connect($this->params->get('ftp_host'))) {
            throw new Exception(JText::sprintf('COM_JEA_GATEWAY_ERROR_FTP_UNABLE_TO_CONNECT_TO_HOST', $this->params->get('ftp_host')));
        }

        if (!$ftpClient->login($this->params->get('ftp_username'), $this->params->get('ftp_password'))) {
            throw new Exception(JText::_('COM_JEA_GATEWAY_ERROR_FTP_UNABLE_TO_LOGIN'));
        }

        if (!$ftpClient->store($file)) {
            throw new Exception(JText::_('COM_JEA_GATEWAY_ERROR_FTP_UNABLE_TO_SEND_FILE'));
        }

        $ftpClient->quit();
    }


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
        $document->addScriptDeclaration("
        jQuery(document).on('registerGatewayAction', function(event, webConsole, dispatcher) {
            dispatcher.register(function(){
                JeaGateway.startExport($this->id, '$title', webConsole)
            })
        })");
    }


    public function export($output=null)
    {
        $dir = $this->getExportDirectory();
        $xmlFile = $dir . '/export.xml';
        $zipName = $this->params->get('zip_name', 'jea_export_{{date}}.zip');
        $zipName = str_replace('{{date}}', date('Y-m-d-H:i:s'), $zipName);
        $zipFile = $dir . '/' . $zipName;
        $zipUrl  = rtrim($this->_baseUrl, '/') . str_replace(JPATH_ROOT, '', $dir) . '/' . $zipName;

        $files = array();

        $properties = $this->getJeaProperties();

        $exportImages = $this->params->get('export_images');

        foreach ($properties as &$property) {

            foreach ($property['images'] as &$image) {

                if ($exportImages == 'file') {

                    $name = $property['id'] . '_' . basename($image['path']);
                    $files[] = array('data' => file_get_contents($image['path']), 'name'=> $name);
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

        $files[] = array('data' => file_get_contents($xmlFile), 'name'=> 'export.xml');

        $this->createZip($zipFile, $files);

        $response = array(
            'exported_properties' => count($properties),
            'zip_url' => $zipUrl,
            'ftp_sent' => false
        );

        $message = JText::_('COM_JEA_EXPORT_END_MESSAGE');
        $message = str_replace(array('{title}', '{count}'), array($this->title, $response['exported_properties']), $message);

        if ($this->params->get('send_by_ftp') == '1') {
            $this->ftpSend($zipFile);
            $response['ftp_sent'] = true;
            $message .= ' ' . JText::_('COM_JEA_GATEWAY_FTP_TRANSFERT_SUCCESS');
        }

        $this->out($message);
        $this->log($message);

        return $response;

    }
}
