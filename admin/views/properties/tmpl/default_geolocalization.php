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

JHTML::_('behavior.mootools');
JHTML::stylesheet('system.css', 'administrator/templates/system/css/');

$document = &JFactory::getDocument();
$langs  = explode('-', $document->getLanguage());

$lang   = $langs[0];
$region = $langs[1];
$town = '';
$department = '';

$db = JFactory::getDBO();

if(!empty($this->row->town_id)) {
    $db->setQuery('SELECT value FROM #__jea_towns WHERE id='. intval($this->row->town_id));
    $town = $db->loadResult();
}
if(!empty($this->row->department_id)) {
    $db->setQuery('SELECT value FROM #__jea_departments WHERE id='. intval($this->row->department_id));
    $department = $db->loadResult();
}


if(!empty($this->row->adress) && !empty($town)){
    $zoom = 16;
    $address = addslashes($this->row->adress) . ', ' . addslashes($town) . ', '. $lang;                                   
} elseif (!empty($town)) {
    $zoom = 13;
    $address = addslashes($town) . ', '. $lang;  
} elseif (!empty($department)) {
    $zoom = 8;
    $address = addslashes($department) . ', '. $lang;  
} else {
    $zoom = 6;
    $address = $lang;
}

$markerLabel = JText::_('Drag and drop the marker to setup your position');
                                                                 
$script = <<<EOD
function initMap(myLatlng) {
    
    $('longitude').setProperty('value', myLatlng.lng());
    $('latitude').setProperty('value', myLatlng.lat());
    
    var options = {
      zoom : $zoom,
      center : myLatlng,
      mapTypeId : google.maps.MapTypeId.ROADMAP
    };
    
    var map = new google.maps.Map($("map_canvas"), options);
    
    var marker = new google.maps.Marker({
        position: myLatlng, 
        map: map, 
        title: '$markerLabel',
        draggable: true,
        cursor: 'move'
    });
    
    google.maps.event.addListener(marker, 'dragend', function(mouseEvent) {
        $('longitude').setProperty('value', mouseEvent.latLng.lng());
        $('latitude').setProperty('value', mouseEvent.latLng.lat());
    });
}

window.addEvent("domready", function(){
    var longitude = {$this->row->longitude};
    var latitude  = {$this->row->latitude};

    if( longitude != 0 && latitude != 0 ) {
        var myLatlng = new google.maps.LatLng(latitude,longitude);
        initMap(myLatlng);
    } else {
    	var geocoder = new google.maps.Geocoder();
    	var opts = {'address':'$address', 'language':'$lang', 'region':'$region'};
        geocoder.geocode(opts, function(results, status) {
        	if (status == google.maps.GeocoderStatus.OK) {
        		var myLatlng = results[0].geometry.location;
        		initMap(myLatlng);
        	}
        });
    }
});

window.addEvent("unload", function(){
 
});
EOD;

$document->addScript('http://maps.google.com/maps/api/js?sensor=false&language=' . $lang
                                                                 . '&region=' . $region );
$document->addScriptDeclaration($script);
?>

<h1><?php echo JText::_('Geolocalization')?> [<?php echo $this->row->ref ?> - 
<small>long : <?php echo $this->row->longitude ?>, lat: <?php echo $this->row->latitude ?> ]</small></h1>

<form action="index.php?option=com_jea&controller=properties" method="post" name="adminForm" id="adminForm" >

<p>
<label for="longitude"><?php echo JText::_('Longitude')?></label>
<input type="text" name="longitude" id="longitude" value="" style="margin-right: 10px"/>
<label for="latitude"><?php echo JText::_('Latitude')?></label>
<input type="text" name="latitude" id="latitude" value="" style="margin-right: 10px" />
<input type="submit" value="<?php echo JText::_('Save') ?>" />
<input type="hidden" name="task"  value="savegeolocalization" />
<input type="hidden" name="id"    value="<?php echo $this->row->id ?>" />
</p>

</form>

<div id="map_canvas" style="width: 100%; height: 400px"></div>
