require('../css/content/content.scss');

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

  // Load tooltips
  $('[data-toggle="tooltip"]').tooltip({trigger: "hover"});

  // Bind to all links
  $(document.body).on('click', function (e) {
    e = e || event;

    var from = findParent('a', e.target || e.srcElement);
    if (from) {
      var $from = $(from);

      // Exclude 'no-link' class from handler
      if ($from.hasClass('no-block')) return;

      // Exclude hash urls
      var url = $from.attr('href');
      if (url.startsWith('#')) return;

      // Exclude javascript urls
      if (url.startsWith('javascript')) return;

      // Build options
      var options = {
        topLevel: $from.hasClass('top-level')
      };

      // Load the new page
      e.preventDefault();
      eDispatch.pageLoad(url, options);
    }
  });

  // Disable submit buttons on submit
  $('form').submit(function () {
    $(this).find(':input[type=submit]')
        .addClass('disabled')
        .on('click', function (e) {
          e.preventDefault();
          return false;
        });
  });

  // Page loaded event
  eDispatch.pageLoaded();
});
