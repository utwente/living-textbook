/**
 * Register slp namespace in the browser, for usage of the sortable learning paths plugin
 *
 * $ has been defined globally in the app.js
 */
(function (slp, $) {
  /**
   * Registers the sortable behavior for the given selector
   *
   * @param elementSelector
   */
  slp.registerSortable = function (elementSelector) {
    let $elem = $(elementSelector);
    $elem.sortable({
      axis: "y",
      handle: '.handle'
    });
  };

}(window.slp = window.slp || {}, $));
