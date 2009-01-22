<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 * 
 * @version     O.7 2009-01-22
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
	
	
	function getItems()
	{
		$result = array();
		$mainframe = &JFactory::getApplication();
		
		$context = 'com_jea.characteristics.' . $this->getTableName() ;
		$default_limit = $mainframe->getCfg('list_limit');

		$limit      = $mainframe->getUserStateFromRequest( $context.'limit', 'limit',  $default_limit, 'int' );
	    $limitstart = $mainframe->getUserStateFromRequest( $context.'offset', 'limitstart', 0, 'int' );
	    
	    
	    $sql = 'SELECT id, value, ordering FROM ' . $this->getSqlTableName() . ' ORDER BY ordering' ;
		
		$rows = $this->_getList( $sql , $limitstart, $limit );
		
		if ( !$this->_db->getErrorNum() ) {
	            
	        $result['limitstart'] = $limitstart ;
			$result['limit'] = $limit ; 
			$result['total'] = $this->_getListCount( $sql );
	        $result['rows'] = $rows ;    
	
	    } else {
	            JError::raiseWarning( 200, $this->_db->getErrorMsg() );
	            return false;
	    }
	         
	    return $result ;
	}
	
	function getListForHtml( $first_txt='' ){
		
        $sql = 'SELECT `id` AS value ,`value` AS text FROM ' . $this->getSqlTableName() . ' ORDER BY ordering' ;
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
		
		if( !$row->id ){
			// save new item at the end of ordering
			$row->ordering = $row->getNextOrder();
		}

        if ( !$row->store() ) {
            JError::raiseWarning( 200, $row->getError() );
            return false;
        }
        
		//TODO: images des slogans
		
		/*
		$table_name = $this->_table->get('_tbl');
		
		
		if($table_name == '#__jea_slogans'){
		    require_once 'File_Utils.php';
		    require_once 'Gd/Transform.php';
		    
		    $upload_dir = COM_IMMO_FRONT_PATH._S_.'upload'._S_.'slogans' ;
		    File_Utils::create_dir($upload_dir);
			$police = COM_IMMO_PATH._S_.'polices'._S_.'ariblk.ttf';
			$gd = new Gd_Transform();
			$gd->createNewImageWithTtfText($name ,$police, 13, 'FF0000');
			$gd->rotate(8,'FFFFFF');
			$gd->save($upload_dir._S_.$this->_table->id.'.png','png');
		}*/ 
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
}