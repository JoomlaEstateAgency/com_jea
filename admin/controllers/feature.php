<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2017 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Feature controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @since       2.0
 */
class JeaControllerFeature extends JControllerForm
{
	/**
	 * The default view for the display method.
	 *
	 * @var string
	 */
	protected $view_list = 'featurelist';

	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param   integer  $recordId  The primary key id for the item.
	 * @param   string   $urlVar    The name of the URL variable for the id.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 *
	 * @see JControllerForm::getRedirectToItemAppend()
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		$feature = $this->input->getCmd('feature');
		$append  = parent::getRedirectToItemAppend($recordId, $urlVar);

		if ($feature)
		{
			$append .= '&feature=' . $feature;
		}

		return $append;
	}

	/**
	 * Method to get a JeaModelFeature model object, loading it if required.
	 *
	 * @param   string  $name    The model name.
	 * @param   string  $prefix  The class prefix.
	 * @param   array   $config  Configuration array for model.
	 *
	 * @return  JeaModelFeature|boolean  Model object on success; otherwise false on failure.
	 *
	 * @see JControllerForm::getModel()
	 */
	public function getModel($name = 'Feature', $prefix = 'JeaModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}
}
