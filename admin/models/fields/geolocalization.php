<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;


/**
 * Form Field class for the Joomla Platform.
 * Displays button to geolocalize coordinates with a map.
 * Multiselect may be forced to be true.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @see         JFormField
 * @since       11.1
 */

class JFormFieldGeolocalization extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 *
	 * @since  11.1
	 */
	protected $type = 'Geolocalization';
	

	
	
	/**
	 * Method to get the button to geolocalize coordinates with a map.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
	    
	    $ouptut ='';
	    $url = 'index.php?option=com_jea&amp;view=property&amp;layout=geolocalization&amp;tmpl=component';
	    
	    $ouptut = '<div class="button2-left">'. "\n"
                . '<div class="blank"><a class="modal" href="#map-box-content" rel="{handler: \'clone\', size: {x: 800, y: 500}, onOpen:initBoxContent, onClose:closeBoxContent }">' . JText::_('COM_JEA_OPEN_MAP'). '</a></div>'. "\n"
                . '</div>'. "\n"
                . '<div id="map-box-content" class="map-box-content" style="display:none">'. "\n"
                . JText::_('Latitude') . ' : <input type="text" readonly="readonly" class="readonly input-latitude" value="" />'
                . JText::_('Longitude') . ' : <input type="text" readonly="readonly" class="readonly input-longitude" value="" />'
                . '  <div class="map-box-container" style="width: 100%; height: 480px"></div>'. "\n"
                . '</div>'. "\n";
                
        // Load the modal behavior script.
		JHtml::_('behavior.modal');
                
        $document = JFactory::getDocument();
        $langs  = explode('-', $document->getLanguage());
        
        $lang   = $langs[0];
        $region = $langs[1];
        $fieldDepartment = $this->form->getField('department_id');
        $fieldTown = $this->form->getField('town_id');
        $fieldAddress = $this->form->getField('address');
        $fieldLongitude = $this->form->getField('longitude');
        $fieldLatitude = $this->form->getField('latitude');
        $markerLabel = JText::_('Drag and drop the marker to setup your position');
                
        JFactory::getDocument()->addScriptDeclaration("
        	function initBoxContent(elementContent) {
        	    var latitude = document.id('{$fieldLatitude->id}').value;
        	    var longitude = document.id('{$fieldLongitude->id}').value;
        	    var address = document.id('{$fieldAddress->id}').value;
        	    var town = document.id('{$fieldTown->id}').getSelected().pick();
        	    var department = document.id('{$fieldDepartment->id}').getSelected().pick();
        	    var zoom = 6;
        	    var request = '{$lang}';
        	    
        	    if (address && town && town.get('value') > 0){
                    zoom = 16;
                    request = address + ', ' + town.get('text') + ', {$lang}';                                   
                } else if (town && town.get('value') > 0){
                    zoom = 13;
                    request = town.get('text') + ', {$lang}';  
                } else if (department && department.get('value') > 0) {
                    zoom = 8;
                    request = department.get('text') + ', {$lang}'; 
                }

        	    var inputLatitude  = elementContent.getElement('.input-latitude');
        	    var inputLongitude = elementContent.getElement('.input-longitude');
        	    var mapContainer   = elementContent.getElement('.map-box-container');
        	    
        	    var initMap = function(myLatlng) {
                    inputLongitude.set('value', myLatlng.lng());
                    inputLatitude.set('value', myLatlng.lat());
                    
                    var options = {
                      zoom : zoom,
                      center : myLatlng,
                      mapTypeId : google.maps.MapTypeId.ROADMAP
                    };
                    
                    var map = new google.maps.Map(mapContainer, options);
                    
                    var marker = new google.maps.Marker({
                        position: myLatlng, 
                        map: map, 
                        title: '{$markerLabel}',
                        draggable: true,
                        cursor: 'move'
                    });
                    
                    google.maps.event.addListener(marker, 'dragend', function(mouseEvent) {
                        inputLongitude.setProperty('value', mouseEvent.latLng.lng());
                        inputLatitude.setProperty('value', mouseEvent.latLng.lat());
                    });
                };
        	    
        	    elementContent.getElement('.map-box-content').setStyle('display', 'block');
        	    
        	    if (longitude != 0 && latitude != 0) {
                    var myLatlng = new google.maps.LatLng(latitude,longitude);
                    initMap(myLatlng);
                } else {
                	var geocoder = new google.maps.Geocoder();
                	var opts = {'address':request, 'language':'{$lang}', 'region':'{$region}'};
                	
                    geocoder.geocode(opts, function(results, status) {
                    	if (status == google.maps.GeocoderStatus.OK) {
                    		var myLatlng = results[0].geometry.location;
                    		initMap(myLatlng);
                    	}
                    });
                }
        	}
        	
        	function closeBoxContent(elementContent)
        	{
        	    document.id('{$fieldLatitude->id}').set('value', elementContent.getElement('.input-latitude').value);
        	    document.id('{$fieldLongitude->id}').set('value', elementContent.getElement('.input-longitude').value);
        	}
        ");

        JFactory::getDocument()->addScript('http://maps.google.com/maps/api/js?sensor=false&language=' 
                                                          . $lang . '&region=' . $region);

        return $ouptut;
	}
	
	
}
