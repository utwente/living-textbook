require('../css/app.scss');

// Create global $ and jQuery variables
const $ = require('jquery');
global.$ = global.jQuery = $;

// Import routing
import Routing from 'fos-routing';
global.Routing = Routing;

// Disable scroll restoration if possible
if ('scrollRestoration' in window.history) {
  // Back off, browser, I got this...
  window.history.scrollRestoration = 'manual';
}
