require('../../css/learningPathBrowser/learningPathBrowser.scss');

// Import routing
import Routing from 'fos-routing';

/**
 * Register lpb namespace in the browser, for usage of the learning path browser object
 *
 * $ has been defined globally in the app.js
 */
(function (lpb, dispatcher, $, undefined) {

  const openSize = '80%';
  const closedSize = '100%';
  const $doubleColumn = $('#double-column-container');
  const $bottomRow = $('#bottom-row');
  const $loader = $('#bottom-container-loader');
  const $closeButton = $('#learning-path-close-button');

  const $title = $('#learning-path-title');
  const $titleLink = $('#learning-path-title-link');
  const $question = $('#learning-path-question');

  /**
   * Register event handlers
   */
  $closeButton.click(() => lpb.closeBrowser());
  $titleLink.click(() => openLearningPath());

  /**
   * Handler to open the learning path browser
   */
  lpb.openBrowser = function (id) {
    // Clear content and show
    updateContents();
    $loader.show();

    // CSS animations are used to make it fluent
    $doubleColumn.css('height', openSize);
    $bottomRow.css('top', openSize);
    triggerResize();

    // Load the data
    $.get({
      url: Routing.generate('app_learningpath_data', {_studyArea: _studyArea, learningPath: id}),
      dataType: 'json'
    }).done(function (data) {
      updateContents(data);
      $loader.hide();
    }).fail(function (error) {
      throw error;
    });
  };

  /**
   * Update the data contents
   * @param data
   */
  function updateContents(data) {
    const dataSet = typeof data !== 'undefined';

    $titleLink.html(data ? data.name : '');
    $question.html(data ? data.question : '');

    if (dataSet) {
      $titleLink.data('learning-path-id', data.id);
    } else {
      $titleLink.removeData('learning-path-id');
    }
  }

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

  function openLearningPath() {
    const id = $titleLink.data('learning-path-id');
    if (id === undefined) {
      return;
    }

    dispatcher.navigateToLearningPath(id);
  }

}(window.lpb = window.lpb || {}, eDispatch, $));
