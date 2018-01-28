require('../css/doubleColumn/doubleColumn.scss');

// Create global d3
const d3 = require('d3');
global.d3 = d3;

/**
 * Load the modules required for the double column page in order to function properly
 *
 * $ has been defined globally in the app.js
 */
$(function () {
  require('./doubleColumn/draggebleWindow');
  require('./search/conceptSearch');
  require('./conceptBrowser/conceptBrowser');
  require('./doubleColumn/draggebleWindow');
  require('./doubleColumn/eventHandler');
  require('./doubleColumn/eventDispatcher');

  $.get({
    url: Routing.generate('app_data_export'),
    dataType: 'json'
  }).done(function (data) {
    conceptSearch.createSearch($('#search'), data);
    cb.init(data);
  }).fail(function (error) {
    throw error;
  });

  // Load tooltips
  $('[data-toggle="tooltip"]').tooltip({trigger: "hover"});

  // Load pop state handler
  window.addEventListener('popstate', function (event) {
    // Check for state object
    if (!event.state) return;

    // Restore document title and page
    eHandler.onPageLoad({url: event.state.currentUrl});
    document.title = event.state.currentTitle;
  });
});
