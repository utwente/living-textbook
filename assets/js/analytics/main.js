import Analytics from './analytics';

require('../../css/analytics/analytics.scss');
require('../conceptBrowser/configuration.js');

$(function () {
  global.analyticsDashboard = new Analytics(global.Routing);
});

if (module.hot) {
  module.hot.accept(['./analytics', './analyticsBrowser'], function () {
    global.analyticsDashboard = new Analytics(global.Routing, global.analyticsDashboard.data);
  });
}
