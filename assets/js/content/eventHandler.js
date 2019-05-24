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
      case types.OPENED_CONCEPT_BROWSER:
        onChangedConceptBrowser(true);
        break;
      case types.CLOSED_CONCEPT_BROWSER:
        onChangedConceptBrowser(false);
        break;
      case types.OPENED_LEARNING_PATH_BROWSER:
        onChangeLearningPathBrowser(true);
        break;
      case types.CLOSED_LEARNING_PATH_BROWSER:
        onChangeLearningPathBrowser(false);
        break;
      case types.CHECK_DOUBLE_COLUMN:
      case types.PAGE_LOADED:
      case types.PAGE_SUBMIT:
      case types.OPEN_CONCEPT_BROWSER:
      case types.CLOSE_CONCEPT_BROWSER:
      case types.CONCEPT_SELECTED:
      case types.SHOW_CONCEPT:
      case types.OPEN_LEARNING_PATH_BROWSER:
      case types.CLOSE_LEARNING_PATH_BROWSER:
      case types.NAVIGATE_LEARNING_PATH:
      case types.OPEN_CONCEPT_FROM_LEARNING_PATH:
      case types.TRACKING_CONSENT:
        // These are not handled from here, but do trigger the handler
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
    window.btoggles.loadState(data.browserStates);
  }

  /**
   * Handle concept browser change
   * @param isOpened
   */
  function onChangedConceptBrowser(isOpened) {
    window.btoggles.loadConceptState(isOpened);
  }

  /**
   * Handle learning path browser change
   * @param isOpened
   */
  function onChangeLearningPathBrowser(isOpened) {
    window.btoggles.loadLearningPathState(isOpened);
  }

  /**
   * Handle tracking consent update event
   */
  function onTrackingConsentUpdated(data) {
    window.dispatchEvent(new CustomEvent('tracking_consent', {detail: data.agree}));
  }

}(window.eHandler = window.eHandler || {}, window.eType));
