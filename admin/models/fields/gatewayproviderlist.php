<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
*
* @package     Joomla.Administrator
* @subpackage  com_jea
* @copyright   Copyright (C) 2008 - 2017 PHILIP Sylvain. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('JPATH_PLATFORM') or die;

jimport('joomla.filesystem.folder');
JFormHelper::loadFieldClass('list');

/**
 * Supports an HTML select list of gateway providers
 *
 */
class JFormFieldGatewayProviderList extends JFormFieldList
{
    /**
     * The form field type.
     *
     * @var    string
     */
    protected $type = 'GatewayProviderList';

    /**
     * The provider type (import or export).
     *
     * @var    string
     */
    protected $providerType = '';


    /**
     * {@inheritDoc}
     * @see JFormField::__get()
     */
    public function __get($name)
    {
        if ($name == 'providerType') {
            return $this->$name;
        }
        return parent::__get($name);
    }

    /**
     * {@inheritDoc}
     * @see JFormField::__set()
     */
    public function __set($name, $value)
    {
        if ($name == 'providerType') {
            $this->$name = (string) $value;
        }

        parent::__set($name, $value);
    }

    /**
     * {@inheritDoc}
     * @see JFormField::setup()
     */
    public function setup(SimpleXMLElement $element, $value, $group = null)
    {
        $return = parent::setup($element, $value, $group);

        if ($return)
        {
            $this->providerType  = (string) $this->element['provider_type'];
        }

        return $return;
    }

    /**
     * {@inheritDoc}
     * @see JFormFieldList::getOptions()
     */
    protected function getOptions()
    {
        $options = array();

        $path = JPATH_ADMINISTRATOR . '/components/com_jea/gateways/providers';

        $folders = JFolder::folders($path);

        $options[] = JHtml::_('select.option', '', JText::alt('JOPTION_DO_NOT_USE', preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)));

        foreach ($folders as $folder)
        {
            if (file_exists($path .'/' . $folder . '/' . $this->providerType . '.xml')) {
                $options[] = JHtml::_('select.option', $folder, $folder);
            }
        }

        return $options;
    }

}
