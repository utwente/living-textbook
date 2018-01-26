require('../css/wiki/wiki.scss');

$(function () {
  require('./wiki/eventHandler');
  require('./wiki/eventDispatcher');

  // Initialize page
  $(function () {
    // Load tooltips
    $('[data-toggle="tooltip"]').tooltip({trigger: "hover"});
  });

  eDispatch.pageLoaded();
});
