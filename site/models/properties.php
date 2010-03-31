<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 * 
 * @version     $Id$
 * @package     Jea.site
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

class JeaModelProperties extends JModel
{
    var $_error = '';
    
    
	/**
	 * Constructor
	 *
	 * @since 1.5
	 */
	function __construct()
	{
		parent::__construct();
		$this->addTablePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
	}
    
    
	function getId()
	{
		return JRequest::getInt('id', 0);
	}
	
	function &getRow()
	{
		static $table = null;
		
		if ( $table === null ) {
			$table =& $this->getTable();
			$table->load( $this->getId() );
		}
		
		return $table;
	}
    
    function getUserProperties()
    {
        
        $result = array() ;
        $mainframe =& JFactory::getApplication();
        $params    =& ComJea::getParams();
        $access    =& ComJea::getAccess();
        $default_limit = $params->get('list_limit', 10);
        $default_order = $params->get('orderby', 'id');
        $default_order_direction = $params->get('orderby_direction', 'asc');
        
        $cat        = $mainframe->getUserStateFromRequest( 'com_jea.user.properties.cat', 'cat', -1, 'int' );
        $limit      = $mainframe->getUserStateFromRequest( 'com_jea.user.limit', 'limit', $default_limit, 'int' );
        $limitstart = JRequest::getInt('limitstart', 0);
        
        $order      = $this->_db->getEscaped( JRequest::getCmd('filter_order', $default_order));
        $order_dir  = $this->_db->getEscaped( JRequest::getCmd('filter_order_Dir', $default_order_direction ));
        
        $rows = array();
        
        if($access->canEdit || $access->canEditOwn){
            
            $select  = $this->_getSqlBaseSelect();
            
            $where = '';
            if($access->canEditOwn){
                 $user    =& JFactory::getUser();
                 $where = ' WHERE tp.created_by=' . intval($user->get('id'));
            }
            
            if($cat >= 0){
                if(!empty($where)){
                    $where .= ' AND is_renting=' . $cat;
                } else {
                    $where .= ' WHERE is_renting=' . $cat;
                }
            }
            
            $sql = $select . $where .  ' ORDER BY ' . $order . ' ' . strtoupper( $order_dir ) ;
            $rows = $this->_getList( $sql , $limitstart, $limit );
            
            if ( $this->_db->getErrorNum() ) {
                JError::raiseWarning( 200, $this->_db->getErrorMsg() );
                return false;
            }                
        }
        
        $result['limitstart'] = $limitstart ;
        $result['limit'] = $limit ; 
        $result['total'] = $this->_getListCount( $sql );
        $result['rows'] = $rows ;
        $result['order'] = $order ;
        $result['order_dir'] = $order_dir;
        $result['cat'] = $cat;
         
        return $result ;        
    }
    
