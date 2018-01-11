require('../css/app.scss');

// Create global $ and jQuery variables
const $ = require('jquery');
global.$ = global.jQuery = $;

// Create global d3
const d3 = require('d3');
global.d3 = d3;

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
