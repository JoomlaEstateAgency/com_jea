	

JEASearch = new Class({

	Implements: [Options],

	form: null,

	forceUpdateLists : false,

	options: {
		fields : {
			filter_amenities : [],
			filter_area_id : 0,
			filter_bathrooms_min : 0,
			filter_bedrooms_min : 0,
			filter_budget_max : 0,
			filter_budget_min : 0,
			filter_condition : 0,
			filter_department_id : 0,
			filter_floor : '',
			filter_heatingtype : 0,
			filter_hotwatertype : 0,
			filter_land_space_max : 0,
			filter_land_space_min : 0,
			filter_living_space_max :0,
			filter_living_space_min : 0,
			filter_orientation : 0,
			filter_rooms_min : 0,
			filter_search : "",
			filter_town_id : 0,
			filter_transaction_type : "",
			filter_type_id : 0,
			filter_zip_codes : ""
		},
		useAJAX : false
	},

	initialize: function(formId, options) {
		this.form = document.id(formId);
		this.setOptions(options);
		this.forceUpdateLists = true;
		
		if (this.options.useAJAX) {
			for (var fieldName in this.options.fields) {
				if (fieldName == 'filter_amenities') {
					fieldName = 'filter_amenities[]';
				}
				this.initFieldBehavior(fieldName);
			}
		}

		this.form.addEvent('reset', this.reset.bind(this));
	},

	initFieldBehavior : function(fieldName) {
		if (this.form[fieldName]) {
			if (typeOf(this.form[fieldName]) == 'element') {
				var field = document.id(this.form[fieldName]);
				this.options.fields[fieldName] = field.get('value');
				field.addEvent('change', function(event) {
					this.forceUpdateLists = false;
					this.options.fields[fieldName] = field.get('value');
					this.refresh();
				}.bind(this));

			} else if (typeOf(this.form[fieldName]) == 'collection') {
				if (fieldName == 'filter_amenities[]') {
					Array.from(this.form['filter_amenities[]']).each(function(item) {
						if (Browser.ie && Browser.version < 9){
							item = document.id(item);
						}
						item.addEvent('change', function(event) {
							var index = this.options.fields.filter_amenities.indexOf(item.get('value'));
							this.forceUpdateLists = true;
							if (item.get('checked') && index == -1) {
								this.options.fields.filter_amenities.push(item.get('value'));
							} else if (!item.get('checked') && index > -1){
								this.options.fields.filter_amenities.splice(index, 1);
							}
							this.refresh();
						}.bind(this));
					}.bind(this));
				} else if (fieldName == 'filter_transaction_type') {
					var transTypes = document.getElements('[name=filter_transaction_type]');
					transTypes.each(function(item) {
						item.addEvent('change', function(event) {
							this.forceUpdateLists = true;
							if (item.get('checked')) {
								this.options.fields.filter_transaction_type = item.get('value');
							}
							this.refresh();
						}.bind(this));
					}.bind(this));
				}
			}
		}
	},

	refresh: function() {
		if (this.options.useAJAX) {
			var jSonRequest = new Request.JSON({
				url: 'index.php?option=com_jea&task=properties.search&format=json',
				onSuccess: function(response) {
					this.appendList('filter_type_id', response.types);
					this.appendList('filter_department_id', response.departments);
					this.appendList('filter_town_id',response.towns);
					this.appendList('filter_area_id', response.areas);
					this.form.getElements('.jea-counter-result').each(function(item){
						item.set('text', response.total);
					});
				}.bind(this)
			});
			
			jSonRequest.post(this.options.fields);
		}
	},

	reset : function() {
		this.options.fields = {
			filter_amenities : [],
			filter_area_id : 0,
			filter_bathrooms_min : 0,
			filter_bedrooms_min : 0,
			filter_budget_max : 0,
			filter_budget_min : 0,
			filter_condition : 0,
			filter_department_id : 0,
			filter_floor : '',
			filter_heatingtype : 0,
			filter_hotwatertype : 0,
			filter_land_space_max : 0,
			filter_land_space_min : 0,
			filter_living_space_max :0,
			filter_living_space_min : 0,
			filter_orientation : 0,
			filter_rooms_min : 0,
			filter_search : "",
			filter_town_id : 0,
			// Keep selected transaction type
			filter_transaction_type : this.options.fields.filter_transaction_type,
			filter_type_id : 0,
			filter_zip_codes : ""
		};

		for (var fieldName in this.options.fields) {
			if (this.form[fieldName]) {
				if (typeOf(this.form[fieldName]) == 'element') {
					var field = document.id(this.form[fieldName]);
					if (field.get('tag') == 'select') {
						field.set('selectedIndex', 0);
					}
					field.set('value', '');

				} else if (typeOf(this.form[fieldName]) == 'collection') {
					Array.from(this.form[fieldName]).each(function(item) {
						item.set('checked', '');
					});
				}
			}
		}

		this.refresh();
	},

	appendList : function(selectName, objectList) {
		if (this.form[selectName]) {
			var selectElt = document.id(this.form[selectName]);
			// Update the list only if its value equals 0
			// Or if this.forceUpdateLists is set to true
			if (selectElt.get('value') == 0 || this.forceUpdateLists) {
				var value = selectElt.get('value');

				// Save the first option element
				var first = selectElt.getFirst().clone();
				selectElt.empty();
				if (first.get('value') == 0) {
					selectElt.adopt(first);
				}

				for (var i in objectList) {
					if (objectList[i].text) {
						var option = new Element('option', objectList[i]);
						if (objectList[i].value == value) {
							option.setProperty('selected', 'selected');
						}
						selectElt.adopt(option);
					}
				}
			}
		}
	}

});
