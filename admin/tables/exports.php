<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2016 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */


// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Exports table class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 */
class TableExports extends JTable
{
    /**
     * Constructor
     * @param    Database    A database connector object
     */
    public function __construct(&$db)
    {
        parent::__construct('#__jea_exports', 'id', $db);
    }

}