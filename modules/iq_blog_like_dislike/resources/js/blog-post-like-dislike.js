(function ($, Drupal) {
  $(document).ready(function () {

    $('.like_dislike a').click(function(e){
      if ($(this).is(":disabled")) {
        e.preventDefaul();
        return;
      }
      $(this).attr('disabled', true);
      $(this).parent().addClass('disabled')
    });
  });



})(jQuery, Drupal);
