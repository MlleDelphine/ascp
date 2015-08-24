(function($) {

  hideContactForm();
  var faqJson, type = 'volontaire';
  var currentLevel1, currentLevel2;
  $.ajax({
    url: Routing.generate('service_civique_faq_json'),
    type: 'GET',
    error: function() {},
    success: function(data) {
      faqJson = data.faq;
      initFaqSelect();
    }
  });

  function initFaqSelect () {
    // Step 1
    $('.faq-select-choice input').on('ifChecked', function(event) {
      var typeChoice = $(this).val();
      if(typeChoice == 1) { // volontaire
        hideContactForm();
        type = 'volontaire'
        hideSelectLevel2And3();
        populateSelect('.faq-select-1', faqJson[type]);
      } else if(typeChoice == 2) { // organisme
        hideContactForm();
        type = 'organisme';
        hideSelectLevel2And3();
        populateSelect('.faq-select-1', faqJson[type]);
      } else { // 0 is always other
        hideAllSelect();
        showContactForm();
        $('#contact-extra-box, .faq-content').hide();
      }
    });
  }

  function showContactForm() {
    $('.faq-contact-form, .faq-content').show();
    $('#contact-extra-box').show();
  }

  function hideContactForm() {
    $('.faq-contact-form, .faq-content').hide();
  }

  function hideAllSelect() {
    $('.faq-select-1').parent().hide();
    hideSelectLevel2And3();
  }
  function hideSelectLevel2And3() {
    $('.faq-select-2').parent().hide();
    $('.faq-select-3').parent().hide();
  }

  function populateSelect(selector, data) {
    $(selector).parent().show();
    $(selector)[0].options.length = 1;
    $.each(data, function(key, value) {
       var opt = document.createElement('option');
       opt.value = key + 1; // 0 is reserved for other
       opt.innerHTML = value.titre;
       $(selector).append(opt);
    });

    // $(selector).append(addOtherOption());
  }

  function addOtherOption() {
    var opt = document.createElement('option');
    opt.value = 0;
    opt.innerHTML = 'Autre';
    return opt;
  }

  $('.faq-select-group select').on('change', function(event) {
    var selectedValue = Number($(this).val()) - 1;

    if($(this).val() == 0) {
      if($(this).parent().hasClass('form-group-faq_level_1')) {
        hideSelectLevel2And3();
      } else if($(this).parent().hasClass('form-group-faq_level_2')) {
        $('.faq-select-3').parent().hide();
      }

      showContactForm();
      $('#contact-extra-box').hide();
      $('.faq-content').hide();
    }
    if($(this).parent().hasClass('form-group-faq_level_1')) {
      currentLevel1 = selectedValue;
      populateSelect('.faq-select-2', faqJson[type][currentLevel1]['rubriques']);
      $('.faq-content').hide();
      hideContactForm();
    } else if($(this).parent().hasClass('form-group-faq_level_2')) {
      currentLevel2 = selectedValue;
      populateSelect('.faq-select-3', faqJson[type][currentLevel1]['rubriques'][currentLevel2]['questions']);
      $('.faq-content').hide();
      hideContactForm();
    } else if($(this).parent().hasClass('form-group-faq_level_3')) {
      // $('.faq-content h3').html(faqJson[type][currentLevel1]['rubriques'][currentLevel2]['questions'][selectedValue].titre);
      $('.faq-content div').html(faqJson[type][currentLevel1]['rubriques'][currentLevel2]['questions'][selectedValue].reponse);
      $('.faq-content').show();
      showContactForm();
    }
  })
})(jQuery);
