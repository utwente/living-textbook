require('../../css/analytics/analytics.scss');
import Analytics from './analytics';

$(function () {
  global.analyticsDashboard = new Analytics(global.Routing);
});

if (module.hot) {
  module.hot.accept('./analytics', function () {
    global.analyticsDashboard = new Analytics(global.Routing);
  });
}
