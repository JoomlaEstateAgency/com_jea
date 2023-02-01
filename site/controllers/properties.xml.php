<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Site
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2020 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

/**
 * Properties xml controller class.
 *
 * @package     Joomla.Site
 * @subpackage  com_jea
 *
 * @since       2.0
 */
class JeaControllerProperties extends BaseController
{
	/**
	 * Generate KML
	 *
	 * @return void
	 */
	public function kml()
	{
		$app = Factory::getApplication();
		$Itemid = $app->input->getInt('Itemid', 0);

		$model = $this->getModel('Properties', 'JeaModel', array('ignore_request' => true));

		$filters = array_keys($model->getFilters());

		// Set the Model state
		foreach ($filters as $filter)
		{
			$model->setState('filter.' . $filter, $app->input->get('filter_' . $filter, null, 'default'));
		}

		// Deactivate pagination
		$model->setState('list.start', 0);
		$model->setState('list.limit', 0);

		// Set language state
		$model->setState('filter.language', $app->getLanguageFilter());

		$items = $model->getItems();

		$doc = new DomDocument;

		$kmlNode = $doc->createElement('kml');
		$kmlNode->setAttribute('xmlns', 'http://www.opengis.net/kml/2.2');
		$documentNode = $doc->createElement('Document');

		foreach ($items as $row)
		{
			if (abs($row->latitude) > 0 && abs($row->longitude) > 0)
			{
				$placemarkNode = $doc->createElement('Placemark');
				$nameNode = $doc->createElement('name');
				$descrNode = $doc->createElement('description');
				$pointNode = $doc->createElement('Point');

				/*
                                                                 * Http://code.google.com/intl/fr/apis/kml/documentation/kml_tut.html#placemarks
                 * (longitude, latitude, and optional altitude)
                 */

				$coordinates = $row->longitude . ',' . $row->latitude . ',0.000000';
				$coordsNode = $doc->createElement('coordinates', $coordinates);

				$row->slug = $row->alias ? ($row->id . ':' . $row->alias) : $row->id;

				$url = Route::_('index.php?option=com_jea&view=property&id=' . $row->slug . '&Itemid=' . $Itemid);

				if (empty($row->title))
				{
					$name = ucfirst(Text::sprintf('COM_JEA_PROPERTY_TYPE_IN_TOWN', $row->type, $row->town));
				}
				else
				{
					$name = $row->title;
				}

				$description = '<div style="clear:both"></div>';

				$images = json_decode($row->images);
				$image = null;

				if (!empty($images) && is_array($images))
				{
					$image = array_shift($images);
					$imagePath = JPATH_ROOT . '/images/com_jea';
					$imageUrl = '';

					if (file_exists($imagePath . '/thumb-min/' . $row->id . '-' . $image->name))
					{
						// If the thumbnail already exists, display it directly
						$baseURL = Uri::root(true);
						$imageUrl = $baseURL . '/images/com_jea/thumb-min/' . $row->id . '-' . $image->name;
					}
					elseif (file_exists($imagePath . '/images/' . $row->id . '/' . $image->name))
					{
						// If the thumbnail doesn't exist, generate it and output it on the fly
						$url = 'index.php?option=com_jea&task=thumbnail.create&size=min&id=' . $row->id . '&image=' . $image->name;
						$imageUrl = Route::_($url);
					}

					$description .= '<img src="' . $imageUrl . '" alt="' . $image->name . '.jpg" style="float:left;margin-right:10px" />';
				}

				$description .= substr(strip_tags($row->description), 0, 255)
					. ' ...<p><a href="' . $url . '">' . Text::_('COM_JEA_DETAIL')
					. '</a></p><div style="clear:both"></div>';

				$nameCDATA = $doc->createCDATASection($name);
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
