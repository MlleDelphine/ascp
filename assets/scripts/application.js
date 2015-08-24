(function($) {

  // var applicationForm = $('.form-create-application');
  // var userForm = applicationForm.children('.form-group-user');
  // var useExistingAccountRadio = $('#service_civique_application_user_use_existing_account_0');

  // if(userForm) {

  //   var userLogin = userForm.children('.user-login');
  //   var userRegistration = userForm.children('.user-registration');

  //   sc_toggle_user_application_mode();
  //   useExistingAccountRadio.on('ifChanged', function () {
  //     sc_toggle_user_application_mode();
  //   });
  // }

  // function sc_toggle_user_application_mode() {
  //   if (useExistingAccountRadio.is(':checked')) {
  //     enableHTML5Validations(userLogin).show();
  //     disableHTML5Validations(userRegistration).hide();
  //   } else {
  //     enableHTML5Validations(userRegistration).show();
  //     disableHTML5Validations(userLogin).hide();
  //   }
  // }

  function disableHTML5Validations(container) {
    container.find('input, textarea, select').each(function() {
      var $this = $(this);
      $this.attr('data-required', $this.attr('required'));
      $this.removeAttr('required');
    });

    return container;
  }

  function enableHTML5Validations(container) {
    container.find('input, textarea, select').each(function() {
      var $this = $(this);
      var required = $this.attr('data-required');

      if(required) {
        $this.attr('required', 'required');
      }
    });

    return container;
  }

  $('.remove-resume-application').on('click', function() {
    $(this).parent().hide();
    $('#service_civique_application_removeResume').val(1);
  })

})(jQuery)

