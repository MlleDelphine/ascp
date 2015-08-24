(function($) {

  $('#service_civique_mission_search_options select').on('change', function() {
    $('#service_civique_mission_search_options #submit').click();
  });

  sc_toggle_french_mission_search_fields();
  $('input[name="criteria\\[is_overseas\\]"]').on('ifChecked', function (event) {
    sc_toggle_french_mission_search_fields();
  });

  function sc_toggle_french_mission_search_fields() {

    var frenchSelects = $('#criteria_location_area, #criteria_location_department');
    var countrySelect = $('#criteria_location_country');

    // If French radio button is checked, we disable area & department fields
    if ($('#criteria_is_overseas_0').is(':checked')) {
      frenchSelects.removeAttr('disabled').parent().show();
      countrySelect.attr('disabled', 'disabled').parent().hide();
    } else {
      countrySelect.removeAttr('disabled').parent().show();
      frenchSelects.attr('disabled', 'disabled').parent().hide();
    }

  }

})(jQuery);