    function getProperties($all=false)
    {        
        $result = array() ;
        $mainframe =& JFactory::getApplication();
        $params =& ComJea::getParams();
        $default_limit = $params->get('list_limit', 10);
        
		if($all===false){
	    	$limit = $mainframe->getUserStateFromRequest( 'com_jea.limit', 'limit', $default_limit, 'int' );
		} else {
			$limit = 0;
		}
	    
	    $limitstart	= JRequest::getInt('limitstart', 0);
	    $order      = $this->_db->getEscaped( JRequest::getCmd('filter_order', 'ordering'));
		$order_dir  = $this->_db->getEscaped( JRequest::getCmd('filter_order_Dir', 'desc'));
	    
	    $select  = $this->_getSqlBaseSelect();
	    
	    $where = 'WHERE published=1 AND tp.is_renting=';
	    
    	$cat	= JRequest::getCmd('cat', $params->get('cat',  'renting'));
		$where .= ($cat == 'renting') ? '1' : '0' ;
		
		if ( $type_id = JRequest::getInt('type_id', $params->get('type_id', 0)) ) {
			$where .= ' AND tp.type_id = ' . intval( $type_id ) ;
		}
			
    	if ( $department_id = JRequest::getInt('department_id', $params->get('department_id', 0)) ) {
			$where .= ' AND tp.department_id = ' . intval( $department_id ) ;
		}

    	if ( $town_id = JRequest::getInt('town_id', $params->get('town_id', 0)) ) {
			$where .= ' AND tp.town_id = ' . intval( $town_id ) ;
		}
			
		if ( $area_id = JRequest::getInt('area_id', $params->get('area_id', 0)) ) {
			$where .= ' AND tp.area_id = ' . intval( $area_id ) ;
		}
			
    	if( $budget_min = JRequest::getFloat('budget_min', 0.0) ) {
			$where .= ' AND tp.price > ' . $this->_db->getEscaped( $budget_min ) ;
		}
		
		if( $budget_max =JRequest::getFloat('budget_max', 0.0) ) {
			$where .= ' AND tp.price < ' . $this->_db->getEscaped( $budget_max ) ;
		}

		if( $living_space_min = JRequest::getInt('living_space_min', 0) ) {
			$where .= ' AND tp.living_space > ' . $this->_db->getEscaped( $living_space_min ) ;
		}

		if( $living_space_max = JRequest::getInt('living_space_max', 0) ) {
			$where .= ' AND tp.living_space < ' . $this->_db->getEscaped( $living_space_max ) ;
		}
		
		if( $rooms_min = JRequest::getInt('rooms_min', 0) ) {
			$where .= ' AND tp.rooms >= ' . $this->_db->getEscaped( $rooms_min ) ;
		}
        
        if ( $advantages = JRequest::getVar( 'advantages', array(), '', 'array' ) ) {
        	
        	$likes = array();
        	
        	foreach( $advantages as $id ){
        		$likes[] = ' tp.advantages LIKE \'%-' .  $id .'-%\' ' ;
        	}
        	
        	$where .= ' AND ' . implode('AND', $likes) ;
        }

		
		$sql = $select . $where .  ' ORDER BY ' . $order . ' ' . strtoupper( $order_dir ) ;
		
		//dump($sql);
        $rows = $this->_getList( $sql , $limitstart, $limit );

        if ( !$this->_db->getErrorNum() ) {
        	  
         	$result['limitstart'] = $limitstart ;
			$result['limit'] = $limit ; 
			$result['total'] = $this->_getListCount( $sql );
	        $result['rows'] = $rows ;
	        $result['order'] = $order ;
	        $result['order_dir'] = $order_dir;          

        } else {
            JError::raiseWarning( 200, $this->_db->getErrorMsg() );
            return false;
        }
         
        return $result ;
        
    }
    
    function &getProperty()
    {
    	 $id = (int) $this->getId() ;
         $sql = $this->_getSqlBaseSelect();
         $sql .= 'WHERE tp.id ='. $id ;
         $this->_db->setQuery($sql) ;
         $res = $this->_db->loadObjectList() ;
         
         // Hit
         $property =& $this->getTable();
		 $property->hit($id);
         
         return $res[0];
    }
    
    function &getFeature( $tableName )
    {
		$table =& $this->getTable( $tableName );
		return $table;
    }
    
    function getFeatureList($tableName, $title='')
    {
    	static $featuresModel = null ;

    	if( $featuresModel === null ) {
	    	JModel::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'models');
	    	$featuresModel =& JModel::getInstance('Features', 'jeaModel');
    	}
    	
