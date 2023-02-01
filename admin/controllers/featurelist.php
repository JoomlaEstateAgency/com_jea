<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2020 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\MVC\Controller\AdminController;

defined('_JEXEC') or die;

/**
 * Featurelist controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @since       2.0
 */
class JeaControllerFeaturelist extends AdminController
{
    /**
     * Method to get a JeaModelFeature model object, loading it if required.
     *
     * @param string $name The model name.
     * @param string $prefix The class prefix.
     * @param array $config Configuration array for model.
     *
     * @return  JeaModelFeature|boolean  Model object on success; otherwise false on failure.
     *
     * @see AdminController::getModel()
     */
    public function getModel($name = 'Feature', $prefix = 'JeaModel', $config = array())
    {
        return parent::getModel($name, $prefix, $config);
    }
}
