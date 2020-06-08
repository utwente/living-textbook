import Color from 'color';

/**
 * Tag color handler
 */
$(function () {
  $('.concept-tag').each(function () {
    const tag = $(this);
    const color = new Color(tag.data('color'));

    tag.css('background-color', color);
    tag.css('border-color', color.darken(0.4));
    if (color.isDark()) {
      tag.addClass('text-white');
    }
  });
});
