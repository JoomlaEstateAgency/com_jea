function updateFeature(name, fieldId, language) {
  //active option selected
  var activeValue = $('#' + fieldId).val();
  // ajax request
  $.ajax({
    url: 'index.php',
    data: {
      'option': 'com_jea',
      'format': 'json',
      'task': 'features.get_list',
      'feature': name,
      'language': language
    }
  })
  .done(function (response) {
    var element = $('#' + fieldId);
    element.empty();
    if (response) {
      for (let item of response) {
        element.append(`<option value="${item.id}" selected="${activeValue
        === item.id}">${item.value}</option>`);
      }
      element.trigger('chosen:updated.chosen'); // Update jQuery choosen
    }
  });
};

function updateAmenities(language) {
  //active option selected
  var labels = document.getElementsByClassName('amenity');
  var checkedLabels = Array();
  //store active amenities & clear labels
  for (let label of labels) {
    var input = label.getElementsByTagName('input')[0];
    if (input.checked) {
      checkedLabels.push(input.value);
    }
  }
  //remove current amenities
  $('#amenities').empty();

  $.ajax({
    url: 'index.php',
    data: {
      'option': 'com_jea',
      'format': 'json',
      'task': 'features.get_list',
      'feature': 'amenity',
      'language': language
    }
  })
  .done(function (response) {
    if (response) {

      const ulElement = $('#amenities');

      for (let i = 0; i < response.length; i++) {
        const item = response[i];

        const isActive = checkedLabels.findIndex(value => value == item.id) >= 0;
        const activeClass = isActive ? 'active' : '';

        const liElement = $(`<li class="amenity ${activeClass}"></li>`);

        const checkbox = $(
            `<input class="am-input" type="checkbox" name="jform[amenities][]" id="jform_amenities${i}" value="${item.id}" checked="${isActive}" style="display: none;">`);

        const label = $(
            `<label class="am-title" id="jform_amenities_label${i}" for="jform_amenities${i}">${item.value} (${item.id})</label>`);

        label.on('click', function (event) {
          if (checkbox.is(':checked')) {
            liElement.removeClass('active');
            checkbox.checked = false;
          } else {
            liElement.addClass('active');
            checkbox.checked = true;
          }
        });

        liElement.append(checkbox, label);
        ulElement.append(liElement);
      }
    }
  });
}

function updateFeatures() {
  //language selected
  var language = $('#jform_language').val();

  //update
  updateFeature('type', 'jform_type_id', language);
  updateFeature('condition', 'jform_condition_id', language);
  updateFeature('heatingtype', 'jform_heating_type', language);
  updateFeature('hotwatertype', 'jform_hot_water_type', language);
  updateFeature('slogan', 'jform_slogan_id', language);
  updateAmenities(language);
}

document.addEventListener('DOMContentLoaded', function () {

  // colors
  var borderColor = '#DE7A7B';

  $('#ajaxupdating').css('display', 'none');
  $('#jform_language').on('change', function (event) {

    // show field alerts
    $('#ajaxupdating').css('display', '');
    $('#jform_type_id').css('border', '1px solid ' + borderColor);
    $('#jform_condition_id').css('border', '1px solid ' + borderColor);
    $('#jform_heating_type').css('border', '1px solid ' + borderColor);
    $('#jform_hot_water_type').css('border', '1px solid ' + borderColor);
    $('#jform_slogan_id').css('border', '1px solid ' + borderColor);
    $('#amenities').css('border', '1px solid ' + borderColor);

    // update dropdowns
    updateFeatures();
  });
  //onload update dropdowns
  updateFeatures();
});
