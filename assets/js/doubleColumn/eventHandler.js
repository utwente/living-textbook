require('../event/eventTypes');

// Import routing
import Routing from 'fos-routing';

/**
 * This module handles events from the content of the application
 */
(function (eHandler, types, tracker, $) {
  // Initialize current state
  eHandler.previousUrl = null;
  eHandler.currentUrl = $('#left-container-content').data('current-url');

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
      case types.BLANK_PAGE_LOAD:
        onBlankPageLoad(data);
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
      case types.CREATE_INSTANCE:
        onCreateInstance(data);
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
    // Forward to page load, without tracking to prevent double events
    onPageLoadInternal({url: Routing.generate('app_concept_show', {_studyArea: _studyArea, concept: data.id})});
  }

  function onBlankPageLoad(data) {
    tracker.trackLinkClick(data.url, true);
  }

  /**
   * Update the iframe src url
   * @param data
   */
  function onPageLoadInternal(data) {
    // Check options
    data.options = data.options || {};
    if (data.options.topLevel) {
      window.location.href = data.url;
      return;
    }

    dw.iframeLoad(data.url);
  }

  /**
   * Update the iframe src url, and track click
   * @param data
   */
  function onPageLoad(data) {
    tracker.trackLinkClick(data.url);
    onPageLoadInternal(data);
  }

  eHandler.onPageLoad = onPageLoad;

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
    tracker.trackPageload(firstRequest);

    // If it was the first request, check whether a direct open was requested
    if (firstRequest && _openMapOnLoad) {
      // Ensure to reset the state to prevent new triggers
      _openMapOnLoad = false;

      // Either open just the map, or directly focus on the selected node
      if (data.concept_id) {
        dw.openWindow(function () {
          cb.moveToConceptById(data.concept_id);
        });
      } else {
        dw.openWindow(data.concept_id);
      }
    }
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
    onPageLoad({
      url: Routing.generate('app_learningpath_show', {
        _studyArea: _studyArea,
        learningPath: data.id
      })
    });
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
      eDispatch.trackingConsentUpdated(tracker.getTrackingConsent());
      return;
    }

    tracker.saveTrackingConsent(data.agree);
  }

  /**
   * Handle create instance event
   * @param data
   */
  function onCreateInstance(data) {
    // Forward to page load
    onPageLoad({
      url: Routing.generate('app_concept_instantiate', {
        _studyArea: _studyArea,
        concept: data.id
      })
    });
  }

}(window.eHandler = window.eHandler || {}, window.eType, window.tracker, $));
