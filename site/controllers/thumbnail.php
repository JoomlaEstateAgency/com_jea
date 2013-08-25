<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @version     $Id$
 * @package     Joomla.Site
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2012 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');
jimport('joomla.filesystem.folder');
jimport('joomla.image');


/**
 * Thumbnail controller class.
 *
 * @package     Joomla.Site
 * @subpackage  com_jea
 */
class JeaControllerThumbnail extends JControllerLegacy
{

    public function create()
    {   
        $output = '';
        
        $size = JRequest::getCmd('size', '');
        if (!in_array($size, array('min', 'medium'))) {
            throw new Exception('The image size is not recognized', 500);
        }

        $image = JRequest::getVar('image', '');
        $id = JRequest::getInt('id', 0);
        $imagePath = JPATH_ROOT.DS.'images'.DS.'com_jea'.DS.'images'.DS.$id.DS.$image;
        $thumbDir  = JPATH_ROOT.DS.'images'.DS.'com_jea'.DS.'thumb-'.$size;
        $thumbPath = $thumbDir.DS.$id.'-'.$image;

        if (file_exists($thumbPath)) {
                $output = readfile($thumbPath);
                
        } elseif (file_exists($imagePath)) {
            
            if (!JFolder::exists($thumbPath)) {
                JFolder::create($thumbDir);
            }

            $params = JComponentHelper::getParams('com_jea');

            if ($size == 'medium') {
                $width  = $params->get('thumb_medium_width', 400);
                $height = $params->get('thumb_medium_height', 300);
            } else {
                $width  = $params->get('thumb_min_width', 120);
                $height = $params->get('thumb_min_height', 90);
            }

            $quality = (int) $params->get('jpg_quality' , 90) ;
            $cropThumbnails = (bool) $params->get('crop_thumbnails', 0);
            $JImage = new JImage($imagePath);
            
            if ($cropThumbnails) {
                $thumb = $JImage->resize($width, $height, true, JImage::SCALE_OUTSIDE);
                $left = $thumb->getWidth() > $width ? intval(($thumb->getWidth() - $width) / 2) : 0;
                $top = $thumb->getHeight() > $height ? intval(($thumb->getHeight() - $height) / 2) : 0;
                $thumb->crop($width, $height, $left, $top, false);
            } else {
                $thumb = $JImage->resize($width, $height);
            }
            $thumb->toFile($thumbPath, IMAGETYPE_JPEG, array('quality'=> $quality));
            $output = readfile($thumbPath);

        } else {
            throw new Exception('The image '.$image.' was not found', 500);
        }

        JResponse::setHeader('Content-Type', 'image/jpeg', true);
        JResponse::setHeader('Content-Transfer-Encoding', 'binary');
        JResponse::allowCache(false);
        JResponse::setBody($output);
        echo JResponse::toString();
        exit();
    }

}
