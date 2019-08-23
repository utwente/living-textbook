/**
 * This module defines several event constants
 */
(function (eType) {
  // Double column state
  eType.CHECK_DOUBLE_COLUMN = 'check_double_column';
  eType.CHECK_DOUBLE_COLUMN_RETURN = 'check_double_column_return';

  // Page actions/states
  eType.PAGE_LOAD = 'page_load';
  eType.PAGE_LOADED = 'page_loaded';
  eType.PAGE_SUBMIT = 'page_submit';
  eType.BLANK_PAGE_LOAD = 'blank_page_load';

  // Concept browser actions/states
  eType.OPEN_CONCEPT_BROWSER = 'open_concept_browser';
  eType.OPENED_CONCEPT_BROWSER = 'opened_concept_browser';
  eType.CLOSE_CONCEPT_BROWSER = 'close_concept_browser';
  eType.CLOSED_CONCEPT_BROWSER = 'closed_concept_browser';
  eType.CONCEPT_SELECTED = 'concept_selected';
  eType.SHOW_CONCEPT = 'show_concept';

  // Toggle learning path browser
  eType.OPEN_LEARNING_PATH_BROWSER = 'open_learning_path_browser';
  eType.OPENED_LEARNING_PATH_BROWSER = 'opened_learning_path_browser';
  eType.CLOSE_LEARNING_PATH_BROWSER = 'close_learning_path_browser';
  eType.CLOSED_LEARNING_PATH_BROWSER = 'closed_learning_path_browser';
  eType.NAVIGATE_LEARNING_PATH = 'navigate_learning_path';
  eType.OPEN_CONCEPT_FROM_LEARNING_PATH = 'open_concept_from_learning_path';

  // Tracking consent
  eType.TRACKING_CONSENT = 'tracking_consent';
  eType.TRACKING_CONSENT_UPDATED = 'tracking_consent_updated';
}(window.eType = window.eType || {}));
