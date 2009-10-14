<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 * 
 * @version     0.9 2009-10-14
 * @package     Jea.site.sef
 * @copyright   Copyright (C) 2008 PHILIP Sylvain. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 * Joomla Estate Agency is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses.
 * 
 */

// This file is a plugin to sh404SEF component.
// You need to install sh404SEF component if you want SEO friendly URLs with JEA


defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

// ------------------  standard plugin initialize function - don't change ---------------------------
global $sh_LANG, $sefConfig;  
$shLangName = '';
$shLangIso = '';
$title = array();
$shItemidString = '';
$dosef = shInitializePlugin( $lang, $shLangName, $shLangIso, $option);
if ($dosef == false) return;
// ------------------  standard plugin initialize function - don't change ---------------------------

// remove common URL from GET vars list, so that they don't show up as query string in the URL
shRemoveFromGETVarsList('option');
shRemoveFromGETVarsList('lang');
if (!empty($view))
  shRemoveFromGETVarsList('view');
if (!empty($Itemid))
  shRemoveFromGETVarsList('Itemid');
if (!empty($limit))  
shRemoveFromGETVarsList('limit');
if (isset($limitstart)) 
  shRemoveFromGETVarsList('limitstart'); // limitstart can be zero

// start by inserting the menu element title (just an idea, this is not required at all)
$view = isset($view) ? @$view : null;
$Itemid = isset($Itemid) ? @$Itemid : null;
$shName = shGetComponentPrefix($option); 
$shName = empty($shName) ?  getMenuTitle($option, '', $Itemid, null, $shLangName) : $shName;
$shName = (empty($shName) || $shName == '/') ? 'jea' : $shName;




switch($view)
{
    case 'properties':
        
        if (!empty($id)) 
            shRemoveFromGETVarsList('id');
        
        $title[] = $shName;
        
        if (!empty($id)){
            $q = 'SELECT p.id AS id, ttype.value AS type, ttown.value AS town FROM #__jea_properties AS p' . PHP_EOL .
            'LEFT JOIN #__jea_types as ttype ON ttype.id = p.type_id'. PHP_EOL .
            'LEFT JOIN #__jea_towns as ttown ON ttown.id = p.town_id'. PHP_EOL .
            'WHERE p.id = '. intval($id);

            $database->setQuery($q);
            $property = $database->loadObject(); 
            $title[]  = $id .'-' . $property->type . '--' . $property->town ;
        }
        break;
        
    case 'manage' :
        $dosef = false;  // these tasks do not require SEF URL
        break;
}


// ------------------  standard plugin finalize function - don't change ---------------------------  
if ($dosef){
   $string = shFinalizePlugin( $string, $title, $shAppendString, $shItemidString, 
      (isset($limit) ? @$limit : null), (isset($limitstart) ? @$limitstart : null), 
      (isset($shLangName) ? @$shLangName : null));
}      
// ------------------  standard plugin finalize function - don't change ---------------------------