require('../css/doubleColumn/doubleColumn.scss');

/**
 * Load the modules required for the double column page in order to function properly
 *
 * $ has been defined globally in the app.js
 */
$(function () {
  require('./doubleColumn/draggebleWindow');
  require('./search/nodeSearch');
  require('./conceptBrowser/conceptBrowser');

  $.get({
    url: Routing.generate('app_data_export'),
    dataType: 'json'
  }).done(function (data) {
    nodeSearch.createSearch($('#search'), data);
    cb.init(data);
  }).fail(function (error) {
    throw error;
  })
});
