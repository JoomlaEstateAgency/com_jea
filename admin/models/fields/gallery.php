<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.filesystem.folder');
jimport('joomla.image');


/**
 * Form Field class for the Joomla Platform.
 * Displays options as a list of input[type="file"].
 * Multiselect may be forced to be true.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @see         JFormField
 * @since       11.1
 */

class JFormFieldGallery extends JFormField
{
    /**
     * The form field type.
     *
     * @var    string
     *
     * @since  11.1
     */
    protected $type = 'Gallery';


    /**
     * Method to get the list of input[type="file"]
     *
     * @return  string  The field input markup.
     *
     * @since   11.1
     */
    protected function getInput()
    {
        $output = '';

        $params = JComponentHelper::getParams('com_jea');
        $imgUploadNumber = $params->get('img_upload_number', 3);

        for ($i=0; $i < $imgUploadNumber; $i++) {
            $output .= '<input type="file" name="newimages[]" value=""  size="30" class="fltnone" /> <br />';
        }

        $output .= "\n";

        $images = (array) json_decode($this->value);
        $propertyId  = $this->form->getValue('id');

        $baseURL = JURI::root(true);
        $imgBaseURL  = $baseURL.'/images/com_jea/images/' . $propertyId;
        $imgBasePath = JPATH_ROOT.DS.'images'.DS.'com_jea'.DS.'images'.DS.$propertyId;

        if (!empty($images)) {
            $output .= "<ul class=\"gallery\">\n";
            foreach ($images as $k => $image) {
                $imgPath = $imgBasePath.DS.$image->name;
                try {
                    $infos = JImage::getImageFileProperties($imgPath);
                    $JImage = new JImage($imgPath);
                } catch (Exception $e) {
                    continue;
                }

                $thumbName = 'thumb-admin-'. $image->name;
                // Create the thumbnail
                if (!file_exists($imgBasePath.DS.$thumbName)) {
                    $thumb = $JImage->resize(150, 90);
                    $thumb->crop(150, 90, 0, 0);
                    $thumb->toFile($imgBasePath.DS.$thumbName);
                }
                $thumbUrl = $imgBaseURL .'/'. $thumbName;
                $url    = $imgBaseURL .'/'. $image->name;
                $weight = round($infos->bits/1024,1); // Ko

                $output .= "<li class=\"item-$k\">\n"
                . "<a href=\"{$url}\" title=\"Zoom\" class=\"imgLink\"><img src=\"{$thumbUrl}\" alt=\"{$image->name}\" /></a>\n"
                . "<div class=\"imgInfos\">\n"
                . $image->name . "<br />\n"
                . JText::_('COM_JEA_IMG_WIDTH') . ' : ' . $infos->width . ' px' . "<br />\n"
                . JText::_('COM_JEA_IMG_HEIGHT') . ' : ' . $infos->height . ' px' . "<br />\n"
                . "</div>\n"
                . "<div class=\"imgTools\">\n"
                . '  <a class="img-move-up" title="'.JText::_('JLIB_HTML_MOVE_UP').'"><img src="'. $baseURL . '/media/com_jea/images/sort_asc.png' .'" alt="Move up" /></a>'
                . '  <a class="img-move-down" title="'.JText::_('JLIB_HTML_MOVE_DOWN').'"><img src="'. $baseURL . '/media/com_jea/images/sort_desc.png' .'" alt="Move down" /></a>'
                . '  <a class="delete-img" title="'.JText::_('JACTION_DELETE').'"><img src="'. $baseURL . '/media/com_jea/images/media_trash.png' .'" alt="Delete" /></a>'
                . "</div>\n"
                        
                . "<div class=\"clr\"></div>\n"
                . '<label for="'. $this->id.$k .'title">'.JText::_('JGLOBAL_TITLE').'</label><input id="'. $this->id.$k .'title" type="text" name="'.$this->name.'['.$k .'][title]" value="'.$image->title.'" size="20"/><br />'
                . '<label for="'. $this->id.$k .'desc">'.JText::_('JGLOBAL_DESCRIPTION').'</label><input id="'. $this->id.$k .'desc" type="text" name="'. $this->name.'['.$k .'][description]" value="'.$image->description.'" size="40"/>'
                . '<input type="hidden" name="'. $this->name.'['.$k .'][name]" value="'.$image->name.'" />'
                . "<div class=\"clr\"></div>\n"
                . "</li>\n";

            }
            $output .= "</ul>\n";
            // Add javascript

            JFactory::getDocument()->addScriptDeclaration("
                window.addEvent('domready', function() {
                    var sortOptions = {
                        transition: Fx.Transitions.Back.easeInOut,
                        duration: 700,
                        mode: 'vertical',
                        onComplete: function() {
                           mySort.rearrangeDOM()
                        }
                    };

                    var mySort = new Fx.Sort($$('ul.gallery li'), sortOptions);

                    $$('a.delete-img').each(function(item) {
                        item.addEvent('click', function() {
                            this.getParent('li').destroy();
                            mySort = new Fx.Sort($$('ul.gallery li'), sortOptions);
                        });
                    });

                    $$('a.img-move-up').each(function(item) {
                        item.addEvent('click', function() {
                            var activeLi = this.getParent('li');
                            if (activeLi.getPrevious()) {
                                mySort.swap(activeLi, activeLi.getPrevious());
                            } else if (this.getParent('ul').getChildren().length > 1 ) {
                                // Swap with the last element
                            	mySort.swap(activeLi, this.getParent('ul').getLast('li'));
                            }
                        });
                    });

                     $$('a.img-move-down').each(function(item) {
                        item.addEvent('click', function() {
                            var activeLi = this.getParent('li');
                            if (activeLi.getNext()) {
                                mySort.swap(activeLi, activeLi.getNext());
                            } else if (this.getParent('ul').getChildren().length > 1 ) {
                                // Swap with the first element
                            	mySort.swap(activeLi, this.getParent('ul').getFirst('li'));
                            }
                        });
                    });
                    
                })"
            );
        }



         
        return $output;

    }



}
