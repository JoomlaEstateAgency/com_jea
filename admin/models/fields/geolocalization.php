<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2017 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

/**
 * Form Field class for JEA.
 * Displays button to geolocalize coordinates with a map.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @see         JFormField
 *
 * @since       2.0
 */
class JFormFieldGeolocalization extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var string
	 *
	 */
	protected $type = 'Geolocalization';

	/**
	 * Method to get the button to geolocalize coordinates with a map.
	 *
	 * @return string The field input markup.
	 */
	protected function getInput()
	{
		$params = JComponentHelper::getParams('com_jea');
		$ouptut = '';
		$url = 'index.php?option=com_jea&amp;view=property&amp;layout=geolocalization&amp;tmpl=component';

		// TODO : use JLayout
		$ouptut = '<div class="button2-left">' . "\n" .
				'<div class="blank"><a class="modal" href="#map-box-content"' .
				' rel="{handler: \'clone\', size: {x: 800, y: 500}, onOpen:initBoxContent, onClose:closeBoxContent }">' .
				JText::_('COM_JEA_MAP_OPEN') . '</a></div>' . "\n" . '</div>' . "\n" .
				'<div id="map-box-content" class="map-box-content" style="display:none">' . "\n" . JText::_('COM_JEA_FIELD_LATITUDE_LABEL') .
				' : <input type="text" readonly="readonly" class="readonly input-latitude" value="" />' . JText::_('COM_JEA_FIELD_LONGITUDE_LABEL') .
				' : <input type="text" readonly="readonly" class="readonly input-longitude" value="" />' .
				'<div class="map-box-container" style="width: 100%; height: 480px"></div>' . "\n" . '</div>' . "\n";

		// Load the modal behavior script.
		JHtml::_('behavior.modal');

		$document = JFactory::getDocument();
		$langs = explode('-', $document->getLanguage());

		$lang = $langs[0];
		$region = $langs[1];
		$fieldDepartment = $this->form->getField('department_id');
		$fieldTown = $this->form->getField('town_id');
		$fieldAddress = $this->form->getField('address');
		$fieldLongitude = $this->form->getField('longitude');
		$fieldLatitude = $this->form->getField('latitude');
		$markerLabel = addslashes(JText::_('COM_JEA_MAP_MARKER_LABEL'));

		JFactory::getDocument()->addScriptDeclaration(
			"
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
                    var retry = 0;
                    var geocodeCallBack = function(results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            var myLatlng = results[0].geometry.location;
                            initMap(myLatlng);
                        } else if (status == google.maps.GeocoderStatus.ZERO_RESULTS && retry < 3 ) {
                            if (town && town.get('value') > 0 && retry == 0) {
                                // retry with town
                                zoom = 13;
                                request = town.get('text') + ', {$lang}';
                            } else if (department && department.get('value') > 0 && retry == 1) {
                                // retry with department
                                zoom = 8;
                                request = department.get('text') + ', {$lang}';
                            } else {
                                zoom = 6;
                                request = '{$lang}';
                            }
                            var opts = {'address':request, 'language':'{$lang}', 'region':'{$region}'};
                            geocoder.geocode(opts, geocodeCallBack);
                            retry++;
                        }
                    };
                    geocoder.geocode(opts, geocodeCallBack);
                }
            }

            function closeBoxContent(elementContent)
            {
                document.id('{$fieldLatitude->id}').set('value', elementContent.getElement('.input-latitude').value);
                document.id('{$fieldLongitude->id}').set('value', elementContent.getElement('.input-longitude').value);
            }"
		);

		JFactory::getDocument()->addScript(
			'http://maps.google.com/maps/api/js?key=' . $params->get('googlemap_api_key') . '&language=' . $lang . '&region=' . $region
		);

		return $ouptut;
	}
}
