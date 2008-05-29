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

class JEA_PropertiesModel extends JModel
{
    var $_error = '';

    /**
     * property category ( renting or selling )
     *
     * @var string $_cat
     */

    var $_cat = '';
    var $_table = null ;

    /**
     * constructor
     *
     * @param string $cat
     * @return void
     */
    function JEA_PropertiesModel($config = array())
    {
		parent::__construct($config = array());
		$this->_table =& $this->getTable('Properties');
         
    }
    
    function &getRow()
    {
        return $this->_table ;
    }
    
    function setCategory($cat)
    {
       if ( !($cat == 'renting' or $cat == 'selling' ) ) {

           return false;
       }
       $this->_cat = $cat;
       
       return true;
    }
    
    function isRenting()
    {
        if ( $this->_cat == 'renting' ) return true;
        return false;
    }
    
    function _getSqlBaseSelect()
    {
        
        $fields = $this->_table->getPublicProperties();

        $select = '';

        foreach ($fields as $field ){
            if(!isset($first)){
                $first = true;
                $select .= 'tp.'.$field.' AS '. '`' . $field . '`';
            } else {
                $select .= ', tp.'.$field.' AS ' . '`' .$field . '`' ;
            }
        }


        $select .= ', td.value AS `department`, tc.value AS `condition`, ta.value AS `area`, '
			    .  'ts.value AS `slogan`, tt.value AS `type`, tto.value AS `town`, ' 
				.  'thwt.value AS `hot_water`, tht.value AS `heating`';

        return "SELECT {$select} FROM #__jea_properties AS tp "
             . "LEFT JOIN #__jea_departments AS td ON td.id = tp.department_id "
			 . "LEFT JOIN #__jea_conditions AS tc ON tc.id = tp.condition_id "
			 . "LEFT JOIN #__jea_areas AS ta ON ta.id = tp.area_id "
			 . "LEFT JOIN #__jea_slogans AS ts ON ts.id = tp.slogan_id "
			 . "LEFT JOIN #__jea_types AS tt ON tt.id = tp.type_id "
			 . "LEFT JOIN #__jea_towns AS tto ON tto.id = tp.town_id "
			 . "LEFT JOIN #__jea_hotwatertypes AS thwt ON thwt.id = tp.hot_water_type "
			 . "LEFT JOIN #__jea_heatingtypes AS tht ON tht.id = tp.heating_type " ;
        
    }
    
    function getItems( $params )
    {        
        $offset = ( !isset($params['offset']) )? 0 : intval( $params['offset'] ) ;
        $limit = ( !isset($params['limit']) )? 0 : intval( $params['limit'] ) ;

        if ($offset < $limit ) {
            $offset = 0 ;
        }

        $ordering = ( !isset($params['ordering'])|| empty($params['ordering']) )? 'ordering' : strval( $params['ordering'] ) ;

        $conditions = array();

        $conditions['town_id']       = ( !isset( $params['town_id'] ) )? 0 : intval( $params['town_id'] ) ;
        $conditions['department_id'] = ( !isset( $params['department_id'] ) )? 0 : intval( $params['department_id'] ) ;
        $conditions['area_id']       = ( !isset( $params['area_id'] ) )? 0 : intval( $params['area_id'] ) ;
        $conditions['type_id']       = ( !isset( $params['type_id'] ) )? 0 : intval( $params['type_id'] ) ;
        $conditions['published']     = ( !isset( $params['published'] ) )? 0 : intval( $params['published'] ) ;
        $conditions['emphasis']      = ( !isset( $params['emphasis'] ) )? 0 : intval( $params['emphasis'] ) ;
        $conditions['ref']           = ( !isset( $params['find_ref'] ) || empty($params['find_ref']) )? 0 : $this->_db->Quote( $params['find_ref'] ) ;

        if( $this->_cat ){
            $where  = "WHERE tp.is_renting=" ;
            $where .= $this->isRenting() ? '1' : '0' ;
        }
        
        foreach ($conditions as $field => $value){
            if($value){
                
                $value = $this->_db->getEscaped( $value );
                
                if (!isset($where)){
                    $where = "WHERE tp.{$field} = {$value}";
                } else {
                    $where .= " AND tp.{$field} = {$value}" ;
                }
            }
        }
        
        /**Advanced search**/
        
        $session =& JFactory::getSession();
        
    	if( $budget_min = $session->get('budget_min', 0.0, 'jea_search') ) {
			$where .= ' AND tp.price > ' . $this->_db->getEscaped( $budget_min ) ;
		}
		
		if( $budget_max = $session->get('budget_max', 0.0, 'jea_search') ) {
			$where .= ' AND tp.price < ' . $this->_db->getEscaped( $budget_max ) ;
		}

		if( $living_space_min = $session->get('living_space_min', 0, 'jea_search') ) {
			$where .= ' AND tp.living_space > ' . $this->_db->getEscaped( $living_space_min ) ;
		}

		if( $living_space_max = $session->get('living_space_max', 0, 'jea_search' ) ) {
			$where .= ' AND tp.living_space < ' . $this->_db->getEscaped( $living_space_max ) ;
		}
		
		if( $rooms_min = $session->get('rooms_min', 0, 'jea_search') ) {
			$where .= ' AND tp.rooms > ' . $this->_db->getEscaped( $rooms_min ) ;
		}
        
        if ( $advantages = $session->get('advantages', array(), 'jea_search') ) {
        	
        	$likes = array();
        	
        	foreach( $advantages as $id ){
        		$likes[] = ' tp.advantages LIKE \'%-' .  $id .'-%\' ' ;
        	}
        	
        	$where .= ' AND ' . implode('AND', $likes) ;
        }
        

        $sql  = $this->_getSqlBaseSelect();
        $sql .= $where ;
        $sql .= ' ORDER BY '. $this->_db->getEscaped( $ordering );
        
        $rows = $this->_getList( $sql , $offset, $limit );

        if ( !$this->_db->getErrorNum() ) {
            
         $result['total'] = $this->_getListCount( $sql );
         $result['rows'] = $rows ;          

        } else {
            JError::raiseWarning( 200, $this->_db->getErrorMsg() ); 
        }
         
        return $result ;
        
    }


