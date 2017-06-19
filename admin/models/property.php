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

use Joomla\Utilities\ArrayHelper;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/upload.php';

/**
 * Property model class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @see         JModelAdmin
 *
 * @since       2.0
 */
class JeaModelProperty extends JModelAdmin
{
	/**
	 * The event to trigger after saving the data.
	 *
	 * @var string
	 */
	protected $event_after_save = 'onAfterSaveProperty';

	/**
	 * The event to trigger before saving the data.
	 *
	 * @var string
	 */
	protected $event_before_save = 'onBeforeSaveProperty';

	/**
	 * Overrides parent method
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm|boolean  A JForm object on success, false on failure
	 *
	 * @see JModelForm::getForm()
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$dispatcher = JDispatcher::getInstance();

		// Include the jea plugins for the on after load property form events.
		JPluginHelper::importPlugin('jea');

		$form = $this->loadForm('com_jea.property', 'property', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		$jinput = JFactory::getApplication()->input;
		$id = $jinput->get('id', 0);
		$user = JFactory::getUser();
		$item = $this->getItem($id);

		// Remove deposit field if transaction type is not SELLING
		if (empty($item->transaction_type) || $item->transaction_type == 'SELLING')
		{
			$form->removeField('deposit');
			$form->removeField('rate_frequency');
		}
		elseif ($item->transaction_type == 'RENTING')
		{
			$form->setFieldAttribute('price', 'label', 'COM_JEA_FIELD_PRICE_RENT_LABEL');
		}

		// Check for existing article.
		// Modify the form based on Edit State access controls.
		if ($id != 0 && (! $user->authorise('core.edit.state', 'com_jea.property.' . (int) $id))
			|| ($id == 0 && ! $user->authorise('core.edit.state', 'com_jea')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('featured', 'disabled', 'true');
			$form->setFieldAttribute('published', 'disabled', 'true');

			// Disable fields while saving. The controller has already verified this is an article you can edit.
			$form->setFieldAttribute('featured', 'filter', 'unset');
			$form->setFieldAttribute('published', 'filter', 'unset');
		}

		// Trigger the onAfterLoadPropertyForm event.
		$dispatcher->trigger('onAfterLoadPropertyForm', array(&$form));

		return $form;
	}

	/**
	 * Overrides parent method
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success, False on error.
	 *
	 * @see JModelAdmin::save()
	 */
	public function save($data)
	{
		// Include the jea plugins for the on save events.
		JPluginHelper::importPlugin('jea');

		// Alter the title for save as copy
		if (JRequest::getVar('task') == 'save2copy')
		{
			$data['ref'] = JString::increment($data['ref']);
			$data['title'] = JString::increment($data['title']);
			$data['alias'] = JString::increment($data['alias'], 'dash');
		}

		if (empty($data['images']))
		{
			// Set a default empty json array
			$data['images'] = '[]';
		}

		if (parent::save($data))
		{
			return $this->processImages();
		}

		return false;
	}

	/**
	 * Method to manage new uploaded images and to remove non existing images from the gallery
	 *
	 * @return boolean true on success otherwise false
	 */
	public function processImages()
	{
		$upload = JeaUpload::getUpload('newimages');
		$item = $this->getItem();
		$images = json_decode($item->images);

		if (empty($images))
		{
			$images = array();
		}

		$imageNames = array();

		foreach ($images as $row)
		{
			$imageNames[$row->name] = $row->name;
		}

		$baseUploadDir = JPATH_ROOT . '/images/com_jea/images';
		$validExtensions = array('jpg', 'JPG', 'jpeg', 'JPEG', 'gif', 'GIF', 'png', 'PNG');

		if (! JFolder::exists($baseUploadDir))
		{
			JFolder::create($baseUploadDir);
		}

		$uploadDir = $baseUploadDir . '/' . $item->id;

		if (is_array($upload))
		{
			foreach ($upload as $file)
			{
				if ($file->isPosted())
				{
					$file->setValidExtensions($validExtensions)->nameToSafe();

					if (! JFolder::exists($uploadDir))
					{
						JFolder::create($uploadDir);
					}

					if ($file->moveTo($uploadDir))
					{
						if (! isset($imageNames[$file->name]))
						{
							$image = new stdClass;
							$image->name = $file->name;
							$image->title = '';
							$image->description = '';
							$images[] = $image;

							// Update the list of image names
							$imageNames[$image->name] = $image->name;
						}
					}
					else
					{
						$errors = $file->getErrors();

						foreach ($errors as $error)
						{
							$this->setError(JText::_($error));
						}

						return false;
					}
				}
			}

			$table = $this->getTable();
			$table->load($item->id);
			$table->images = json_encode($images);
			$table->store();
		}

		if (JFolder::exists($uploadDir))
		{
			// Remove image files
			$list = JFolder::files($uploadDir);

			foreach ($list as $filename)
			{
				if (strpos($filename, 'thumb') === 0)
				{
					continue;
				}

				if (! isset($imageNames[$filename]))
				{
					$removeList = JFolder::files($uploadDir, $filename . '$', false, true);

					foreach ($removeList as $removeFile)
					{
						JFile::delete($removeFile);
					}
				}
			}
		}

		return true;
	}

