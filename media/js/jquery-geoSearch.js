function JEAGeoSearch(mapId, options) {
  this.content = document.getElementById(mapId)
  this.mask = null
  this.map = null

  this.options = {
    opacity: 0.5,
    counterElement: '',
    defaultArea: '',
    Itemid: 0
  }

  jQuery.extend(this.options, options)
}

JEAGeoSearch.prototype.refresh = function () {

  var params = this.getFilters();

  var kml = 'index.php?option=com_jea&task=properties.kml&format=xml'

  for (key in params) {
    kml += '&' + key + '=' + params[key]
  }

  kml += '&Itemid=' + this.options.Itemid

  var mapOptions = {
    mapTypeId: google.maps.MapTypeId.ROADMAP
  }

  this.map = new google.maps.Map(this.content, mapOptions)

  var that = this

  geoXml = new geoXML3.parser({
    map: this.map,
    afterParse: function (docSet) {

      // Count results
      var count = 0;

      jQuery.each(docSet, function (idx, doc) {

        if (doc.markers) {
          count += doc.markers.length
        }
      })

      if (!count) {
        var geocoder = new google.maps.Geocoder()
        var opts = {
          address: that.options.defaultArea
        }

        geocoder.geocode(opts, function (results, status) {
          if (status == google.maps.GeocoderStatus.OK) {
            that.map.fitBounds(results[0].geometry.viewport);
          }
        })
      }

      jQuery('#' + that.options.counterElement).text(count)
    }
  })

  geoXml.parse(kml);
}

JEAGeoSearch.prototype.getFilters = function () {

  var form = jQuery('#jea-search-form')
  var filters = {}
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
  ]

  var transTypes = form.find('[name="filter_transaction_type"]')

  if (transTypes.length > 1) {

    jQuery.each(transTypes, function (idx, item) {
      if (jQuery(item).prop('checked')) {
        filters['filter_transaction_type'] = jQuery(item).val()
      }
    })

  } else if (transTypes.length == 1) {
    filters['filter_transaction_type'] = transTypes.val()
  }

  jQuery.each(fields, function (idx, field) {
    var val = jQuery(form).find('[name="' + field + '"]').val()
    if (val > 0) {
      filters[field] = val
    }
  })

  var amenities = form.find('[name="filter_amenities[]"]')
  jQuery.each(amenities, function (idx, item) {
    if (jQuery(item).prop('checked')) {
      filters['filter_amenities[' + idx + ']'] = jQuery(item).val()
    }
  })

  return filters
}

