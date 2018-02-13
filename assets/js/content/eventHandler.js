require('../event/eventTypes');

/**
 * This module handles events from the index/d3 side of the application
 */
(function (eHandler, types) {

  /**
   * Register message handler for communication
   */
  window.addEventListener('message', function (event) {
    var type = event.data.type;
    var data = event.data.payload;

    switch (type) {
    }
  });

}(window.eHandler = window.eHandler || {}, window.eType));
