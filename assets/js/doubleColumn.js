require('../css/doubleColumn/doubleColumn.scss');
const $ = require('jquery');

$(function () {
  require('./doubleColumn/draggebleWindow');
  require('./search/nodeSearch');

  nodeSearch.createSearch($('#search'), Routing.generate('app_data_export'), true);
});
