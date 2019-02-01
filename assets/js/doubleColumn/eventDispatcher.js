require('../event/eventTypes');

/**
 * This module generates event from the index/d3 side of the application
 */
(function (eDispatch, types) {

  /**
   * Dispatches the given message to the parent window
   * @param type
   * @param data
   */
  function dispatchParent(type, data) {
    console.info('Event dispatched from double column to parent', type, data);

    parent.postMessage({
      type: type,
      payload: data
    }, '*');
  }

  /**
   * Dispatches the given message to the iframe
   * @param type
   * @param data
   */
  function dispatchIframe(type, data) {
    console.info('Event dispatched from double column to iframe', type, data);

    document.getElementById("data-iframe").contentWindow.postMessage({
      type: type,
      payload: data
    }, '*');
  }

  /**
   * Send the checksum back to the content
   * @param checksum
   */
  eDispatch.returnDoubleColumnChecksum = function (checksum) {
    dispatchIframe(types.CHECK_DOUBLE_COLUMN_RETURN, {
      checksum: checksum
    });
  };

  /**
   * Concept selected event
   * @param id
   */
  eDispatch.conceptSelected = function (id) {
    dispatchParent(types.CONCEPT_SELECTED, {
      id: id
    });
  };

  /**
   * Navigate to a specific learning path in the content window
   * @param id
   */
  eDispatch.navigateToLearningPath = function (id) {
    dispatchParent(types.NAVIGATE_LEARNING_PATH, {
      id: id
    });
  };

}(window.eDispatch = window.eDispatch || {}, window.eType));
