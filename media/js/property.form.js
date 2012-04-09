
Element.implement({
	flash: function (to,from,reps,prop,dur) {

		//defaults
		if(!reps) { reps = 1; }
		if(!prop) { prop = 'background-color'; }
		if(!dur) { dur = 250; }
	
		//create effect
		var effect = new Fx.Tween(this, {
			duration: dur,
			link: 'chain'
		})
	
		//do it!
		for(x = 1; x <= reps; x++)
		{
			effect.start(prop,from,to).start(prop,to,from);
		}
	}
});

function updateFeature(name, fieldId, language) {
	//active option selected
	var activeValue = document.id(fieldId).get('value');
	//ajax request
	var jSonRequest = new Request.JSON({
		url: 'index.php',
		onSuccess: function(response) {
			var first = document.id(fieldId).getFirst().clone();
			document.id(fieldId).empty();
			document.id(fieldId).adopt(first);
			if (response) {
				response.each(function(item) {
					var option  = new Element('option', {'value' : item.id});
					// keep selected value if active value is found as a result
					if (activeValue == item.id) {
						option.setProperty('selected','selected');
					}
					option.appendText(item.value);
					document.id(fieldId).adopt(option);
				});
			}
		}
	});
	jSonRequest.post({
		'option' : 'com_jea',
		'format' : 'json',
		'task' : 'features.get_list',
		'feature' : name,
		'language' : language
	});
};

function updateAmenities(language) {
	//active option selected
	var labels = document.getElements('.amenity');
	var checkedLabels = Array();
	//store active amenities & clear labels
	labels.each(function(label){
		var input = label.getElement('input');
		if (input.get('checked')) {
			checkedLabels.push(input.get('value'));
		} 
    });
	//remove current amenities
	document.id('amenities').empty();
	//ajax request
	var jSonRequest = new Request.JSON({
		url: 'index.php',
		onSuccess: function(response) {
			if (response) {
				response.each(function(item, idx) {
					//amenity li container
					var li = new Element('li.amenity');
					//generate the label
					var label = new Element('label', { 
						'text' : item.value,
						'for' : 'jform_amenities' + idx
					});
					//generate the input checkbox
					var checkbox  = new Element('input', {
						'name' : 'jform[amenities][]',
						'value' : item.id, 
						'type' : 'checkbox',
						'class' : 'am-input',
						'id' : 'jform_amenities' + idx
					});
					//hide checkbox. It will be enabled/disabled by clicking on parent div
					checkbox.setStyle('display','none');
					// keep selected value if it's found as a result
					if (checkedLabels.contains(item.id)) {
						checkbox.checked = 'checked';
						li.addClass('active');
					}
					//div click => toggle checkbox status
					li.addEvent('click', function(event) {
						if (checkbox.checked) {
							li.removeClass('active');
							checkbox.set('checked', false);
						}
						else {
							li.addClass('active');
							checkbox.set('checked', true);
						}
					});
					//add the content to the amenity div
					li.adopt(label);
					li.adopt(checkbox);
					document.id('amenities').adopt(li);
				});
			}
		}
	});
	jSonRequest.post({
		'option' : 'com_jea',
		'format' : 'json',
		'task' : 'features.get_list',
		'feature' : 'amenity',
		'language' : language
	});
}

function updateFeatures() {
	//language selected
	var language = document.id('jform_language').get('value');
	//update
	updateFeature('type','jform_type_id',language);
	updateFeature('condition','jform_condition_id',language);
	updateFeature('heatingtype','jform_heating_type',language);
	updateFeature('hotwatertype','jform_hot_water_type',language);
	updateFeature('slogan','jform_slogan_id',language);
	updateAmenities(language);
}

window.addEvent('domready', function() {
	// colors
	var bgColor = '#F8E9E9';
	var borderColor = '#DE7A7B';

	document.id('ajaxupdating').setStyle('display','none');
	document.id('jform_language').addEvent('change', function(event) {
		// show field alerts
		document.id('ajaxupdating').setStyle('display','');
		document.id('ajaxupdating').flash('#fff',bgColor,2,'background-color',500);
		document.id('jform_type_id').setStyle('border','1px solid '+borderColor);
		document.id('jform_type_id').flash('#fff',bgColor,2,'background-color',500);
		document.id('jform_condition_id').setStyle('border','1px solid '+borderColor);
		document.id('jform_condition_id').flash('#fff',bgColor,2,'background-color',500);
		document.id('jform_heating_type').setStyle('border','1px solid '+borderColor);
		document.id('jform_heating_type').flash('#fff',bgColor,2,'background-color',500);
		document.id('jform_hot_water_type').setStyle('border','1px solid '+borderColor);
		document.id('jform_hot_water_type').flash('#fff',bgColor,2,'background-color',500);
		document.id('jform_slogan_id').setStyle('border','1px solid '+borderColor);
		document.id('jform_slogan_id').flash('#fff',bgColor,2,'background-color',500);
		document.id('amenities').setStyle('border','1px solid '+borderColor);
		document.id('amenities').flash('#fff',bgColor,2,'background-color',500);
		// update dropdowns	
		updateFeatures();
	});
	//onload update dropdowns
	updateFeatures();
});
