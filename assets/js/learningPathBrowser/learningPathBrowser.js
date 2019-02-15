require('../conceptBrowser/configuration.js');
require('../../css/learningPathBrowser/learningPathBrowser.scss');

// Import routing
import Routing from 'fos-routing';

/**
 * Register lpb namespace in the browser, for usage of the learning path browser object
 *
 * $ has been defined globally in the app.js
 */
(function (lpb, bConfig, dispatcher, $, d3, undefined) {

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
  const $tooltipHandle = $('#learning-path-tooltip-handle');

  const content = document.getElementById('bottom-container-content');
  const $titleLink = $('#learning-path-title-link');
  const $question = $('#learning-path-question');
  const canvas = document.getElementById('learning-path-canvas');

  const contextMenuContainer = '#learning-path-canvas-div';

  const lineScale = 2, textScale = 2;
  const fontSize = bConfig.defaultNodeLabelFontSize * textScale;

  /******************************************************************************************************
   * Internal variables
   *****************************************************************************************************/

  let lpbCanvas, context, canvasWidth, canvasHeight;
  let elements = [], pathId = -1;
  let dx = 0;
  let elementPadding, elementLine, elementRadius, pathDescriptionRadius, elementSpacing, totalElementLength;
  let clickSend = false;
  let contextMenuElement = null, tooltipElement = null;

  /******************************************************************************************************
   * Data types
   * Required for JS-hinting
   *****************************************************************************************************/
  let Types = {};
  Types.ElementType = {
    x: 0,
    y: 0,
    label: '',
    expandedLabel: [],
    expandedLabelStart: 0,
    color: 0,
    highlighted: false,
  };

  /******************************************************************************************************
   * External browser control
   *****************************************************************************************************/

  /**
   * Handler to open the learning path browser
   */
  lpb.openBrowser = function (id) {
    // Clear content and show
    doLoadData();
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
      doLoadData(data);
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
  function doLoadData(data) {
    const dataSet = typeof data !== 'undefined';

    pathId = data ? data.id : -1;
    $titleLink.html(data ? data.name : '');
    $question.html(data ? data.question : '');

    if (dataSet) {
      elements = data.elements;
    } else {
      elements = [];
    }

    // Enrich element data
    doEnrichElementData();

    // Set the element locations
    updateElementLocations();

    // (Re)set draw parameters
    dx = 0;
    totalElementLength = (2 * elementPadding)
        + (elements.length * elementRadius * 2)
        + ((elements.length - 1) * (elementSpacing - (elementRadius * 2)));
  }

  /**
   * Enrich the element data
   */
  function doEnrichElementData() {
    let counter = 0;
    elements.map(function (element) {
      // Load color
      loadElementColor(element);

      // Load label data
      element.label = element.concept.name;
      bConfig.updateLabel(element, textScale);

      // Add number (this is done after label splitting to prevent the number being put on a separate line)
      element.label = ++counter + ". " + element.label;
      if (element.expandedLabel.length > 0) {
        element.expandedLabel[0] = counter + ". " + element.expandedLabel[0];
      }
    });
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
    if (pathId === -1) {
      return;
    }

    dispatcher.navigateToLearningPath(pathId);
  }

  /******************************************************************************************************
   * Draw functions
   *****************************************************************************************************/

  function drawGraph() {
    // Save state
    context.save();

    // Clear canvas
    context.clearRect(0, 0, canvasWidth, canvasHeight);


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

    // Draw links
    if (elements.length >= 2) {
      let first = elements[0];
      let last = elements[elements.length - 1];
      context.beginPath();
      context.lineWidth = bConfig.linkLineWidth * 3;
      context.strokeStyle = bConfig.defaultLinkStrokeStyle;
      context.moveTo(first.x, first.y);
      context.lineTo(last.x, last.y);
      context.stroke();
    }

    // Draw normal elements
    for (let nn = -1; nn <= 4; nn++) {
      bConfig.applyStyle(nn);
      context.beginPath();
      context.lineWidth = bConfig.nodeLineWidth * lineScale;
      context.fillStyle = bConfig.defaultNodeFillStyle;
      context.strokeStyle = bConfig.defaultNodeStrokeStyle;
      elements.filter(filterElementOnColor(nn)).map(drawNormalElement);
      context.fill();
      context.stroke();
    }

    //////////////////////
    // DESCRIPTIONS     //
    //////////////////////
    bConfig.applyStyle(-1);
    context.beginPath();
    context.fillStyle = bConfig.defaultLinkStrokeStyle;
    elements.map(drawPathDescription);
    context.fill();

    //////////////////////
    // HIGHLIGHT        //
    //////////////////////

    // Draw highlighted elements
    for (let hn = -1; hn <= 4; hn++) {
      bConfig.applyStyle(hn);
      context.beginPath();
      context.lineWidth = bConfig.nodeLineWidth * lineScale;
      context.fillStyle = bConfig.highlightedNodeFillStyle;
      context.strokeStyle = bConfig.highlightedNodeStrokeStyle;
      elements.filter(filterElementOnColor(hn)).map(drawHighlightedElement);
      context.fill();
      context.stroke();
    }

    //////////////////////
    // ARROWS           //
    //////////////////////

    // Draw path arrows
    context.fillStyle = bConfig.defaultLinkStrokeStyle;
    elements.slice(1).map(drawElementArrow);

    //////////////////////
    // LABELS           //
    //////////////////////

    // Set this lower to prevent horns on M/W letters
    // https://github.com/CreateJS/EaselJS/issues/781
    context.miterLimit = 2.5;

    // Draw element labels
    context.fillStyle = bConfig.defaultNodeLabelColor;
    context.textBaseline = 'middle';
    context.textAlign = 'center';
    context.lineWidth = bConfig.activeNodeLabelLineWidth * textScale;
    context.strokeStyle = bConfig.activeNodeLabelStrokeStyle;
    elements.map(drawElementText);

    // Draw path description question marks
    context.fillStyle = bConfig.whiteNodeLabelColor;
    context.font = fontSize + 'px "DroidSans, arial, serif"';
    elements.map(function (element) {
      if (typeof element.description === "undefined") return;

      context.fillText("?", pathDescriptionX(element), element.y);
    });

    // Restore state
    context.restore();
  }

  /**
   * Draw an element if it's normal
   * @param element
   */
  function drawNormalElement(element) {
    if (element.highlighted) {
      return;
    }
    drawElement(element);
  }

  /**
   * Draw an element if it's highlighted
   * @param element
   */
  function drawHighlightedElement(element) {
    if (!element.highlighted) {
      return;
    }
    drawElement(element);
  }

  /**
   * Draw the element
   * @param element
   */
  function drawElement(element) {
    context.moveTo(element.x + elementRadius, element.y);
    context.arc(element.x, element.y, elementRadius, 0, 2 * Math.PI);
  }

  /**
   * Draw the path description
   * @param element
   */
  function drawPathDescription(element) {
    if (typeof element.description === "undefined") return;

    let x = pathDescriptionX(element);
    context.moveTo(x, element.y);
    context.arc(x, element.y, pathDescriptionRadius, 0, 2 * Math.PI);
  }

  /**
   * Draw the element arrow
   * @param element
   */
  function drawElementArrow(element) {
    // Draw the triangle
    context.save();
    context.beginPath();
    context.translate(element.x, element.y);
    context.moveTo(-elementRadius + 1, 0);
    context.lineTo(-16 - elementRadius, 8);
    context.lineTo(-16 - elementRadius, -8);
    context.closePath();
    context.restore();
    context.fill();
  }

  /**
   * Draw the element text
   * @param element
   */
  function drawElementText(element) {
    // Set font accordingly
    context.font = fontSize + 'px "DroidSans, arial, serif"';
    if (element.highlighted) {
      context.font = 'bold ' + context.font;
    }

    // Draw the actual text (which can be multiple lines)
    let yStart = element.y - element.expandedLabelStart;
    element.expandedLabel.map(function (line) {
      if (element.highlighted) context.strokeText(line, element.x, yStart);
      context.fillText(line, element.x, yStart);
      yStart += fontSize;
    });
  }

  /******************************************************************************************************
   * Color functions
   *****************************************************************************************************/

  /**
   * Function to filter elements on a given color index
   * @param color
   * @returns {Function}
   */
  function filterElementOnColor(color) {
    return function (element) {
      return element.color === color;
    };
  }

  /**
   * Color the given element and save in the local storage
   * @param element
   * @param color
   */
  function colorElement(element, color) {
    element.color = color;

    if (typeof (Storage) !== 'undefined') {
      const colorsJson = localStorage.getItem(colorStoragePath());
      let colorsObject = colorsJson !== null ? JSON.parse(colorsJson) : {};
      colorsObject['e.' + element.id] = color;
      localStorage.setItem(colorStoragePath(), JSON.stringify(colorsObject));
    }
  }

  /**
   * Resets all element colors and clears the local storage
   */
  function resetElementColors() {
    elements.map(function (element) {
      element.color = 0;
    });

    // Clear local storage
    if (typeof (Storage) !== 'undefined') {
      localStorage.removeItem(colorStoragePath());
    }
  }

  /**
   * Load element colors from the local storage
   */
  function loadElementColor(element) {
    element.color = 0;
    if (typeof (Storage) !== 'undefined') {
      const colorsJson = localStorage.getItem(colorStoragePath());
      if (colorsJson !== null) {
        const elementColor = JSON.parse(colorsJson)['e.' + element.id];
        if (typeof elementColor !== 'undefined') {
          element.color = parseInt(elementColor);
        }
      }
    }
  }

  /**
   * Local storage path
   * @returns {string}
   */
  function colorStoragePath() {
    return 'lpbElementColors.' + pathId;
  }

  /******************************************************************************************************
   * Element functions
   *****************************************************************************************************/

  /**
   * Retrieve the actual event location
   * @returns {{ox: number, oy: number}}
   */
  function getEventLocation() {
    const rect = canvas.getBoundingClientRect();
    return {
      ox: d3.event.pageX - rect.left - canvas.clientLeft - window.pageXOffset,
      oy: d3.event.pageY - rect.top - canvas.clientTop - window.pageYOffset
    }
  }

  /**
   * Find an element base on the event location
   * @returns {*}
   */
  function findElement() {
    // Retrieve the actual location
    const loc = getEventLocation();

    // Find the element
    let i,
        dx,
        dy,
        d2,
        element,
        closest,
        searchRadius = elementRadius * elementRadius;

    for (i = 0; i < elements.length; ++i) {
      element = elements[i];
      dx = loc.ox - element.x;
      dy = loc.oy - element.y;
      d2 = dx * dx + dy * dy;
      if (d2 < searchRadius) {
        closest = element;
        searchRadius = d2;
      }
    }

    return closest;
  }

  /**
   * Find an description based on the event location
   * @returns {*}
   */
  function findDescription() {
    // Retrieve the actual location
    const loc = getEventLocation();

    // Find the description
    let i,
        dx,
        dy,
        d2,
        element,
        closest,
        searchRadius = pathDescriptionRadius * pathDescriptionRadius;

    for (i = 0; i < elements.length; ++i) {
      element = elements[i];
      if (typeof element.description === "undefined") continue;

      dx = loc.ox - pathDescriptionX(element);
      dy = loc.oy - element.y;
      d2 = dx * dx + dy * dy;
      if (d2 < searchRadius) {
        closest = element;
        searchRadius = d2;
      }
    }

    return closest;
  }

  /**
   * Updates the element locations
   */
  function updateElementLocations() {
    let cx = elementPadding;
    elements.map(function (element) {
      element.x = dx + cx + elementRadius;
      element.y = elementLine;
      cx += elementSpacing;
    });
  }

  /**
   * Path description x location
   * @param element
   * @returns {*}
   */
  function pathDescriptionX(element) {
    return element.x + (elementSpacing / 2);
  }

  /******************************************************************************************************
   * Event handlers
   *****************************************************************************************************/

  /**
   * Drag event handler, which updates the dx being used in the render loop
   */
  function onDrag() {
    if (totalElementLength > canvasWidth) {
      dx = Math.max(-totalElementLength + canvasWidth, Math.min(0, dx + d3.event.dx));
    } else {
      dx = 0;
    }
    updateElementLocations();
    drawGraph();
  }

  /**
   * Mouse move event handler, which sets elements as highlighted when hovered
   */
  function onMouseMove() {
    let element = findElement();
    if (element !== undefined) {
      // Do not set again
      if (element.highlighted) {
        return;
      }

      // Set element as highlighted
      elements.map(function (element) {
        element.highlighted = false;
      });
      element.highlighted = true;
      drawGraph();
    }

    let description = findDescription();
    if (description === undefined) {
      if (tooltipElement !== null) {
        tooltipElement = null;
        $tooltipHandle.tooltip('hide');
      }
      return;
    }

    if (tooltipElement === null) {
      tooltipElement = description;
      $tooltipHandle.css({
        top: (description.y - pathDescriptionRadius) + "px",
        left: pathDescriptionX(description)
      });
      $tooltipHandle.tooltip('show');
    }
  }

  /**
   * Mouse left click handler, opens the clicked element in the reader and browser
   */
  function onClick() {
    let element = findElement();
    if (element === undefined) {
      return;
    }

    if (!clickSend) {
      clickSend = true;
      dispatcher.openConceptFromLearningPath(element.concept.id);
      setTimeout(function () {
        clickSend = false;
      }, 250);
    }
  }

  /**
   * Mouse right click handler, opens the context menu with color options
   */
  function onRightClick() {
    d3.event.preventDefault();

    let element = findElement();
    contextMenuElement = element === undefined ? null : element;
    $(contextMenuContainer).contextMenu({x: d3.event.clientX, y: d3.event.clientY});
  }

  /******************************************************************************************************
   * Register event handlers
   *****************************************************************************************************/

  $closeButton.click(() => lpb.closeBrowser());
  $titleLink.click(() => openLearningPath());

  $tooltipHandle.tooltip({
    title: function () {
      return tooltipElement.description;
    },
    placement: "top",
    trigger: "manual"
  });

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
  pathDescriptionRadius = elementRadius / 5;
  elementSpacing = elementRadius * 4;

  /******************************************************************************************************
   * Initialize processing
   *****************************************************************************************************/

  // Load canvas and processing
  context = canvas.getContext('2d');
  lpbCanvas = d3.select(canvas);
  lpbCanvas
      .call(d3.drag().on('drag', onDrag))
      .call(drawGraph)
      .on('mousemove', onMouseMove)
      .on('click', onClick)
      .on('contextmenu', onRightClick);

  // Define context menu
  //noinspection JSUnusedGlobalSymbols
  $.contextMenu({
    selector: contextMenuContainer,
    trigger: 'none',
    build: function () {
      //noinspection JSUnusedGlobalSymbols
      return {
        callback: function (key) {
          if (key === 'quit') return;
          if (key.startsWith('style')) colorElement(contextMenuElement, parseInt(key.substr(6)));
          if (key === 'reset') resetElementColors();
          if (key === 'center') cb.centerView();
          drawGraph();
        },
        items: getContextMenuItems()
      };
    }
  });

  /**
   * Context menu builder
   * @returns {*}
   */
  function getContextMenuItems() {
    if (contextMenuElement === null) {
      // Global
      return {
        'reset': {name: 'Reset node colors', icon: 'fa-undo'},
        'sep1': '---------',
        'quit': {name: 'Close', icon: 'fa-times'}
      };
    } else {
      // Element
      let elementItems = {};

      // Color data
      if (!contextMenuElement.empty) {
        const colorData = {
          'styles': {
            name: 'Change color',
            icon: 'fa-paint-brush',
            items: {
              'style-1': {name: 'Red', icon: contextMenuElement.color === 1 ? 'fa-check' : ''},
              'style-2': {name: 'Green', icon: contextMenuElement.color === 2 ? 'fa-check' : ''},
              'style-3': {name: 'Blue', icon: contextMenuElement.color === 3 ? 'fa-check' : ''},
              'style-4': {name: 'Orange', icon: contextMenuElement.color === 4 ? 'fa-check' : ''},
              'sep1': '---------',
              'style-0': {name: 'Default', icon: contextMenuElement.color === 0 ? 'fa-check' : 'fa-undo'}
            }
          },
          'sep2': '---------',
        };

        elementItems = $.extend(elementItems, colorData);
      }

      // Merge with default data
      const defaultData = {
        'quit': {name: 'Close', icon: 'fa-times'}
      };

      return $.extend(elementItems, defaultData);
    }
  }

}(window.lpb = window.lpb || {}, bConfig, eDispatch, jQuery, d3));
