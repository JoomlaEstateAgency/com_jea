<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 * 
 * @version		0.1 2008-02-26
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

require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'library/Gd/Abstract.php';

class Gd_Transform extends Gd_Abstract
{
	
	function Smoos_Gd_Transform ()
    {
        $this->Gd_Abstract();
    }
    
    function resize ($width=NULL, $height=NULL)
	{
		
		if (!$height && $width > 0){
			$newWidth = $width;
			$newHeight = round(($width/$this->width)*$this->height);
			
		} elseif (!$width && $height >0 ){
			$newHeight = $height;
			$newWidth = round(($height/$this->height)*$this->width);
			
		} elseif ($width > 0 && $height > 0){
			$newHeight = $height;
			$newWidth = $width;
			
		} else {
			return false ;
		}
		
		$newimg = imagecreatetruecolor ($newWidth, $newHeight);
		
		if (function_exists('imagecopyresampled')){
			imagecopyresampled($newimg, $this->image, 0, 0, 0, 0, $newWidth,
										$newHeight, $this->width, $this->height);
		} else {
			imagecopyresized($newimg, $this->image, 0, 0, 0, 0, $newWidth,
										$newHeight, $this->width, $this->height);
		}
		$this->width = $newWidth;
		$this->height = $newHeight;
		imagedestroy($this->image);
		$this->image = $newimg ;
		return true;
	}
	
	
	function crop($width, $height, $x = 0, $y = 0)
	{
		$width   = min($width ,  $this->width - $x );
		$height  = min($height , $this->height - $y);
		$new_img = imagecreatetruecolor($width, $height);
		
		if (!imagecopy($new_img, $this->image, 0, 0, $x, $y, $width, $height)) {
			return $this->_setError("Failed transformation: crop().");
		}
		$this->width = $width;
		$this->height = $height;
		imagedestroy($this->image);
		$this->image = $new_img;
		return true;
	}
    
    function centerCrop($width, $height)
    {
        $y = ($this->height > $height )? round(($this->height - $height) /2 ) : 0 ;
        $x = ($this->width > $width )? round(($this->width - $width) /2 ) : 0 ;
        return $this->crop($width, $height, $x, $y );
    }
    
    function rotate($angle, $bgcolor)
    {
        $color =  $this->_getRVBColor($bgcolor);
        $this->image = imagerotate ( $this->image, $angle, $color );
    }

}
?>