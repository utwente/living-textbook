require('../css/app.scss');

// Create global $ and jQuery variables
const $ = require('jquery');
global.$ = global.jQuery = $;

// Initialize page
$(function () {
  // Load tooltips
  $('[data-toggle="tooltip"]').tooltip({trigger: "hover"});
});

// Export loadLocale function
global.loadRoutingLocale = function(locale) {
  var routes = Routing.getRoutes();
  for (var i = 0; i < routes.b.length; i++) {
    routes.c[routes.b[i]].defaults._locale = locale;
  }
  Routing.setRoutes(routes);
};
