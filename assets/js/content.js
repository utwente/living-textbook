require('../css/content/content.scss');

let inDoubleColumn = false;
export const inDoubleColumnChecksum = Math.random().toString(36);

export function setDoubleColumnDetected(checksum) {
  if (checksum === inDoubleColumnChecksum) {
    console.info("DoubleColumn context detected!");
    $('#no-browser-warning').slideUp();
    inDoubleColumn = true;
  }
}

function findParent(tag, el) {
  while (el) {
    if ((el.nodeName || el.tagName).toLowerCase() === tag.toLowerCase()) {
      return el;
    }
    el = el.parentNode;
  }
  return null;
}

$(function () {
  require('./content/eventHandler');
  require('./content/eventDispatcher');
  require('./content/customTags');
  require('./content/search');

  eDispatch.checkForDoubleColumn(inDoubleColumnChecksum);

  // Load tooltips
  $('[data-toggle="tooltip"]').tooltip({trigger: "hover"});

  // Bind to all links
  $(document.body).on('click', function (e) {
    // Disable this behavior if the browser has not been found
    if (!inDoubleColumn) return;

    e = e || event;

    let from = findParent('a', e.target || e.srcElement);
    if (from) {
      let $from = $(from);

      // Exclude _blank target links
      if ($from.attr('target') === '_blank') return;

      // Exclude 'no-link' class from handler
      if ($from.hasClass('no-block')) return;

      // Exclude hash urls
      let url = $from.attr('href');
      if (url.startsWith('#')) return;

      // Exclude javascript urls
      if (url.startsWith('javascript')) return;

      // Exclude mailto urls
      if (url.startsWith('mailto:')) return;

      // Build options
      let options = {
        topLevel: $from.hasClass('top-level')
      };

      // Load the new page
      e.preventDefault();
      eDispatch.pageLoad(url, options);
    }
  });

  // Disable submit buttons on submit
  let $form = $('form');
  $form.submit(function () {
    if ($(this).attr('target') === '_blank') return;
    $(this).find(':input[type=submit]')
        .addClass('disabled')
        .on('click', function (e) {
          e.preventDefault();
          return false;
        });
  });

  // Auto focus first form field
  $form.find('input').first().focus();

  // Page loaded event
  eDispatch.pageLoaded();

  // Check in double column has been detected (after timeout)
  setTimeout(() => {
    if (!inDoubleColumn) {
      $('#no-browser-warning').slideDown();
    }
  }, 5000)
});
