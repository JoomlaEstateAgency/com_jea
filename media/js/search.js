	

JEASearchAJAX = new Class({

	Implements: Options,
	
	form: null,

	options: {
		type_id : 0,
		department_id : 0,
		town_id : 0,
		area_id : 0
	},

	initialize: function(formId, options) {
		this.form = document.id(formId);
		this.setOptions(options);
	},

	refresh: function() {
		var formUrl = 'index.php?option=com_jea&task=properties.search&format=json';

		var optionsCallback = {
				onComplete : function(response) {
/*
					if ($('type_id') && $('type_id').getProperty('value') == '0') {
						$('type_id').empty();
						this.addOptionsList($('type_id'), response.types);
					}
					if ($('department_id')
							&& $('department_id').getProperty('value') == '0') {
						$('department_id').empty();
						this.addOptionsList($('department_id'), response.departments);
					}

					if ($('town_id') && $('town_id').getProperty('value') == '0') {
						$('town_id').empty();
						this.addOptionsList($('town_id'), response.towns);
					}
*/
				}.bind(this)
		};

		var jSonRequest = new Request.JSON({url:formUrl, onSuccess:optionsCallback});
		jSonRequest.send({data : this.options});
	},

	reinit : function() {
		if ($('type_id')) {
			$('type_id').selectedIndex = 0;
		}
		if ($('department_id')) {
			$('department_id').selectedIndex = 0;
		}
		if ($('town_id')) {
			$('town_id').selectedIndex = 0;
		}
		type_id = 0;
		department_id = 0;
		town_id = 0;
		refreshForm();
	},

	addOptionsList : function(selectElt, objectList) {
		var value = eval(selectElt.name); // value of global var
		for ( var i in objectList) {
			if (objectList[i].text) {
				var optionsElt = new Element('option', {
					'value' : objectList[i].value
				});
				if (objectList[i].value == value) {
					optionsElt.setProperty('selected', 'selected');
				}
				optionsElt.appendText(objectList[i].text);
				selectElt.appendChild(optionsElt);
			}
		}
	},

	updateList : function(selectElt) {

			switch (selectElt.name) {
			case 'type_id':
				type_id = selectElt.value;
				break;
			case 'town_id':
				town_id = selectElt.value;
				break;
			case 'department_id':
				department_id = selectElt.value;
				break;
			}

			if (type_id == 0 || town_id == 0 || department_id == 0) {
				refreshForm();
			}
	}

});
