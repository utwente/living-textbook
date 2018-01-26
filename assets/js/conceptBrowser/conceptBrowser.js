require('../../css/conceptBrowser/conceptBrowser.scss');

/**
 * Register cb (concept browser) namespace for usage
 */
(function (cb, $, d3, undefined) {

  /******************************************************************************************************
   * Configuration variables
   *****************************************************************************************************/

  // Display settings
  cb.mapWidth = 3000;
  cb.mapWidthDragMargin = cb.mapWidth / 30;
  cb.mapHeight = 2000;
  cb.mapHeightDragMargin = cb.mapHeight / 20;

  // Force settings
  cb.collideStrength = 0.6;
  cb.collideIterations = 1;
  cb.linkStrength = 0.9;
  cb.manyBodyStrength = -70;
  cb.manyBodyDistanceMin = 20;
  cb.manyBodyDistanceMax = 1500;
  cb.boundForceStrenght = 80;
  cb.linkNodeRadius = 20;
  cb.nodeRadiusMargin = 10;

  // General settings
  cb.drawGrid = false;
  cb.drawLinkNodes = false;
  cb.zoomExtent = [0.1, 8]; // [min,max] zoom, min is also limited by screen size

  /******************************************************************************************************
   * Style configuration variables
   *****************************************************************************************************/

  // Fixed node layout
  cb.baseNodeRadius = 8; // Node base radius
  cb.extendNodeRatio = 3;
  cb.nodeLineWidth = 2;

  // Fixed link styles
  cb.linkLineWidth = 1;

  // Fixed node label layout
  cb.minCharCount = 12;
  cb.defaultNodeLabelFontSize = 10;
  cb.activeNodeLabelLineWidth = 1.5;
  cb.defaultNodeLabelFont = cb.defaultNodeLabelFontSize + 'px';
  cb.activeNodeLabelFont = 'bold ' + cb.defaultNodeLabelFont;

  // Node styles
  cb.defaultNodeFillStyle = '';
  cb.defaultNodeStrokeStyle = '';
  cb.draggedNodeFillStyle = '';
  cb.draggedNodeStrokeStyle = '';
  cb.fadedNodeFillStyle = '';
  cb.fadedNodeStrokeStyle = '';
  cb.highlightedNodeFillStyle = '';
  cb.highlightedNodeStrokeStyle = '';

  // Link styles
  cb.linkLineWidth = 1;
  cb.defaultLinkStrokeStyle = '#696969';
  cb.draggedLinkStrokeStyle = '#333';
  cb.fadedLinksStrokeStyle = '#E0E0E0';
  cb.highlightedLinkStrokeStyle = cb.draggedLinkStrokeStyle;

  // Node label styles
  cb.defaultNodeLabelColor = '#000';
  cb.activeNodeLabelStrokeStyle = '#fff';

  cb.applyStyle = function (style) {
    switch (style) {
      case 1: {
        // Node styles
        cb.defaultNodeFillStyle = '#de5356';
        cb.defaultNodeStrokeStyle = '#fff';
        cb.draggedNodeFillStyle = cb.defaultNodeFillStyle;
        cb.draggedNodeStrokeStyle = '#ff2340';
        cb.fadedNodeFillStyle = '#bc6d73';
        cb.fadedNodeStrokeStyle = '#fff';
        cb.highlightedNodeFillStyle = cb.draggedNodeFillStyle;
        cb.highlightedNodeStrokeStyle = cb.draggedNodeStrokeStyle;

        break;
      }
      case 2: {
        // Node styles
        cb.defaultNodeFillStyle = '#75de79';
        cb.defaultNodeStrokeStyle = '#fff';
        cb.draggedNodeFillStyle = cb.defaultNodeFillStyle;
        cb.draggedNodeStrokeStyle = '#1ac321';
        cb.fadedNodeFillStyle = '#9ebc9d';
        cb.fadedNodeStrokeStyle = '#fff';
        cb.highlightedNodeFillStyle = cb.draggedNodeFillStyle;
        cb.highlightedNodeStrokeStyle = cb.draggedNodeStrokeStyle;

        break;
      }
      case 3: {
        // Node styles
        cb.defaultNodeFillStyle = '#a4a5fe';
        cb.defaultNodeStrokeStyle = '#fff';
        cb.draggedNodeFillStyle = cb.defaultNodeFillStyle;
        cb.draggedNodeStrokeStyle = '#1513ff';
        cb.fadedNodeFillStyle = '#55557a';
        cb.fadedNodeStrokeStyle = '#fff';
        cb.highlightedNodeFillStyle = cb.draggedNodeFillStyle;
        cb.highlightedNodeStrokeStyle = cb.draggedNodeStrokeStyle;

        break;
      }
      case 4: {
        // Node styles
        cb.defaultNodeFillStyle = '#deaf6c';
        cb.defaultNodeStrokeStyle = '#fff';
        cb.draggedNodeFillStyle = cb.defaultNodeFillStyle;
        cb.draggedNodeStrokeStyle = '#ff5d00';
        cb.fadedNodeFillStyle = '#bcac9b';
        cb.fadedNodeStrokeStyle = cb.fadedNodeFillStyle;
        cb.highlightedNodeFillStyle = cb.draggedNodeFillStyle;
        cb.highlightedNodeStrokeStyle = cb.draggedNodeStrokeStyle;

        break;
      }
      case 0:
        /* falls through */
      default: {
        // Node styles
        cb.defaultNodeFillStyle = '#b1ded2';
        cb.defaultNodeStrokeStyle = '#fff';
        cb.draggedNodeFillStyle = cb.defaultNodeFillStyle;
        cb.draggedNodeStrokeStyle = '#2359ff';
        cb.fadedNodeFillStyle = '#E6ECE4';
        cb.fadedNodeStrokeStyle = '#fff';
        cb.highlightedNodeFillStyle = cb.draggedNodeFillStyle;
        cb.highlightedNodeStrokeStyle = cb.draggedNodeStrokeStyle;

        break;
      }
    }

    if (d3.event && !d3.event.active) cbSimulation.restart();
  };

  /******************************************************************************************************
   * Data types
   * Required for JS-hinting
   *****************************************************************************************************/
  var Types = {};

  Types.JsonType = {
    numberOfLinks: 0,
    id: 0,
    name: '',
    relations: [
      {
        targetId: 0,
        relationName: 0
      }
    ]
  };

  /**
   * Data type description
   * @type {{nodes: [*], links: [*]}}
   */
  Types.DataType = {
    nodes: [
      {
        id: 0,
        label: '',
        link: '',
        numberOfLinks: 0
      }
    ],
    links: [
      {
        source: 0,
        target: 0,
        relationName: ''
      }
    ]
  };

  /**
   * Node type description
   * @type {{index: number, x: number, y: number, vx: number, vy: number, fx: number, fy: number, radius: number, label: string, expandedLabel: Array, expandedLabelStart: number, link: string, numberOfLinks: number, dragged: boolean, highlighted: boolean, linkNode: boolean}}
   */
  Types.NodeType = {
    index: 0,
    x: 0,
    y: 0,
    vx: 0,
    vy: 0,
    fx: 0,
    fy: 0,
    color: 0,
    radius: 0,
    label: '',
    expandedLabel: [],
    expandedLabelStart: 0,
    link: '',
    numberOfLinks: 0,
    individuals: '',
    dragged: false,
    highlighted: false,
    linkNode: false
  };

  /**
   * Link type description
   * @type {{source: number, target: number, relationName: string}}
   */
  Types.LinkType = {
    source: 0,
    target: 0,
    relationName: ''
  };

  /******************************************************************************************************
   * Internal variables
   *****************************************************************************************************/

  var canvas, context, canvasWidth, canvasHeight, halfCanvasWidth, halfCanvasHeight;
  var halfMapWidth = cb.mapWidth / 2, halfMapHeight = cb.mapHeight / 2;
  var cbCanvas, cbSimulation, cbGraph, cbZoom, cbTransform = d3.zoomIdentity, cbDrag;
  var dragPosY, dragPosX, isDragging = false;
  var highlightedNode = null, mouseMoveDisabled = false;
  var clickSend = false;
  var contextMenuNode = null, lastTransformed;
  var isLoaded = false;

  // Initialize the graph object
  cbGraph = {nodes: [], links: [], linkNodes: []};

  /******************************************************************************************************
   * Exposed functionality (for easy debugging purposes)
   *****************************************************************************************************/

  cb.getCurrentTransform = function () {
    return cbTransform;
  };

  cb.getSimulation = function () {
    return cbSimulation;
  };

  cb.getGraph = function () {
    return cbGraph;
  };

  /******************************************************************************************************
   * Exposed functionality
   *****************************************************************************************************/

  /**
   * Search and focus on a node based on its concept name
   * @param name
   */
  cb.searchConcept = function (name) {
    // Find the node by label
    var nodes = cbGraph.nodes.filter(function (node) {
      return node.label === name;
    });

    // If found, move to it
    if (nodes.length > 0) {
      moveToNode(nodes[0]);
      setNodeAsHighlight(nodes[0]);
    }
  };

  /**
   * Search and focus on a node based on a concept id
   * @param id
   */
  cb.searchConceptById = function (id) {
    // Find the node by id
    var nodes = cbGraph.nodes.filter(function (node) {
      return node.id === id;
    });

    // If found, move to it
    if (nodes.length > 0) {
      moveToNode(nodes[0]);
      setNodeAsHighlight(nodes[0]);
    }
  };

  /**
   * Recenter the viewport
   * @param duration
   */
  cb.centerView = function (duration) {
    // Find current locations of all nodes, and select max
    var minX = cb.mapWidth, maxX = 0, minY = cb.mapHeight, maxY = 0;
    cbGraph.nodes.map(function (node) {
      minX = Math.min(minX, node.x - node.radius);
      maxX = Math.max(maxX, node.x + node.radius);
      minY = Math.min(minY, node.y - node.radius);
      maxY = Math.max(maxY, node.y + node.radius);
    });

    moveToPosition(minX, maxX, minY, maxY, duration);
  };

  /******************************************************************************************************
   * Utility functions
   *****************************************************************************************************/

  cb.resizeCanvas = function () {
    d3.select(window).dispatch('custom_resize');
  };

  cb.resizeCanvasWithSizes = function(width, height){
    d3.select(window).dispatch('custom_resize', {detail: {width: width, height: height}});
  };

  /**
   * Resize the canvas (draw area)
   * This should be done on draggable window size changes, or browser window changes
   */
  function resizeCanvas() {
    // Get container size, and set sizes and zoom extent
    var $container = $('#graph_container_div');
    canvas.width = canvasWidth = ((d3.event && d3.event.detail && d3.event.detail.width) ? d3.event.detail.width : $container.innerWidth());
    canvas.height = canvasHeight = ((d3.event && d3.event.detail && d3.event.detail.height) ? d3.event.detail.height : $container.innerHeight());
    halfCanvasWidth = canvasWidth / 2;
    halfCanvasHeight = canvasHeight / 2;
    cb.zoomExtent[0] = Math.max(canvasWidth / cb.mapWidth, canvasHeight / cb.mapHeight, 0.1);

    // Get context if not available
    if (context === undefined) {
      context = canvas.getContext('2d');
    }

    // Check if the event loop is running, if not, restart
    if (d3.event && !d3.event.active) cbSimulation.restart();
  }

  /**
   * Retrieve the node radius
   * @returns {number}
   */
  function getNodeRadius(node) {
    // Check whether link node
    if (node.linkNode === true) {
      node.radius = 1;
      return cb.linkNodeRadius;
    }

    node.radius = cb.baseNodeRadius + cb.extendNodeRatio * (node.numberOfLinks ? node.numberOfLinks : 1);
    return node.radius + cb.nodeRadiusMargin;
  }

  /**
   * Limit the node position based on the map dimensions
   * @param node
   */
  function limitNode(node) {
    node.x = Math.max(node.radius, Math.min(cb.mapWidth - node.radius, node.x));
    node.y = Math.max(node.radius, Math.min(cb.mapHeight - node.radius, node.y));
  }

  /**
   * Limits the transformation struct, by the map size with a small white margin
   * @param transform
   * @returns {*}
   */
  function limitTransform(transform) {
    transform.x =
        Math.max(-(((cb.mapWidth + cb.mapWidthDragMargin) * transform.k) - canvasWidth),
            Math.min(cb.mapWidthDragMargin * transform.k, transform.x));
    transform.y =
        Math.max(-(((cb.mapHeight + cb.mapHeightDragMargin) * transform.k) - canvasHeight),
            Math.min(cb.mapHeightDragMargin * transform.k, transform.y));

    return transform;
  }

  /**
   * Mark the given node as being dragged
   * @param node
   */
  function setNodeAsDragged(node) {
    isDragging = true;
    node.dragged = true;
    clearNodeHighlight();
    setHighlightsByNode(node);
  }

  /**
   * Unmark the given node as being dragged
   * @param node
   */
  function clearNodeAsDragged(node) {
    isDragging = false;
    node.dragged = false;
    clearHighlightsByNode(node);
  }

  /**
   * Mark the given node as being highlighted
   * @param node
   */
  function setNodeAsHighlight(node) {
    // Check if node the same
    if (highlightedNode && highlightedNode.index === node.index) return;

    // Check for previous highlight
    clearNodeHighlight();

    // Set as highlighted
    if (node === undefined) return;
    highlightedNode = node;
    node.highlighted = true;
    setHighlightsByNode(node);
  }

  /**
   * Unmark the given node as being highlighted
   */
  function clearNodeHighlight() {
    if (highlightedNode !== null) {
      highlightedNode.highlighted = false;
      clearHighlightsByNode(highlightedNode);
      highlightedNode = null;
    }
  }

  /**
   * Mark relations of the given node as highlighted
   * @param node
   */
  function setHighlightsByNode(node) {
    cbGraph.links.map(function (link) {
      if (link.target.index === node.index) link.source.highlighted = true;
      if (link.source.index === node.index) link.target.highlighted = true;
    });
  }

  /**
   * Unmark relations of the given node as highlighted
   * @param node
   */
  function clearHighlightsByNode(node) {
    cbGraph.links.map(function (link) {
      if (link.target.index === node.index) link.source.highlighted = false;
      if (link.source.index === node.index) link.target.highlighted = false;
    });
  }

  /**
   * Retrieve the link minimum length
   * @param {Types.LinkType.} link
   * @returns {number}
   */
  function getLinkDistance(link) {
    return getNodeRadius(link.source) +
        getNodeRadius(link.target) +
        getLinkLabelLength(link);
  }

  /**
   * Estimate the length of the link label
   * @param link
   * @returns {number}
   */
  function getLinkLabelLength(link) {
    return link.relationName.length * 5 + 10;
  }

  /**
   * Transform a location with the current transformation
   * @param loc
   */
  function transformLocation(loc) {
    return {
      x: (loc.clientX - canvas.getBoundingClientRect().x - cbTransform.x) / cbTransform.k,
      y: (loc.clientY - cbTransform.y) / cbTransform.k
    };
  }

  /******************************************************************************************************
   * Event handlers
   *****************************************************************************************************/

  /**
   * Find a node based on the event location
   * @returns {undefined}
   */
  function findNode() {
    var transformed;
    if (typeof d3.event.clientX === 'undefined' || typeof d3.event.clientY === 'undefined') {
      transformed = lastTransformed;
    } else {
      transformed = lastTransformed = transformLocation(d3.event);
    }
    var node = cbSimulation.find(transformed.x, transformed.y, 20);
    return node && node.linkNode !== true ? node : undefined;
  }

  /**
   * Event fired when the drag action starts
   */
  function onDragStarted() {
    if (!d3.event.active) cbSimulation.alphaTarget(0.3).restart();
    d3.event.subject.fx = dragPosX = d3.event.subject.x;
    d3.event.subject.fy = dragPosY = d3.event.subject.y;

    mouseMoveDisabled = false;
    setNodeAsDragged(d3.event.subject);
  }

  /**
   * Event fired during dragging progress
   */
  function onDragged() {
    dragPosX += d3.event.dx / cbTransform.k;
    dragPosY += d3.event.dy / cbTransform.k;
    d3.event.subject.fx = Math.max(0, Math.min(cb.mapWidth, dragPosX));
    d3.event.subject.fy = Math.max(0, Math.min(cb.mapHeight, dragPosY));
  }

  /**
   * Event fired when the drag action stops
   */
  function onDragEnded() {
    if (!d3.event.active) cbSimulation.alphaTarget(0);
    d3.event.subject.fx = null;
    d3.event.subject.fy = null;

    clearNodeAsDragged(d3.event.subject);
  }

  /**
   * Event for mouse move, to select a node to highlight
   */
  function onMouseMove() {
    if (mouseMoveDisabled) return;
    highlightNode(findNode());
  }

  /**
   * Left mouse button click, in order to fix node highlight
   * Communicates with the wiki in order to open the correct page
   */
  function onClick() {
    var node = findNode();
    if (node && !mouseMoveDisabled) {
      setNodeAsHighlight(node);
    }
    mouseMoveDisabled = !mouseMoveDisabled;

    if (!clickSend && node !== undefined) {
      if (typeof node.link !== 'undefined' && node.link !== '') {
        clickSend = true;
        //noinspection JSCheckFunctionSignatures
        parent.postMessage({'type': 'wiki_update', 'data': node.link}, '*');
        setTimeout(function () {
          clickSend = false;
        }, 250);
      }
    }
  }

  /**
   * Right click event.
   * On empty space, shows context menu for styling
   */
  function onRightClick() {
    d3.event.preventDefault();

    var node = findNode();
    contextMenuNode = typeof node !== 'undefined' ? node : null;
    $('#graph_container_div').contextMenu({x: d3.event.clientX, y: d3.event.clientY});
  }

  /**
   * Double click event, move to the clicked node
   */
  function onDoubleClick() {
    moveToNode(findNode());
  }

  /**
   * Keyboard event handler
   * space -> Stop simulation
   */
  function onKeyDown() {
    var node = findNode();

    switch (d3.event.keyCode) {
      case 32: // Space
        // Force movement stop with space bar
        cbSimulation.stop();
        return;

      case 73: // I
        /* falls through */
      case 48: // 0
        if (node !== undefined) colorNode(node, 0);
        break;

      case 82: // R
        /* falls through */
      case 49: // 1
        if (node !== undefined) colorNode(node, 1);
        break;

      case 71: // G
        /* falls through */
      case 50: // 2
        if (node !== undefined) colorNode(node, 2);
        break;

      case 66: // B
        /* falls through */
      case 51: // 3
        if (node !== undefined) colorNode(node, 3);
        break;

      case 79: // O
        /* falls through */
      case 52: // 4
        if (node !== undefined) colorNode(node, 4);
        break;
    }

    // Check if the event loop is running, if not, restart
    if (!d3.event.active) cbSimulation.restart();
  }

  /**
   * Highlight the current event location node
   * @param node
   */
  function highlightNode(node) {
    if (node) {
      setNodeAsHighlight(node);
    } else {
      clearNodeHighlight();
    }

    // Check if the event loop is running, if not, restart
    if (!d3.event.active) cbSimulation.restart();
  }

  /**
   * Move the view to the given node
   * It keeps the relations inside the view
   * @param node
   */
  function moveToNode(node) {
    // Check for node existence
    if (node === undefined) return;

    // Stop simulation for now to prevent node walking
    mouseMoveDisabled = true;
    cbSimulation.stop();

    // Set clicked node as highlighted
    setNodeAsHighlight(node);

    // Find current locations of highlighted nodes
    var minX = cb.mapWidth, maxX = 0, minY = cb.mapHeight, maxY = 0;
    cbGraph.nodes.map(function (node) {
      if (!node.highlighted) return;
      minX = Math.min(minX, node.x - node.radius);
      maxX = Math.max(maxX, node.x + node.radius);
      minY = Math.min(minY, node.y - node.radius);
      maxY = Math.max(maxY, node.y + node.radius);
    });

    // Do the actual move
    moveToPosition(minX, maxX, minY, maxY);
  }

  /**
   * Move the view to the given view bounds
   * @param minX
   * @param maxX
   * @param minY
   * @param maxY
   * @param duration
   */
  function moveToPosition(minX, maxX, minY, maxY, duration) {
    if (!isLoaded) return;

    // Check duration
    duration = typeof duration !== 'undefined' ? duration : 3000;

    // Calculate scale
    var scale = 0.9 / Math.max((maxX - minX) / canvasWidth, (maxY - minY) / canvasHeight);
    scale = Math.min(cb.zoomExtent[1], Math.max(1, scale));

    // Calculate zoom identify
    var transform = d3.zoomIdentity
        .translate(halfCanvasWidth, halfCanvasHeight)
        .scale(scale)
        .translate(-(minX + maxX) / 2, -(minY + maxY) / 2);

    // Move to it
    cbCanvas
        .transition()
        .duration(duration)
        .call(cbZoom.transform, transform);
  }

  /**
   * Zoom event handler
   * Limits the transformation and calls the draw function
   */
  function zoomGraph() {
    cbTransform = limitTransform(d3.event.transform);
    drawGraph();
  }

  /******************************************************************************************************
   * Canvas draw methods
   *****************************************************************************************************/

  /**
   * Draws the complete concept browser, refreshes the view on every iteration
   * @note Order in this function is important!
   */
  function drawGraph() {
    // Limit the nodes
    cbGraph.nodes.map(limitNode);

    // Save state
    context.save();

    // Clear canvas
    context.clearRect(0, 0, canvasWidth, canvasHeight);

    // Adjust scaling
    context.translate(cbTransform.x, cbTransform.y);
    context.scale(cbTransform.k, cbTransform.k);

    // Draw grid lines
    if (cb.drawGrid) {
      context.beginPath();
      for (var i = 0; i <= cb.mapWidth; i += 100) {
        context.moveTo(i, 0);
        context.lineTo(i, cb.mapHeight);
      }
      for (var j = 0; j <= cb.mapHeight; j += 100) {
        context.moveTo(0, j);
        context.lineTo(cb.mapWidth, j);
      }
      context.strokeStyle = 'black';
      context.stroke();

      // Draw canvas size rectangle
      context.beginPath();
      context.moveTo(0, 0);
      context.lineTo(canvasWidth, 0);
      context.lineTo(canvasWidth, canvasHeight);
      context.lineTo(0, canvasHeight);
      context.lineTo(0, 0);
      context.strokeStyle = 'blue';
      context.stroke();
    }

    //////////////////////
    // NORMAL           //
    //////////////////////

    // Draw normal links
    context.beginPath();
    context.lineWidth = cb.linkLineWidth;
    context.strokeStyle = isDragging || highlightedNode !== null ? cb.fadedLinksStrokeStyle : cb.defaultLinkStrokeStyle;
    cbGraph.links.map(drawNormalLink);
    context.stroke();

    // Draw normal nodes
    for (var nn = 0; nn <= 4; nn++) {
      cb.applyStyle(nn);
      context.beginPath();
      context.lineWidth = cb.nodeLineWidth;
      context.fillStyle = isDragging || highlightedNode !== null ? cb.fadedNodeFillStyle : cb.defaultNodeFillStyle;
      context.strokeStyle = isDragging || highlightedNode !== null ? cb.fadedNodeStrokeStyle : cb.defaultNodeStrokeStyle;
      cbGraph.nodes.filter(filterNodeOnColor(nn)).map(drawNormalNode);
      context.fill();
      context.stroke();
    }

    // Draw link nodes
    if (cb.drawLinkNodes) {
      context.beginPath();
      context.lineWidth = cb.nodeLineWidth;
      context.fillStyle = cb.fadedNodeFillStyle;
      context.strokeStyle = cb.fadedNodeStrokeStyle;
      cbGraph.linkNodes.map(drawNormalNode);
      context.fill();
      context.stroke();
    }

    // Draw normal link arrows
    context.fillStyle = isDragging || highlightedNode !== null ? cb.fadedLinksStrokeStyle : cb.defaultLinkStrokeStyle;
    cbGraph.links.map(drawNormalLinkArrow);

    //////////////////////
    // DRAGGED          //
    //////////////////////

    // Draw dragged links
    if (isDragging) {
      context.beginPath();
      context.lineWidth = cb.linkLineWidth;
      context.strokeStyle = cb.draggedLinkStrokeStyle;
      cbGraph.links.map(drawDraggedLink);
      context.stroke();
    }

    // Draw dragged nodes
    if (isDragging) {
      for (var dn = 0; dn <= 4; dn++) {
        cb.applyStyle(dn);
        context.beginPath();
        context.lineWidth = cb.nodeLineWidth;
        context.fillStyle = cb.draggedNodeFillStyle;
        context.strokeStyle = cb.draggedNodeStrokeStyle;
        cbGraph.nodes.filter(filterNodeOnColor(dn)).map(drawDraggedNode);
        context.fill();
        context.stroke();
      }
    }

    // Draw dragged link arrows
    if (isDragging) {
      context.fillStyle = cb.draggedLinkStrokeStyle;
      cbGraph.links.map(drawDraggedLinkArrow);
    }

    //////////////////////
    // HIGHLIGHT        //
    //////////////////////

    // Draw highlighted links
    if (highlightedNode !== null) {
      context.beginPath();
      context.lineWidth = cb.linkLineWidth;
      context.strokeStyle = cb.highlightedLinkStrokeStyle;
      cbGraph.links.map(drawHighlightedLink);
      context.stroke();
    }

    // Draw highlighted nodes
    for (var hn = 0; hn <= 4; hn++) {
      cb.applyStyle(hn);
      context.beginPath();
      context.lineWidth = cb.nodeLineWidth;
      context.fillStyle = cb.highlightedNodeFillStyle;
      context.strokeStyle = cb.highlightedNodeStrokeStyle;
      cbGraph.nodes.filter(filterNodeOnColor(hn)).map(drawHighlightedNode);
      context.fill();
      context.stroke();
    }

    // Draw highlighted link arrows
    if (highlightedNode !== null) {
      context.fillStyle = cb.highlightedLinkStrokeStyle;
      cbGraph.links.map(drawHighlightedLinkArrow);
    }

    //////////////////////
    // LABELS           //
    //////////////////////

    // Set this lower to prevent horns on M/W letters
    // https://github.com/CreateJS/EaselJS/issues/781
    context.miterLimit = 2.5;

    // Draw link labels
    if (isDragging || highlightedNode !== null) {
      context.fillStyle = cb.defaultNodeLabelColor;
      context.font = cb.defaultNodeLabelFont;
      context.textBaseline = 'top';
      context.lineWidth = cb.activeNodeLabelLineWidth;
      context.strokeStyle = cb.activeNodeLabelStrokeStyle;
      cbGraph.links.map(drawLinkText);
    }

    // Draw node labels
    context.fillStyle = cb.defaultNodeLabelColor;
    context.font = cb.defaultNodeLabelFont;
    context.textBaseline = 'middle';
    context.textAlign = 'center';
    context.lineWidth = cb.activeNodeLabelLineWidth;
    context.strokeStyle = cb.activeNodeLabelStrokeStyle;
    cbGraph.nodes.map(drawNodeText);

    // Restore state
    context.restore();
  }

  /**
   * Draw the link line
   * @param link
   */
  function drawLink(link) {
    context.moveTo(link.target.x, link.target.y);
    context.lineTo(link.source.x, link.source.y);
  }

  /**
   * Draw a link when in normal state
   * @param link
   */
  function drawNormalLink(link) {
    if ((link.target.dragged && link.source.dragged) || (link.target.highlighted && link.source.highlighted)) return;
    drawLink(link);
  }

  /**
   * Draw a link when in dragged state
   * @param link
   */
  function drawDraggedLink(link) {
    if (link.source.dragged || link.target.dragged) drawLink(link);
  }

  /**
   * Draw a link when in highlight state
   * @param link
   */
  function drawHighlightedLink(link) {
    if (link.source.index === highlightedNode.index || link.target.index === highlightedNode.index) drawLink(link);
  }

  /**
   * Draw the link text
   * @param link
   */
  function drawLinkText(link) {
    // Only draw the text when the link is active
    if (link.relationName &&
        (!isDragging && (link.source.index === highlightedNode.index || link.target.index === highlightedNode.index)) ||
        (link.source.dragged || link.target.dragged)) {

      // Calculate angle of label
      var startRadians = Math.atan((link.source.y - link.target.y) / (link.source.x - link.target.x));
      startRadians += (link.source.x >= link.target.x) ? Math.PI : 0;

      // Transform the context
      context.save();
      context.translate(link.source.x, link.source.y);
      context.rotate(startRadians);

      // Check rotation and add extra if required
      if ((startRadians * 2) > Math.PI) {
        context.rotate(Math.PI);
        context.textAlign = 'right';
        context.strokeText(link.relationName, -(getNodeRadius(link.source) + 5), 0, getLinkLabelLength(link));
        context.fillText(link.relationName, -(getNodeRadius(link.source) + 5), 0, getLinkLabelLength(link));
      } else {
        context.textAlign = 'left';
        context.strokeText(link.relationName, getNodeRadius(link.source) + 5, 0, getLinkLabelLength(link));
        context.fillText(link.relationName, getNodeRadius(link.source) + 5, 0, getLinkLabelLength(link));
      }

      // Restore context
      context.restore();
    }
  }

  /**
   * Draw the link arrow
   * @param link
   */
  function drawLinkArrow(link) {
    // Calculate head rotation
    var startRadians = Math.atan((link.source.y - link.target.y) / (link.source.x - link.target.x));
    startRadians += ((link.source.x >= link.target.x) ? -1 : 1) * Math.PI / 2;

    // Draw the triangle
    context.save();
    context.beginPath();
    context.translate(link.target.x, link.target.y);
    context.rotate(startRadians);
    context.moveTo(0, link.target.radius - 1);
    context.lineTo(3, 9 + link.target.radius);
    context.lineTo(-3, 9 + link.target.radius);
    context.closePath();
    context.restore();
    context.fill();
  }

  /**
   * Draw a link arrow when in normal state
   * @param link
   */
  function drawNormalLinkArrow(link) {
    if ((link.target.dragged && link.source.dragged) || (link.target.highlighted && link.source.highlighted)) return;
    drawLinkArrow(link);
  }

  /**
   * Draw a link arrow when in dragged state
   * @param link
   */
  function drawDraggedLinkArrow(link) {
    if (link.target.dragged || link.source.dragged) drawLinkArrow(link);
  }

  /**
   * Draw a link arrow when in highlighted state
   * @param link
   */
  function drawHighlightedLinkArrow(link) {
    if (link.target.index === highlightedNode.index || link.source.index === highlightedNode.index) drawLinkArrow(link);
  }

  /**
   * Draw the node
   * @param node
   */
  function drawNode(node) {
    context.moveTo(node.x + node.radius, node.y);
    context.arc(node.x, node.y, node.radius, 0, 2 * Math.PI);
  }

  /**
   * Draw a node when in normal state
   * @param node
   */
  function drawNormalNode(node) {
    if (node.highlighted || node.dragged) return;
    drawNode(node);
  }

  /**
   * Draw a node when in dragged state
   * @param node
   */
  function drawDraggedNode(node) {
    if (node.dragged) drawNode(node);
  }

  /**
   * Draw a node when in highlighted state
   * @param node
   */
  function drawHighlightedNode(node) {
    if (node.highlighted) drawNode(node);
  }

  /**
   * Draw the node text
   * @param node
   */
  function drawNodeText(node) {
    // Adjust font if necessary, or skip if not
    if ((isDragging && node.dragged) || ((highlightedNode !== null || isDragging) && node.highlighted)) {
      context.font = cb.activeNodeLabelFont;
    } else {
      if (isDragging || highlightedNode !== null) return;
    }

    // Draw the actual text (which can be multiple lines)
    var yStart = node.y - node.expandedLabelStart;
    node.expandedLabel.map(function (line) {
      if (node.dragged || node.highlighted) context.strokeText(line, node.x, yStart);
      context.fillText(line, node.x, yStart);
      yStart += cb.defaultNodeLabelFontSize;
    });
  }

  /******************************************************************************************************
   * Color functions
   *****************************************************************************************************/

  /**
   * Function to filter nodes on a given color index
   * @param color
   * @returns {Function}
   */
  function filterNodeOnColor(color) {
    return function (node) {
      return node.color === color;
    };
  }

  /**
   * Color the given node and save in the local storage
   * @param node
   * @param color
   */
  function colorNode(node, color) {
    node.color = color;

    if (typeof(Storage) !== 'undefined') {
      localStorage.setItem('nodeColor.' + node.label, color);
    }
  }

  /**
   * Resets all node colors and clears the local storage
   */
  function resetNodeColors() {
    cbGraph.nodes.map(function (node) {
      node.color = 0;
    });

    // Clear local storage
    if (typeof(Storage) !== 'undefined') {
      localStorage.clear();
    }
  }

  /**
   * Load node colors from the local storage
   */
  function loadNodeColor(node) {
    node.color = 0;
    if (typeof(Storage) !== 'undefined') {
      var color = localStorage.getItem('nodeColor.' + node.label);
      if (color !== null) {
        node.color = parseInt(color);
      }
    }
  }

  /******************************************************************************************************
   * Force functions
   *****************************************************************************************************/

  /**
   * Force to keep the nodes inside the map box
   * @param alpha
   */
  function keepInBoxForce(alpha) {
    for (var i = 0, n = cbGraph.nodes.length,
             node, kx = (alpha * cb.boundForceStrenght) / cb.mapWidth,
             ky = (alpha * cb.boundForceStrenght) / cb.mapHeight; i < n; ++i) {
      // Set variables
      node = cbGraph.nodes[i];

      // Calculate forces
      node.vx -= (node.x - halfMapWidth) * kx;
      node.vy -= (node.y - halfMapHeight) * ky;
    }
  }

  /******************************************************************************************************
   * Initialize canvas
   *****************************************************************************************************/

  canvas = document.getElementById('graph_container_canvas');
  cbCanvas = d3.select(canvas);
  resizeCanvas();

  /******************************************************************************************************
   * Register init function
   *****************************************************************************************************/

  cb.init = function (data) {

    /******************************************************************************************************
     * Convert the data to be suitable for the concept browser
     *****************************************************************************************************/
    var idIndexMap = {};
    cbGraph = {
      nodes: [],
      links: []
    };

    // First, map the nodes
    data.map(function (concept) {
      idIndexMap[concept.id] = cbGraph.nodes.length;
      cbGraph.nodes.push({
        id: concept.id,
        label: concept.name,
        link: '',
        numberOfLinks: concept.relations.length
      });
    });

    // Secondly, map the links from every node
    data.map(function (concept) {
      concept.relations.map(function (relation) {
        cbGraph.links.push({
          source: idIndexMap[concept.id],
          target: idIndexMap[relation.targetId],
          relationName: relation.relationName
        })
      });
    });

    /******************************************************************************************************
     * Start simulation
     *****************************************************************************************************/

    cbSimulation = d3.forceSimulation()
        .force('sidedetection', keepInBoxForce)     // To keep nodes in the map view
        .force('charge', d3.forceManyBody()         // To keep nodes grouped
            .distanceMin(cb.manyBodyDistanceMin)
            .distanceMax(cb.manyBodyDistanceMax)
            .strength(cb.manyBodyStrength))
        .force('collide', d3.forceCollide()         // To prevent nodes from overlapping
            .radius(getNodeRadius)
            .strength(cb.collideStrength)
            .iterations(cb.collideIterations))
        .force('link', d3.forceLink()               // To force a certain link distance
            .distance(getLinkDistance)
            .strength(cb.linkStrength))
        .force('center',                            // To force the node to move around the map center
            d3.forceCenter(cb.mapWidth / 2, cb.mapHeight / 2));

    // Calculate some one-time values before rendering starts
    cbGraph.nodes.map(function (node) {
      getNodeRadius(node);
      loadNodeColor(node);

      // Set default label values
      node.expandedLabelStart = 0;
      node.expandedLabel = [];
      if (node.label === '') return;

      // Calculate node text lines
      var lines = node.label.split(' ');
      if (lines.length <= 2 && node.label.length <= (cb.minCharCount + 1)) {
        node.expandedLabel = lines;
      } else {
        // Check if next line can be combined with the last line
        node.expandedLabel.push(lines[0]);
        for (var i = 1; i < lines.length; i++) {
          if (node.expandedLabel[node.expandedLabel.length - 1].length + lines[i].length <= cb.minCharCount) {
            node.expandedLabel[node.expandedLabel.length - 1] += ' ' + lines[i];
          } else {
            node.expandedLabel.push(lines[i]);
          }
        }
      }

      // Calculate offset for the amount of lines
      node.expandedLabelStart = (node.expandedLabel.length - 1) * (0.5 * cb.defaultNodeLabelFontSize);
    });

    // Create linkNodes to avoid overlap (http://bl.ocks.org/couchand/7190660)
    cbGraph.linkNodes = [];
    cbGraph.links.map(function (link) {
      cbGraph.linkNodes.push({
        source: cbGraph.nodes[link.source],
        target: cbGraph.nodes[link.target],
        linkNode: true
      });
    });

    // Load data (nodes and links)
    cbSimulation.nodes(cbGraph.nodes.concat(cbGraph.linkNodes));
    cbSimulation.force('link').links(cbGraph.links);

    // Load handlers for the tick event
    cbSimulation.on('tick', function () {

      // Update link node positions
      cbGraph.linkNodes.map(function (linkNode) {
        linkNode.x = (linkNode.source.x + linkNode.target.x) * 0.5;
        linkNode.y = (linkNode.source.y + linkNode.target.y) * 0.5;
      });

      // Draw the actual graph
      drawGraph();
    });

    // Create zoom handler
    cbZoom = d3.zoom()
        .scaleExtent(cb.zoomExtent)
        .on('zoom', zoomGraph);

    // Create drag handlers
    cbDrag = d3.drag()
        .container(canvas)
        .subject(findNode)
        .on('start', onDragStarted)
        .on('drag', onDragged)
        .on('end', onDragEnded);

    // Add handlers to canvas
    cbCanvas
        .call(cbDrag)
        .call(cbZoom)
        .on('mousemove', onMouseMove)
        .on('click', onClick)
        .on('dblclick.zoom', onDoubleClick)
        .on('contextmenu', onRightClick);

    // Add window handlers
    d3.select(window)
        .on('resize', resizeCanvas)
        .on('custom_resize', resizeCanvas)
        .on('keydown', onKeyDown);

    // @todo switch to new API
    // Add message handler
    // window.addEventListener('message', function (event) {
    //   var message = event.data;
    //   console.log(message);
    //
    //   // Search a node due to wiki interaction
    //   if (message.type === 'cb_update_opened') {
    //     cb.searchConcept(message.data);
    //   }
    // });

    // Define context menu
    //noinspection JSUnusedGlobalSymbols
    $.contextMenu({
      selector: '#graph_container_div',
      trigger: 'none',
      build: function () {
        //noinspection JSUnusedGlobalSymbols
        return {
          callback: function (key) {
            if (key === 'quit') return;
            if (key.startsWith('style')) colorNode(contextMenuNode, parseInt(key.substr(6)));
            if (key === 'reset') resetNodeColors();
            if (key === 'center') cb.centerView();
            cbSimulation.restart();
          },
          items: getContextMenuItems()
        };
      }
    });

    // Center view and center image
    isLoaded = true;
    resizeCanvas();
    setTimeout(function(){cb.centerView(500);}, 500);
  };

  /**
   * Context menu builder
   * @returns {*}
   */
  function getContextMenuItems() {
    if (contextMenuNode === null) {
      // Global
      return {
        'reset': {name: 'Reset node colors', icon: 'fa-undo'},
        'sep1': '---------',
        'center': {name: 'Back to center', icon: 'fa-sign-in'},
        'sep2': '---------',
        'quit': {name: 'Close', icon: 'fa-times'}
      };
    } else {
      // Node
      var individuals = {};

      // Generate individuals, if any
      if (contextMenuNode.individuals !== undefined) {
        var individualsItems = {};
        var individualsTextItems = contextMenuNode.individuals.split(';');
        individualsTextItems.map(function (item) {
          item = item.trim();
          individualsItems['individual-' + item] = {
            name: item
          };
        });

        individuals = {
          individuals: {
            name: 'Examples',
            items: individualsItems
          },
          sep1: '---------'
        };
      }

      // Merge with default data
      var defaultData = {
        'styles': {
          name: 'Change color',
          icon: 'fa-paint-brush',
          items: {
            'style-1': {name: 'Red', icon: contextMenuNode.color === 1 ? 'fa-check' : ''},
            'style-2': {name: 'Green', icon: contextMenuNode.color === 2 ? 'fa-check' : ''},
            'style-3': {name: 'Blue', icon: contextMenuNode.color === 3 ? 'fa-check' : ''},
            'style-4': {name: 'Orange', icon: contextMenuNode.color === 4 ? 'fa-check' : ''},
            'sep1': '---------',
            'style-0': {name: 'Default', icon: contextMenuNode.color === 0 ? 'fa-check' : 'fa-undo'}
          }
        },
        'sep2': '---------',
        'quit': {name: 'Close', icon: 'fa-times'}
      };

      return $.extend(individuals, defaultData);
    }
  }

}(window.cb = window.cb || {}, jQuery, d3));
