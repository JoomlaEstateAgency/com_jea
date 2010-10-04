<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 * 
 * @version     $Id$
 * @package     Jea.admin
 * @copyright   Copyright (C) 2008 PHILIP Sylvain. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 * Joomla Estate Agency is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses.
 * 
 */

// no direct access
defined('_JEXEC') or die();

jimport('joomla.application.component.model');

class JeaModelFeatures extends JModel
{
	
	/**
	* Name of the current displayed table.
	*
	* @var string
	*/
	var $_currentTableName = '' ;
	
	var $_lastId = 0;
	
	function getId()
	{
		//First loooking for new insertion
		if ($this->_lastId > 0) {
			return $this->_lastId ;
		}
		
		$cid = $this->getCid();
		
		if (empty($cid[0])) {
			//try to see id
			return JRequest::getInt('id', 0);
		}
		
		return $cid[0] ;
	}
	
	function getCid()
	{
		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
		JArrayHelper::toInteger( $cid, array(0) );
		
		return $cid ;
	}
	
	function &getRow()
	{
		$table =& $this->getTable();
		$table->load( $this->getId() );

		return $table;
	}
	
	function &getTable()
	{
		static $tables = array();
		
		$tableName = $this->getTableName() ;
		
		if (!isset($tables[$tableName])) {
			$tables[$tableName] =& parent::getTable( ucfirst( $tableName ) );
		}

		return $tables[$tableName]  ;
	}
	
	function getSqlTableName()
	{
		$table =& $this->getTable();
	    return $table->getTableName();
	}
	
	
	function setTableName( $tableName )
	{
		$tables = array(
			    'types', 
			    'conditions', 
			    'departments', 
			    'slogans', 
			    'towns', 
			    'areas', 
			    'advantages',
		        'heatingtypes',
			    'hotwatertypes'
			);
			
		if( !in_array( $tableName, $tables ) ){
			    
			    JError::raiseWarning( 200, 'table name : ' . $tableName . 'doesn\'t exists' );
				return false;
		}
		
		$this->_currentTableName =  $tableName ;
		
		
		return $this->_currentTableName ;
	}
	
	
	function getTableName()
	{
		if ( empty($this->_currentTableName) ) {
		
			$mainframe = &JFactory::getApplication();		
			$tableName = $mainframe->getUserStateFromRequest( 'com_jea.feature', 'table', 'types', 'word' );
			$this->setTableName( $tableName );
		}
		
		return $this->_currentTableName ;
	}
	
	
	function getItems($all=false)
	{
		$result = array();
		$mainframe = &JFactory::getApplication();
		
		$context = 'com_jea.characteristics.' . $this->getTableName() ;
		$default_limit = $mainframe->getCfg('list_limit');

		$limit      = $mainframe->getUserStateFromRequest( $context.'limit', 'limit',  $default_limit, 'int' );
	    $limitstart = $mainframe->getUserStateFromRequest( $context.'offset', 'limitstart', 0, 'int' );
	    
	    
	    $sql = 'SELECT id, value, ordering FROM ' . $this->getSqlTableName() . ' ORDER BY ordering' ;
		
		$result['total'] = $this->_getListCount( $sql );
		
	    if( $all === true ){
	    	
			$result['limitstart'] = 0 ;
			$result['limit'] = 0 ; 
			$result['rows'] = $this->_getList( $sql);
			
		} else {
			
			$result['limitstart'] = $limitstart ;
			$result['limit'] = $limit ; 
	        $result['rows'] = $this->_getList( $sql , $limitstart, $limit );
		}
		
		if ( $this->_db->getErrorNum() ) {
	            JError::raiseWarning( 200, $this->_db->getErrorMsg() );
	            return false;
	    }
	         
	    return $result ;
	}
	
    function getListForHtml( $first_txt='', $orderby='ordering', $where='' ){
		
		if(!empty($where)){
			$where = 'WHERE ' . $where;
		}
		
		$sql = 'SELECT `id` AS value ,`value` AS text FROM ' . $this->getSqlTableName()
             . ' ' . $where . ' ORDER BY ' . $orderby ;
             
        $rows = $this->_getList( $sql );

        if ( $this->_db->getErrorNum() ) {
            
            JError::raiseWarning( 200, $this->_db->getErrorMsg() );
        }
		
		//unshift default option
		array_unshift($rows, JHTML::_('select.option', '0', $first_txt ));
		
		return $rows ;
	}
	
	
	function save()
	{
		$row = & $this->getRow();
		
		$row->value = JRequest::getVar( 'value', '' ) ;
		
	    if($department_id = JRequest::getInt( 'department_id', '' )){
		    $row->department_id = $department_id ;
		}
		
	    if($town_id = JRequest::getInt( 'town_id', '' )){
		    $row->town_id = $town_id ;
		}
		
		if( !$row->id ){
			// save new item at the end of ordering
			$row->ordering = $row->getNextOrder();
		}

        if ( !$row->store() ) {
            JError::raiseWarning( 200, $row->getError() );
            return false;
        }
        
        $this->_lastId = $row->id;
        return true;
		
	}
	
