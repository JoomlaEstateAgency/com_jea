var JeaGeoSearch = new Class({
    
	options: {
		waitImage : 'media/system/images/spinner.gif',
		opacity: 0.5,
		counterElement: '',
		defaultArea : ''
	},
	
	initialize: function(content, options){
		this.content = $(content);
    	this.setOptions(options);
    	
    	this.mask = Class.empty;
    	this.map = Class.empty;
	},
	
	updateMap: function(params) {

		var kml = 'index.php?option=com_jea&controller=kml&task=getproperties';
		
		if(params) {
			for (key in params) {
				kml += '&' + key + '=' + params[key] ;
			}
		}
		
		var mapOptions = {
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};
		      
		this.map = new google.maps.Map(this.content, mapOptions);
		this.applyMask();

		geoXml = new geoXML3.parser({
			map: this.map,
			afterParse: function(docSet) {
				
				// Count results
				var count = 0;
				
				docSet.each(function(doc){
					if(!!doc.markers) {
						count += doc.markers.length;
					}
				});
				
				if(!count) {
					var geocoder = new google.maps.Geocoder();
					var opts = {address: this.options.defaultArea};
					geocoder.geocode(opts, function(results, status) {
						if (status == google.maps.GeocoderStatus.OK) {
							this.map.fitBounds(results[0].geometry.viewport);
						}
					}.bind(this));
				}
				
				if($(this.options.counterElement)){
					$(this.options.counterElement).setHTML(count);
				}
				
				this.removeMask();
				
				
			}.bind(this)  
		});
		
		geoXml.parse(kml);
	},
	
	applyMask: function() {
		this.mask = new Element('div');
		this.mask.setStyles({
			'position'   : 'absolute',
			'width'      : this.content.getStyle('width'),
			'height'     : this.content.getStyle('height'),
			'background' : '#000 url('+this.options.waitImage+') center center no-repeat',
			'z-index'      : '9999'
		});
		
		this.content.appendChild(this.mask);
	    this.myFx = new Fx.Style(this.mask, 'opacity').start(0,this.options.opacity);
	},
	
	removeMask: function() {
		this.myFx.start(this.options.opacity, 0);
		if(this.mask.parentNode) {
			this.mask.remove();
		}
	}
	
	
});

JeaGeoSearch.implement(new Options);
