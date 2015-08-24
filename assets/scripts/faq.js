(function($) {

  $(document).ready(function() {
    showMeTheBox();
  });
  $(window).on('hashchange', function() {
    showMeTheBox();
  });

  function showMeTheBox() {
    var hash = window.location.hash;
    if (hash != '') {
      $('.panel-collapse').removeClass('in');
      $('h4.panel-title a').removeClass('in').addClass('collapsed');
      $('a[href$="'+ hash +'"]').removeClass('collapsed').addClass('in');
      $(hash).parents('.panel-collapse').addClass('in');
      $(hash).addClass('in');
      $('html, body').animate({
        scrollTop: $(hash).siblings('h4.panel-title').offset().top
      }, 200);
    }
  }

})(jQuery);
