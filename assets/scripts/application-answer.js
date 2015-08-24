(function($) {
  $('#service_civique_application_answer_single_status input').on('ifChecked', function(event) {
    if($(this).val() == 1) {
      $('#service_civique_application_answer_single_messageText').val(
        $('#application-positive-answer').text()
      );
    } else {
      $('#service_civique_application_answer_single_messageText').val(
        $('#application-negative-answer').text()
      );
    }
  });
})(jQuery);
