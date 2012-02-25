<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @version     $Id$
 * @package		Jea.admin
 * @copyright	Copyright (C) 2008 PHILIP Sylvain. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla Estate Agency is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses.
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class FeaturesFactory extends JTable
{
    /**
     * Object constructor
     * 
     * @param string $featuresTable The feature table name
     * @param string $pk The primary key of the table
     * @param JDatabase &$db JDatabase connector object.
     */
    public function __construct($featuresTable, $pk='id', &$db) {
         
        parent::__construct( $featuresTable, $pk, $db );
    }

    /* (non-PHPdoc)
     * @see JTable::getFields()
     */
    public function getFields()
    {
        $fields = $this->_db->getTableColumns($this->_tbl, false);
        if (empty($fields))
        {
            $this->setError(JText::_('JLIB_DATABASE_ERROR_COLUMNS_NOT_FOUND'));
            return false;
        }

        return $fields;
    }

    /* (non-PHPdoc)
     * @see JTable::check()
     */
    public function check()
    {
        //For new insertion
        if (empty($this->id)) {
            $this->ordering = $this->getNextOrder();
        }
        
        return true;
    }
}