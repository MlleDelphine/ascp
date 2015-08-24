(function($) {

  var $cityInput = $('.city-input');
  var $countryInput = $('.country-input');
  var $zipCodeInput = $('.zipcode-input');
  var $areaInput = $('.area-input');
  var $departementInput = $('.departement-input');

  var formatChoiceInput = function(input, choices) {
    var attributes = input.prop('attributes');

    var html = [];

    // if multiple results
    if(choices.length > 0) {
      html.push('<select name="'+attributes['name'].value+'" class="'+attributes['class'].value+'" id="'+attributes['id'].value+'">');

      $.each(choices, function(key, choice) {
        html.push('<option value="' + choice.name + '">' + choice.name + '</option>');
      });

      html.push('</select>');
    } else {
      var value = choices;
      html.push('<input type="text" name="'+attributes['name'].value +'" class="'+attributes['class'].value+'" id="'+attributes['id'].value+'" value="'+value+'" />');
    }

    html = html.join('');

    input.replaceWith(html);

    // refresh
    input = $(input.selector);

    return input;
  };


  $countryInput.on('input', function() {
    if($(this).val() != 'FR') {
      $cityInput = formatChoiceInput($cityInput, '');
    } else {
    }
  });

  $areaInput.on('change', function(event) {
    var datasUrl = $(this).attr('data-departements-datas-url');
    var target = $($(this).attr('data-target'));

    $.getJSON(datasUrl, { 'region' : $(this).val() }, function(data) {
      var options = [];

      // sort results
      data = data.sort(function(a, b) {
        return a.name > b.name;
      });

      options.push('<option value="">Tous les départements</option>');
      $.each(data, function(key, val) {
        options.push('<option value="' + val.code + '">' + val.name + '</option>');
      });

      target.html(options);
    });
  });

  $departementInput.on('change', function(event) {
    var datasUrl = $(this).attr('data-area-datas-url');
    var target = $($(this).attr('data-target'));

    $.getJSON(datasUrl, { 'departement' : $(this).val() }, function(data) {
      $areaInput.children('option[value=' + data + ']').attr("selected", "selected");
    });
  });

  $zipCodeInput.on('input', function() {
    var value = $(this).val();

    if($countryInput.val() != 'FR') {
      return;
    }

    if(value.length != 5) {
      $cityInput = formatChoiceInput($cityInput, '');
      return;
    }

    $.ajax({
      url: $(this).attr('data-cities-url') + '?zipcode=' + value,
      success: function(data) {
        $cityInput = formatChoiceInput($cityInput, data);
      },
      error: function() {
      }
    });
  });
})(jQuery)
