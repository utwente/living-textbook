import {setDoubleColumnDetected} from "../content";

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
      case types.TRACKING_CONSENT_UPDATED:
        onTrackingConsentUpdated(data);
        break;
      default:
        console.warn('Unknown event!', type);
    }
  });

  /**
   * Handle checksum return
   * @param data
   */
  function onDoubleColumnReturn(data) {
    setDoubleColumnDetected(data.checksum);
  }

  /**
   * Handle tracking consent update event
   */
  function onTrackingConsentUpdated(data) {
    window.dispatchEvent(new CustomEvent('tracking_consent', {detail: data.agree}));
  }

}(window.eHandler = window.eHandler || {}, window.eType));
