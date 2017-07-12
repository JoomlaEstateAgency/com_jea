<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Site
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2017 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
jimport('joomla.image');

/**
 * Thumbnail controller class.
 *
 * @package     Joomla.Site
 * @subpackage  com_jea
 *
 * @since       2.0
 */
class JeaControllerThumbnail extends JControllerLegacy
{
	/**
	 * Create a thumbnail
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function create()
	{
		/* @var JApplicationWeb  $application */
		$application = JFactory::getApplication();
		$output      = '';
		$size        = $this->input->getCmd('size', '');
		$image       = $_REQUEST['image'];
		$id          = $this->input->getInt('id', 0);
		$imagePath   = JPATH_ROOT . '/images/com_jea/images/' . $id . '/' . $image;
		$thumbDir    = JPATH_ROOT . '/images/com_jea/thumb-' . $size;
		$thumbPath   = $thumbDir . '/' . $id . '-' . $image;

		if (empty($image))
		{
			throw new RuntimeException('Empty \'image\' parameter', 500);
		}

		if (!in_array($size, array('min', 'medium')))
		{
			throw new RuntimeException('The image size is not recognized', 500);
		}

		if (file_exists($thumbPath))
		{
			$output = readfile($thumbPath);
		}
		elseif (file_exists($imagePath))
		{
			if (! JFolder::exists($thumbPath))
			{
				JFolder::create($thumbDir);
			}

			$params = JComponentHelper::getParams('com_jea');

			if ($size == 'medium')
			{
				$width = $params->get('thumb_medium_width', 400);
				$height = $params->get('thumb_medium_height', 300);
			}
			else
			{
				$width = $params->get('thumb_min_width', 120);
				$height = $params->get('thumb_min_height', 90);
			}

			$quality = (int) $params->get('jpg_quality', 90);
			$cropThumbnails = (bool) $params->get('crop_thumbnails', 0);
			$JImage = new JImage($imagePath);

			if ($cropThumbnails)
			{
				$thumb = $JImage->resize($width, $height, true, JImage::SCALE_OUTSIDE);
				$left = $thumb->getWidth() > $width ? intval(($thumb->getWidth() - $width) / 2) : 0;
				$top = $thumb->getHeight() > $height ? intval(($thumb->getHeight() - $height) / 2) : 0;
				$thumb->crop($width, $height, $left, $top, false);
			}
			else
			{
				$thumb = $JImage->resize($width, $height);
			}

			$thumb->toFile($thumbPath, IMAGETYPE_JPEG, array('quality' => $quality));

			$output = readfile($thumbPath);
		}
		else
		{
			throw new RuntimeException('The image ' . $image . ' was not found', 500);
		}

		$application->setHeader('Content-Type', 'image/jpeg', true);
		$application->setHeader('Content-Transfer-Encoding', 'binary', true);
		$application->sendHeaders();

		echo $output;

		$application->close();
	}
}
