<?php



class JeaHelper
{
    
	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	$viewName	The name of the active view.
	 *
	 * @return	void
	 */
	public static function addSubmenu($viewName)
	{
		JSubMenuHelper::addEntry(
			JText::_('Properties management'),
			'index.php?option=com_jea&view=properties',
			$viewName == 'properties'
		);
		JSubMenuHelper::addEntry(
			JText::_('Features management'),
			'index.php?option=com_jea&view=features',
			$viewName == 'features'
		);
	}
	
	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param	int		The property ID.
	 *
	 * @return	JObject
	 * @since	1.6
	 */
	public static function getActions($propertyId = 0)
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		if (empty($propertyId)) {
			$assetName = 'com_jea';
		}  else {
			$assetName = 'com_jea.property.'.(int) $propertyId;
		}

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action,	$user->authorise($action, $assetName));
		}

		return $result;
	}
	
	/**
	 * Gets the list of images informations for the given property.
	 *
	 * @param int	$propertyId	The property ID.
	 *
	 * @return	array
	 */
    public static function getGalleryInfos($propertyId)
    {
    	$result = array();
    	
    	if (empty($propertyId)) {
    	    return $result;
    	}
    	
        $baseURL = JURI::root().'images/com_jea/images/' . $propertyId;
        $basePath = JPATH_ROOT.DS.'images'.DS.'com_jea'.DS.'images'.DS.$propertyId;
        $result = array();
        
        if (JFolder::exists($basePath)) {
            $list = JFolder::files($basePath);
            foreach ($list as $filename) {
                $detail = array();
                $img = @getimagesize($basePath.DS.$filename);
                if ($img !== FALSE){
                    $detail['name']   = $filename;
                    $detail['path']   = $basePath.DS.$filename;
                    $detail['url']    = $baseURL.'/'.$filename;
                    $detail['width']  = $img[0];
                    $detail['height'] = $img[1];
                    $file = stat($detail['path']);
                    $detail['weight'] = round(($file[7]/1024),1); // Ko
                    $result[$filename] = $detail ;
                }
            }
        }
        
        return $result;
    }
    
    public static function getImageResized()
    {
        
    }
}