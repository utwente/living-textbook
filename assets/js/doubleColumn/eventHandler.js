require('../event/eventTypes');

// Import routing
import Routing from 'fos-routing';

/**
 * This module handles events from the content of the application
 */
(function (eHandler, types, $) {
  const uuidV1 = require('uuid/v1');
  let sessionConsent = null;

  // Initialize current state
  eHandler.sessionId = uuidV1();
  eHandler.previousUrl = null;
  eHandler.currentUrl = $('#data-iframe').attr('src');

  /**
   * Register message handler for communication
   */
  window.addEventListener('message', function (event) {
    console.info('Event received in double column', event.data);

    let type = event.data.type;
    let data = event.data.payload;

    switch (type) {
      case types.CHECK_DOUBLE_COLUMN:
        onCheckDoubleColumn(data);
        break;
      case types.PAGE_LOAD:
        onPageLoad(data);
        break;
      case types.PAGE_LOADED:
        onPageLoaded(data);
        break;
      case types.PAGE_SUBMIT:
        onPageSubmit();
        break;
      case types.OPEN_CONCEPT_BROWSER:
        onOpenConceptBrowser();
        break;
      case types.CLOSE_CONCEPT_BROWSER:
        onCloseConceptBrowser();
        break;
      case types.CONCEPT_SELECTED:
        onConceptSelected(data);
        break;
      case types.SHOW_CONCEPT:
        onShowConcept(data);
        break;
      case types.OPEN_LEARNING_PATH_BROWSER:
        onOpenLearningPath(data);
        break;
      case types.CLOSE_LEARNING_PATH_BROWSER:
        onCloseLearningPath(data);
        break;
      case types.NAVIGATE_LEARNING_PATH:
        onNavigateLearningPath(data);
        break;
      case types.OPEN_CONCEPT_FROM_LEARNING_PATH:
        onOpenConceptFromLearningPath(data);
        break;
      case types.TRACKING_CONSENT:
        onTrackingConsent(data);
        break;
      default:
        console.warn('Unknown event!', type);
    }
  });

  /**
   * Return the checksum to indicate it's here
   * @param data
   */
  function onCheckDoubleColumn(data) {
    eDispatch.returnDoubleColumnChecksum(data.checksum);
  }

  /**
   * Load the concept page
   * @param data
   */
  function onConceptSelected(data) {
    // Forward to page load
    onPageLoad({url: Routing.generate('app_concept_show', {_studyArea: _studyArea, concept: data.id})})
  }

  /**
   * Update the iframe src url
   * @param data
   */
  function onPageLoad(data) {
    // Check options
    data.options = data.options || {};
    if (data.options.topLevel) {
      window.location.href = data.url;
      return;
    }

    dw.iframeLoad(data.url);
  }

  eHandler.onPageLoad = function (data) {
    onPageLoad(data);
  };

  /**
   * Update the page url
   * @param data
   */
  function onPageLoaded(data) {
    // Calculate new url
    const newUrl = '/page' + data.url;

    // Update the page state
    const firstRequest = eHandler.previousUrl == null;
    eHandler.previousUrl = eHandler.currentUrl;
    eHandler.currentUrl = data.url;
    const state = {
      currentUrl: eHandler.currentUrl,
      currentTitle: document.title
    };

    // Update or replace the state
    if (window.location.pathname === newUrl || window.location.pathname === '/' || window.history.state === null) {
      window.history.replaceState(state, '', newUrl);
    } else {
      window.history.pushState(state, '', newUrl);
    }

    // Set the title
    document.title = data.title;

    // Trigger tracking
    trackUser(firstRequest);
  }

  /**
   * Show the loaded screen
   */
  function onPageSubmit() {
    dw.iframeLoader(true);
  }

  /**
   * Open concept browser state
   */
  function onOpenConceptBrowser() {
    dw.openWindow();
  }

  /**
   * Close concept browser state
   */
  function onCloseConceptBrowser() {
    dw.closeWindow();
  }

  /**
   * A concept must be shown
   * @param data
   */
  function onShowConcept(data) {
    if (data.id === -1) {
      dw.openWindow(function () {
        cb.centerView();
      });
    } else {
      dw.openWindow(function () {
        cb.moveToConceptById(data.id, data.nodeOnly);
      });
    }
  }

  /**
   * Opens the learning path
   * @param data
   */
  function onOpenLearningPath(data) {
    lpb.openBrowser(data.id);
  }

  /**
   * Closes the learning path
   * @param data
   */
  function onCloseLearningPath(data) {
    lpb.closeBrowser();
  }

  /**
   * Load the requested learning path url
   * @param data
   */
  function onNavigateLearningPath(data) {
    // Forward to page load
    onPageLoad({url: Routing.generate('app_learningpath_show', {_studyArea: _studyArea, learningPath: data.id})})
  }

  /**
   * Open a concept from the learning path
   * @param data
   */
  function onOpenConceptFromLearningPath(data) {
    onConceptSelected(data);
    if (dw.isOpened()) {
      data.nodeOnly = true;
      onShowConcept(data);
    }
  }

  /**
   * Update tracking consent based on browser setting
   * @param data
   */
  function onTrackingConsent(data) {
    // Check for special null event to only retrieve the status
    if (data.agree === null) {
      // Create event
      eDispatch.trackingConsentUpdated(sessionConsent);
      return;
    }

    saveTrackingConsent(data.agree);
  }

  /**
   * Track the user' page load
   * @param firstRequest
   */
  function trackUser(firstRequest) {
    // Verify whether tracking is enabled
    if (!_trackUser) {
      return;
    }

    // Retrieve consent status, sessionConsent holds the session cached value
    if (sessionConsent === null && typeof (Storage) !== 'undefined') {
      // Retrieve from local storage for this study area
      sessionConsent = localStorage.getItem('tracking-consent.' + _studyArea);
    }

    // Check whether consent is granted
    if (sessionConsent === "false") {
      // Denied, return;
      return;
    }

    if (sessionConsent === null) {
      // Opt-in question not yet asked, ask now. Disabled backdrop and keyboard modal exit.
      let $trackingModal = $('#tracking-modal');
      $trackingModal.modal({
        backdrop: 'static',
        keyboard: false,
      });

      // Register event handlers to modal buttons. Use off to ensure they are not bound multiple times
      // when a previous consent is reset.
      let $agreeButton = $('#tracking-modal-agree');
      $agreeButton.off('click');
      $agreeButton.on('click', function () {
        // Save and send tracking data
        saveTrackingConsent(true);
        $trackingModal.modal('hide');
        sendTrackingData(firstRequest);
      });
      let $disagreeButton = $('#tracking-modal-disagree');
      $disagreeButton.off('click');
      $disagreeButton.on('click', function () {
        // Save only
        saveTrackingConsent(false);
        $trackingModal.modal('hide');
      });

      return;
    }

    // Try to send data
    sendTrackingData(firstRequest);
  }

  /**
   * Save tracking consent
   *
   * @param agree
   */
  function saveTrackingConsent(agree) {
    sessionConsent = agree ? "true" : "false";

    // Store in browser if possible
    if (typeof (Storage) !== 'undefined') {
      localStorage.setItem('tracking-consent.' + _studyArea, sessionConsent);
    }

    // Create event
    eDispatch.trackingConsentUpdated(sessionConsent);
  }

  /**
   * Sends the actual tracking data
   *
   * @param firstRequest
   */
  function sendTrackingData(firstRequest) {
    // Validate consent before sending
    if (sessionConsent !== "true") {
      return;
    }

    // Post page load back to server
    $.ajax({
      type: "POST",
      url: Routing.generate('app_tracking_pageload', {_studyArea: _studyArea}),
      contentType: 'application/json; charset=utf-8',
      dataType: 'json',
      data: JSON.stringify({
        sessionId: eHandler.sessionId,
        timestamp: new Date().toISOString().split('.')[0] + 'Z', // remove milliseconds
        path: eHandler.currentUrl,
        origin: firstRequest ? null : eHandler.previousUrl
      })
    });
  }

}(window.eHandler = window.eHandler || {}, window.eType, $));
