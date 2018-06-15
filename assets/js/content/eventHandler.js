import {inDoubleColumnChecksum, setDoubleColumnDetected} from "../content";

require('../event/eventTypes');

/**
 * This module handles events from the index/d3 side of the application
 */
(function (eHandler, types) {

  /**
   * Register message handler for communication
   */
  window.addEventListener('message', function (event) {
    console.info('Event received in content', event.data);

    let type = event.data.type;
    let data = event.data.payload;

    switch (type) {
      case types.CHECK_DOUBLE_COLUMN_RETURN:
        onDoubleColumnReturn(data);
        break;
    }
  });

  /**
   * Handle checksum return
   * @param data
   */
  function onDoubleColumnReturn(data) {
    setDoubleColumnDetected(data.checksum);
  }

}(window.eHandler = window.eHandler || {}, window.eType));
