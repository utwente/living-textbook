require('../css/wiki/wiki.scss');

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
  require('./wiki/eventHandler');
  require('./wiki/eventDispatcher');

  // Load tooltips
  $('[data-toggle="tooltip"]').tooltip({trigger: "hover"});

  // Bind to all links
  $(document.body).on('click', function (e) {
    e = e || event;

    var from = findParent('a', e.target || e.srcElement);
    if (from) {
      var $from = $(from);

      // Exclude 'no-link' class from handler
      if ($from.hasClass('no-link')) return;

      // Load the new page
      e.preventDefault();
      eDispatch.pageLoad($from.attr('href'));
    }
  });

  // Page loaded event
  eDispatch.pageLoaded();
});
