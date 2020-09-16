require('../css/app.scss');

// IE 11 polyfill
import './ie11';
// Sentry
import * as Sentry from '@sentry/browser';
// Import routing
import Routing from 'fos-routing';

// Only bind when production mode is set
if (window.SENTRY_DSN) {
  // Create the default sentry client
  // This instance will communicate any default JS errors
  Sentry.init({
    dsn: window.SENTRY_DSN,
    release: window.SENTRY_RELEASE,
  });

  Sentry.setUser({
    username: window.SENTRY_USERNAME,
    ip_address: window.SENTRY_IP_ADDRESS,
  });
}

// Create global $ and jQuery variables
const $ = require('jquery');
global.$ = global.jQuery = $;

global.Routing = Routing;

// Disable scroll restoration if possible
if ('scrollRestoration' in window.history) {
  // Back off, browser, I got this...
  window.history.scrollRestoration = 'manual';
}
