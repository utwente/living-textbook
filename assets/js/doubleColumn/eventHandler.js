require('../event/eventTypes');

/**
 * This module handles events from the wiki of the application
 */
(function (eHandler, types) {

  /**
   * Register message handler for communication
   */
  window.addEventListener('message', function (event) {
    var type = event.data.type;
    var data = event.data.payload;

    switch (type) {
      case types.PAGE_LOAD:
        onPageLoad(data);
        break;
      case types.TOGGLE_CONCEPT_BROWSER:
        onToggleConceptBrowser();
        break;
    }
  });

  /**
   * Update the page url
   * @param data
   */
  function onPageLoad(data) {
    // Calculate new url
    var newUrl = '/page' + data.url;

    // Do not update if already on the page
    if (window.location.pathname === newUrl) return;

    // Update or replace the state
    if (window.location.pathname === '/') {
      window.history.replaceState(null, null, newUrl);
    } else {
      window.history.pushState(null, null, newUrl);
    }
  }

  /**
   * Toggle concept browser state
   */
  function onToggleConceptBrowser() {
    dw.toggleWindow();
  }

}(window.eHandler = window.eHandler || {}, window.eType));
