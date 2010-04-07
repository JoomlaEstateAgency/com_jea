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
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.model');

class JeaModelProperties extends JModel
{
    var $_error = '';

    /**
     * property category ( renting or selling )
     *
     * @var string $_cat
     */

    var $_cat = '';
    
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
	
	function getCategory()
	{
		return $this->_cat ;
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
    
	function checkout()
	{
		$user = & JFactory::getUser();
		$row = & $this->getRow();
		
		/*
		 * If the item is checked out we cannot edit it... unless it was checked
		 * out by the current user.
		 */
		if ( $row->isCheckedOut( $user->get('id'), $row->checked_out )) {
			$msg = JText::sprintf('DESCBEINGEDITTED', JText::_('The property'), $row->ref);
			JError::raiseWarning( 200, $msg );
			return false;
		}
		
		$row->checkout($user->get('id'));
		
		return true ;
		
	}
    
    
    
    function getItems()
    {        
        $result = array() ;
    	$context = 'com_jea.properties.'.$this->_cat ;
        $mainframe =& JFactory::getApplication();
        
        $default_limit = $mainframe->getCfg('list_limit');
        
	    $limit         = $mainframe->getUserStateFromRequest( $context.'limit', 'limit', $default_limit, 'int' );
	    $limitstart    = $mainframe->getUserStateFromRequest( $context.'limitstart', 'limitstart', 0, 'int' );
	    $type_id       = $mainframe->getUserStateFromRequest( $context.'type_id', 'type_id', 0, 'int' );
	    $town_id       = $mainframe->getUserStateFromRequest( $context.'town_id', 'town_id', 0, 'int' );
	    $department_id = $mainframe->getUserStateFromRequest( $context.'department_id', 'department_id', 0, 'int' );
    	$search        = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
    	$order         = $this->_db->getEscaped( JRequest::getCmd('filter_order', 'ordering'));
		$order_dir     = $this->_db->getEscaped( JRequest::getCmd('filter_order_Dir', 'desc'));

        $select = 'SELECT tp.id AS `id`, tp.ref AS `ref`, tp.adress AS `adress`, tp.price AS `price`,' . PHP_EOL
         		. 'tp.date_insert AS `date_insert`,tp.emphasis AS `emphasis`, td.value AS `department`,'. PHP_EOL
         		. 'tt.value AS `type`, tto.value AS `town`, tp.published AS published, tp.ordering AS `ordering`,' . PHP_EOL
         		. 'tp.checked_out AS `checked_out`, tp.checked_out_time AS `checked_out_time`,' . PHP_EOL
         		. 'tp.created_by AS  `created_by`, tu.username AS `author`, tp.hits AS `hits`' . PHP_EOL
                . 'FROM #__jea_properties AS tp' . PHP_EOL
                . 'LEFT JOIN #__jea_departments AS td ON td.id = tp.department_id' . PHP_EOL
			    . 'LEFT JOIN #__jea_types AS tt ON tt.id = tp.type_id' . PHP_EOL
			    . 'LEFT JOIN #__jea_towns AS tto ON tto.id = tp.town_id' . PHP_EOL
			    . 'LEFT JOIN #__users AS tu ON tu.id = tp.created_by' . PHP_EOL;
    	
    	$where  = 'WHERE tp.is_renting=' ;
        $where .= $this->isRenting() ? '1' : '0' ;
        if ( $town_id )       $where .= ' AND tp.town_id=' . $town_id ;
        if ( $department_id ) $where .= ' AND tp.department_id=' . $department_id ;
        if ( $type_id )       $where .= ' AND tp.type_id=' . $type_id ;
		if ( $search ) {
			$search = $this->_db->getEscaped( trim( strtolower( $search ) ) );
			$where .= ' AND tp.ref LIKE \'%' .$search . '%\'';
		}
		
		$sql = $select . $where .  ' ORDER BY ' . $order . ' ' . strtoupper( $order_dir ) ;
        $rows = $this->_getList( $sql , $limitstart, $limit );

        if ( !$this->_db->getErrorNum() ) {
        	  
         	$result['limitstart'] = $limitstart ;
			$result['limit'] = $limit ; 
			$result['total'] = $this->_getListCount( $sql );
	        $result['rows'] = $rows ;
	        $result['type_id'] = $type_id ;
	        $result['town_id'] = $town_id ;
	        $result['department_id'] = $department_id ;
	        $result['search'] = $search ;
	        $result['order'] = $order ;
	        $result['order_dir'] = $order_dir;          

        } else {
            JError::raiseWarning( 200, $this->_db->getErrorMsg() );
            return false;
        }
         
        return $result ;
        
    }


    function order($inc)
    { 
        $row =& $this->getRow();
        $where = $this->isRenting() ? 'is_renting=1' : 'is_renting=0' ;
        $row->move( $inc ,$where);
    }

    function publish( $bool )
    {
    	$cid = $this->getCid();
		$user	= & JFactory::getUser();
		$uid	= $user->get('id');
    	
    	$table =& $this->getTable();
    	
    	if ( !$table->publish( $cid, (int)$bool, $uid) ){
    		JError::raiseWarning( 200, $table->getError() );
    		return false;
    	}
    	
		return true;
    }

    function emphasize()
    {
        $row =& $this->getRow();
        $row->emphasis = ($row->emphasis )? 0 : 1 ;
        $row->store();
    }
    
    function resetHits()
    {
        $row =& $this->getRow();
        $row->hits = 0 ;
        $row->store();
    }

    function getItem()
    {
        $result = array();
        $row =& $this->getRow();
        
        if( $row->id == 0 ) {
        	
	        $row->published = 1;
	        $row->dispo ='';
        }
        
        $imgs = ComJea::getImagesById($row->id) ;
        $rootURL = JURI::root();
        
		if (!empty($imgs['main_image']) && is_array($imgs['main_image'])) {
	        $imgs['main_image']['delete_url'] = $rootURL . 'administrator/index2.php?option=com_jea'
	            .'&amp;controller=properties&amp;task=deleteimg&amp;id='.$row->id.'&amp;cat=' . $this->_cat;
	        $imgs['main_image']['iptc_url'] = $rootURL . 'administrator/index.php?option=com_jea'
	            .'&amp;controller=properties&amp;tmpl=component&amp;task=editiptc&amp;id='.$row->id; 
		}
            
        foreach ( $imgs['secondaries_images']  as $k => $v) {
        	$imgs['secondaries_images'][$k]['delete_url'] = $rootURL . 'administrator/index.php?option=com_jea'
                    .'&amp;controller=properties&amp;task=deleteimg&amp;id=' . $row->id
                    .'&amp;image='.$v['name'].'&amp;cat=' .$this->_cat ;
            $imgs['secondaries_images'][$k]['iptc_url'] = $rootURL . 'administrator/index.php?option=com_jea'
                    .'&amp;controller=properties&amp;tmpl=component&amp;task=editiptc&amp;id=' . $row->id
                    .'&amp;image='.urlencode($v['name']) ;
        }
        
		$result['row'] = $row;
        
        return $result + $imgs ;

    }

    function save()
    {
        $row = & $this->getRow();
        
        $datas = array(
        	'ref'            => JRequest::getVar( 'ref', '', 'POST' ),
        	'title'          => JRequest::getVar( 'title', '', 'POST' ),
        	'alias'          => JRequest::getVar( 'alias', '', 'POST' ),
			'type_id'        => JRequest::getInt( 'type_id', 0 , 'POST' ),
			'price'          => JRequest::getFloat( 'price', 0.0, 'POST' ),
			'adress'         => JRequest::getVar( 'adress' , '', 'POST' ),
			'town_id'        => JRequest::getInt( 'town_id', 0 , 'POST' ),
			'area_id'        => JRequest::getInt( 'area_id', 0 , 'POST' ),
			'zip_code'       => JRequest::getVar( 'zip_code' , '', 'POST' ),
			'department_id'  => JRequest::getInt( 'department_id', 0 , 'POST' ),
			'condition_id'   => JRequest::getInt( 'condition_id', 0 , 'POST' ),
			'living_space'   => JRequest::getInt( 'living_space', 0 , 'POST' ),
			'land_space'     => JRequest::getInt( 'land_space', 0 , 'POST' ),
			'rooms'          => JRequest::getInt( 'rooms', 0 , 'POST' ),
			'charges'        => JRequest::getFloat( 'charges', 0.0, 'POST' ),
			'fees'           => JRequest::getFloat( 'fees', 0.0, 'POST' ),
			'hot_water_type' => JRequest::getInt( 'hot_water_type', 0 , 'POST' ),
			'heating_type'   => JRequest::getInt( 'heating_type', 0 , 'POST' ),
			'bathrooms'      => JRequest::getInt( 'bathrooms', 0 , 'POST' ),
			'toilets'        => JRequest::getInt( 'toilets', 0 , 'POST' ),
			'availability'   => JRequest::getVar( 'availability' , '', 'POST' ),
			'floor'          => JRequest::getInt( 'floor', 0 , 'POST' ),
			'advantages'     => JRequest::getVar( 'advantages', array(), 'POST', 'array' ),
			'description'    => JRequest::getVar( 'description', '', 'POST', 'string', JREQUEST_ALLOWRAW ),
			'slogan_id'      => JRequest::getInt( 'slogan_id', 0 , 'POST' ),
			'published'      => JRequest::getInt( 'published', 0 , 'POST' ),
			'emphasis'       => JRequest::getInt( 'emphasis', 0 , 'POST' )
        );
        
        if ($created_by = JRequest::getInt( 'created_by', 0 , 'POST' )){
            $datas['created_by'] = $created_by;
        }
        
        
        if ( !$row->bind($datas) ) {
            JError::raiseWarning( 200, $row->getError() );
            return false;
        }
        
        $row->is_renting = $this->isRenting() ? 1 : 0;
        
        if ( ! $row->check() ) {
            JError::raiseWarning( 200, $row->getError() );
            return false;
        }
       
        if ( !$row->store() ) {
            JError::raiseWarning( 200, $row->getError() );
            return false;
        }
        
        //check newsletter
        $row->checkin();

		if ( !$this->_uploadImages($row->id) ) {
			JError::raiseWarning( 200, 'Image upload error' );
            return false;
		}
		
		$this->_lastId = $row->id;

        return true;
    }
    
    function copy()
	{
		$cids = implode( ',', $this->getCid() );
		$table =& $this->getTable();
		$nextOrdering = $table->getNextOrder();
		
		//only one request
		$inserts = array();
		$fields = $table->getPublicProperties();
		unset($fields['id']);
		unset($fields['checked_out']);
		unset($fields['checked_out_time']);
		
		$fields = array_keys($fields);
		
		$query = 'SELECT '.implode(', ', $fields).' FROM #__jea_properties WHERE id IN (' . $cids . ')';

		$rows = $this->_getList($query);
		
		foreach ($rows as $row){
		    $row = (array) $row;
		    $row['ref'] .= '_COPY'; 
		    $row['ordering'] = $nextOrdering;
		    $row['date_insert']  = date('Y-m-d H:i:s');
		    foreach($row as $k => $values) {
		        $row[$k] = $this->_db->Quote($values);
		    }
		    $inserts[]= '(' . implode(', ', $row) . ')';
		    $nextOrdering++;
		}
		
		$query = 'INSERT INTO #__jea_properties ('.implode(', ', $fields).') VALUES' . "\n"
		       . implode(", \n", $inserts);
		         
		$this->_db->setQuery($query);
		
	    if ( !$this->_db->query() ) {
			JError::raiseError( 500, $this->_db->getErrorMsg() );
			return false;
		}
		
		return true;
	}
    
    
    function remove()
	{
		$cid = $this->getCid() ;
		$cids = implode( ',', $cid );
		
		//only one request
		$this->_db->setQuery( 'DELETE FROM `#__jea_properties` WHERE id IN (' . $cids . ')' );
		
		if ( !$this->_db->query() ) {
			JError::raiseError( 500, $this->_db->getErrorMsg() );
			return false;
		}
		
		//remove image upload directory if exists
		jimport('joomla.filesystem.folder');
		
		foreach ( $cid as $id) {
            $dirimg = JPATH_ROOT.DS.'images'.DS.'com_jea'.DS.'images'.DS.$id ;
            if( JFolder::exists( $dirimg ) ) JFolder::delete( $dirimg );
        }
		
		return true;
	}
	
    
    function delete_img( $id, $image='' )
    {  
		
	    $row = & $this->getRow();
	    $image	= JRequest::getVar( 'image' , '');
	    
		$deleteFiles = array();
		$dir = JPATH_ROOT . DS . 'images' .DS. 'com_jea' .DS. 'images' .DS. $row->id.DS ;
		
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
		
		return true;
			
    }
    
    
    function saveGeolocalization()
    {
        $row = & $this->getRow();
        if(empty($row->id)){
            JError::raiseWarning( 200, 'Only existing property can save geolocalization' );
            return false;
        }
        
        $row->latitude  = JRequest::getVar('latitude', 0);
        $row->longitude = JRequest::getVar('longitude', 0);
       
        if ( !$row->store() ) {
            JError::raiseWarning( 200, $row->getError() );
            return false;
        }

        return true;        
    }
    
/* ------------------ Protected methods ----------------------- */
    
    
    function _uploadImages( $id=null )
    {
    	if (!$id) return false;
    	
    	require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'library/Http_File.php';
    	jimport('joomla.filesystem.folder');
    	
    	$base_upload_dir = JPATH_ROOT.DS . 'images' . DS . 'com_jea' . DS . 'images' ;
    	$validExtensions = array('jpg','JPG','jpeg','JPEG','gif','GIF','png','PNG') ;
    	
		$mainImage   = new Http_File( JRequest::getVar( 'main_image', array(), 'files', 'array' ) ) ;
    	$secondImage = new Http_File( JRequest::getVar( 'second_image', array(), 'files', 'array' ) );
        
        if ( !JFolder::exists($base_upload_dir) ) { JFolder::create($base_upload_dir); }

        $upload_dir = $base_upload_dir . DS . $id;
        
        $config =& ComJea::getParams();

        $maxPreviewWidth = $config->get('max_previews_width', 400) ;
        $maxPreviewHeight = $config->get('max_previews_height', 400) ;
        $maxThumbnailWidth = $config->get('max_thumbnails_width', 120);
        $maxThumbnailHeight = $config->get('max_thumbnails_height', 90);
        $jpgQuality = $config->get( 'jpg_quality' , 90) ;
        $cropThumbnails = $config->get( 'crop_thumbnails' , 0) ;

        //main image
        if ( $mainImage->isPosted() ){
            	
            if ( !JFolder::exists($upload_dir) ) { JFolder::create($upload_dir); }
            
            //First delete main image before upload
            $this->delete_img($id);
            	
            $mainImage->setValidExtensions( $validExtensions );
            $mainImage->setName('main.jpg');
            	
            if( !$fileName = $mainImage->moveTo($upload_dir) ){

                JError::raiseWarning( 200, JText::_( $mainImage->getError() ) );
                return false;
            }
            
            //make preview       
            $this->_resizeImage( $upload_dir.DS.$fileName, 
                                 $upload_dir.DS.'preview.jpg',
                                 $maxPreviewHeight,
                                 $maxPreviewWidth, 
                                 $jpgQuality );            
            
            	
            //make min
            if($cropThumbnails){
	            $this->_cropImage( $upload_dir.DS.'preview.jpg', 
	                               $upload_dir.DS.'min.jpg',
	                               $maxThumbnailHeight,
	                               $maxThumbnailWidth,
	                               $jpgQuality );            	
            } else {
	            $this->_resizeImage( $upload_dir.DS.'preview.jpg', 
	                                 $upload_dir.DS.'min.jpg',
	                                 $maxThumbnailHeight,
	                                 $maxThumbnailWidth,
	                                 $jpgQuality );
            }
        }

        if($secondImage->isPosted()){

            $upload_dir = $upload_dir.DS.'secondary';
            $preview_dir = $upload_dir.DS.'preview' ;
            $thumbnail_dir = $upload_dir.DS.'min' ;
        	if ( !JFolder::exists($upload_dir) ) { JFolder::create($upload_dir); }
        	if ( !JFolder::exists($preview_dir) ) { JFolder::create($preview_dir); }
        	if ( !JFolder::exists($thumbnail_dir) ) { JFolder::create($thumbnail_dir); }
        
            $secondImage->setValidExtensions( $validExtensions );
            $secondImage->nameToSafe();
            	
            if(! $fileName = $secondImage->moveTo( $upload_dir )){
                JError::raiseWarning( 200, JText::_( $secondImage->getError() ) );
                return false;
            }
            
            //make preview
            $this->_resizeImage( $upload_dir.DS.$fileName, 
                                 $preview_dir.DS.$fileName, 
                                 $maxPreviewHeight,
                                 $maxPreviewWidth, 
                                 $jpgQuality );	
            
            //make min
            if($cropThumbnails){
	            $this->_cropImage( $preview_dir.DS.$fileName, 
	                               $thumbnail_dir.DS.$fileName,
	                               $maxThumbnailHeight,
	                               $maxThumbnailWidth,
	                               $jpgQuality );            	
            } else {
            
	            $this->_resizeImage( $preview_dir.DS.$fileName, 
	                                 $thumbnail_dir.DS.$fileName,
	                                 $maxThumbnailHeight,
	                                 $maxThumbnailWidth,
	                                 $jpgQuality );
            }
        }
        return true;
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
			//var_dump($iptc);

			if($iptc->hasmeta){
				$ret->title       = $iptc->get(IPTC_HEADLINE);
				$ret->description = $iptc->get(IPTC_CAPTION);
			}
			
		}
		
		return $ret;
    }
    
    
    