    	$featuresModel->setTableName( $tableName );
    	return $featuresModel->getListForHtml( $title );
    	
    }
    
    function getFeatureOptionsList($tableName, $title='')
    {
    	static $featuresModel = null ;

    	if( $featuresModel === null ) {
	    	JModel::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'models');
	    	$featuresModel =& JModel::getInstance('Features', 'jeaModel');
    	}
    	
    	$featuresModel->setTableName( $tableName );
    	return $featuresModel->getListForHtml( $title );
    }
    
    function &getIptc()
    {
        require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'library/Iptc.php';
    	$id = JRequest::getInt('id');
    	$image = JRequest::getVar('image', '');
    	$dir = JPATH_ROOT . DS . 'images' .DS. 'com_jea' 
		     .DS. 'images' . DS . $id;
		
		if( !$image ){
			$file = $dir . DS . 'main.jpg' ;
		} else {
			$file = $dir. DS . 'secondary'.DS.'preview'.DS.$image;
		}
		
		$ret = new stdClass();
		$ret->title = '';
		$ret->description = '';
		
		if(file_exists($file)) {

			$iptc = new iptc($file);
			if($iptc->hasmeta){
				$ret->title       = $iptc->get(IPTC_HEADLINE);
				$ret->description = $iptc->get(IPTC_CAPTION);
			}
			
		}
		
		return $ret;
    }
    
    
    function getPreviousAndNext()
    {
    	$result = array();
    	$currentRow =& $this->getRow();
        $result['prev_item'] = null;
        $result['next_item'] = null;
        
        $params = ComJea::getParams();
        
        $sql = 'SELECT id,
        CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(":", id, alias) ELSE id END AS slug
        FROM #__jea_properties WHERE ';
        
        $where = ( $currentRow->is_renting )? 'is_renting=1' : 'is_renting=0' ;
        $where .= ' AND published=1';
        
        // Bug fix [#16275] Problem with 'Previous' and 'Next' navigation
    	if ( $type_id = JRequest::getInt('type_id', $params->get('type_id', 0)) ) {
			$where .= ' AND type_id = ' . intval( $type_id ) ;
		}
			
    	if ( $department_id = JRequest::getInt('department_id', $params->get('department_id', 0)) ) {
			$where .= ' AND department_id = ' . intval( $department_id ) ;
		}

    	if ( $town_id = JRequest::getInt('town_id', $params->get('town_id', 0)) ) {
			$where .= ' AND town_id = ' . intval( $town_id ) ;
		}
			
		if ( $area_id = JRequest::getInt('area_id', $params->get('area_id', 0)) ) {
			$where .= ' AND area_id = ' . intval( $area_id ) ;
		}
        // End Bug fix [#16275]
        
		// $order = ' ORDER'
        
        
        $this->_db->setQuery( $sql . $where );
        $rows = $this->_db->loadObjectList();
        
        if($rows){
            $place = 0;
            foreach($rows as $k => $row){
                if($row->id == $currentRow->id) $place = $k;
            }
            if ( isset($rows[$place-1]) ) $result['prev_item'] = $rows[$place-1] ;
            if ( isset($rows[$place+1]) ) $result['next_item'] = $rows[$place+1] ;
        }
        return $result;
    }

    
	function _getSqlBaseSelect()
    {
        
       
    	$table =& $this->getTable();
    	$fields = $table->getProperties();
    	
        $temp = array();

        foreach ($fields as $field => $value){
                $temp[]= 'tp.'.$field.' AS '. '`' . $field . '`';
        }
        //$select = 'tp'
        $select = implode(', ', $temp );

        $select .= ', td.value AS `department`, tc.value AS `condition`, ta.value AS `area`, '
			    .  'ts.value AS `slogan`, tt.value AS `type`, tto.value AS `town`, ' 
				.  'thwt.value AS `hot_water`, tht.value AS `heating`';
		
		//Joomfish compatibility:
		$select .= ', td.id AS `id2`, tc.id AS `id3`, ta.id AS `id4`, '
			    .  'ts.id AS `id5`, tt.id AS `id6`, tto.id AS `id7`, ' 
				.  'thwt.id AS `id8`, tht.id AS `id9`';

        // Routing capability
        $select .= ',CASE WHEN CHAR_LENGTH(tp.alias) THEN CONCAT_WS(":", tp.id, tp.alias) ELSE tp.id END AS slug';

        return 'SELECT ' . $select . ' FROM #__jea_properties AS tp' . PHP_EOL
             . 'LEFT JOIN #__jea_departments AS td ON td.id = tp.department_id' . PHP_EOL
			 . 'LEFT JOIN #__jea_conditions AS tc ON tc.id = tp.condition_id' . PHP_EOL
			 . 'LEFT JOIN #__jea_areas AS ta ON ta.id = tp.area_id' . PHP_EOL
			 . 'LEFT JOIN #__jea_slogans AS ts ON ts.id = tp.slogan_id' . PHP_EOL
			 . 'LEFT JOIN #__jea_types AS tt ON tt.id = tp.type_id' . PHP_EOL
			 . 'LEFT JOIN #__jea_towns AS tto ON tto.id = tp.town_id' . PHP_EOL
			 . 'LEFT JOIN #__jea_hotwatertypes AS thwt ON thwt.id = tp.hot_water_type' . PHP_EOL
			 . 'LEFT JOIN #__jea_heatingtypes AS tht ON tht.id = tp.heating_type'. PHP_EOL ;
        
    }    
    
    


     
}

