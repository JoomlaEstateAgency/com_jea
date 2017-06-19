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

$action = $displayData['action'];

$script = <<<JS
jQuery(document).ready(function($) {

    $('#php-interpreter').on('keyup', function(e) {
        var text = $('#cli-command').text().replace(/^[a-zA-Z0-9-_./]+/, this.value);
        $('#cli-command').text(text);
    });

    $( "#cli-form" ).submit(function(e) {
        e.preventDefault();
        $('#cli-console').empty();
        $('#cli-launch').toggleClass('active');

        var token = $(this).find("input[type='hidden']").attr('name');

        var data = {
            php_interpreter: $('#php-interpreter').val(),
        };

        data[''+token] = 1;

        $.post($(this).attr('action'), data).done(function(data) {
            $('#cli-launch').toggleClass('active');
            $("#cli-console").text(data);
        });
    });
});
JS;

$document = JFactory::getDocument();
$document->addScriptDeclaration($script);

?>

<form action="<?php echo JRoute::_('index.php?option=com_jea&task=gateways.'. $action) ?>" class="form-horizontal" id="cli-form" method="post" >

  <div class="control-group">
    <label for="php-interpreter" class="control-label" ><?php echo JText::_('COM_JEA_FIELD_PHP_INTERPRETER_LABEL') ?></label>
    <div class="controls">
      <input class="input-small" type="text" name="php_interpreter" id="php-interpreter" value="php" />
    </div>
  </div>

  <p><?php echo JText::_('COM_JEA_FIELD_COMMAND_LABEL') ?></p>

  <?php if ($action == 'export') :?>
  <pre id="cli-command"><?php echo 'php ' . JPATH_COMPONENT_ADMINISTRATOR . '/cli/gateways.php --export --basedir="' . JPATH_ROOT . '" --baseurl="' . JUri::root() . '"' ?></pre>
  <?php else: ?>
  <pre id="cli-command"><?php echo 'php ' . JPATH_COMPONENT_ADMINISTRATOR . '/cli/gateways.php --import --basedir="' . JPATH_ROOT . '"' ?></pre>
  <?php endif ?>

  <div>
    <?php echo JHtml::_('form.token'); ?>
    <button type="submit" id="cli-launch" class="btn btn-success has-spinner">
      <span class="spinner"><i class="jea-icon-spin icon-refresh"></i></span>
      <?php echo JText::_('COM_JEA_LAUNCH')?>
    </button>
  </div>

  <pre id="cli-console" class="console"></pre>

</form>