    function saveIptc()
    {
    	require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'library/Iptc.php';
    	
    	$id = JRequest::getInt('id');
    	$image = JRequest::getVar('image', '');
    	$title = JRequest::getVar('title', '');
    	$description = JRequest::getVar('description', '');
	    
		$dir = JPATH_ROOT . DS . 'images' .DS. 'com_jea' 
		     .DS. 'images' . DS . $id;
		
		if( !$image ){
			$file = $dir . DS . 'main.jpg' ;
		} else {
			$file = $dir. DS . 'secondary'.DS.'preview'.DS. $image;
		}
		
		if(file_exists($file)) {
			
			$infos = getimagesize($file);
			
			// Iptc class works only with Jpg files
			if($infos[2] != IMAGETYPE_JPEG){
				return false;
			}
			
			$iptc = new iptc($file);
			
			if($iptc->hasmeta){
				$iptc->removeAllTags();
			}
			
			if(!empty($title)){
				$iptc->set(IPTC_HEADLINE,$title);
			}
			
			if(!empty($description)){
				$iptc->set(IPTC_CAPTION, $description);
			}
			
			$iptc->write();
		}
		
		return true;
    }
    
    
    function _resizeImage( $from, $to, $maxHeight=null, $maxWidth=null, $jpgQuality=90 )
    {
    		$gd =& $this->_getGd();
    	
    		$gd->load( $from );
    		
    		if ($maxHeight) {
    			
    			if ( $gd->getSize( 'height' ) > $maxHeight ) {
    				$gd->resize( null, $maxHeight );
    			}
    		
	            if ( $gd->getSize( 'width' ) > $maxWidth ) {
	                $gd->resize( $maxWidth , null );
	            }
	            
    		} else {
    			
    			if ( $gd->getSize( 'width' ) > $maxWidth ) {
	                $gd->resize( $maxWidth , null );
	            }
    		}
           	if($gd->type == 'jpeg'){
            	$gd->saveToJpeg( $to , $jpgQuality );
           	} else {
           		$gd->save($to);
           	}
    }
    
    function _cropImage($from, $to, $height=null, $width=null, $jpgQuality=90)
    {
    	$gd =& $this->_getGd();
    	$gd->load( $from );
    	
    	// Anticipate Height by cross product
    	$redim_height = (intval($gd->getSize( 'height' )) * intval($width)) / intval($gd->getSize( 'width' ));
    	
    	if($redim_height < $height){
    		$gd->resize( null, $height );
    	} else {
		    $gd->resize( $width , null );
    	}
    	
    	$gd->centerCrop($width, $height);
    	$gd->saveToJpeg( $to , $jpgQuality );
    }
    

    function & _getGd()
    {
    	static $gd = null;
    		
   		if ( $gd === null){
   			require JPATH_COMPONENT_ADMINISTRATOR.DS.'library/Gd/Transform.php';
   			$gd = new Gd_Transform();
   		}
   		
   		return $gd;
    }
     
}

