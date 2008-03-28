<?php // no direct access
defined('_JEXEC') or die('Restricted access');

$ajax = '0';


?>

<?php if(empty($_POST)): ?>

<form action="index.php?option=com_jea&amp;task=search" method="post" id="jea_search_form" enctype="application/x-www-form-urlencoded" >

	<fieldset><legend><?php echo JText::_('Quick search') ?></legend>
	<p>
    <input type="radio" name="cat" id="renting" value="renting" checked="checked">
    <label for="renting"><?php echo JText::_('Renting') ?></label>
    <input type="radio" name="cat" id="selling" value="selling">
    <label for="selling"><?php echo JText::_('Selling') ?></label>
    </p>
	<p>
	<?php echo $this->getHtmlList('types', '--'.JText::_( 'Property type' ).'--', 'type_id' ) ?>
	<?php echo $this->getHtmlList('departments', '--'.JText::_( 'Department' ).'--', 'department_id' ) ?>
  	<?php echo $this->getHtmlList('towns', '--'.JText::_( 'Town' ).'--', 'town_id' ) ?>
  	</p>
  	
  	</fieldset>
  	
  	<p><input type="submit" class="button" value="<?php echo JText::_('Search') ?>" /></p>
  	
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
  	<p><?php echo $this->getAdvantages('', 'checkbox') ?></p>
  	
  	</fieldset>
  	<p><input type="submit" class="button" value="<?php echo JText::_('Search') ?>" /></p>


    
    <input type="hidden" name="Itemid" value="<?php echo JRequest::getInt('Itemid', 0) ?>">
  
</form>

<?php else: ?>
    
    <!-- 
    <p><select id="type_id" name="type_id" onchange="filter(this)" class="inputbox" style="width:100%"><option value="0"> </option></select></p>
    <p><select id="departement_id"  name="departement_id" onchange="filter(this)" class="inputbox" style="width:100%"><option value="0"> </option><select></p>
    <p><select id="ville_id" name="ville_id" onchange="filter(this)" class="inputbox" style="width:100%"><option value="0"> </option></select></p>
    <p><select id="quartier_id" name="quartier_id" onchange="filter(this)" class="inputbox" style="width:100%"><option value="0"> </option></select></p>
	 -->
<?php endif ?>
