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

require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'library/Gd/Color.php';

class Gd_Abstract 
{
	/** Holds the image resource for manipulation
	*@var resource $image 
	*/
	var $image = null;
	
	/** Holds the original image file
	*@var resource $image 
	*/
	var $old_image = null;
	
	var $imageSrc = '';
	var $type = 'png';
	
	var $width=0;
	var $height=0;

	var $_defaultColor = null;
	
	var $_supported_format = array();
	
	var $_memory_limit = '32M';
    
    var $_error = '' ;
	
	function Gd_Abstract()
	{
		if (!( extension_loaded('gd') || extension_loaded('gd2')) ) {
            die ('GD is not installed on your system.');
		}
		
		$types = imagetypes();
		
		if ($types & IMG_PNG) {
			$this->_supported_format['png'] = 'rw' ;
		}
		
		if (($types & IMG_GIF) || function_exists('imagegif')) {
			$this->_supported_format['gif'] = 'rw';	
		}
		
		if ($types & IMG_JPG) {
			$this->_supported_format['jpeg'] = 'rw';
		}
		
		if (empty($this->_supported_format)) {
			die ('No supported image types available');
		}
		
		//try to increase memory
		ini_set( 'memory_limit' , '32M' );
		
	}
    
    
    function getError()
    {
        return $this->_error;
    }
    
    function _setError($error)
    {
        $this->_error = $error ;
        return false;
    }
    
	function load($src)
	{
		if(!file_exists($src)){
            return $this->_setError("Wrong path or file doesn't exists.");
		}
		
		if (is_resource($this->image)) imagedestroy($this->image);
		
		$this->getDetails(@getimagesize($src));
		
		$imagecreatefromtype = 'imagecreatefrom'.$this->type ;
		$this->image = $imagecreatefromtype($src);
		$this->imageSrc = $src ;
        return true;
	}   
	
	function createNewImage($width, $height)
	{
		if ( !( is_numeric($width) && is_numeric($height))  ){
			return $this->_setError('Wrong image format');
		}
		if (is_resource($this->image)) imagedestroy($this->image);
		
		$this->width = $width ;
		$this->height = $height ;
		$this->image = imagecreatetruecolor($this->width, $this->height);
		$bgcolor = imagecolorallocate($this->image, 255, 255, 255);
		imagefill($this->image, 0, 0, $bgcolor);
        return true;
	}
	
	function createNewImageWithTtfText($text, $font_file, $fontsize, $color='000000' )
	{
	    $bbox = imagettfbbox ( $fontsize, 0, $font_file, $text );
	    //print_r($bbox);
	    $width = abs($bbox[0]) + abs($bbox[2]); // distance from left to right
	    $height = abs($bbox[1]) + abs($bbox[5]); // distance from top to bottom
	    
	    $this->createNewImage($width, $height);
	    
	    $xpos = abs($bbox[0])-1;
	    $ypos = abs($bbox[7])-1;
	    
	    $this->addTtfText($text, $font_file, $fontsize, $color, $xpos, $ypos, 0 );

	}

	
	function addTtfText($text='',$font_file, $fontsize='2', $color, $x=0, $y=0, $angle=0 )
	{
	   $textcolor =  $this->_getRVBColor($color);
	   return imagettftext( $this->image, $fontsize , $angle, $x, $y, $textcolor, $font_file, $text );
	}

	
	function getSize($side='')
    {
		if ($side == 'width') return $this->width ;
		if ($side == 'height') return $this->height ;
		return array('w'=>$this->width,'h'=>$this->height) ;
	}


	// Affichage de l'image
	function render($type='')
	{
		if (!($type == 'png' || $type == 'jpeg' || $type == 'gif')){
			$type = $this->type ;
		}
		header("Content-type: image/{$type}");
		$gdfunction = 'image'.$type ;
		$gdfunction ($this->image);
		imagedestroy($this->image);
	}
	
	//enregistremment de l'image
	 
	function save($destination='', $type='')
	{
		if (!($type == 'png' || $type == 'jpeg' || $type == 'gif')){
			$type = $this->type ;
		}
		if(empty($destination)){
			if (!empty($this->imageSrc )){
				$destination = $this->imageSrc;
			} else {
				return $this->_setError("No destination to save image.");
			}
		}
		$gdfunction = 'image'.$type ;
		if (! $gdfunction($this->image, $destination)){
			return $this->_setError("Fail to save image.");
		}
		imagedestroy($this->image);
        return true;
	}
	
	//special jpeg
	function saveToJpeg($destination='', $quality=100)
	{
		if(empty($destination)){
			if (!empty($this->imageSrc )){
				$destination = $this->imageSrc;
			} else {
				return $this->_setError ("No destination to save image.");
			}
		}
		
		if (! imagejpeg($this->image, $destination, $quality)){
			return $this->_setError ("Fail to save image.");
		}
		imagedestroy($this->image);
        return true;
	}
	
	
	function getDetails($infos)
	{
		if (!is_array($infos)) {
			return $this->_setError ("Cannot fetch image or images details.");
		}
		 switch ($infos[2]) {
            case IMAGETYPE_GIF:
                $this->type = 'gif';
                break;
            case IMAGETYPE_JPEG:
                $this->type = 'jpeg';
                break;
            case IMAGETYPE_PNG:
                $this->type = 'png';
                break;
			default :
				return $this->_setError ("Wrong image format");
		}
		
		$this->width = $infos[0];
		$this->height = $infos[1];
        return true;
	}
	
	function _getRVBColor($hexColor)
	{
	    $color = new Gd_Color($hexColor);
		$rvb = $color->getRVB();
	    return imagecolorallocate($this->image, $rvb['R'], $rvb['V'], $rvb['B']) ;
	}

}
?>