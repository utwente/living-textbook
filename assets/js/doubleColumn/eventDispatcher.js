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
   * Close learning path browser
   */
  eDispatch.closeLearningPath = function () {
    dispatchParent(types.CLOSE_LEARNING_PATH_BROWSER);
  };

}(window.eDispatch = window.eDispatch || {}, window.eType));
