<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 * 
 * @version		0.1 2008-02-26
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
defined( '_JEXEC' ) or die( 'Restricted access' );


class JEA_CharacteristicsModel extends JModel
{
	var $_table = null;
    
    function JEA_CharacteristicsModel ($tb_name, $config = array())
    {
		parent::__construct($config);
		$this->_table =& $this->getTable( ucfirst( $tb_name ) );
         
    }
    
    function getLastSavedRowId()
    {
        return $this->_table->id ;
    }
	
	function getItems( $offset = 0 , $limit = 0 )
	{  
	    $result = array( 'total' => 0 , 'rows' => array() );

	    $table_name = $this->_table->getTableName();
	    $sql = "SELECT id, value, ordering FROM {$table_name} ORDER BY ordering" ;
	    
		if ( $offset < $limit ) {
            $offset = 0 ;
        }

        $rows = $this->_getList( $sql , $offset, $limit );

        if ( ! $this->_db->getErrorNum() ) {
            
         $result['total'] = $this->_getListCount( $sql );
         $result['rows'] = $rows ;          

        } else {
            JError::raiseWarning( 200, $this->_db->getErrorMsg() ); 
        }
         
        return $result ;
	}
	
	function getListForHtml( $first_txt='' ){
	    
	   /* $database =& $this->_table->getDBO();
	    $table_name = $this->_table->getTableName();
	    
	    $database->setQuery("SELECT id AS value ,name AS text FROM {$table_name} ORDER BY ordering");
		$res = $database->loadObjectList();
		
	    if ( $database->getErrorNum() ) {
	        
	        JError::raiseWarning( 200, 'SQL error : ' . $database->getErrorMsg() ); 
        }*/
		$table_name = $this->_table->getTableName();
		
        $sql = "SELECT `id` AS value ,`value` AS text FROM {$table_name} ORDER BY ordering" ;
        
        $rows = $this->_getList( $sql );

        if ( $this->_db->getErrorNum() ) {
            
            JError::raiseWarning( 200, $this->_db->getErrorMsg() );
        }
		
		//unshift default option
		array_unshift($rows, JHTML::_('select.option', '0', $first_txt ));
		
		return $rows ;
	}
	
	
	function load ( $id = 0 )
	{
	    $this->_table->load($id);
	    return $this->_table;
	}
	
	function orderList( $inc, $id )
	{
	    $this->_table->load( $id);
		$this->_table->move( $inc);
	}
	
	function save( $id, $value = 'Untitled' )
	{
	    $this->_table->load( $id );
		$this->_table->value = $value ;
		
		if( !$this->_table->id ){
			// save new item at the end of ordering
			$this->_table->ordering = $this->_table->getNextOrder();
		}
		
		if ( !$this->_table->store() ) {
		    
			JError::raiseWarning( 200, $this->_table->getError() );
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

		
		return true ;
		
	}
	
	function remove( $items_id = array() )
	{
	    //$table_name = $this->_table->get('_tbl');
	    
	    foreach ( $items_id as $id) {
	        
			if ( !$this->_table->delete( $id ) ) {
			    
				JError::raiseWarning( 200, $this->_table->getError() );
				return false;
			}
			
			/*$img = COM_IMMO_FRONT_PATH.'/upload/slogans/'.$id.'.png';
			
			if (is_file($img) && $table_name == '#__immo_slogans' ){
			    unlink($img);
			}*/
		}
		
		return true;
	}
	
   
}

