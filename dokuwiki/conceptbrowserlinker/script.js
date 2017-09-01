/* DOKUWIKI:include_once scripts/crosstab.js */

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