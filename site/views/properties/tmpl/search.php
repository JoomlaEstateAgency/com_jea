<?php // no direct access
defined('_JEXEC') or die('Restricted access');

JHTML::stylesheet('jea.css', 'media/com_jea/css/');

$use_ajax = $this->params->get('use_ajax', 0);

if ($use_ajax ) {
	JHTML::script('search.js', 'media/com_jea/js/', true);
	
	$document =& JFactory::getDocument();
	
	//initialize the form when the page load
	$document->addScriptDeclaration("
		window.addEvent('domready', function() {
			refreshForm(); 
		});");
}

?>

<form action="<?php echo JRoute::_('&task=search&layout=default') ?>" method="post" id="jea_search_form" enctype="application/x-www-form-urlencoded" >

	<fieldset><legend><?php echo JText::_('Quick search') ?></legend>
	<p>
    <input type="radio" name="cat" id="renting" value="renting" checked="checked" <?php echo $use_ajax ? 'onclick="refreshForm()"' : '' ?> >
    <label for="renting"><?php echo JText::_('Renting') ?></label>
    <input type="radio" name="cat" id="selling" value="selling" <?php echo $use_ajax ? 'onclick="refreshForm()"' : '' ?> >
    <label for="selling"><?php echo JText::_('Selling') ?></label>
    </p>
    
<?php if ( $use_ajax ): ?>
    <p>
    <select id="type_id" name="type_id" onchange="updateList(this)" class="inputbox"><option value="0"> </option></select>
    <select id="department_id"  name="department_id" onchange="updateList(this)" class="inputbox" ><option value="0"> </option></select>
    <select id="town_id" name="town_id" onchange="updateList(this)" class="inputbox"><option value="0"> </option></select>
    </p>
    
<?php else: ?> 

   	<p>
	<?php echo $this->getHtmlList('types', '--'.JText::_( 'Property type' ).'--', 'type_id' ) ?>
	<?php echo $this->getHtmlList('departments', '--'.JText::_( 'Department' ).'--', 'department_id' ) ?>
  	<?php echo $this->getHtmlList('towns', '--'.JText::_( 'Town' ).'--', 'town_id' ) ?>
  	</p>
  	
<?php endif ?>
  	
  	</fieldset>
  	<p><input type="submit" class="button" value="<?php echo JText::_('Search') ?>" /></p>
  	
<?php if ( $this->params->get('advanced_search', 0)): ?>
	  	
  	<fieldset><legend><?php echo JText::_('Advanced search') ?></legend>
  	
  	<table>
  		<tr>
  			<td class="jea_label"><label for="budget_min"><?php echo JText::_('Budget min') ?> : </label></td>
  			<td><input id="budget_min" type="text" name="budget_min" size="5" /> <?php echo $this->params->get('currency_symbol', '&euro;') ?></td>
  			<td class="jea_label"><label for="budget_max"><?php echo JText::_('Budget max') ?> : </label></td>
  			<td><input id="budget_max" type="text" name="budget_max" size="5" /> <?php echo $this->params->get('currency_symbol', '&euro;') ?></td>
  		</tr>
  		<tr>
  			<td class="jea_label"><label for="living_space_min"><?php echo JText::_('Living space min') ?> : </label></td>
  			<td><input id="living_space_min" type="text" name="living_space_min" size="5" /> <?php echo $this->params->get( 'surface_measure' ) ?></td>
  			<td class="jea_label"><label for="living_space_max"><?php echo JText::_('Living space max') ?> : </label></td>
  			<td><input id="living_space_max" type="text" name="living_space_max" size="5" /> <?php echo $this->params->get( 'surface_measure' ) ?></td>
  		</tr>
  	</table>
  	<p><?php echo JText::_('Minimum number of rooms') ?>  : <input type="text" name="rooms_min" size="1" /></p>
  	
  	<p><?php echo JText::_('Advantages') ?> : <br />
  	<?php echo $this->getAdvantages('', 'checkbox') ?>
  	</p>	
  	</fieldset>
  	
  	<p><input type="submit" class="button" value="<?php echo JText::_('Search') ?>" /></p>
  	
<?php endif ?>
    
    <div>
    <input type="hidden" name="Itemid" value="<?php echo JRequest::getInt('Itemid', 0) ?>" />
    <?php echo JHTML::_( 'form.token' ) ?>
    </div>
  
</form>
