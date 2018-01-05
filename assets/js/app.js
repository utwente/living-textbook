require('../css/app.scss');

// Create global $ and jQuery variables
const $ = require('jquery');
global.$ = global.jQuery = $;

// Initialize page
$(function () {
  // Load tooltips
  $('[data-toggle="tooltip"]').tooltip({ trigger: "hover" });
});
