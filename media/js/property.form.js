
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
	var jSonRequest = new Request.JSON({
		url: 'index.php',
		onSuccess: function(response) {
			var first = document.id(fieldId).getFirst().clone();
			document.id(fieldId).empty();
			document.id(fieldId).adopt(first);
			if (response) {
				response.each(function(item) {
					var option  = new Element('option', {'value' : item.id});
					option.appendText(item.value);
					document.id(fieldId).adopt(option);
				});
			}
		}
	});
	jSonRequest.get({
		'option' : 'com_jea',
		'format' : 'json',
		'task' : 'features.get_list',
		'feature' : name,
		'language' : language
	});
};

window.addEvent('domready', function() {
	// colors
	var bgColor = '#F8E9E9';
	var borderColor = '#DE7A7B';

	document.id('ajaxupdating').setStyle('display','none');
	document.id('jform_language').addEvent('change', function(event) {
		var language = this.get('value');
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
		// update dropdowns
		updateFeature('type','jform_type_id',language);
		updateFeature('condition','jform_condition_id',language);
		updateFeature('heatingtype','jform_heating_type',language);
		updateFeature('hotwatertype','jform_hot_water_type',language);
		updateFeature('slogan','jform_slogan_id',language);
	});
});
