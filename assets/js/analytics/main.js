import Analytics from './analytics';

require('../../css/analytics/analytics.scss');

$(function () {
  global.analyticsDashboard = new Analytics(global.Routing);
});

if (module.hot) {
  module.hot.accept(['./analytics', './analyticsBrowser'], function () {
    global.analyticsDashboard = new Analytics(global.Routing, global.analyticsDashboard.data);
  });
}
