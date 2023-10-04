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
        let option = $(`<option value="${item.id}">${item.value}</option>`);
        if (activeValue == item.id) {
          option.attr('selected', 'selected');
        }
        element.append(option);
      }
      element.trigger('chosen:updated.chosen'); // Update jQuery choosen
    }
  });
};

function updateAmenities(language) {

  const ulElement = $('#amenities');
  var selected = [];

  ulElement.find('.amenity input').each(function(index, input) {
    if (input.checked) {
      selected.push(input.value);
    }
  });

  ulElement.empty();

  $.getJSON('index.php', {
      'option': 'com_jea',
      'format': 'json',
      'task': 'features.get_list',
      'feature': 'amenity',
      'language': language
    })
  .done(function (response) {
    if (response.length) {
      $.each(response, (i, item) => {
        const isActive = selected.findIndex(value => value == item.id) >= 0;
        const activeClass = isActive ? ' active' : '';
        const liElement = $(`<li class="amenity${activeClass}"></li>`);
        const checkbox = $(
            `<input class="am-input" type="checkbox" name="jform[amenities][]" id="jform_amenities${i}" value="${item.id}"${isActive ? ' checked="checked"' : ''}>`);
        const label = $(
            `<label class="am-title" for="jform_amenities${i}">${item.value}</label>`);

        liElement.on('click', function () {
          const input = $(this).find('input');

          if (input.is(':checked')) {
            $(this).removeClass('active');
            input.prop('checked', false);
          } else {
            $(this).addClass('active');
            input.prop('checked', true);
          }
        });

        liElement.append(checkbox, label);
        ulElement.append(liElement);
      });
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
    $('#ajaxupdating').css('display', 'block');
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