    function order($inc,$id)
    { 
        $this->_table->load($id);
        $where = $this->isRenting() ? 'is_renting=1' : 'is_renting=0' ;
        $this->_table->move( $inc ,$where);
    }

    function publish($cid, $bool)
    {
        foreach ($cid as $id) {
            $this->_table->load( $id );
            $this->_table->published = ($bool)? 1 : 0 ;
            $this->_table->store();
        }
    }

    function emphasize($id)
    {
        $this->_table->load($id);
        $this->_table->emphasis = ($this->_table->emphasis )? 0 : 1 ;
        $this->_table->store();
    }
    
    function getPreviousAndNext( $id=0 ){
        $result = array();
        $result['prev_item'] = null;
        $result['next_item'] = null;
        
        $sql = 'SELECT id FROM #__jea_properties WHERE ';
        
        $where = ($this->isRenting())? 'is_renting=1' : 'is_renting=0' ;
        $where .= ' AND published=1';
        
        $this->_db->setQuery( $sql . $where );
        $rows = $this->_db->loadObjectList();
        
        if($rows){
            $place = 0;
            foreach($rows as $k => $row){
                if($row->id == $id) $place = $k;
            }
            if ( isset($rows[$place-1]) ) $result['prev_item'] = $rows[$place-1] ;
            if ( isset($rows[$place+1]) ) $result['next_item'] = $rows[$place+1] ;
        }
        return $result;
    }
    
    function findRef($ref)
    {
        return $this->_table->findRef($ref);
    }


