require("../css/doubleColumn/doubleColumn.scss");

// Create global d3
const d3 = require("d3");
global.d3 = d3;

// Import routing
import Routing from "fos-routing";
// Create global tracker
import Tracker from "./tracking/tracker";
import ConceptBrowser from "./conceptBrowser/ConceptBrowser";

global.tracker = new Tracker(_studyArea, _trackUser);

/**
 * Load the modules required for the double column page in order to function properly
 *
 * $ has been defined globally in the app.js
 */
$(function () {
  require("./doubleColumn/eventHandler");
  require("./doubleColumn/eventDispatcher");
  require("./doubleColumn/draggebleWindow");
  require("./search/conceptSearch");
  require("./learningPathBrowser/learningPathBrowser");

  global.cb = new ConceptBrowser("graph_container_canvas");

  // if the study area is flagged as isDotron (true), no need to init the concept browser data.
  if (_isDotronStudyArea) {
    console.debug("_isDotronStudyArea", _isDotronStudyArea);
    console.debug("concept browser data not initialized");
  } else {
    $.get({
      url: Routing.generate("app_data_export", { _studyArea: _studyArea }),
      dataType: "json",
    })
      .done(function (data) {
        conceptSearch.createSearch($("#search"), data);
        cb.init(data);
      })
      .fail(function (error) {
        console.error(error);
      });
  }

  // Load tooltips
  $('[data-toggle="tooltip"]').tooltip({ trigger: "hover" });

  // Load pop state handler
  window.addEventListener("popstate", function (event) {
    // Check for state object
    if (!event.state) return;

    // Restore document title and page
    eHandler.onPageLoad({ url: event.state.currentUrl });
    document.title = event.state.currentTitle;

    return false;
  });

  // Load refresh behavior
  $("#refresh-button").on("click", function () {
    var $button = $(this);
    var $icon = $button.find("i");
    $icon
      .addClass("fa-spin")
      .addClass("fa-circle-o-notch")
      .removeClass("fa-refresh");
    $button.attr("disabled", "disabled");
    $button.tooltip("hide");

    $.get({
      url: Routing.generate("app_data_export", { _studyArea: _studyArea }),
      dataType: "json",
    })
      .done(function (data) {
        // conceptSearch.updateData($('#search'), data);
        cb.update(data);
      })
      .fail(function (error) {
        console.error(error);
      })
      .always(function () {
        $icon
          .removeClass("fa-spin")
          .removeClass("fa-circle-o-notch")
          .addClass("fa-refresh");
        $button.removeAttr("disabled");
      });
  });
});
