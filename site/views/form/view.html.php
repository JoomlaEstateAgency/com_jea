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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.view');

class JeaViewForm extends JView
{
    public function display( $tpl = null )
    {
        $app  = JFactory::getApplication();
        $user = JFactory::getUser();

        $this->form   = $this->get('Form');
        $this->item   = $this->get('Item');
        $this->state  = $this->get('State');
        $this->params = $app->getParams();

        if (empty($this->item->id)) {
            $authorised = $user->authorise('core.create', 'com_jea');
        } else {
            $asset    = 'com_jea.property.'.$this->item->id;
            // Check general edit permission first.
            if ($user->authorise('core.edit', $asset)) {
                $authorised = true;
            } elseif ($user->authorise('core.edit.own', $asset) &&
                      $this->item->created_by == $user->get('id')) {
                $authorised = true;
            }
        }

        if ($authorised !== true) {
            JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
            return false;
        }

        parent::display($tpl);
    }

}

