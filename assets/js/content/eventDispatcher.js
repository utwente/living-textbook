require('../event/eventTypes');

/**
 * This module generates events from the content side of the application
 */
(function (eDispatch, types) {

  /**
   * Dispatches the given message to the parent window
   * @param type
   * @param data
   */
  function dispatchParent(type, data) {
    console.info('Event dispatched from content', type, data);

    parent.postMessage({
      type: type,
      payload: data
    }, '*');
  }

  /**
   * Blank page opened event
   */
  eDispatch.blankPageLoad = function (url) {
    dispatchParent(types.BLANK_PAGE_LOAD, {
      url: url
    });
  };

  /**
   * Send a message to determine if it is executed in a double column environment
   */
  eDispatch.checkForDoubleColumn = function (checksum) {
    dispatchParent(types.CHECK_DOUBLE_COLUMN, {
      checksum: checksum,
    });
  };

  /**
   * Page needs to load event
   */
  eDispatch.pageLoad = function (url, options) {
    options = options || {};

    dispatchParent(types.PAGE_LOAD, {
      url: url,
      options: options
    });
  };

  /**
   * Page loaded event
   */
  eDispatch.pageLoaded = function () {
    // Check current path
    if (typeof currentPath === 'undefined') {
      currentPath = window.location.pathname;
    }

    dispatchParent(types.PAGE_LOADED, {
      url: currentPath,
      title: document.title,
      concept_id: typeof _currentConceptId === 'undefined' ? undefined : _currentConceptId,
    });
  };

  /**
   * Page submit event
   */
  eDispatch.pageSubmit = function (form) {
    if (form.attr('target') === '_blank') return;

    dispatchParent(types.PAGE_SUBMIT);
  };

  /**
   * Open concept browser event
   */
  eDispatch.openConceptBrowser = function () {
    dispatchParent(types.OPEN_CONCEPT_BROWSER);
  };

  /**
   * Open concept browser event
   */
  eDispatch.closeConceptBrowser = function () {
    dispatchParent(types.CLOSE_CONCEPT_BROWSER);
  };

  /**
   * Show the given concept
   * @param id
   */
  eDispatch.showConcept = function (id) {
    dispatchParent(types.SHOW_CONCEPT, {
      id: id
    });
  };

  /**
   * Open the given learning path
   * @param id
   */
  eDispatch.openLearningPathBrowser = function (id) {
    dispatchParent(types.OPEN_LEARNING_PATH_BROWSER, {
      id: id
    });
  };

  eDispatch.closeLearningPathBrowser = function () {
    dispatchParent(types.CLOSE_LEARNING_PATH_BROWSER);
  };

  /**
   * Update tracking consent for the current study area
   * @param agree
   */
  eDispatch.updateTrackingConsent = function (agree) {
    dispatchParent(types.TRACKING_CONSENT, {
      agree: agree
    });
  };

}(window.eDispatch = window.eDispatch || {}, window.eType));
