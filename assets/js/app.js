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
