var JEAGeoSearch = new Class({

	Implements : [ Options ],

	mask : null,

	map : null,

	options : {
		opacity : 0.5,
		counterElement : '',
		defaultArea : '',
		Itemid : 0
	},

	initialize : function(content, options) {
		this.content = document.id(content);
		this.setOptions(options);
		this.mask = Class.empty;
		this.map = Class.empty;
	},

	refresh : function() {

		var params = this.getFilters();

		var kml = 'index.php?option=com_jea&task=properties.kml&format=xml';

		for (key in params) {
			kml += '&' + key + '=' + params[key];
		}
		kml += '&Itemid=' + this.options.Itemid;

		var mapOptions = {
			mapTypeId : google.maps.MapTypeId.ROADMAP
		};

		this.map = new google.maps.Map(this.content, mapOptions);
		this.applyMask();

		geoXml = new geoXML3.parser({
			map : this.map,
			afterParse : function(docSet) {

				// Count results
				var count = 0;

				docSet.each(function(doc) {
					if (!!doc.markers) {
						count += doc.markers.length;
					}
				});

				if (!count) {
					var geocoder = new google.maps.Geocoder();
					var opts = {
						address : this.options.defaultArea
					};
					geocoder.geocode(opts, function(results, status) {
						if (status == google.maps.GeocoderStatus.OK) {
							this.map.fitBounds(results[0].geometry.viewport);
						}
					}.bind(this));
				}

				if (document.id(this.options.counterElement)) {
					document.id(this.options.counterElement).set('text', count);
				}

				this.removeMask();

			}.bind(this)
		});

		geoXml.parse(kml);
	},

	getFilters : function () {
		
		var form = document.id(this.options.form);
		var filters = {};
		var fields = [ 
		'filter_type_id', 
		'filter_department_id',
		'filter_town_id', 
		'filter_area_id', 
		'filter_budget_min',
		'filter_budget_max', 
		'filter_living_space_min',
		'filter_living_space_max', 
		'filter_rooms_min' 
		];

		if (form) {

			var transTypes = document.getElements('[name=filter_transaction_type]');
			if (transTypes.length > 1) {
				transTypes.each(function(item) {
					if (item.get('checked')) {
						filters['filter_transaction_type'] = item.get('value');
					}
				});
			} else if (transTypes.length == 1) {
				filters['filter_transaction_type'] = transTypes[0].get('value');
			}

			fields.each(function(field) {
				var inputfield = document.getElement('[name='+field+']');
				if (inputfield) {
					if (inputfield.get('value') > 0) {
						filters[field] = inputfield.get('value');
					}
				}
			});

			if (form['filter_amenities[]']) {
				var amenities = document.getElements('[name=filter_amenities[]]');
				amenities.each(function(item, i) {
					if (item.get('checked')) {
						filters['filter_amenities[' + i + ']'] = item.get('value');
					}
				});
			}
		}

		return filters;
	},
	

	applyMask : function() {
		this.mask = new Element('div');
		this.mask.set('class', 'google-map-mask')
		this.mask.setStyles({
			'position' : 'absolute',
			'width' : this.content.getStyle('width'),
			'height' : this.content.getStyle('height'),
			'z-index' : '9999'
		});

		this.content.appendChild(this.mask);
		this.myFx = new Fx.Tween(this.mask, {
			property : 'opacity'
		}).start(0, this.options.opacity);
	},

	removeMask : function() {
		this.myFx.start(this.options.opacity, 0);
		if (this.mask.parentNode) {
			this.mask.destroy();
		}
	}

});
