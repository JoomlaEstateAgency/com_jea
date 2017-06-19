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

jimport('joomla.filesystem.folder');
jimport('joomla.image');

/**
 * Form Field class for JEA.
 * Provides a complete widget to manage a gallery
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @see         JFormField
 *
 * @since       2.0
 */
class JFormFieldGallery extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var string
	 */
	protected $type = 'Gallery';

	/**
	 * Method to get the list of input[type="file"]
	 *
	 * @return string The field input markup.
	 */
	protected function getInput()
	{
		$output = '';

		$params = JComponentHelper::getParams('com_jea');

		if (is_string($this->value))
		{
			$images = (array) json_decode($this->value);
		}
		else
		{
			$images = (array) $this->value;

			foreach ($images as $k => $image)
			{
				$images[$k] = (object) $image;
			}
		}

		$propertyId = $this->form->getValue('id');

		$baseURL = JURI::root(true);
		$imgBaseURL = $baseURL . '/images/com_jea/images/' . $propertyId;
		$imgBasePath = JPATH_ROOT . '/images/com_jea/images/' . $propertyId;

		foreach ($images as $k => &$image)
		{
			$imgPath = $imgBasePath . '/' . $image->name;

			try
			{
				$infos = JImage::getImageFileProperties($imgPath);
			}
			catch (Exception $e)
			{
				$image->error = 'Recorded Image ' . $image->name . ' cannot be accessed';
				continue;
			}

			$thumbName = 'thumb-admin-' . $image->name;

			// Create the thumbnail
			if (!file_exists($imgBasePath . '/' . $thumbName))
			{
				try
				{
					// This is where the JImage will be used, so only create it here
					$JImage = new JImage($imgPath);
					$thumb = $JImage->resize(150, 90);
					$thumb->crop(150, 90, 0, 0);
					$thumb->toFile($imgBasePath . '/' . $thumbName);

					// To avoid memory overconsumption, destroy the JImage. We don't need it anymore
					$JImage->destroy();
					$thumb->destroy();
				}
				catch (Exception $e)
				{
					$image->error = 'Thumbnail for ' . $image->name . ' cannot be generated';
					continue;
				}
			}

			$image->thumbUrl = $imgBaseURL . '/' . $thumbName;
			$image->url = $imgBaseURL . '/' . $image->name;

			// Kbytes
			$image->weight = round($infos->bits / 1024, 1);
			$image->height = $infos->height;
			$image->width = $infos->width;
		}

		$layoutModel = array (
			'uploadNumber' => $params->get('img_upload_number', 3),
			'images' => $images,
			'name' => $this->name,
		);

		return JLayoutHelper::render('jea.fields.gallery', $layoutModel);
	}
}