    function load($id=0)
    {

        $rootURL = JURI::root();

        $result = array();
        $this->_table->load($id);
        
        if($id == 0){
	        $this->_table->published = 1;
	        $this->_table->dispo ='';
	        
	        $result['row'] = $this->_table;
        } else {
        
            $sql = $this->_getSqlBaseSelect();
            $sql .= 'WHERE tp.id ='.intval($id) ;

            $database =& $this->_table->getDBO();
            $database->setQuery($sql) ;
            $res = $database->loadObjectList() ;
            $result['row'] = $res[0];
        }
        
        
        //main image
        $img = JPATH_COMPONENT_SITE.DS.'upload'.DS.'properties'.DS.$this->_table->id.DS.'main.jpg';
        
        $result['main_image'] = array();

        if(is_file($img)){
            $result['main_image']['url'] = "{$rootURL}components/com_jea/upload/properties/{$this->_table->id}/main.jpg";
            $result['main_image']['preview_url'] = "{$rootURL}components/com_jea/upload/properties/{$this->_table->id}/preview.jpg";
            $result['main_image']['min_url'] = "{$rootURL}components/com_jea/upload/properties/{$this->_table->id}/min.jpg";
            $result['main_image']['delete_url'] = "{$rootURL}administrator/index2.php?option=com_jea"
            ."&amp;controller=properties&amp;task=deleteimg&amp;id={$this->_table->id}&amp;cat={$this->_cat}";
            	
            $im = @getimagesize($img);
            $result['main_image']['width'] = $im[0];
            $result['main_image']['height'] = $im[1];
            	
            $file = stat ($img);
            $result['main_image']['weight'] = round(($file[7]/1024),1) ;// poid en Ko
        }


        //secondaries images
        $dir = JPATH_COMPONENT_SITE.DS.'upload'.DS.'properties'.DS.$this->_table->id.DS.'secondary';
        $result['secondaries_images'] = array();
        
        jimport('joomla.filesystem.folder');
        
        if( JFolder::exists( $dir ) ){
                
            $filesList = JFolder::files( $dir );

            $viewfilesList = array();
            foreach ( $filesList as $filename ) {

                $detail = array();
                $im = @getimagesize($dir.DS.$filename);
                if ($im !== FALSE){
                    $detail['name'] = $filename;
                    $detail['width'] = $im[0];
                    $detail['height'] = $im[1];
                    	
                    $file = stat ($dir.DS.$filename);
                    $detail['weight'] = round(($file[7]/1024),1) ;// poid en Ko
                    	
                    $detail['url'] = "{$rootURL}components/"
                    ."com_jea/upload/properties/{$this->_table->id}/secondary/{$filename}";
                    $detail['preview_url'] = "{$rootURL}components/"
                    ."com_jea/upload/properties/{$this->_table->id}/secondary/preview/{$filename}";
                    $detail['min_url'] = "{$rootURL}components/"
                    ."com_jea/upload/properties/{$this->_table->id}/secondary/min/{$filename}";
                    	
                    $detail['delete_url'] = "{$rootURL}administrator/index.php?option=com_jea"
                    ."&amp;controller=properties&amp;task=deleteimg&amp;id={$this->_table->id}"
                    ."&amp;image={$filename}&amp;cat={$this->_cat}";
                    	
                    $viewfilesList[] = $detail ;
                }
            }
            	
            $result['secondaries_images'] =  $viewfilesList ;
        }
        
        return $result;

    }

    function save( $id, $datas, &$file, &$secondfile )
    {
        $this->_table->load($id);
        

        if( empty($datas['ref'] ) ) {
			
		    JError::raiseError( 404, JText::_('Property must have a reference') );
			return false;
			
		} elseif ( empty($datas['type_id']) ) {
		    
		    JError::raiseError( 404, JText::_('Select a type of property') );
			return false;
		    
		}
		
        //avoid duplicate entry for ref
        $query = 'SELECT id FROM #__jea_properties WHERE ref=' . $this->_db->Quote( $datas['ref'] ) 
               . ' AND id <>' . intval( $id );

        if ( $this->_getListCount( $query ) > 0 ){
            
            JError::raiseWarning( 200, JText::sprintf( 'Reference already exists', $datas['ref'] ) );
            return false;
        }
		
		
		//serialize advantages
		if ( !empty($datas['advantages']) && is_array($datas['advantages']) ) {
		    
		    //Sort in order to find easily property advantages in sql where clause
		    sort( $datas['advantages'] );
		    $datas['advantages'] = '-'. implode('-' ,$datas['advantages'] ) . '-';
		    
		} else {
		    
		    $datas['advantages'] = '';
		}
		
		//check availability
		
		if ( ! preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}/', trim($datas['availability']) ) ){
		    
		    $datas['availability'] = '0000-00-00';
		}
		
		// Clean description for xhtml transitional compliance
		$datas['description'] = str_replace( '<br>', '<br />', $datas['description'] );
		
		//check if renting
		$datas['is_renting'] = $this->isRenting() ? 1 : 0;



		//For new insertion
        if ( !$this->_table->id ) {
            	
            $this->_table->published = 1;
            
            //Save ordering at the end
            $where = $this->isRenting() ? 'is_renting=1' : 'is_renting=0';
            $this->_table->ordering = $this->_table->getNextOrder( $where );
            
            $this->_table->date_insert = date('Y-m-d');
        }
        
        if ( !$this->_table->bind($datas) ) {
            JError::raiseWarning( 200, $this->_table->getError() );
            return false;
        }
       
        if ( !$this->_table->store() ) {
            JError::raiseWarning( 200, $this->_table->getError() );
            return false;
        }

        /*images upload*/

        require_once 'Gd/Transform.php';
        jimport('joomla.filesystem.folder');
        
        $base_upload_dir = JPATH_COMPONENT_SITE.DS.'upload'.DS.'properties' ;
        
        if ( !JFolder::exists($base_upload_dir) ) { JFolder::create($base_upload_dir); }

