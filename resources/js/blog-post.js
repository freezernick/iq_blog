

(function ($, Drupal) {
  $(document).ready(function () {

    document.addEventListener(
      "scroll",
      function () {
        var scrollTop =
          document.documentElement["scrollTop"] || document.body["scrollTop"];
        var scrollBottom =
          (document.documentElement["scrollHeight"] ||
            document.body["scrollHeight"]) - document.documentElement.clientHeight;
        scrollPercent = scrollTop / scrollBottom * 100 + "%";

        $('[data-scroll-progress]')[0].style.setProperty("--scroll", scrollPercent);
      },
      { passive: true }
    );

    $('.comment').find('.comment-reply').find('a').click(function(e){
      e.preventDefault();
      $(this).closest('.comment').find('.field--type-comment').toggleClass('active')
    })

  });



})(jQuery, Drupal);
