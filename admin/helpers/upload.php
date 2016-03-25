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

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

/**
 * Upload class helper.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 */
class JeaUpload
{

    /**
     * The upload key (ex: $files[0][{picture}] or $files[0][{1}] or $files[0][{picture1}])
     * @var unknown_type
     */
    public $key = '';

    public $name = '';
    public $temp_name = '';
    public $type = '';
    public $error = false;

    /**
     * Errors list
     * @var array
     */
    protected $_errors = array();

    /**
     * It's a common security risk in pages who has the upload dir
     * under the document root
     *
     * @var array
     * @see JeaUpload::setValidExtensions()
     */
    protected $_extensionsCheck = array('php', 'phtm', 'phtml', 'php3', 'inc');

    /**
     * @see HTTP_Upload_File::setValidExtensions()
     * @var string
     */
    protected $_extensionsMode  = 'deny';


    /**
     * Constructor
     * @param array $params
     */
    public function __construct($params)
    {
        $this->name      = isset($params['name']) ? $params['name'] : '' ;
        $this->temp_name = isset($params['tmp_name']) ? $params['tmp_name'] : '';
        $this->type      = isset($params['type']) ? $params['type'] : '' ;
        $this->error     = isset($params['error']) ? (int) $params['error'] : UPLOAD_ERR_NO_FILE ;
    }


    /**
     * Return one or multiple instances of JeaUpload
     *
     * @param string $name The name of the posted file
     * @return mixed
     */
    public static function getUpload($name='')
    {
        $rawUploaded = JRequest::getVar($name, array(), 'files', 'array');

        if (is_array($rawUploaded['name'])) {
             
            $fields = array('name', 'type', 'tmp_name', 'error', 'size');
            $arrUploaded = array();
            $keys = array_keys($rawUploaded['name']);

            foreach ($keys as $key) {
                $params = array();
                foreach ($fields as $field) {
                    $params[$field] = $rawUploaded[$field][$key];
                }

                $uploaded = new JeaUpload($params);
                $uploaded->key = $key;
                $arrUploaded[] = $uploaded;
            }
             
            return $arrUploaded;

        } else { // Single post
            $uploaded = new JeaUpload($rawUploaded);
            $uploaded->key = $name;
            return $uploaded;
             
        }
    }


    /**
     * Verify if the file was uploaded
     * @return boolean
     */
    public function isPosted()
    {
        if ( $this->error === UPLOAD_ERR_NO_FILE ) {
            return false;
        }

        return true;
    }


    /**
     * Check errors
     * @return JeaUpload
     */
    public function check()
    {
        if ($this->error !== UPLOAD_ERR_OK) {
            switch ($this->error) {
                case UPLOAD_ERR_INI_SIZE :
                case UPLOAD_ERR_FORM_SIZE :
                    $this->_errors[] = 'COM_JEA_UPLOAD_ERR_SIZE';
                    break;
                case UPLOAD_ERR_PARTIAL :
                    $this->_errors[] = 'COM_JEA_UPLOAD_ERR_PARTIAL';
                    break;
                case UPLOAD_ERR_NO_TMP_DIR :
                    $this->_errors[] = 'COM_JEA_UPLOAD_ERR_NO_TMP_DIR';
                    break;
                case UPLOAD_ERR_CANT_WRITE :
                    $this->_errors[] = 'COM_JEA_UPLOAD_ERR_CANT_WRITE';
                    break;
                default:
                    $this->_errors[] = 'COM_JEA_UPLOAD_UNKNOWN_ERROR';
            }
        }

        //Valid extensions check
        if (!$this->_evalValidExtensions()) {
            $this->_errors[] ='COM_JEA_UPLOAD_FILE_EXTENSION_NOT_PERMITTED';
        }

        return $this;
    }


    /**
     * Get the errors list
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }


    /**
     * Set the file name ...
     * @param string $name
     *
     * @return JeaUpload
     */
    public function setName($name)
    {
        // Verify extention before change the filename
        if ($this->_evalValidExtensions()){
            $this->name = $name;
        }

        return $this;
    }


    /**
     * Moves the uploaded file to its destination directory.
     *
     * @param    string  $dir  Destination directory
     * @param    boolean $overwrite Overwrite if destination file exists?
     * @return   boolean true on success or false on error
     */
    public function moveTo($dir='', $overwrite = true)
    {
        $this->check();

        if (!JFolder::exists($dir)) {
            $this->_errors[] = 'COM_JEA_UPLOAD_DESTINATION_DIRECTORY_DOESNT_EXISTS';
        }
         
        if (!is_writable($dir)) {
            $this->_errors[] = 'COM_JEA_UPLOAD_DESTINATION_DIRECTORY_NOT_WRITABLE';
        }

        $file = $dir.DS.$this->name;

        if (JFile::exists($file)) {
            if ($overwrite === false) {
                $this->_errors[] = 'COM_JEA_UPLOAD_DESTINATION_FILE_ALREADY_EXISTS';
            } elseif (!is_writable($file )) {
                $this->_errors[] = 'COM_JEA_UPLOAD_DESTINATION_FILE_NOT_WRITABLE';
            }
        }

        if (empty($this->_errors)) {
            return JFile::upload($this->temp_name, $file);
        }

        return false;
    }


    /**
     * Format file name to be safe
     *
     * @param    int    $maxlen Maximun permited string lenght
     * @return  JeaUpload
     *
     */
    public function nameToSafe($maxlen=250)
    {
        $this->name = substr($this->name, 0, $maxlen);
        $this->name = JFile::makeSafe($this->name);
        return $this;
    }


    /**
     * Function to restrict the valid extensions on file uploads
     *
     * @param array $exts File extensions to validate
     * @param string $mode The type of validation:
     *                       1) 'deny'   Will deny only the supplied extensions
     *                       2) 'accept' Will accept only the supplied extensions as valid
     * @return JeaUpload
     */
    public function setValidExtensions($exts, $mode = 'accept')
    {
        $this->_extensionsCheck = $exts;
        $this->_extensionsMode  = $mode;
        return $this;
    }


    /**
     * Evaluates the validity of the extensions set by setValidExtensions
     *
     * @return bool False on non valid extension, true if they are valid
     */
    protected function _evalValidExtensions()
    {
        $extension = JFile::getExt($this->name);

        if ($this->_extensionsMode == 'deny') {
            if (!in_array($extension, $this->_extensionsCheck)) {
                return true;
            }

        } else {
            if (in_array($extension, $this->_extensionsCheck)) {
                return true;
            }
        }

        return false;
    }

}
