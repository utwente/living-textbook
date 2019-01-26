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

  // Concept browser actions/states
  eType.TOGGLE_CONCEPT_BROWSER = 'open_concept_browser';
  eType.CONCEPT_SELECTED = 'concept_selected';
  eType.SHOW_CONCEPT = 'show_concept';

  // Toggle learning path browser
  eType.OPEN_LEARNING_PATH_BROWSER = 'open_learning_path_browser';
  eType.CLOSE_LEARNING_PATH_BROWSER = 'close_learning_path_browser';
}(window.eType = window.eType || {}));
