require('../event/eventTypes');

/**
 * This module handles events from the wiki of the application
 */
(function (eHandler, types) {

  /**
   * Register message handler for communication
   */
  window.addEventListener('message', function (event) {
    // @todo remove
    console.info('Event received', event.data);

    var type = event.data.type;
    var data = event.data.payload;

    switch (type) {
      case types.PAGE_LOAD:
        onPageLoad(data);
        break;
      case types.PAGE_LOADED:
        onPageLoaded(data);
        break;
      case types.TOGGLE_CONCEPT_BROWSER:
        onToggleConceptBrowser();
        break;
    }
  });

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

    // By removing the iframe before changing it's url, we do not influence the browser history stack
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
    var newUrl = '/page' + data.url;
    var state = {
      currentUrl: $('#data-iframe').attr('src'),
      currentTitle: document.title
    };

    // Update or replace the state
    if (window.location.pathname === newUrl || window.location.pathname === '/') {
      window.history.replaceState(state, '', newUrl);
    } else {
      window.history.pushState(state, '', newUrl);
    }

    // Set the title
    document.title = data.title;
  }

  /**
   * Toggle concept browser state
   */
  function onToggleConceptBrowser() {
    dw.toggleWindow();
  }

}(window.eHandler = window.eHandler || {}, window.eType));
