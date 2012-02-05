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
}