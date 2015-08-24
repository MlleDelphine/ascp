(function($) {
  var OrganizationInput = $('#service_civique_widget_search_mission_organization');
  var iframeWidget = $('#iframe-wrapper iframe');
  var iframeWidgetCode = $('#widget-code');
  if (organizationId === null) {
    OrganizationInput.val(organizationName);
    // var iframeUrl = Routing.generate('service_civique_widget_show', {id: organizationId})
    var searchUrl = OrganizationInput.attr('data-url');

    OrganizationInput.selectize({
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
        var organizationName = encodeURIComponent(OrganizationInput.val());
        var selectizeField = OrganizationInput[0].selectize;
        selectizeField.clearOptions();
        loadApprovedOrganizationResults(searchUrl, query, organizationName, callback);

      },
      onChange: function(val) {
        updateIframeUrl(val);
      },
    });

    var loadApprovedOrganizationResults = function (url, query, organizationName, callback) {
      var data = {}

      if(typeof query != "undefined") {
        data.name = encodeURIComponent(query);
      }

      // if(typeof organizationName != "undefined") {
      //   data.name = encodeURIComponent(organizationName);
      // }


      $.ajax({
        url: searchUrl,
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

    var updateOrganizationInput = function() {
      var selectizeField = OrganizationInput[0].selectize;
      if(typeof OrganizationInput[0].checkValidity === "function" && OrganizationInput[0].checkValidity()) {
        var organizationName = OrganizationInput.val();
        var url = searchUrl;
        var query;

        selectizeField.clearOptions();

        selectizeField.load(function(callback) {
          loadApprovedOrganizationResults(url, query, organizationName, callback);
          selectizeField.enable();
          selectizeField.open();
        });
      } else {
        selectizeField.disable();
      }
    }

    updateOrganizationInput();

    OrganizationInput.on('input', function() {
      updateOrganizationInput();
    });

    function updateIframeUrl(id) {
      var url = Routing.generate('service_civique_widget_show', {id: id}, true);
      iframeWidget.attr('src', url);
      updateWidgetCode();
    }
  }

  function updateIframeWidth(width) {
    iframeWidget.attr('width', width);
    updateWidgetCode();
  }

  function updateWidgetCode() {
    iframeWidgetCode.val($('#iframe-wrapper').html());
  }

  $('#service_civique_widget_search_mission_width').blur(function() {
    var width = $(this).val();
    if(width > 500) {
      width = 500;
    } else if(width < 280) {
      width = 280;
    }
    $(this).val(width)
    updateIframeWidth(width);
  });

  updateWidgetCode();
})(jQuery)
