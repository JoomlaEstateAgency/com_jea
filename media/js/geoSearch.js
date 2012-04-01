var JEAGeoSearch = new Class({

	Implements : [ Options ],

	mask : null,

	map : null,

	options : {
		waitImage : 'media/com_jea/images/spinner.gif',
		opacity : 0.5,
		counterElement : '',
		defaultArea : ''
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

			if (typeOf(form.filter_transaction_type) == 'collection') {
				Array.from(form.filter_transaction_type).each(function(item) {
					if (item.get('checked')) {
						filters['filter_transaction_type'] = item.get('value');
					}
				});

			} else if (typeOf(form.filter_transaction_type) == 'element') {
				filters['filter_transaction_type'] = form.filter_transaction_type.get('value');
			}
	
			fields.each(function(field) {
				if (form[field]) {
					if (document.id(form[field]).get('value') > 0) {
						filters[field] = document.id(form[field]).get('value');
					}
				}
			});

			if (form['filter_amenities[]']) {
				Array.from(form['filter_amenities[]']).each(function(item, i) {
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
		this.mask.setStyles({
			'position' : 'absolute',
			'width' : this.content.getStyle('width'),
			'height' : this.content.getStyle('height'),
			'background' : '#000 url(' + this.options.waitImage
					+ ') center center no-repeat',
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
