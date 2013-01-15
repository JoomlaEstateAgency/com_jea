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

jimport('joomla.application.component.view');

require JPATH_COMPONENT.DS.'helpers'.DS.'jea.php';

/**
 * JEA default view.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 */
class JeaViewDefault extends JView
{

    public function display( $tpl = null )
    {
        JeaHelper::addSubmenu('default');
        JToolBarHelper::title( 'Joomla Estate Agency', 'jea.png' );

        $canDo  = JeaHelper::getActions();

        if ($canDo->get('core.admin')) {
            JToolBarHelper::preferences('com_jea');
        }
        parent::display($tpl);
    }

    /**
     * Get version of JEA
     * @return string
     */
    protected function getVersion()
    {
        if (is_file(JPATH_COMPONENT . DS . 'jea.xml')) {
            $xml = JFactory::getXML(JPATH_COMPONENT . DS . 'jea.xml');
            return $xml->version;
        }
         
        return '';
    }

}
