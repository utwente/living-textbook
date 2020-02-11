// Polyfills for IE11

// Custom event creation
const CustomEvent = require('custom-event');
global.CustomEvent = CustomEvent;

// Start startsWith
if (!String.prototype.startsWith) {
  String.prototype.startsWith = function (search, pos) {
    return this.substr(!pos || pos < 0 ? 0 : +pos, search.length) === search;
  };
}