	/**
	 * Method to toggle the featured setting of properties.
	 *
	 * @param   array    $pks    The ids of the items to toggle.
	 * @param   integer  $value  The value to toggle to.
	 *
	 * @return  void.
	 */
	public function featured($pks, $value = 0)
	{
		// Sanitize the ids.
		$pks = ArrayHelper::toInteger((array) $pks);

		$db = $this->getDbo();
		$db->setQuery('UPDATE #__jea_properties' . ' SET featured = ' . (int) $value . ' WHERE id IN (' . implode(',', $pks) . ')');
		$db->execute();
	}

	/**
	 * Method to copy a set of properties.
	 *
	 * @param   array  $pks  The ids of the items to copy.
	 *
	 * @return  void.
	 */
	public function copy($pks)
	{
		// Sanitize the ids.
		$pks = (array) $pks;
		ArrayHelper::toInteger($pks);

		$table = $this->getTable();
		$nextOrdering = $table->getNextOrder();

		$fields = $table->getProperties();
		$db = $this->getDbo();

		unset($fields['id']);
		unset($fields['checked_out']);
		unset($fields['checked_out_time']);

		$fields = array_keys($fields);
		$query = 'SELECT ' . implode(', ', $fields) . ' FROM #__jea_properties WHERE id IN (' . implode(',', $pks) . ')';
		$rows = $this->_getList($query);

		foreach ($rows as $row)
		{
			$row = (array) $row;
			$row['ref'] = JString::increment($row['ref']);
			$row['title'] = JString::increment($row['title']);
			$row['alias'] = JString::increment($row['alias'], 'dash');
			$row['ordering'] = $nextOrdering;
			$row['created'] = date('Y-m-d H:i:s');

			$table->bind($row);
			$table->store();

			$nextOrdering ++;
		}

		return true;
	}

	/**
	 * Overrides parent method
	 *
	 * @param   array  &$pks  An array of record primary keys.
	 *
	 * @return  boolean  True if successful, false if an error occurs.
	 *
	 * @see JModelAdmin::delete()
	 */
	public function delete(&$pks)
	{
		if (parent::delete($pks))
		{
			// Remove images folder
			foreach ($pks as $id)
			{
				if (JFolder::exists(JPATH_ROOT . '/images/com_jea/images/' . $id))
				{
					JFolder::delete(JPATH_ROOT . '/images/com_jea/images/' . $id);
				}
			}
		}
	}

	/**
	 * Overrides parent method
	 *
	 * @return  array  The default data is an empty array.
	 *
	 * @see JModelForm::loadFormData()
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data. See
		// JControllerForm::save()
		$data = JFactory::getApplication()->getUserState('com_jea.edit.property.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Overrides parent method
	 *
	 * @param   JTable  $table  A JTable object.
	 *
	 * @return  array  An array of conditions to add to ordering queries.
	 *
	 * @see JModelAdmin::getReorderConditions()
	 */
	protected function getReorderConditions($table)
	{
		$db = $table->getDbo();
		$condition = array();
		$condition[] = 'transaction_type = ' . $db->quote($table->transaction_type);

		return $condition;
	}
}
