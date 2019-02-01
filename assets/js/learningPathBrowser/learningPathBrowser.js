require('../../css/learningPathBrowser/learningPathBrowser.scss');

/**
 * Register lpb namespace in the browser, for usage of the learning path browser object
 *
 * $ has been defined globally in the app.js
 */
(function (lpb, $, undefined) {

  const openSize = '80%';
  const closedSize = '100%';
  const $doubleColumn = $('#double-column-container');
  const $bottomRow = $('#bottom-row');
  const $closeButton = $('#learning-path-close-button');

  /**
   * Register event handlers
   */
  $closeButton.click(() => lpb.closeBrowser());

  /**
   * Handler to open the learning path browser
   */
  lpb.openBrowser = function (id) {
    // CSS animations are used to make it fluent
    $doubleColumn.css('height', openSize);
    $bottomRow.css('top', openSize);
    triggerResize();
  };

  /**
   * Handler to close the learning path browser
   */
  lpb.closeBrowser = function () {
    // CSS animations are used to make it fluent
    $doubleColumn.css('height', closedSize);
    $bottomRow.css('top', closedSize);
    triggerResize();
  };

  /**
   * Function to trigger the resize event after the animation has finished
   */
  function triggerResize() {
    $doubleColumn.one("webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend", function () {
      setTimeout(function () {
        $(window).trigger('resize');
      }, 100);
    });
  }
}(window.lpb = window.lpb || {}, $));
