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

jimport('joomla.application.component.controller');

class JeaControllerProperties extends JController
{

    public function kml()
    {
        $app = JFactory::getApplication();
        $model = $this->getModel('Properties', 'JeaModel', array('ignore_request' => true));

        $filters = $model->getFilters();
        // Set the Model state
        foreach ($filters as $name => $value) {
            $model->setState('filter.'.$name, $app->input->get('filter_'.$name, null, 'default'));
        }
        // Deactivate pagination
        $model->setState('list.start', 0);
        $model->setState('list.limit', 0);
        
        
        $items = $model->getItems();

        $doc = new DomDocument();
        $kmlNode = $doc->createElement('kml');
        $kmlNode->setAttribute('xmlns', 'http://www.opengis.net/kml/2.2');
        $documentNode = $doc->createElement('Document');
        
        foreach($items as $row) {
            if(abs($row->latitude) > 0 && abs($row->longitude) > 0) {

                $placemarkNode = $doc->createElement('Placemark');
                $nameNode      = $doc->createElement('name');
                $descrNode     = $doc->createElement('description');
                $pointNode     = $doc->createElement('Point');

                // http://code.google.com/intl/fr/apis/kml/documentation/kml_tut.html#placemarks
                // (longitude, latitude, and optional altitude)
                $coordinates = $row->longitude.',' .  $row->latitude . ',0.000000';
                $coordsNode    = $doc->createElement('coordinates', $coordinates);
                
                $row->slug = $row->alias ? ($row->id . ':' . $row->alias) : $row->id;

                $url = JRoute::_( 'index.php?index.php?option=com_jea&view=properties&id='.$row->slug);

                if (empty($row->title)) {
                    $name = ucfirst(JText::sprintf('PROPERTY TYPE IN TOWN', $row->type, $row->town));
                } else {
                    $name = $row->title;
                }

                $name = '<a href="'.$url.'">' . $name 
                      . ' (' . JText::_('Ref' ) . ' : ' . $row->ref. ')</a>' ;

                $description = '<div style="clear:both"></div>';

                if ( is_file( JPATH_ROOT.DS.'images'.DS.'com_jea'.DS.'images'.DS.$row->id.DS.'min.jpg' ) ) {
                     $description .= '<img src="'.JURI::root().'images/com_jea/images/'
                      .$row->id.'/min.jpg" alt="min.jpg" style="float:left;margin-right:10px" />';
                }

    		    $description .= substr(strip_tags($row->description), 0, 255) . ' ...'
                . '<p><a href="'.$url.'">'
                . JText::_('READMORE') . '</a></p>'
                . '<div style="clear:both"></div>';

                
                $nameCDATA        = $doc->createCDATASection($name);
                $descriptionCDATA = $doc->createCDATASection($description);
                $nameNode->appendChild($nameCDATA);
                $descrNode->appendChild($descriptionCDATA);
                $pointNode->appendChild($coordsNode);

                $placemarkNode->appendChild($nameNode);
                $placemarkNode->appendChild($descrNode);
                $placemarkNode->appendChild($pointNode);
                
                $documentNode->appendChild($placemarkNode);
            }
        }

        $kmlNode->appendChild($documentNode);
        $doc->appendChild($kmlNode);
        echo $doc->saveXML();
    }
    
    


}
