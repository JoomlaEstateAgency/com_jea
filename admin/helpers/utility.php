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
defined('_JEXEC') or die;

/**
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 */

class JeaHelperUtility
{

    public static function arrayToCSV($data)
    {
        $outstream = fopen("php://temp", 'r+');
        fputcsv($outstream, $data, ';', '"');
        rewind($outstream);
        $csv = fgets($outstream);
        fclose($outstream);
        return $csv;
    }

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