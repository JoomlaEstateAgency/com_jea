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

jimport( 'joomla.application.component.model' );
jimport( 'joomla.application.component.view' );
jimport( 'joomla.application.component.controller' );

Class JEA_AbstractController extends JController
{
	
   // var $_view = null;

	var $_viewDatas = array();
	var $_model = null;
	var $_application = null;
	
	//var $_option = '';
	
	var $_cid = array(); //currents id


	
	/**
	 * Constructor.
	 *
	 * @access	public
	 */
	function JEA_AbstractController()
	{
	    parent::__construct();
	    
	     //index will be default method
	    $this->registerDefaultTask( 'index' ); 
	    
	    //global $mainframe;
	    $this->_application = &JFactory::getApplication();
	    
	    $this->_cid = JRequest::getVar( 'cid', array(0), '', 'array' );
		JArrayHelper::toInteger( $this->_cid, array(0) );
		
		
		//get the config
		
		//$registry =& JRegistry::getInstance( 'jea.config' );
		//$config =& $registry->toObject();

		//echo 'test',$config->thousands_separator, 'test';
		
		//echo $registry->toString();
		
		
	    
	   // $this->_defaulLimit = 
	    
	    //dump ($mainframe);



		
		
/*
		$this->_view = & $this->getView( $viewName, $viewType);

		// Get/Create the model
		if ($this->_model = & $this->getModel($viewName)) {
			// Push the model into the view (as default)
			$this->_view->setModel($this->_model, true);
		}

		// Set the layout
		$this->_view->setLayout($viewLayout);
	    



	    /*global $mainframe, $mosConfig_list_limit, $mosConfig_live_site, $option ;
		
		$this->_mainframe = $mainframe;
		$this->_defaulLimit = $mosConfig_list_limit;
		$this->_option = $option; //componant name
		
		$this->_view = new CatalogView();
		$this->_view->setTmplPath(COM_CATALOG_PATH._S_.'views');
		$this->_view->assign('option', $option );
		$this->_view->assign('mosConfig_live_site', $mosConfig_live_site );
		$this->_cid = josGetArrayInts( 'cid' ); //get $_POST['cid[]']
		*/
		
	}
	
	/*function execute( $task )
	{
	    parent::execute( $task );
	}*/
	
	function display( $layout = '' )
	{
	   
	    $viewName = str_replace('jea_', '', $this->_name);
	    
	    
	    $path = JPath::find($this->_path['view'], $viewName.'.html.php');
	    
	    if ($path) {
	        
	        require $path;
	        
	        $config = array();
	        $config['name'] = $viewName ;
	        $config['template_path'] = dirname($path).DS.'tmpl';
	        
	        if ( !empty( $layout ) )
	            $config['layout'] = $layout;
	        
	        $viewMethod = $layout;
	        
	        $viewClass =  ucfirst($viewName) . 'View' ;
	        if ( !class_exists($viewClass) )   
	            die( $viewClass . ' class must be defined in ' . $viewName.'.html.php' );
	            
	        $view = new $viewClass($config);
	        $view->assign($this->_viewDatas);
	        
	       if ( $this->_model !== null ) {
				// Push the model into the view (as default)
				$view->setModel($this->_model, true);

	       }
			
	        if ( method_exists( $view, $viewMethod ) ) {
	            
				$view->$viewMethod();
				 
	        } elseif ( method_exists( $view, 'index' ) ) {
	            
	            $view->index();
	        }
	        
	        $view->display();
	        
	    }

	   
	}
	function preDispatch(){}
	
	function postDispatch(){}
	

}

