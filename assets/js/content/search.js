/**
 * Search handler
 */
$(function () {
  $('.global-search').each(function () {
    const $search = $(this);

    $search.click(function (e) {
      e.preventDefault();

      const $formContainer = $('.global-search-form');
      const buttonPosition = this.getBoundingClientRect();
      const formContainerWidth = $formContainer.outerWidth();
      const formContainerLeft = $(window).width() - formContainerWidth - 16;
      const topPosition = buttonPosition.y + buttonPosition.height + 11 + 'px';

      if (buttonPosition.left > formContainerLeft) {
        $formContainer.css({
          top: topPosition,
          right: '1rem',
          left: 'unset'
        });
        document.styleSheets[document.styleSheets.length - 1].addRule('.global-search-form:before', 'left: ' + (buttonPosition.left - formContainerLeft + 13) + 'px;');
      } else {
        $formContainer.css({
          top: topPosition,
          left: buttonPosition.x,
          right: 'unset'
        });
        document.styleSheets[document.styleSheets.length - 1].addRule('.global-search-form:before', 'left: 13px;');
      }

      // Show popup
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
