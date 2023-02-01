<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2020 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

defined('_JEXEC') or die;

/**
 * Gateway View
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @since       2.0
 */
class JeaViewGateway extends HtmlView
{
    /**
     * The form object
     *
     * @var Form
     */
    protected $form;

    /**
     * The database record
     *
     * @var JObject|boolean
     */
    protected $item;

    /**
     * The model state
     *
     * @var JObject
     */
    protected $state;

    /**
     * Overrides parent method.
     *
     * @param string $tpl The name of the template file to parse.
     *
     * @see     HtmlView::display()
     */
    public function display($tpl = null)
    {
        JeaHelper::addSubmenu('tools');

        $this->state = $this->get('State');

        $title = Text::_('COM_JEA_GATEWAYS');

        $this->item = $this->get('Item');

        switch ($this->_layout) {
            case 'edit':
                $this->form = $this->get('Form');

                ToolbarHelper::apply('gateway.apply');
                ToolbarHelper::save('gateway.save');
                ToolbarHelper::cancel('gateway.cancel');
                $isNew = ($this->item->id == 0);
                $title .= ' : ' . ($isNew ? Text::_('JACTION_CREATE') : Text::_('JACTION_EDIT') . ' : ' . $this->item->title);
                break;
        }

        ToolbarHelper::title($title, 'jea');

        parent::display($tpl);
    }
}
