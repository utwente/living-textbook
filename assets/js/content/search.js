/**
 * Search handler
 */
$(function () {
  $('.global-search').each(function () {
    const $search = $(this);

    $search.click(function (e) {
      e.preventDefault();

      const $formContainer = $('.global-search-form');
      const position = this.getBoundingClientRect();

      // Show popup
      $formContainer.css({
        top: position.y + position.height + 11 + 'px',
        left: position.x + position.width / 2 - $formContainer.outerWidth() + 20 + 'px'
      });
      $formContainer.fadeIn(200);

      // Focus form
      const $input = $formContainer.find('input');
      $input.focus();
    });
  });

  $('.global-search-form').on('focusout', function () {
    $(this).fadeOut(200);
  });
});
