import Color from 'color';

/**
 * Tag color handler
 */
$(function () {
  $('.concept-tag').each(function () {
    const tag = $(this);
    const color = new Color(tag.data('color'));

    tag.css('background-color', color.hex());
    tag.css('border-color', color.darken(0.4).hex());
    if (color.isDark()) {
      tag.addClass('text-white');
    }

    const link = tag.data('link');
    if (link) {
      tag.addClass('clickable');
      tag.on('click', () => {
        if (inDoubleColumn) {
          eDispatch.pageLoad(link);
        } else {
          window.location.href = link;
        }
      });
    }
  });
});
