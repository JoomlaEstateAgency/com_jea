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
 * Displays options as a list of check boxes.
 * Multiselect may be forced to be true.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @see         JFormField
 * @since       11.1
 */

class JFormFieldAmenities extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 *
	 * @since  11.1
	 */
	protected $type = 'Amenities';
	
	/**
	 * Flag to tell the field to always be in multiple values mode.
	 *
	 * @var    boolean
	 * @since  11.1
	 */
	protected $forceMultiple = true;
	
	
	/**
	 * Method to get the field input markup for check boxes.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		$options = $this->getOptions();
		$output = '';

	    if (!empty( $this->value)) {
	        $this->value = explode( '-' , $this->value );
	    } else {
	        $this->value = array();
	    }
	    
	    foreach ( $options as $row ) {
	        
	        $checked = '';
	        
	        if ( in_array($row->id, $this->value) ) {
	            $checked = 'checked="checked"' ;
	        }

	        $title = '';
	        $label = JHtml::_('string.truncate', $row->value, 23, false, false );
	        
	        if ($row->value != $label) {
	            $title = ' title="'.$row->value.'"';
	        }
	        
	        $output .= '<label class="amenity"'.$title.'">'
	                .  '<input type="checkbox" name="'.$this->name. '"'
	                .  ' value="'. $row->id . '" ' . $checked . ' />' . $label .'</label>' ;
	    }
	    
	    return $output;
		
	}
	
	protected function getOptions() 
	{
	    $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('f.id , f.value');
        $query->from('#__jea_amenities AS f');

        $query->order('f.value ASC');
        $db->setQuery($query);
        return $db->loadObjectList();
	}
	
}
