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

/**
 * Utility class helper.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @since       2.0
 */
class JeaHelperUtility
{
	/**
	 * Transform an array to csv strng
	 *
	 * @param   array  $data  An array of data
	 *
	 * @return  string CSV formatted
	 */
	public static function arrayToCSV($data)
	{
		$outstream = fopen("php://temp", 'r+');
		fputcsv($outstream, $data, ';', '"');
		rewind($outstream);
		$csv = fgets($outstream);
		fclose($outstream);

		return $csv;
	}

	/**
	 * Transform csv to array
	 *
	 * @param   string  $data  The CSV string
	 *
	 * @return  array
	 */
	protected function CSVToArray($data)
	{
		$instream = fopen("php://temp", 'r+');
		fwrite($instream, $data);
		rewind($instream);
		$csv = fgetcsv($instream, 9999999, ';', '"');
		fclose($instream);

		return $csv;
	}
}
