<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 * 
 * @package		Jea.library
 * @copyright	Copyright (C) 2008 PHILIP Sylvain. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla Estate Agency is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses.
 * 
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class Http_File
{
	var $name = '';
	var $temp_name = '';
	var $type = '';
    var $_error = false;
    var $_error_code = 0;

	/**
	* Contains the desired chmod for uploaded files
	* @var int
	* @access private
	*/
	var $_chmod = 0644; //rw-r--r--

	/**
	 * It's a common security risk in pages who has the upload dir
	 * under the document root (remember the hack of the Apache web?)
	 *
	 * @var array
	 * @access private
	 * @see HTTP_Upload_File::setValidExtensions()
	 */
	var $_extensions_check = array('php', 'phtm', 'phtml', 'php3', 'inc');

	/**
	 * @see HTTP_Upload_File::setValidExtensions()
	 * @var string
	 * @access private
	 */
	var $_extensions_mode  = 'deny';

	function Http_File( $params )
	{
		$this->name = isset( $params['name'] ) ? $params['name'] : '' ;
		$this->temp_name = isset( $params['tmp_name'] ) ? $params['tmp_name'] : '';
		$this->type = isset( $params['type'] ) ? $params['type'] : '' ;
		$this->_error_code = isset( $params['error'] ) ? $params['error'] : UPLOAD_ERR_NO_FILE ;	
	}
    
	function isPosted()
	{
		if ( $this->_error_code === UPLOAD_ERR_NO_FILE ) return false;
		
		return true;
	}
	
    function getError()
    {
        
		return $this->_error;
    }
    
    function getMaxSise()
    {
        return ini_get('upload_max_filesize');
    }
    
    
    function _setError($error)
    {
        $this->_error = $error ;
        return false;
    }

	function setName($name)
	{
		//verify extention before change the filename
        if ($this->_evalValidExtensions()){
            $this->name = $name;
        }
	}


	/**
	 * Moves the uploaded file to its destination directory.
	 *
	 * @param    string  $dir_dest  Destination directory
	 * @param    bool    $overwrite Overwrite if destination file exists?
	 * @return   mixed   True on success or Pear_Error object on error
	 * @access public
	 */
	function moveTo($dir_dest='', $overwrite = true)
	{
		if ( $this->_error_code !== UPLOAD_ERR_OK ) {
		    
		    switch ( $this->_error_code ) {
		        case UPLOAD_ERR_INI_SIZE :
		            return $this->_setError('UPLOAD_ERR_INI_SIZE');
		            break;
		        case UPLOAD_ERR_FORM_SIZE :
		            return $this->_setError('UPLOAD_ERR_INI_SIZE');
		            break;
		       	case UPLOAD_ERR_FORM_SIZE :
		            return $this->_setError('UPLOAD_ERR_INI_SIZE');
		            break;
		        case UPLOAD_ERR_PARTIAL :
		            return $this->_setError('UPLOAD_ERR_PARTIAL');
		            break;
		        case UPLOAD_ERR_FORM_SIZE :
		            return $this->_setError('UPLOAD_ERR_NO_TMP_DIR');
		            break;
		        case UPLOAD_ERR_FORM_SIZE :
		            return $this->_setError('UPLOAD_ERR_INI_SIZE');
		            break;		        
		    }
		    
		}
		

		if (!@is_dir ($dir_dest)) {
			return $this->_setError( 'The destination directory doesn\'t exist or is a regular file' );
		}

		//Valid extensions check
		if (!$this->_evalValidExtensions()) {
			return $this->_setError( 'File extension not permitted');
		}


		if (!is_writable($dir_dest)) {
			return $this->_setError('The destination directory doesn\'t have write perms');
		}

		$name_dest = $dir_dest . DIRECTORY_SEPARATOR . $this->name;

		if ( @is_file( $name_dest ) ) {
			if ($overwrite === false) {
				return $this->_setError( 'The destination file already exists' );
			} elseif ( !is_writable($name_dest )) {
				return $this->_setError( 'The destination file already exists and could not be overwritten');
			}
		}

		// copy the file and let php clean the tmp
		if (!@move_uploaded_file($this->temp_name, $name_dest)) {
			return $this->_setError('Impossible to move the file');
		}
		@chmod($name_dest, $this->_chmod);
		return $this->name;
	}


	/**
	 * Format file name to be safe
	 *
	 * @param    int    $maxlen Maximun permited string lenght
	 * @return   string Formatted file name
	 *
	 */
	function nameToSafe($maxlen=250)
	{
		$noalpha = '�����������������������������������������������������@���';
		$alpha   = 'AEIOUYaeiouyAEIOUaeiouAEIOUaeiouAEIOUaeiouyAaOoAaNnCcaooa';

		$this->name = substr($this->name, 0, $maxlen);
		$this->name = strtr($this->name, $noalpha, $alpha);
		// not permitted chars are replaced with "_"
		$this->name = preg_replace('/[^a-zA-Z0-9,._\+\()\-]/', '_', $this->name);
		return $this->name ;
	}


	/**
	 * Function to restrict the valid extensions on file uploads
	 *
	 * @param array $exts File extensions to validate
	 * @param string $mode The type of validation:
	 *                       1) 'deny'   Will deny only the supplied extensions
	 *                       2) 'accept' Will accept only the supplied extensions
	 *                                   as valid
	 * @access public
	 */
	function setValidExtensions($exts, $mode = 'accept')
	{
		$this->_extensions_check = $exts;
		$this->_extensions_mode  = $mode;
	}

	/**
	 * Evaluates the validity of the extensions set by setValidExtensions
	 *
	 * @return bool False on non valid extension, true if they are valid
	 * @access private
	 */
	function _evalValidExtensions()
	{
		$exts = $this->_extensions_check;
		settype($exts, 'array');
		if ($this->_extensions_mode == 'deny') {
			if (in_array($this->_getExtension(), $exts)) {
				return false;
			}
		// mode == 'accept'
		} else {
			if (!in_array($this->_getExtension(), $exts)) {
				return false;
			}
		}
		return true;
	}

	function _getExtension()
	{
		 $ext = false;
		 if (($pos = strrpos($this->name, '.')) !== false) {
				$ext = substr($this->name, $pos + 1);
		 }
		 return $ext;
	}

}

?>