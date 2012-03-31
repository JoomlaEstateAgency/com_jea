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

/**
 * Default controller class.
 *
 * @package     Joomla.Site
 * @subpackage  com_jea
 */
class JeaControllerDefault extends JController
{
    protected $default_view = 'properties';


    public function sendContactForm()
    {
        // Check for request forgeries
        if (!JRequest::checkToken()) {
            return $this->setRedirect($returnURL, JText::_('JINVALID_TOKEN'), 'warning');
        }
        
        $model = $this->getModel('Property', 'JeaModel');
        $returnURL = $model->getState('contact.propertyURL');
        
        if (!$model->sendContactForm()) {
            $errors = $model->getErrors();
            $msg = '';
            foreach ($errors as $error) {
                $msg .= $error . "\n";
            }
            return $this->setRedirect($returnURL, $msg, 'warning');
        }
        $msg = JText::_('COM_JEA_CONTACT_FORM_SUCCESSFULLY_SENT');

        $this->setRedirect($returnURL, $msg);
    }

}
