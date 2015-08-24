(function($) {

  sc_toggle_organization_fields();
  $('input[name="service_civique_mission\\[organization\\]\\[is_new_organization\\]"]').on('ifChecked', function (event) {
    sc_toggle_organization_fields();
  });

  sc_toggle_french_mission_search_fields();
  $('input[name="service_civique_mission\\[is_overseas\\]"]').on('ifChecked', function (event) {
    sc_toggle_french_mission_search_fields();
  });

  if($('#service_civique_mission_approvalNumber').val() != null) {
    search_mission();
  }

  $('#service_civique_mission_approvalNumber').on('blur', function(event) {
    search_mission();
  });

  function search_mission () {
    $.ajax({
      url: Routing.generate('service_civique_approval_show', {
        approval_number: $('#service_civique_mission_approvalNumber').val()
      }),
      beforeSend: function() {
        approval_block_state('loading');
      }
    })
    .done(function(data) {
      fill_approval_block(data);
    })
    .fail(function() {
      fill_approval_block(null, true);
    });
  }

  function fill_approval_block(data, isError) {
    approval_block_state('ok');
    $('[data-approval]').each(function() {
      var $this = $(this);
      var value = 'Information non disponible'
      if(!isError) {
        value = data[$this.attr('data-approval')];
      }
      if($this.attr('data-approval') === 'pdf_url') {
        if(value != '' && !isError) {
          $this.html('<div class="btn-box center"><a class="btn-sc-red-2 btn btn-lg" href="' + value + '" title="Télécharger l\'agrément"><i class="icon-download"></i>  Télécharger l\'agrément</a></div>');
        }
      } else if($this.attr('data-approval') === 'term_date' || $this.attr('data-approval') === 'decision_date') {
        if(value != '' && value != 'Information non disponible') {
          var date = new Date(value);
          $this.html(addZeroToNumber(date.getDate()) + '/' + addZeroToNumber(date.getMonth() + 1) + '/' + date.getFullYear());
        }
      }  else {
        $this.html(value);
      }
    });
  }
  function addZeroToNumber(number) {
    if(number < 10) {
      return '0' + number;
    }

    return number;
  }

  function approval_block_state(state) {
    if(state === 'loading') {
      $('.agreement-content').hide();
      $('.agreement-loader').show();
    } else {
      $('.agreement-loader').hide();
      $('.agreement-content').show();
    }
  }

  function sc_toggle_french_mission_search_fields() {

    var frenchSelects = $('#service_civique_mission_location_area, #service_civique_mission_location_department');
    var countrySelect = $('#service_civique_mission_location_country');

    // If French radio button is checked, we disable area & department fields
    if ($('#service_civique_mission_is_overseas_1').is(':checked')) {
      frenchSelects.removeAttr('disabled').parent().show();
      countrySelect.attr('disabled', 'disabled').parent().hide();
    } else {
      countrySelect.removeAttr('disabled').parent().show();
      frenchSelects.attr('disabled', 'disabled').parent().hide();
    }

  }

  function sc_toggle_organization_fields() {

    var newOrganizationInput = $('#service_civique_mission_organization_new_organization_user_email, #service_civique_mission_organization_new_organization_name');
    var organizationSelect = $('#service_civique_mission_organization_organization');

    // If French radio button is checked, we disable area & department fields
    if ($('#service_civique_mission_organization_is_new_organization_1').is(':checked')) {
      newOrganizationInput.removeAttr('disabled').parent().show();
      organizationSelect.attr('disabled', 'disabled').parent().hide();
    } else {
      organizationSelect.removeAttr('disabled').parent().show();
      newOrganizationInput.attr('disabled', 'disabled').parent().hide();
    }

  }
  if ($('.form-group-approved_choice').length) {
    $('.form-group-is_new_organization').hide();
    $('.form-group-organization-select').hide();
  };

  $('#service_civique_mission_approved_choice input').on('ifChecked', function (event) {
    sc_toggle_isNewOrganization_fields();
  });

  function sc_toggle_isNewOrganization_fields() {
    if ($('#service_civique_mission_approved_choice_1').is(':checked')) {
      $('.form-group-is_new_organization').show();
      $('.form-group-organization-select').hide();
    } else {
      $('.form-group-is_new_organization').hide();
      $('.form-group-organization-select').hide();
    }

  }
  sc_toggle_isNewOrganization_fields();

})(jQuery);
