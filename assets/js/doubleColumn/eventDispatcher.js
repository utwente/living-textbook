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
    parent.postMessage({
      type: type,
      payload: data
    }, '*');
  }

  /**
   * Concept selected event
   * @param id
   */
  eDispatch.conceptSelected = function(id){
    dispatchParent(types.CONCEPT_SELECTED, {
      id: id
    });
  };

}(window.eDispatch = window.eDispatch || {}, window.eType));
