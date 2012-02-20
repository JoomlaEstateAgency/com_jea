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

class TableProperties extends JTable
{
	/**
	 * Constructor
	 * @param	JDatabase	A database connector object
	 */
	public function __construct(&$db)
	{
		$dispatcher = JDispatcher::getInstance();
        JPluginHelper::importPlugin( 'jea' );
        
        parent::__construct('#__jea_properties', 'id', $db);
        
        $dispatcher->trigger('onInitTableProperty', array($this));
	}
	
	function check()
	{
	   if ( empty( $this->type_id ) ) {
		     $this->setError( JText::_('Select a type of property') );
			return false;
		    
		}
		
        //avoid duplicate entry for ref
        $query = 'SELECT id FROM #__jea_properties WHERE ref=' . $this->_db->Quote( $this->ref ) 
               . ' AND id <>' . intval( $this->id );
        $this->_db->setQuery( $query );
		$this->_db->query();

        if ( $this->_db->getNumRows() > 0 ){
            
            $this->setError( JText::sprintf( 'Reference already exists', $this->ref ) );
            return false;
        }
        
        
        // Alias cleanup
        if(empty($this->alias)) {
			$this->alias = $this->title;
		}
		$this->alias = JFilterOutput::stringURLSafe($this->alias);
        
	    //avoid duplicate entry for alias
        $query = 'SELECT id FROM #__jea_properties WHERE alias=' . $this->_db->Quote( $this->alias )
               . ' AND id <>' . intval( $this->id );
        $this->_db->setQuery( $query );
		$this->_db->query();
		$numRows = $this->_db->getNumRows();

        if ( $numRows > 0 ){
            $this->alias .= '_'. ($numRows+1);
        }
        
        
		
		//serialize advantages
		if ( !empty($this->advantages) && is_array($this->advantages) ) {
		    
		    //Sort in order to find easily property advantages in sql where clause
		    sort( $this->advantages );
		    $this->advantages = '-'. implode('-' , $this->advantages ) . '-';
		    
		} else {
		    
		    $this->advantages = '';
		}
		
		//check availability
		
		if ( ! preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}/', trim( $this->availability ) ) ){
		    
		    $this->availability = '0000-00-00';
		}
		
		// Clean description for xhtml transitional compliance
		$this->description = str_replace( '<br>', '<br />', $this->description );

		//For new insertion
        if ( !$this->id ) {	
            $user =& JFactory::getUser();
            //Save ordering at the end
            $where =  'is_renting=' . (int) $this->is_renting ;
            $this->ordering = $this->getNextOrder( $where );
            $this->date_insert = date('Y-m-d H:i:s');
            $this->created_by = $user->get('id');
        }
        
        return true;
	}
	
	
	
}