	function remove()
	{
		$cids = implode( ',', $this->getCid() );
		
		//only one request
		$this->_db->setQuery( 'DELETE FROM `'. $this->getSqlTableName() .'` WHERE id IN (' . $cids . ')' );
		
		if ( !$this->_db->query() ) {
			JError::raiseError( 500, $this->_db->getErrorMsg() );
			return false;
		}
		
		return true;
	}
	
	function getCSVData($tableName='')
	{
	    $this->setTableName($tableName);
	    $table      =& $this->getTable();
	    $cols_names = array_keys($table->getPublicProperties());
        $parser     =& $this->_getCSVParser();
	    $this->_db->setQuery('SELECT * FROM '. $this->getSqlTableName());
        return $parser->unparse($this->_db->loadAssocList(), $cols_names);
	}
	
	/**
	 * Import rows from CSV file and return the number of inserted rows
	 *
	 * @param string $file
	 * @param string $tableName
	 * @return int
	 */
	
	function importFromCSV($file='', $tableName='') 
	{
	    jimport('joomla.filesystem.file');
	    if(!JFile::exists($file)) {
	        JError::raiseWarning( 'file', JText::_('File doesn\'t exist') );
	        return 0;
	    }
	    
	    $this->setTableName($tableName);
	    $table =& $this->getTable();
        $empty_row = $table->getPublicProperties();
        $array_backQuotes = create_function('&$v,$k', '$v = \'`\'.$v .\'`\';');
        $cols_names = array_keys($empty_row);
        array_walk($cols_names, $array_backQuotes);
        $parser =& $this->_getCSVParser();
        $parser->parse($file);
        
	    $rows = $this->_getList('SELECT * FROM '. $this->getSqlTableName());
	    $ids = array();
	    foreach($rows as $row) {
	        $ids[$row->id] = $row;
	    }
	    
	    $insert_with_ids = array();
	    $insert_without_ids = array();
	    
	    foreach($parser->data as $row) {
	        $raw_line = array_merge($empty_row, $row);
	        if(empty($raw_line['value'])) {
	            // if value is empty, we don't need to import row
	            continue;
	        }
	        
	        $row_value = trim($raw_line['value']);
	        $row_id = 0;
	        
	        if(isset($raw_line['id'])) {
	            $row_id = intval($raw_line['id']);
	        }
	        
	        foreach($raw_line as $k=> $v) {
	            $raw_line[$k] = $this->_db->Quote(trim($v));
	        }
	        
	        $insert_row = '(' . implode(',', $raw_line). ')';
	        
	        if(isset($ids[$row_id])) {
	            if($ids[$row_id]->value == $row_value) {
	                // Exactly the same
	                continue;
	            }
	            
	            $insert_without_ids[] = $insert_row;
	        } else {
	            $insert_with_ids[] = $insert_row;
	        }
	    }
	    
	    
	    if(!empty($insert_with_ids)) {
    	    $query = 'INSERT INTO '. $this->getSqlTableName() 
    	           .'('. implode(',', $cols_names) . ') VALUES'. PHP_EOL
    	           . implode(",\n", $insert_with_ids);
    	           
    	    $this->_db->setQuery($query);
    	    if ( !$this->_db->query() ) {
    			JError::raiseError( 500, $this->_db->getErrorMsg() );
    			return 0;
    		}
	    }
	    
	    if(!empty($insert_without_ids) && isset($empty_row['id'])) {
	        // Insertion without id
	        unset($empty_row['id']);
	        $cols_names = array_keys($empty_row);
            array_walk($cols_names, $array_backQuotes);
            $query = 'INSERT INTO '. $this->getSqlTableName() 
	               . '('. implode(',', $cols_names) . ') VALUES'. PHP_EOL
	               . implode(",\n", $insert_without_ids);
    	    $this->_db->setQuery($query);
    	    if ( !$this->_db->query() ) {
    			JError::raiseError( 500, $this->_db->getErrorMsg() );
    			return 0;
    		}
	    }
	    
	    return count($insert_with_ids) + count($insert_without_ids);
	}
	
	/**
	 * Get CSV parser instance
	 *
	 * @return parseCSV
	 */
	function &_getCSVParser() 
	{
	    static $parser;
	    if($parser === null) {
	        require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'library/parsecsv.lib.php';
	        $parser = new parseCSV();
	        $parser->delimiter = ";";
	    }
	    // Reinit internal vars
        $parser->file = null;
        $parser->file_data = null;
        $parser->data = array();
        $parser->titles = array();
        
	    return $parser;
	}
}