/**
 * This module defines several event constants
 */
(function (eType) {
  // Page actions/states
  eType.PAGE_LOAD = 'page_load';
  eType.PAGE_LOADED = 'page_loaded';
  eType.PAGE_SUBMIT = 'page_submit';

  // Concept browser actions/states
  eType.TOGGLE_CONCEPT_BROWSER = 'open_concept_browser';
  eType.CONCEPT_SELECTED = 'concept_selected';
  eType.SHOW_CONCEPT = 'show_concept';
}(window.eType = window.eType || {}));
