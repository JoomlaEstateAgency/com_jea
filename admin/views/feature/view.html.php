<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @version     $Id$
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2012 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */


// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view');

require JPATH_COMPONENT.DS.'helpers'.DS.'jea.php';

/**
 * View to edit a feature.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 */
class JeaViewFeature extends JView
{

    protected $form;
    protected $item;
    protected $state;

    function display( $tpl = null )
    {
        $this->form  = $this->get('Form');
        $this->item  = $this->get('Item');
        $this->state = $this->get('State');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }

        $this->addToolbar();

        parent::display($tpl);
    }


    /**
     * Add the page title and toolbar.
     *
     */
    function addToolbar()
    {
        JRequest::setVar('hidemainmenu', true);
        $isNew = ($this->item->id == 0);
        $canDo  = JeaHelper::getActions();

        $title = $this->item->id ? JText::_( 'JACTION_EDIT' ) . ' ' . $this->escape( $this->item->value ) : JText::_( 'JACTION_CREATE' ) ;
        JToolBarHelper::title( $title , 'jea.png' ) ;

        // For new records, check the create permission.
        if ($canDo->get('core.create')) {
            JToolBarHelper::apply('feature.apply');
            JToolBarHelper::save('feature.save');
            JToolBarHelper::save2new('feature.save2new');
        }

        JToolBarHelper::cancel('feature.cancel');
    }

}