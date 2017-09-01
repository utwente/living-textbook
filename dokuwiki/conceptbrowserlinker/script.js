/* DOKUWIKI:include_once scripts/crosstab.js */

/**
 * Register a toolbar button to insert a new concept browser link
 */
if (typeof window.toolbar !== 'undefined') {
  window.toolbar[window.toolbar.length] = {
    type: "format",
    title: "CB Link",
    icon: "../../plugins/conceptbrowserlinker/img/toolbar/cb.png", // located in lib/images/toolbar/
    key: "",
    open: "<cb cb-link>",
    sample: "link description",
    close: "</cb>"
  };
}

/**
 * This function is executed after DOM load, and registers the concept-browser-link
 * span as clickable in order to create the update event
 */
jQuery(function () {
  jQuery('span.concept-browser-link').click(function (e) {
    e.preventDefault();
    var $element = jQuery(this);
    if ($element.data('cbLink') !== ""){
      crosstab.broadcast('cb_update', $element.data('cbLink'));
    } else {
      crosstab.broadcast('cb_update', $element.html().trim());
    }
  });
});