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

class Gd_Color {

	var $rvb = array();

	function Gd_Color($hexacolor)
	{
		if ( ctype_xdigit($hexacolor) && strlen($hexacolor) == 6 ){
			$this->rvb['R'] = hexdec($hexacolor[0].$hexacolor[1]); //red conversion
	 		$this->rvb['V'] = hexdec($hexacolor[2].$hexacolor[3]); //green conversion
	  		$this->rvb['B'] = hexdec($hexacolor[4].$hexacolor[5]); //blue conversion
		} else {
			die("Wrong hexa color");
		}
	}

	function getRVB()
	{
		return $this->rvb ;
	}

	function getHexa()
	{
		return dechex($this->rvb['R']).dechex($this->rvb['V']).dechex($this->rvb['B']) ;
	}


	/* Adjust color brightness : 0 to 100 (%)
	/* @param int $coef
	*/
	function brightness ( $coef )
	{
		$rvb = $this->getRVB();
		foreach($rvb as $color => $value ){
			if ($coef >= 100) $this->rvb[$color] = 255;
			else if($coef <= 0)   $this->rvb[$color] = 0;
			else $this->rvb[$color] = round(($value*$coef)/100) ;
		}
		return $this->getRVB();
	}

}