        $upload_dir = $base_upload_dir.DS.$this->_table->id;
        
        $config =& ComJea::getParams();

        //main image

        if ( $file->isPosted() ){
            	
            if ( !JFolder::exists($upload_dir) ) { JFolder::create($upload_dir); }
            	
            $file->setValidExtensions(array('jpg','JPG','jpeg','JPEG','gif','GIF','png','PNG'));
            $file->setName('main.jpg');
            	
            if( !$fileName = $file->moveTo($upload_dir) ){

                JError::raiseWarning( 200, JText::_( $file->getError() ) );
                return false;
            }
            	
            $gd = new Gd_Transform();
            	
            $gd->load($upload_dir.DS.$fileName);
            //make preview
            $gd->resize( $config->get('max_previews', 400) ,null);
            $gd->saveToJpeg( $upload_dir.DS.'preview.jpg', $config->get( 'jpg_quality' , 90) );
            	
            $gd->load( $upload_dir.DS.'preview.jpg' );
            	
            //make min
            $gd->resize( null, 90 ); //default max height : 90px 
            if ($gd->getSize('width') > $config->get('max_thumbnails', 120) ) {
                $gd->resize( $config->get('max_thumbnails', 120), null);
            }
            
            $gd->saveToJpeg($upload_dir.DS.'min.jpg', $config->get( 'jpg_quality' , 90) );
        }

        if($secondfile->isPosted()){

            $upload_dir = $upload_dir.DS.'secondary';
            $preview_dir = $upload_dir.DS.'preview' ;
            $thumbnail_dir = $upload_dir.DS.'min' ;
        	if ( !JFolder::exists($upload_dir) ) { JFolder::create($upload_dir); }
        	if ( !JFolder::exists($preview_dir) ) { JFolder::create($preview_dir); }
        	if ( !JFolder::exists($thumbnail_dir) ) { JFolder::create($thumbnail_dir); }
	
            $secondfile->setValidExtensions(array('jpg','JPG','jpeg','JPEG','gif','GIF','png','PNG'));
            $secondfile->nameToSafe();
            	
            if(! $fileName = $secondfile->moveTo( $upload_dir )){
                JError::raiseWarning( 200, JText::_( $secondfile->getError() ) );
                return false;
            }
            	
            $gd = new Gd_Transform();
            	
            $gd->load( $upload_dir.DS.$fileName );
            
            //make preview
            $gd->resize( $config->get('max_previews', 400) , null );
            $gd->saveToJpeg( $preview_dir.DS.$fileName, $config->get( 'jpg_quality' , 90) );
            	
            $gd->load( $preview_dir.DS.$fileName );
            	
            //make min

            //$gd->centerCrop([w],[h]);
            $gd->resize( null, 90 ); //default max height : 90px 
            if ( $gd->getSize( 'width' ) > $config->get('max_thumbnails', 120) ) {
                $gd->resize( $config->get('max_thumbnails', 120) , null ); //height :90px;
            }
            	
            $gd->saveToJpeg( $thumbnail_dir.DS.$fileName, $config->get( 'jpg_quality' , 90) );
        }

        return true;
    }

    function remove($cid)
    {
        foreach ( $cid as $id){
            
           if ( !$this->_table->delete( $id )) {
                
                JError::raiseWarning( 200, $this->_table->getError() );
                return false;
            }
            
            //remove image upload directory if exists
            $dirimg = JPATH_COMPONENT_SITE.DS.'upload'.DS.'properties'.DS.$id ;
            jimport('joomla.filesystem.folder');

            if( JFolder::exists($dirimg) ) {
                
                JFolder::delete($dirimg);
            }
        }

        return true;
    }
    
    function delete_img( $id, $image='' )
    {  
		
	    $this->_table->load($id);
		$deleteFiles = array();
		$dir = JPATH_COMPONENT_SITE.DS.'upload'.DS.'properties'.DS.$this->_table->id.DS ;
		
		if( !$image ){
			//main image to delete
			$deleteFiles[] = $dir.'main.jpg';
			$deleteFiles[] = $dir.'preview.jpg';
			$deleteFiles[] = $dir.'min.jpg';
		} else {
			//secondary image to delete
			$deleteFiles[] = $dir.'secondary'.DS.$image;
			$deleteFiles[] = $dir.'secondary'.DS.'preview'.DS.$image;
			$deleteFiles[] = $dir.'secondary'.DS.'min'.DS.$image;
		}
		
		foreach($deleteFiles as $file){
			if( is_file($file) ) @unlink($file);
		}
			
    }
     
}

