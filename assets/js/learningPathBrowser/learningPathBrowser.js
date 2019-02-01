require('../../css/learningPathBrowser/learningPathBrowser.scss');

// Import routing
import Routing from 'fos-routing';

/**
 * Register lpb namespace in the browser, for usage of the learning path browser object
 *
 * $ has been defined globally in the app.js
 */
(function (lpb, dispatcher, $, d3, undefined) {

  /******************************************************************************************************
   * Configuration variables
   *****************************************************************************************************/

  // General settings
  lpb.drawGrid = false;

  /******************************************************************************************************
   * Internal constants
   *****************************************************************************************************/

  const openSize = '70%';
  const closedSize = '100%';
  const $doubleColumn = $('#double-column-container');
  const $bottomRow = $('#bottom-row');
  const $loader = $('#bottom-container-loader');
  const $closeButton = $('#learning-path-close-button');

  const content = document.getElementById('bottom-container-content');
  const $titleLink = $('#learning-path-title-link');
  const $question = $('#learning-path-question');
  const canvas = document.getElementById('learning-path-canvas');

  /******************************************************************************************************
   * Internal variables
   *****************************************************************************************************/

  let lpbCanvas, context, canvasWidth, canvasHeight;
  let elements = [];
  let dx = 0, cx = 0;
  let elementPadding, elementLine, elementRadius, elementSpacing, totalElementLength;

  /******************************************************************************************************
   * External browser control
   *****************************************************************************************************/

  /**
   * Handler to open the learning path browser
   */
  lpb.openBrowser = function (id) {
    // Clear content and show
    updateContents();
    $loader.show();

    // CSS animations are used to make it fluent
    $doubleColumn.css('height', openSize);
    $bottomRow.css('top', openSize);
    triggerResize();

    // Load the data
    loadData(id);
  };

  /**
   * Handler to close the learning path browser
   */
  lpb.closeBrowser = function () {
    // CSS animations are used to make it fluent
    $doubleColumn.css('height', closedSize);
    $bottomRow.css('top', closedSize);
    triggerResize();
  };

  /******************************************************************************************************
   * Internal helper function
   *****************************************************************************************************/

  /**
   * Load the data for the given learning path
   */
  function loadData(id) {
    $.get({
      url: Routing.generate('app_learningpath_data', {_studyArea: _studyArea, learningPath: id}),
      dataType: 'json'
    }).done(function (data) {
      updateContents(data);
      drawGraph();
      $loader.hide();
    }).fail(function (error) {
      throw error;
    });
  }

  /**
   * Update the data contents
   * @param data
   */
  function updateContents(data) {
    const dataSet = typeof data !== 'undefined';

    $titleLink.html(data ? data.name : '');
    $question.html(data ? data.question : '');

    if (dataSet) {
      $titleLink.data('learning-path-id', data.id);
      elements = data.elements;
    } else {
      $titleLink.removeData('learning-path-id');
      elements = [];
    }

    dx = 0;
    totalElementLength = (2 * elementPadding)
        + (elements.length * elementRadius * 2)
        + ((elements.length - 1) * (elementSpacing - (elementRadius * 2)));
  }

  /**
   * Function to trigger the resize event after the animation has finished
   */
  function triggerResize() {
    $doubleColumn.one("webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend", function () {
      setTimeout(function () {
        $(window).trigger('resize');
      }, 100);
    });
  }

  /**
   * Function to open the learning path in the content pane
   */
  function openLearningPath() {
    const id = $titleLink.data('learning-path-id');
    if (id === undefined) {
      return;
    }

    dispatcher.navigateToLearningPath(id);
  }

  /******************************************************************************************************
   * Draw functions
   *****************************************************************************************************/

  function drawGraph() {
    // Save state
    context.save();

    // Clear canvas
    context.clearRect(0, 0, canvasWidth, canvasHeight);
    cx = elementPadding;

    // Draw grid lines
    if (lpb.drawGrid) {
      context.beginPath();
      for (let i = 0; i <= canvasWidth; i += 100) {
        context.moveTo(i, 0);
        context.lineTo(i, canvasHeight);
      }
      for (let j = 0; j <= canvasHeight; j += 100) {
        context.moveTo(0, j);
        context.lineTo(canvasWidth, j);
      }
      context.strokeStyle = 'black';
      context.stroke();
    }

    //////////////////////
    // NORMAL           //
    //////////////////////

    context.beginPath();
    elements.map(drawElement);
    context.fill();
    context.stroke();
  }

  /**
   * Draw the node
   * @param node
   */
  function drawElement(node) {
    let x = dx + cx + elementRadius;
    context.moveTo(x, elementLine);
    context.arc(x, elementLine, elementRadius, 0, 2 * Math.PI);

    cx += elementSpacing;
  }


  /******************************************************************************************************
   * Register event handlers
   *****************************************************************************************************/

  $closeButton.click(() => lpb.closeBrowser());
  $titleLink.click(() => openLearningPath());

  /******************************************************************************************************
   * Initialize canvas
   *****************************************************************************************************/

      // Setup sizes
  const contentRect = content.getBoundingClientRect();
  const canvasRect = canvas.getBoundingClientRect();
  canvasWidth = canvasRect.width;
  canvasHeight = contentRect.height - (canvasRect.y - contentRect.y) - 5;
  canvas.height = canvasHeight;
  canvas.width = canvasWidth;

  // Determine element sizes
  elementPadding = canvasHeight / 10;
  elementLine = canvasHeight / 2;
  elementRadius = canvasHeight / 3;
  elementSpacing = elementRadius * 4;

  // Load canvas and processing
  context = canvas.getContext('2d');
  lpbCanvas = d3.select(canvas);
  lpbCanvas
      .call(d3.drag().on('drag', dragged))
      .call(drawGraph);

  /**
   * Drag event handler, which updates the dx being used in the render loop
   */
  function dragged() {
    if (totalElementLength > canvasWidth) {
      dx = Math.max(-totalElementLength + canvasWidth, Math.min(0, dx + d3.event.dx));
    } else {
      dx = 0;
    }
    drawGraph();
  }

}(window.lpb = window.lpb || {}, eDispatch, jQuery, d3));
