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

class JeaControllerProperties extends JController
{

    public function search()
    {
        $json = JRequest::getVar('json', '');
        $post = json_decode($json);
        JRequest::set((array) $post, 'POST');

        $model = $this->getModel();
        $items = $model->getItems();

        $result = array();
        $result['types'][] = array( 'value' => 0, 'text' => '- '. Jtext::_('Property type') .' -' );
        $result['towns'][]   = array( 'value' => 0, 'text' => '- '. Jtext::_('town') .' -' );
        $result['departments'][]   = array( 'value' => 0, 'text' => '- '. Jtext::_('Department') .' -' );

        $temp = array();
        $temp['types'] = array();
        $temp['towns'] = array();
        $temp['departments'] = array();

        foreach ($items as $row){

            if( $row->type_id && !isset($temp['types'][$row->type_id]) ) {

                $result['types'][] = array( 'value' => $row->type_id , 'text' =>  $row->type );
                $temp['types'][$row->type_id] = true;
            }

            if($row->town_id && !isset($temp['towns'][$row->town_id]) ) {

                $result['towns'][] = array( 'value' => $row->town_id , 'text' =>  $row->town );
                $temp['towns'][$row->town_id] = true;
            }

            if($row->department_id && !isset($temp['departments'][$row->department_id]) ) {

                $result['departments'][] = array( 'value' => $row->department_id , 'text' =>  $row->department );
                $temp['departments'][$row->department_id] = true ;
            }
        }

        // TODO: User parameter : Alpha ou order
        if (isset($result['departments'])) usort($result['departments'], array('JeaControllerProperties', '__ajaxAlphaSort'));
        if (isset($result['towns'])) usort($result['towns'], array('JeaControllerProperties', '__ajaxAlphaSort'));

        echo json_encode($result);
    }

    function __ajaxAlphaSort(&$arg1, &$arg2)
    {
        $val1       = strtolower($arg1['text']);
        $val2      = strtolower($arg2['text']);

        return strnatcmp($val1, $val2);
    }

    /* (non-PHPdoc)
     * @see JController::getModel()
     */
    public function getModel($name = 'Properties', $prefix = 'JeaModel', $config = array())
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
}
