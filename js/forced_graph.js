/**
 * Register cb namespace for usage
 */
(function (cb, $, d3, undefined) {

  /******************************************************************************************************
   * Data source
   *****************************************************************************************************/
  // cb.data_source = "data/GIS_RS.json";
  // cb.data_source = "data/GIS_RS_REDUCED.json";
  cb.data_source = "data/GIS_RS_link_count.json";

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
  cb.defaultLinkStrokeStyle = '';
  cb.draggedLinkStrokeStyle = '';
  cb.fadedLinksStrokeStyle = '';
  cb.highlightedLinkStrokeStyle = '';

  // Node label styles
  cb.defaultNodeLabelColor = '';
  cb.activeNodeLabelStrokeStyle = "";

  cb.applyStyle = function (style) {
    switch (style) {
      case 'orange': {
        // Node styles
        cb.defaultNodeFillStyle = '#deaf6c';
        cb.defaultNodeStrokeStyle = cb.defaultNodeFillStyle;
        cb.draggedNodeFillStyle = cb.defaultNodeFillStyle;
        cb.draggedNodeStrokeStyle = '#ff5d00';
        cb.fadedNodeFillStyle = '#bcac9b';
        cb.fadedNodeStrokeStyle = cb.fadedNodeFillStyle;
        cb.highlightedNodeFillStyle = cb.draggedNodeFillStyle;
        cb.highlightedNodeStrokeStyle = cb.draggedNodeStrokeStyle;

        // Link styles
        cb.linkLineWidth = 1;
        cb.defaultLinkStrokeStyle = '#d3c2b6';
        cb.draggedLinkStrokeStyle = '#222222';
        cb.fadedLinksStrokeStyle = '#E0E0E0';
        cb.highlightedLinkStrokeStyle = cb.draggedLinkStrokeStyle;

        // Node label styles
        cb.defaultNodeLabelColor = '#222222';
        cb.activeNodeLabelStrokeStyle = "#fff";
        break;
      }
      case 'red':{
        // Node styles
        cb.defaultNodeFillStyle = '#de5356';
        cb.defaultNodeStrokeStyle = '#fff';
        cb.draggedNodeFillStyle = cb.defaultNodeFillStyle;
        cb.draggedNodeStrokeStyle = '#ff2340';
        cb.fadedNodeFillStyle = '#bc6d73';
        cb.fadedNodeStrokeStyle = '#fff';
        cb.highlightedNodeFillStyle = cb.draggedNodeFillStyle;
        cb.highlightedNodeStrokeStyle = cb.draggedNodeStrokeStyle;

        // Link styles
        cb.linkLineWidth = 1;
        cb.defaultLinkStrokeStyle = '#d39a96';
        cb.draggedLinkStrokeStyle = '#222222';
        cb.fadedLinksStrokeStyle = '#E0E0E0';
        cb.highlightedLinkStrokeStyle = cb.draggedLinkStrokeStyle;

        // Node label styles
        cb.defaultNodeLabelColor = '#222222';
        cb.activeNodeLabelStrokeStyle = "#fff";
        break;
      }
      case 'blue':{
        // Node styles
        cb.defaultNodeFillStyle = '#a4a5fe';
        cb.defaultNodeStrokeStyle = '#fff';
        cb.draggedNodeFillStyle = cb.defaultNodeFillStyle;
        cb.draggedNodeStrokeStyle = '#1513ff';
        cb.fadedNodeFillStyle = '#55557a';
        cb.fadedNodeStrokeStyle = '#fff';
        cb.highlightedNodeFillStyle = cb.draggedNodeFillStyle;
        cb.highlightedNodeStrokeStyle = cb.draggedNodeStrokeStyle;

        // Link styles
        cb.linkLineWidth = 1;
        cb.defaultLinkStrokeStyle = '#c6c5d3';
        cb.draggedLinkStrokeStyle = '#222222';
        cb.fadedLinksStrokeStyle = '#E0E0E0';
        cb.highlightedLinkStrokeStyle = cb.draggedLinkStrokeStyle;

        // Node label styles
        cb.defaultNodeLabelColor = '#222222';
        cb.activeNodeLabelStrokeStyle = "#fff";
        break;
      }
      case 'green':{
        // Node styles
        cb.defaultNodeFillStyle = '#75de79';
        cb.defaultNodeStrokeStyle = '#c4ffd9';
        cb.draggedNodeFillStyle = cb.defaultNodeFillStyle;
        cb.draggedNodeStrokeStyle = '#1ac321';
        cb.fadedNodeFillStyle = '#9ebc9d';
        cb.fadedNodeStrokeStyle = cb.fadedNodeFillStyle;
        cb.highlightedNodeFillStyle = cb.draggedNodeFillStyle;
        cb.highlightedNodeStrokeStyle = cb.draggedNodeStrokeStyle;

        // Link styles
        cb.linkLineWidth = 1;
        cb.defaultLinkStrokeStyle = '#c0d3be';
        cb.draggedLinkStrokeStyle = '#222222';
        cb.fadedLinksStrokeStyle = '#E0E0E0';
        cb.highlightedLinkStrokeStyle = cb.draggedLinkStrokeStyle;

        // Node label styles
        cb.defaultNodeLabelColor = '#222222';
        cb.activeNodeLabelStrokeStyle = "#fff";
        break;
      }
      case 'itc':
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

        // Link styles
        cb.linkLineWidth = 1;
        cb.defaultLinkStrokeStyle = '#696969';
        cb.draggedLinkStrokeStyle = '#333';
        cb.fadedLinksStrokeStyle = '#E0E0E0';
        cb.highlightedLinkStrokeStyle = cb.draggedLinkStrokeStyle;

        // Node label styles
        cb.defaultNodeLabelColor = '#000';
        cb.activeNodeLabelStrokeStyle = "#fff";

        break;
      }
    }

    if (d3.event && !d3.event.active) cbSimulation.restart();
  };

  cb.applyStyle('itc');

  /******************************************************************************************************
   * Data types
   *****************************************************************************************************/
  var Types = {};
  Types.DataType = {
    "nodes": [
      {
        "nodeName": "string",
        "label": "string",
        "link": "string",
        "numberOfLinks": 1
      }
    ],
    "links": [
      {
        "source": 1,
        "target": 1,
        "relationName": "string"
      }
    ]
  };
  Types.NodeType = {
    "index": 0,
    "x": 0,
    "y": 0,
    "vx": 0,
    "vy": 0,
    "fx": 0,
    "fy": 0,
    "radius": 0,
    "nodeName": "",
    "label": "",
    "expandedLabel": [],
    "expandedLabelStart": 0,
    "link": "",
    "numberOfLinks": 0,
    "dragged": false,
    "highlighted": false,
    "linkNode": false
  };
  Types.LinkType = {
    "source": 0,
    "target": 0,
    "relationName": ""
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

  // Initialize the graph object
  cbGraph = {nodes: [], links: [], linkNodes: []};

  /******************************************************************************************************
   * External functions
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

  cb.searchNode = function (nodeName) {
    // Find the node by label
    var nodes = cbGraph.nodes.filter(function (node) {
      return node.label === nodeName;
    });

    // If found, move to it
    if (nodes.length > 0) {
      moveToNode(nodes[0]);
      setNodeAsHighlight(nodes[0]);
    }
  };

  /******************************************************************************************************
   * Utility functions
   *****************************************************************************************************/

  /**
   * Resize the canvas
   */
  function resizeCanvas() {
    // Get container size, and set sizes and zoom extent
    var container = $('#graph_container_div');
    canvas.width = canvasWidth = container.innerWidth();
    canvas.height = canvasHeight = container.innerHeight();
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

    node.radius = cb.baseNodeRadius + cb.extendNodeRatio * (node.numberOfLinks ? parseInt(node.numberOfLinks) : 1);
    return node.radius + cb.nodeRadiusMargin;
  }

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

  function setNodeAsDragged(node) {
    isDragging = true;
    node.dragged = true;
    clearNodeHighlight();
    setHighlightsByNode(node);
  }

  function clearNodeAsDragged(node) {
    isDragging = false;
    node.dragged = false;
    clearHighlightsByNode(node);
  }

  function setNodeAsHighlight(node) {
    // Check if node the same
    if (highlightedNode && highlightedNode.index === node.index) return;

    // Check for previous highlight
    clearNodeHighlight();

    if (node === undefined) return;
    highlightedNode = node;
    node.highlighted = true;
    setHighlightsByNode(node);
  }

  function clearNodeHighlight() {
    if (highlightedNode !== null) {
      highlightedNode.highlighted = false;
      clearHighlightsByNode(highlightedNode);
      highlightedNode = null;
    }
  }

  function setHighlightsByNode(node) {
    cbGraph.links.forEach(function (link) {
      if (link.target.index === node.index) link.source.highlighted = true;
      if (link.source.index === node.index) link.target.highlighted = true;
    });
  }

  function clearHighlightsByNode(node) {
    cbGraph.links.forEach(function (link) {
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

  function getLinkLabelLength(link) {
    return link.relationName.length * 5 + 10;
  }

  /**
   * @param loc
   */
  function transformLocation(loc) {
    return {
      'x': (loc.x - cbTransform.x) / cbTransform.k,
      "y": (loc.y - cbTransform.y) / cbTransform.k
    };
  }

  /******************************************************************************************************
   * Event handlers
   *****************************************************************************************************/

  function selectNode() {
    var transformed = transformLocation(d3.event);
    var node = cbSimulation.find(transformed.x, transformed.y, 20);
    return node && node.linkNode !== true ? node : undefined;
  }

  function onDragStarted() {
    if (!d3.event.active) cbSimulation.alphaTarget(0.3).restart();
    d3.event.subject.fx = dragPosX = d3.event.subject.x;
    d3.event.subject.fy = dragPosY = d3.event.subject.y;

    mouseMoveDisabled = false;
    setNodeAsDragged(d3.event.subject);
  }

  function onDragged() {
    dragPosX += d3.event.dx / cbTransform.k;
    dragPosY += d3.event.dy / cbTransform.k;
    d3.event.subject.fx = Math.max(0, Math.min(cb.mapWidth, dragPosX));
    d3.event.subject.fy = Math.max(0, Math.min(cb.mapHeight, dragPosY));
  }

  function onDragEnded() {
    if (!d3.event.active) cbSimulation.alphaTarget(0);
    d3.event.subject.fx = null;
    d3.event.subject.fy = null;

    clearNodeAsDragged(d3.event.subject);
  }

  function onMouseMove() {
    if (mouseMoveDisabled) return;
    highlightNode();
  }

  function onClick() {
    var node = selectNode();
    if (node && !mouseMoveDisabled) {
      setNodeAsHighlight(node);
    }
    mouseMoveDisabled = !mouseMoveDisabled;

    if (!clickSend && node !== undefined) {
      if (typeof node.link !== 'undefined' && node.link !== "") {
        clickSend = true;
        //noinspection JSCheckFunctionSignatures
        parent.postMessage({'type': 'wiki_update', 'data': node.link}, "*");
        setTimeout(function () {
          clickSend = false;
        }, 250);
      }
    }
  }

  function onRightClick() {
    d3.event.preventDefault();

    var node = selectNode();
    if (node === undefined) {
      $('#graph_container_div').contextMenu({x: d3.event.clientX, y: d3.event.clientY});
    }
  }

  function onDoubleClick() {
    moveToNode();
  }

  function onKeyDown() {
    // Force movement stop with spacebar
    if (d3.event.keyCode === 32) {
      cbSimulation.stop();
    }
  }

  function highlightNode() {
    var node = selectNode();

    if (node) {
      setNodeAsHighlight(node);
    } else {
      clearNodeHighlight();
    }

    // Check if the event loop is running, if not, restart
    if (!d3.event.active) cbSimulation.restart();
  }

  function moveToNode(node) {
    // If necessary, transform the event location and find a corresponding node
    node = typeof(node) !== "undefined" ? node : selectNode();

    // Check for node existence
    if (node === undefined) return;

    // Stop simulation for now to prevent node walking
    mouseMoveDisabled = true;
    cbSimulation.stop();

    // Set clicked node as highlighted
    setNodeAsHighlight(node);

    // Find current locations of highlighted nodes
    var minX = cb.mapWidth, maxX = 0, minY = cb.mapHeight, maxY = 0;
    cbGraph.nodes.forEach(function (node) {
      if (!node.highlighted) return;
      minX = Math.min(minX, node.x - node.radius);
      maxX = Math.max(maxX, node.x + node.radius);
      minY = Math.min(minY, node.y - node.radius);
      maxY = Math.max(maxY, node.y + node.radius);
    });

    // Calculate scale
    var scale = 0.9 / Math.max((maxX - minX) / canvasWidth, (maxY - minY) / canvasHeight);

    // Calculate zoom identify
    var transform = d3.zoomIdentity
        .translate(halfCanvasWidth, halfCanvasHeight)
        .scale(scale)
        .translate(-(minX + maxX) / 2, -(minY + maxY) / 2);

    // Move to it
    cbCanvas
        .transition()
        .duration(3000)
        .call(cbZoom.transform, transform);
  }

  function zoomGraph() {
    cbTransform = limitTransform(d3.event.transform);
    drawGraph();
  }

  /******************************************************************************************************
   * Canvas draw methods
   *****************************************************************************************************/

  /**
   * @note Order in this function is important!
   */
  function drawGraph() {
    // Limit the nodes
    cbGraph.nodes.forEach(limitNode);

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
      context.strokeStyle = "black";
      context.stroke();

      context.beginPath();
      context.moveTo(0, 0);
      context.lineTo(canvasWidth, 0);
      context.lineTo(canvasWidth, canvasHeight);
      context.lineTo(0, canvasHeight);
      context.lineTo(0, 0);
      context.strokeStyle = "blue";
      context.stroke();
    }

    //////////////////////
    // NORMAL           //
    //////////////////////

    // Draw normal links
    context.beginPath();
    context.lineWidth = cb.linkLineWidth;
    context.strokeStyle = isDragging || highlightedNode !== null ? cb.fadedLinksStrokeStyle : cb.defaultLinkStrokeStyle;
    cbGraph.links.forEach(drawNormalLink);
    context.stroke();

    // Draw normal nodes
    context.beginPath();
    context.lineWidth = cb.nodeLineWidth;
    context.fillStyle = isDragging || highlightedNode !== null ? cb.fadedNodeFillStyle : cb.defaultNodeFillStyle;
    context.strokeStyle = isDragging || highlightedNode !== null ? cb.fadedNodeStrokeStyle : cb.defaultNodeStrokeStyle;
    cbGraph.nodes.forEach(drawNormalNode);
    context.fill();
    context.stroke();

    // Draw link nodes
    if (cb.drawLinkNodes) {
      context.beginPath();
      context.lineWidth = cb.nodeLineWidth;
      context.fillStyle = cb.fadedNodeFillStyle;
      context.strokeStyle = cb.fadedNodeStrokeStyle;
      cbGraph.linkNodes.forEach(drawNormalNode);
      context.fill();
      context.stroke();
    }

    // Draw normal link arrows
    context.fillStyle = isDragging || highlightedNode !== null ? cb.fadedLinksStrokeStyle : cb.defaultLinkStrokeStyle;
    cbGraph.links.forEach(drawNormalLinkArrow);

    //////////////////////
    // DRAGGED          //
    //////////////////////

    // Draw dragged links
    if (isDragging) {
      context.beginPath();
      context.lineWidth = cb.linkLineWidth;
      context.strokeStyle = cb.draggedLinkStrokeStyle;
      cbGraph.links.forEach(drawDraggedLink);
      context.stroke();
    }

    // Draw dragged nodes
    if (isDragging) {
      context.beginPath();
      context.lineWidth = cb.nodeLineWidth;
      context.fillStyle = cb.draggedNodeFillStyle;
      context.strokeStyle = cb.draggedNodeStrokeStyle;
      cbGraph.nodes.forEach(drawDraggedNode);
      context.fill();
      context.stroke();
    }

    // Draw dragged link arrows
    if (isDragging) {
      context.fillStyle = cb.draggedLinkStrokeStyle;
      cbGraph.links.forEach(drawDraggedLinkArrow);
    }

    //////////////////////
    // HIGHLIGHT        //
    //////////////////////

    // Draw highlighted links
    if (highlightedNode !== null) {
      context.beginPath();
      context.lineWidth = cb.linkLineWidth;
      context.strokeStyle = cb.highlightedLinkStrokeStyle;
      cbGraph.links.forEach(drawHighlightedLink);
      context.stroke();
    }

    // Draw highlighted nodes
    context.beginPath();
    context.lineWidth = cb.nodeLineWidth;
    context.fillStyle = cb.highlightedNodeFillStyle;
    context.strokeStyle = cb.highlightedNodeStrokeStyle;
    cbGraph.nodes.forEach(drawHighlightedNode);
    context.fill();
    context.stroke();

    // Draw highlighted link arrows
    if (highlightedNode !== null) {
      context.fillStyle = cb.highlightedLinkStrokeStyle;
      cbGraph.links.forEach(drawHighlightedLinkArrow);
    }

    //////////////////////
    // LABELS           //
    //////////////////////

    // Set this higher to prevent horns on M/W letters
    // https://github.com/CreateJS/EaselJS/issues/781
    context.miterLimit = 2.5;

    // Draw link labels
    if (isDragging || highlightedNode !== null) {
      context.fillStyle = cb.defaultNodeLabelColor;
      context.font = cb.defaultNodeLabelFont;
      context.textBaseline = 'top';
      context.lineWidth = cb.activeNodeLabelLineWidth;
      context.strokeStyle = cb.activeNodeLabelStrokeStyle;
      cbGraph.links.forEach(drawLinkText);
    }

    // Draw node labels
    context.fillStyle = cb.defaultNodeLabelColor;
    context.font = cb.defaultNodeLabelFont;
    context.textBaseline = 'middle';
    context.textAlign = 'center';
    context.lineWidth = cb.activeNodeLabelLineWidth;
    context.strokeStyle = cb.activeNodeLabelStrokeStyle;
    cbGraph.nodes.forEach(drawNodeText);

    // Restore state
    context.restore();
  }

  function drawLink(link) {
    context.moveTo(link.target.x, link.target.y);
    context.lineTo(link.source.x, link.source.y);
  }

  function drawNormalLink(link) {
    if (link.source.dragged || link.target.dragged) return;
    drawLink(link);
  }

  function drawDraggedLink(link) {
    if (link.source.dragged || link.target.dragged) drawLink(link);
  }

  function drawHighlightedLink(link) {
    if (link.source.index === highlightedNode.index || link.target.index === highlightedNode.index) drawLink(link);
  }

  function drawLinkText(link) {
    if (link.relationName &&
        (!isDragging && (link.source.index === highlightedNode.index || link.target.index === highlightedNode.index)) ||
        (link.source.dragged || link.target.dragged)) {

      // Calculate angle of label
      var startRadians = Math.atan((link.source.y - link.target.y) / (link.source.x - link.target.x));
      startRadians += (link.source.x >= link.target.x) ? Math.PI : 0;

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

  function drawLinkArrow(link) {
    var startRadians = Math.atan((link.source.y - link.target.y) / (link.source.x - link.target.x));
    startRadians += ((link.source.x >= link.target.x) ? -1 : 1) * Math.PI / 2;

    context.save();
    context.beginPath();
    context.translate(link.target.x, link.target.y);
    context.rotate(startRadians);
    context.moveTo(0, link.target.radius);
    context.lineTo(2, 5 + link.target.radius);
    context.lineTo(-2, 5 + link.target.radius);
    context.closePath();
    context.restore();
    context.fill();
  }

  function drawNormalLinkArrow(link) {
    if (link.target.dragged || link.source.dragged || link.target.highlighted) return;
    drawLinkArrow(link);
  }

  function drawDraggedLinkArrow(link) {
    if (link.target.dragged || link.source.dragged) drawLinkArrow(link);
  }

  function drawHighlightedLinkArrow(link) {
    if (link.target.index === highlightedNode.index || link.source.index === highlightedNode.index) drawLinkArrow(link);
  }

  function drawNode(node) {
    context.moveTo(node.x + node.radius, node.y);
    context.arc(node.x, node.y, node.radius, 0, 2 * Math.PI);
  }

  function drawNormalNode(node) {
    if (node.highlighted || node.dragged) return;
    drawNode(node);
  }

  function drawDraggedNode(node) {
    if (node.dragged) drawNode(node);
  }

  function drawHighlightedNode(node) {
    if (node.highlighted) drawNode(node);
  }

  function drawNodeText(node) {
    // Adjust font if necessary, or skip if not
    if ((isDragging && node.dragged) || ((highlightedNode !== null || isDragging) && node.highlighted)) {
      context.font = cb.activeNodeLabelFont;
    } else {
      if (isDragging || highlightedNode !== null) return;
    }

    var yStart = node.y - node.expandedLabelStart;
    node.expandedLabel.forEach(function (line) {
      if (node.dragged || node.highlighted) context.strokeText(line, node.x, yStart);
      context.fillText(line, node.x, yStart);
      yStart += cb.defaultNodeLabelFontSize;
    });

  }

  /******************************************************************************************************
   * Force functions
   *****************************************************************************************************/
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
   * Start execution after DOM load
   *****************************************************************************************************/
  $(function () {

    /******************************************************************************************************
     * Initialize canvas
     *****************************************************************************************************/
    canvas = document.getElementById('graph_container_canvas');
    resizeCanvas();

    /******************************************************************************************************
     * Start simulation
     *****************************************************************************************************/

    cbSimulation = d3.forceSimulation()
        .force("sidedetection", keepInBoxForce)
        .force("charge", d3.forceManyBody()
            .distanceMin(cb.manyBodyDistanceMin)
            .distanceMax(cb.manyBodyDistanceMax)
            .strength(cb.manyBodyStrength))
        .force("collide", d3.forceCollide()
            .radius(getNodeRadius)
            .strength(cb.collideStrength)
            .iterations(cb.collideIterations))
        .force("link", d3.forceLink()
            .distance(getLinkDistance)
            .strength(cb.linkStrength))
        .force("center", d3.forceCenter(cb.mapWidth / 2, cb.mapHeight / 2));

    d3.json(cb.data_source, function (error, data) {
      if (error) throw error;

      // Set graph global
      cbGraph = data;

      // Calculate some values for rendering
      cbGraph.nodes.forEach(getNodeRadius);
      cbGraph.nodes.forEach(function (node) {
        node.expandedLabelStart = 0;
        node.expandedLabel = [];
        if (node.label === "") return;

        // Calculate lines
        var lines = node.label.split(" ");

        if (lines.length <= 2 && node.label.length <= (cb.minCharCount + 1)) {
          node.expandedLabel = lines;
        } else {
          // Check if next line can be combined with the last line
          node.expandedLabel.push(lines[0]);
          for (var i = 1; i < lines.length; i++) {
            if (node.expandedLabel[node.expandedLabel.length - 1].length + lines[i].length <= cb.minCharCount) {
              node.expandedLabel[node.expandedLabel.length - 1] += " " + lines[i];
            } else {
              node.expandedLabel.push(lines[i]);
            }
          }
        }

        // Calculate offset for the amount of lines
        node.expandedLabelStart = (node.expandedLabel.length - 1) * (0.5 * cb.defaultNodeLabelFontSize);
      });

      // Create linkNodes to avoid overlap
      cbGraph.linkNodes = [];
      cbGraph.links.forEach(function (link) {
        cbGraph.linkNodes.push({
          source: cbGraph.nodes[link.source],
          target: cbGraph.nodes[link.target],
          linkNode: true
        });
      });

      // Load data
      cbSimulation.nodes(cbGraph.nodes.concat(cbGraph.linkNodes));
      // cbSimulation.nodes(cbGraph.nodes);
      cbSimulation.force("link").links(cbGraph.links);

      // Load handlers
      cbSimulation.on("tick", function () {

        // Update link node positions
        cbGraph.linkNodes.forEach(function (linkNode) {
          linkNode.x = (linkNode.source.x + linkNode.target.x) * 0.5;
          linkNode.y = (linkNode.source.y + linkNode.target.y) * 0.5;
        });

        // Draw graph
        drawGraph();
      });

      // Create zoom handler
      cbZoom = d3.zoom()
          .scaleExtent(cb.zoomExtent)
          .on("zoom", zoomGraph);

      // Create drag handler
      cbDrag = d3.drag()
          .container(canvas)
          .subject(selectNode)
          .on("start", onDragStarted)
          .on("drag", onDragged)
          .on("end", onDragEnded);

      // Add handlers to canvas
      cbCanvas = d3.select(canvas);
      cbCanvas
          .call(cbDrag)
          .call(cbZoom)
          .on('mousemove', onMouseMove)
          .on('click', onClick)
          .on("dblclick.zoom", onDoubleClick)
          .on('contextmenu', onRightClick);

      // Add window handler
      d3.select(window)
          .on("resize", resizeCanvas)
          .on("keydown", onKeyDown);
    });

    // Add message handler
    window.addEventListener('message', function (event) {
      var message = event.data;
      console.log(message);
      if (message.type === 'cb_update_opened') {
        cb.searchNode(message.data);
      }
    });

    // Define context menu
    //noinspection JSUnusedGlobalSymbols
    $.contextMenu({
      selector: '#graph_container_div',
      trigger: 'none',
      callback: function (key) {
        if (key === "quit") return;
        if (key.startsWith('style')) cb.applyStyle(key.substr(6));
      },
      items: {
        "styles": {
          name: "Change style",
          icon: "edit",
          items: {
            "style-itc": {name: "ITC"},
            "style-orange": {name: "Orange"},
            "style-blue": {name: "Blue"},
            "style-red": {name: "Red"},
            "style-green": {name: "Green"}
          }
        },
        "sep1": "---------",
        "quit": {name: "Close", icon: 'quit'}
      }
    });

  });

}(window.cb = window.cb || {}, jQuery, d3));
