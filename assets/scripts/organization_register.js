(function($) {

  var approvedOrganizationInput = $('.form-group-approvedOrganization input');
  var approvalNumberInput = $('.form-group-approvalNumber input');
  var url = approvedOrganizationInput.attr('data-url');
  var organizationValue;

  approvedOrganizationInput.selectize({
    valueField: 'id',
    labelField: 'name',
    searchField: ['name', 'approval_number'],
    create: false,
    persist: false,
    openOnFocus: true,
    maxItems: 1,
    render: {
      option: function(item, escape) {
        html = '<div>';

        if(item.approval_number) {
          html += '<div><small>' + escape(item.approval_number) + '</small></div>';
        }

        html += '<div class="name"><strong>' + escape(item.name) + '</strong></div>';

        if(item.description) {
          html += '<p><small>' + escape(item.description.substring(0, 140)) + '</small></p>';
        }

        return html + '</div>';
      }
    },
    score: function(search) {
      var score = this.getScoreFunction(search);
      return function(item) {
        return item.score + score(item);
      };
    },
    load: function(query, callback) {

      var approvalNumber = encodeURIComponent(approvalNumberInput.val());

      loadApprovedOrganizationResults(url, query, approvalNumber, callback);

    }
  });

  var loadApprovedOrganizationResults = function (url, query, approvedNumber, callback) {

    var data = {}

    if(typeof query != "undefined") {
      data.q = encodeURIComponent(query);
    }

    if(typeof approvedNumber != "undefined") {
      data.approval_number = encodeURIComponent(approvedNumber);
    }


    $.ajax({
      url: url,
      data: data,
      type: 'GET',
      error: function() {
        callback();
      },
      success: function(res) {
        callback(res);
      }
    });
  }

  var updateApprovedOrganizationInput = function() {
    var selectizeField = approvedOrganizationInput[0].selectize;
    if(typeof approvalNumberInput[0].checkValidity === "function" && approvalNumberInput[0].checkValidity()) {
      var approvedNumber = approvalNumberInput.val();
      var url = approvedOrganizationInput.attr('data-url');
      var query;

      selectizeField.clearOptions();

      selectizeField.load(function(callback) {
        loadApprovedOrganizationResults(url, query, approvedNumber, callback);
        selectizeField.enable();
        selectizeField.open();
      });
    } else {
      selectizeField.disable();
    }
  }

  updateApprovedOrganizationInput();

  approvalNumberInput.on('input', function() {
    updateApprovedOrganizationInput();
  });
  var organizationName = '';
  var updateApprovedOrganizationInputVisibility = function () {
    var selectizeField = approvedOrganizationInput[0].selectize;
    if ($('#fos_user_registration_form_organization_type_1').is(':checked') || $('#fos_user_profile_form_organization_type_1').is(':checked')) {
      approvedOrganizationInput.parent().show();
      $('#fos_user_registration_form_organization_name').val('');
      selectizeField.setValue(organizationValue);
    } else {
      $('#fos_user_registration_form_organization_name').val(organizationName);
      approvedOrganizationInput.parent().hide();
    }
  }

  updateApprovedOrganizationInputVisibility();
  $('#fos_user_registration_form_organization_type').on('ifChecked', updateApprovedOrganizationInputVisibility);
  $('#fos_user_profile_form_organization_type').on('ifChecked', updateApprovedOrganizationInputVisibility);

  $('#approval_number_preview_form').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
      url: Routing.generate('service_civique_organization_approval_search', {approval_number: $('#approval_number_preview').val()}),
      type: 'GET',
      error: function() {},
      success: function(data) {
        if (data.match == true) {
          var approvalNumber = data.approval_number;
          organizationValue = data.organization.value
          organizationName = data.organization.name;
          $('#approval_number_preview_error').text();
          approvalNumberInput.val(approvalNumber).trigger('input');
          $('#fos_user_registration_form_organization_name').val(data.organization.name);
          $('#fos_user_registration_form_organization_location_zipCode').val(data.organization.zip_code);
          $('#fos_user_registration_form_organization_location_address').val(data.organization.address);
          $('#fos_user_registration_form_organization_location_city').val(data.organization.city);

          $('#organization_register_block, #form_rest_block').show();
          $('#approval_number_preview_form').hide();

        } else {
          $('#approval_number_preview_error').text('Ce numéro d\'agrément n\'est pas reconnu.');
          $('.form-group-approval_number_preview').addClass('has-error');
        }
      }
    });

  });
  if (organizationFormError) {
    $('#organization_register_block, #form_rest_block').show();
    $('#approval_number_preview_form').hide();
  } else {
    $('#approval_number_preview_form').show();
    $('#organization_register_block, #form_rest_block').hide();
  }
})(jQuery)
