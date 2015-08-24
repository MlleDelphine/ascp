(function($) {
  $('.signalBoxAlert').hide();
  $('#signalContent form').submit(function(event) {
    event.preventDefault();
    $.ajax({
      url: Routing.generate('service_civique_mission_report_create', {id: $(this).attr('data-missionId'), status: $('.select-report').val()}),
      type: 'GET',
      error: function() {},
      success: function(dataStatus) {
        $('.signalBox').hide();
        $('.signalBoxAlert').show();
      }
    });
  });

  $('input').iCheck({
    checkboxClass: 'icheckbox_minimal',
    radioClass: 'iradio_minimal',
    increaseArea: '20%' // optional
  });

  // Show tooltips
  $('.show-tooltip').tooltip({'trigger':'focus'});

  // load grid system if media queries are supported
  yepnope({
    test: Modernizr.mq('only all'),
    yep : '/assets/js/vendor/salvattore.js',
    complete: function () {
      // show grids
      $('html').addClass('grid-ready');
    }
  });

  var hasFormValidations = function() {
    return (typeof document.createElement('input').checkValidity == 'function');
  }

  // form validation fallback
  yepnope({
    test: !hasFormValidations(), //!hasFormValidations,
    yep : '/assets/js/vendor/webshim.js',
    complete: function () {
    }
  });

  var initAccordions = function() {

    var openClass = 'open';

    var update = function(trigger, target, transition) {
      if (trigger.attr('aria-expanded') == 'true') {
        trigger.addClass(openClass)
        target.addClass(openClass).removeClass("sr-only");
        if(transition) {
          target.slideDown();
        } else {
          target.show();
        }
      } else {
        trigger.removeClass(openClass);
        if(transition) {
          target.removeClass(openClass).slideUp();
        } else {
          target.hide().removeClass("sr-only");
        }
      }
    }

    var toggle = function(trigger, target) {
      var expanded = (trigger.attr('aria-expanded') === "true") ? "false" : "true";
      var animated = (typeof trigger.attr('data-animated') == 'undefined') || (trigger.attr('data-animated') === "true");
      trigger.attr('aria-expanded', expanded);
      update(trigger, target, animated);
    }

    $('a[aria-expanded]').each(function () {
      var trigger = $(this);
      var target = $('#' + trigger.attr('aria-controls'));
      update(trigger, target, false);
    }).on('click', function(event) {
      var trigger = $(this);
      var target = $('#' + trigger.attr('aria-controls'));
      toggle(trigger, target);
      event.preventDefault();
    });
  };

  // data pickers
  var initDataPickers = function initDataPickers() {
    if (!Modernizr.inputtypes.date) {

      var inputDate = $('.input-group.date');
      var input = inputDate.children('input');

      input.val(input.attr('data-formatted'));

      inputDate.datepicker({
        autoclose: true,
        format: 'dd/mm/yyyy',
        language: "fr",
      });

    } else {
      $('.input-group.date .input-group-addon').hide().parent().removeClass('input-group');
    }
  };

  initDataPickers();
  initAccordions();

  $(".control-header-link, .profil-header-link").click(function(event){
    if($(this).hasClass("actived")) {
      $(this).removeClass("actived");
      $('#' + $(this).attr('aria-controls')).removeClass("in");
    }
    else {
      $(this).addClass("actived");
      $('#' + $(this).attr('aria-controls')).addClass("in");
    }
    event.preventDefault();
  })

  /* menu */
  // Menu dropdown au roll hover
  /*
  $("#main-menu .dropdown").hover(function() {
    if (!$(this).hasClass("open")) {
      $(this).find("a[data-toggle='dropdown']").trigger("click");
    }
  }, function() {
    if ($(this).hasClass("open")) {
      $(this).find("a[data-toggle='dropdown']").trigger("click");
    }
  });
  */

  // Jeunes select
  var applicationsId = [];
  var cptAppCheck = 0;

  $(".jeunes-select .icheckbox_minimal").each(function( index ) {
    if($(this).hasClass('checked')) {
      applicationsId.push($(this).children('input[type=checkbox].data-jeune-select').val());
      cptAppCheck++;
    }
  });

  updateJeunesSelectUrl();
  activateApplicationButtons();

  function applicationIdToParams (applicationsId) {
    params = '';
    $.each(applicationsId, function(key, value) {
      params += value + ',';
    });

    return '?applications=' + encodeURIComponent(params.replace(/,\s*$/, ''));
  }

  function updateJeunesSelectUrl () {
      $('#selected-jeunes-target').attr('href', $('#selected-jeunes-target').attr('data-href') + applicationIdToParams(applicationsId));
  }

  $('input[type=checkbox].data-jeune-select').on('ifChecked', function(event){
    applicationsId.push($(this).val());
    updateJeunesSelectUrl();
    cptAppCheck++;
    activateApplicationButtons();
  });

  $('input[type=checkbox].data-jeune-select').on('ifUnchecked', function(event){
    applicationsId.splice(applicationsId.indexOf($(this).val()), 1);
    updateJeunesSelectUrl();
    cptAppCheck--;
    activateApplicationButtons();
  });

  $('.jeunes-select .select-all-checkbox').on('ifChecked', function(event){
    $('input[type=checkbox].data-jeune-select').each(function () {
      $(this).iCheck('check');
    });
  });

  $('.jeunes-select .select-all-checkbox').on('ifUnchecked', function(event){
    $('input[type=checkbox].data-jeune-select').each(function () {
      $(this).iCheck('uncheck');
    });
  });

  function activateApplicationButtons () {
    if(cptAppCheck > 0) {
      $('#selected-jeunes-target').removeClass('disabled')
    } else {
      $('#selected-jeunes-target').addClass('disabled')
    }
  }

  // todo select all

  var mails = [];
  var cptCheck = 0;

  // Init
  $(".icheckbox_minimal").each(function( index ) {
    if($(this).hasClass('checked')) {
      mails.push($(this).children('input[type=checkbox].data-mails').val());
      cptCheck++;
    }
  });
  updateMailsUrl();
  activateAnswerButtons();

  $('input[type=checkbox].data-mails').on('ifChecked', function(event){
    mails.push($(this).val());
    updateMailsUrl();
    cptCheck++;
    activateAnswerButtons();
  });

  $('input[type=checkbox].data-mails').on('ifUnchecked', function(event){
    mails.splice(mails.indexOf($(this).val()), 1);
    updateMailsUrl();
    cptCheck--;
    activateAnswerButtons();
  });

  function mailsToParams (mails) {
    params = '';
    $.each(mails, function(key, value) {
      params += value + ',';
    });

    return '?mails=' + encodeURIComponent(params.replace(/,\s*$/, ''));
  }

  function updateMailsUrl () {
    $('.data-mails-targets').each(function() {
      $(this).attr('href', $(this).attr('data-href') + mailsToParams(mails));
    });
  }

  $('.status_select').on('change', function() {
    var $this = $(this);
    var mission = $this.parents('tr').attr('data-mission');
    var status = $this.val();
    $.ajax({
      url: Routing.generate('service_civique_mission_status', {id: mission, status: status}),
      type: 'POST',
      error: function() {},
      success: function(dataStatus) {
        if (status == 2) {
          window.location.href = Routing.generate('service_civique_application_user_mission_select', {id: mission});
        } else {
          location.reload();
        }
      }
    });
  });

  $('.cancelupdates').on('click', function(event) {
    var $this = $(this);
    var mission = $this.parents('tr').attr('data-mission');

    $.ajax({
      url: Routing.generate('service_civique_mission_cancel_updates', {id: mission}),
      type: 'POST',
      error: function() {},
      success: function(dataStatus) {
        location.reload();
      }
    });
    event.preventDefault();
  });

  $('#fos_user_registration_form_organization_approvalNumber').on('blur', function(event) {
    $.ajax({
      url: Routing.generate('service_civique_approval_show', {
        approval_number: $('#fos_user_registration_form_organization_approvalNumber').val()
      })
    })
    .done(function(data) {
      $('#fos_user_registration_form_organization_name').val(data['organization_name']);
      $('#fos_user_registration_form_organization_location_address').val(data['address']);
      $('#fos_user_registration_form_organization_location_zipCode').val(data['zip_code']);
      $('#fos_user_registration_form_organization_location_city').val(data['city']);
    })
    .fail(function() {});
  });

  $('.select-all-checkbox').on('ifChecked', function(event){
    $('input[type=checkbox].data-mails').each(function () {
      $(this).iCheck('check');
    });
  });

  $('.select-all-checkbox').on('ifUnchecked', function(event){
    $('input[type=checkbox].data-mails').each(function () {
      $(this).iCheck('uncheck');
    });
  });

  $('.remove-resume').on('click', function() {
    $(this).parent().hide();
    $.ajax({
      url: Routing.generate('service_civique_resume_delete'),
      type: 'POST',
      error: function() {},
      success: function() {
        // location.reload();
      }
    });
  });

  function activateAnswerButtons () {
    if(cptCheck > 0) {
      $('.data-mails-targets').removeClass('disabled')
    } else {
      $('.data-mails-targets').addClass('disabled')
    }
  }

  if($('.form-group-hasaccount').length > 0) {
    $('.form-group-_username').hide();
    $('.form-group-_password').hide();
    $('.form-group-user').hide();

    $('#service_civique_application_hasaccount_0').on('ifChecked', function(event){
      show_login_or_create_user_form('login');
    });
    $('#service_civique_application_hasaccount_1').on('ifChecked', function(event){
      show_login_or_create_user_form('create');
    });
  }

  function show_login_or_create_user_form(isShown) {
    if(isShown == 'login') {
      $('.form-group-_username').show();
      $('.form-group-_password').show();
      $('.form-group-user').hide();
    } else {
      $('.form-group-_username').hide();
      $('.form-group-_password').hide();
      $('.form-group-user').show();
    }
  }

  $(function () {
    $('[data-toggle="tooltip"]').tooltip();
  });

  if (window.location.href.indexOf("?sorting%5B") > -1) {
    location.href = '#scroll-content';
  }

  // Newsletter
  $('.form-group-organizationName').hide();
  $('#service_civique_newsletter_role').on('change', function() {
    var $this = $(this);
    if ($this.val() == 2) {
      $('.form-group-organizationName').show();
    } else {
      $('.form-group-organizationName').hide();
    }
  });

  $('#fos_user_registration_form_organization_approvalNumber, #approval_number_preview, #fos_user_profile_form_organization_approvalNumber, #form_approval_number, .approvalnumberfield').mask('BB-000-00-00000-00', {
    'translation': {
      B: {
          pattern: /[A-Z]/,
          transform: 'X'
        }
    }
  });

  $('#fos_user_registration_form_organization_approvalNumber, #approval_number_preview, #fos_user_profile_form_organization_approvalNumber, #form_approval_number, .approvalnumberfield').on('input', function() {
    $(this).val($(this).val().toUpperCase());
  });
  $('#approval_number_preview, #fos_user_registration_form_organization_approvalNumber, #form_approval_number').on('input', function() {
    var url = $('.conflict_page').attr('data-url') + '?approval_number=' + $(this).val();
    $('.conflict_page').attr('href', url);
  });
  var url = $('.conflict_page').attr('data-url') + '?approval_number=' + $('#form_approval_number').val();
  $('.conflict_page').attr('href', url);

  


})(jQuery);

