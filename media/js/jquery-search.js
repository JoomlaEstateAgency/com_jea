/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate
 * agency
 * 
 * @copyright Copyright (C) 2008 - 2019 PHILIP Sylvain. All rights reserved.
 * @license GNU/GPL, see LICENSE.txt
 */

function JEASearch(formId, options) {
	this.form = jQuery(formId)[0]
	this.forceUpdateLists = true
	this.options = {
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
		useAJAX: false,
		transactionType: ""
	}

	jQuery.extend(this.options, options)

	if (this.options.useAJAX) {
		for (var fieldName in this.options.fields) {
			if (fieldName == 'filter_amenities') {
				fieldName = 'filter_amenities[]'
			}
			this.initFieldBehavior(fieldName)
		}
	}
	
	jQuery(formId).on('reset', jQuery.proxy(this.reset, this))
}

JEASearch.prototype.initFieldBehavior = function(fieldName) {

	if (!this.form[fieldName]) {
		return
	}

	switch (fieldName) {
		case 'filter_amenities[]':
			var that = this
			jQuery(this.form).find('[name="filter_amenities[]"]').on('change', function() {
				var index = that.options.fields.filter_amenities.indexOf(jQuery(this).val())

				if (jQuery(this).prop('checked') && index == -1) {
					that.options.fields.filter_amenities.push(jQuery(this).val());
				} else if (!jQuery(this).prop('checked') && index > -1){
					that.options.fields.filter_amenities.splice(index, 1);
				}

				that.forceUpdateLists = true
				that.refresh()
			})
			break
		case 'filter_transaction_type':
			var that = this
			jQuery(this.form).find('[name="filter_transaction_type"]').on('change', function() {

				if (jQuery(this).prop('checked')) {
					that.options.fields.filter_transaction_type = jQuery(this).val()
				}
				that.forceUpdateLists = true
				that.refresh()
			})
			break
		default:
			var field = jQuery(this.form[fieldName])
			this.options.fields[fieldName] = field.val()
			field.on('change', jQuery.proxy(function() {
				this.forceUpdateLists = false
				this.options.fields[fieldName] = field.val()
				this.refresh()
			}, this))
	}
}

JEASearch.prototype.reset = function() {

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
		filter_transaction_type : this.options.transactionType,
		filter_type_id : 0,
		filter_zip_codes : ""
	};
	
	jQuery(this.form).find(':input')
		.not(':button, :submit, :reset, :hidden')
		.val('')
		.prop('checked', false)
		.prop('selected', false)
		.removeAttr('checked')
		.removeAttr('selected')

	this.refresh()
},

JEASearch.prototype.refresh = function() {

	if (this.options.useAJAX) {
		jQuery.ajax({
			dataType: 'json',
			url: 'index.php?option=com_jea&task=properties.search&format=json',
			method: 'POST',
			data: this.options.fields,
			context: this,
			success: function(response) {
				this.appendList('filter_type_id', response.types)
				this.appendList('filter_department_id', response.departments)
				this.appendList('filter_town_id',response.towns)
				this.appendList('filter_area_id', response.areas)
				jQuery('.jea-counter-result').text(response.total)
			}
		})
	}
}

JEASearch.prototype.appendList = function(selectName, objectList) {
	
	if (!this.form[selectName]) {
		return
	}

	var selectElt = jQuery(this.form[selectName])

	// Update the list only if its value equals 0
	// Or if this.forceUpdateLists is set to true
	if (selectElt.val() == 0 || this.forceUpdateLists) {
		var value = selectElt.val()

		// Save the first option element
		var first = selectElt.children(':first').clone()
		selectElt.empty().append(first)

		jQuery.each(objectList, function( idx, item ){
			var option = jQuery('<option></option>').text(item.text).attr('value', item.value)

			if (item.value == value) {
				option.attr('selected', 'selected')
			}
			
			selectElt.append(option)
		})
	}
}
