;(function($) {
  var routes = new Bloodhound({
    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('title'),
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    limit: 10,
    prefetch: {
      // url points to a json file that contains an array of country names, see
      // https://github.com/twitter/typeahead.js/blob/gh-pages/data/countries.json
      url: Routing.generate('service_civique_backend_static_content_list_autocomplete'),
      // the json file contains an array of strings, but the Bloodhound
      // suggestion engine expects JavaScript objects so this converts all of
      // those strings
      filter: function(list) {
        return $.map(list, function(data) {
          return data;
        });
      }
    }
  });
  // routes.clearPrefetchCache();

  // kicks off the loading/processing of `local` and `prefetch`
  routes.initialize();

  $('input').iCheck({
    checkboxClass: 'icheckbox_minimal',
    radioClass: 'iradio_minimal',
    increaseArea: '20%' // optional
  });

  // passing in `null` for the `options` arguments will result in the default
  // options being used
  $('.form-group-route .typeahead').typeahead(null, {
    name: 'routes',
    displayKey: 'url',
    // `ttAdapter` wraps the suggestion engine in an adapter that
    // is compatible with the typeahead jQuery plugin
    source: routes.ttAdapter(),
    templates: {
      empty: [
        '<div class="empty-message">',
        // 'unable to find any Best Picture winners that match the current query',
        '</div>'
      ].join('\n'),
      suggestion: Handlebars.compile('<p><strong>{{title}}</strong> – {{url}}</p>')
    }
  });

  $('.wysiwyg').redactor({
    lang: 'fr',
    imageUpload: Routing.generate('service_civique_backend_upload_image'),
    imageGetJson: Routing.generate('service_civique_backend_get_image'),
    fileUpload: Routing.generate('service_civique_backend_upload_file'),
    plugins: ['fontcolor'],
    buttons: ['html', 'formatting',  'bold', 'italic', 'deleted', 'underline', 'justify', 'unorderedlist', 'orderedlist', 'outdent', 'indent', 'image', 'video', 'file', 'table', 'link', 'alignment', 'horizontalrule', 'fontcolor', 'backcolor']
  });
  $('.sortable').sortable({
    appendTo: document.body,
    cursor: 'move',
    update: function(event, ui) {
        var positions = [];
        $('table tbody tr').each(function() {
          $(this).attr('data-position', $(this).index());
          positions.push({
            id: $(this).attr('data-id'),
            position: $(this).attr('data-position'),
          });
        });
        $.ajax({
          type: 'POST',
          url: Routing.generate('service_civique_backend_menu_item_update_position'),
          data: { data: positions }
        })
        .done(function() {});
    },
  });

  if($('#service_civique_mission_admin_approvalNumber').val() != null) {
    search_mission();
  }

  $('#service_civique_mission_admin_approvalNumber').on('blur', function(event) {
    search_mission();
  });

  function search_mission () {
    $.ajax({
      url: Routing.generate('service_civique_approval_show', {
        approval_number: $('#service_civique_mission_admin_approvalNumber').val()
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
      } else {
        $this.html(value);
      }
    });
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
  initAccordions();

  if($('#service_civique_mission_answer_mail_answer_mail')) {
    var vars = $('#mailto-vars');
    var missionTitle  = vars.attr('data-missiontTitle');
    var missionUrl    = vars.attr('data-missiontUrl');
    var mailtoContacts = vars.attr('data-mailtoContacts');
    $('#service_civique_mission_answer_mail_answer_mail').change(function() {
      if($(this).val() != '') {
        $.ajax({
          type: 'GET',
          url: Routing.generate('service_civique_backend_answer_mail_get_description', {id: $(this).val()})
        })
        .done(function(answerMail) {
          createMailToLink(answerMail)
        });
      }
    });
  }

  function createMailToLink (answerMail) {
    var subject = missionTitle + ' - ' + answerMail.title;
    var body = answerMail.text + "\n\n---\n" + missionTitle + "\n" + missionUrl;

    var href = 'mailto:' + mailtoContacts + '?subject=' + encodeURIComponent(subject) + '&body=' + encodeURIComponent(body);
    $('.mailto-link').attr('href', href);
  }

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
  $('#delete-pin').on('click', function() {
    $this = $(this);
    $.ajax({
      type: 'POST',
      url: Routing.generate('service_civique_backend_header_delete_pin', {id: $this.attr('data-id')}),
    }).done(function() {
      $this.parent('.imageWrapper').children('img').hide();
    });
  });

  $('.paginate-url').change(function(e) {
    // var url = $(location).attr('href');
    // window.location.replace(url);
    // e.preventDefault();
    $(this.form).submit();
  });

  var selectedMissions = [];
  $('input[name="mass_update_mission"]').on('ifChecked', function () {
    selectedMissions.push($(this).val());
  });
  $('input[name="mass_update_mission"]').on('ifUnchecked', function () {
    var index = selectedMissions.indexOf($(this).val());
    if (index > -1) {
      selectedMissions.splice(index, 1);
    }
  });

  $('.select-all-checkbox-mass-update').on('ifChecked', function(){
    $('input[type=checkbox].mass-update-mission-checkbox').each(function () {
      $(this).iCheck('check');
      selectedMissions.push($(this).val());
    });
  });
  $('.select-all-checkbox-mass-update').on('ifUnchecked', function(){
    $('input[type=checkbox].mass-update-mission-checkbox').each(function () {
      $(this).iCheck('uncheck');
      var index = selectedMissions.indexOf($(this).val());
      if (index > -1) {
        selectedMissions.splice(index, 1);
      }
    });
  });

  $('.mass-update-btn').on('click', function() {
    var url = $('.mass-update-btn').attr('data-base-mass-update-url');
    $.ajax({
      type: 'GET',
      url: url,
      data: { missions: selectedMissions.toString() }
    })
    .done(function() {location.reload();});
  })
})(jQuery);
