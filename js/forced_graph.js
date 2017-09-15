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
  cb.mapWidth = 3000;
  cb.mapWidthDragMargin = cb.mapWidth / 30;
  cb.mapHeight = 1500;
  cb.mapHeightDragMargin = cb.mapHeight / 15;
  cb.baseNodeRadius = 5; // Node base radius
  cb.boundForceStrenght = 40;

  // Node styles
  cb.nodeLineWidth = 2;
  cb.defaultNodeFillStyle = '#b1ded2';
  cb.defaultNodeStrokeStyle = '#fff';
  cb.draggedNodeFillStyle = cb.defaultNodeFillStyle;
  cb.draggedNodeStrokeStyle = '#2359ff';
  cb.fadedNodeFillStyle = '#E6ECE4';
  cb.fadedNodeStrokeStyle = '#fff';
  cb.highlightedNodeFillStyle = cb.draggedNodeFillStyle;
  cb.highlightedNodeStrokeStyle = cb.draggedNodeStrokeStyle;

  // Link styles
  cb.defaultLinkStrokeStyle = '#696969';
  cb.draggedLinkStrokeStyle = '#333';
  cb.fadedLinksStrokeStyle = '#E0E0E0';
  cb.highlightedLinkStrokeStyle = cb.draggedLinkStrokeStyle;

  // Node label styles
  cb.defaultNodeLabelColor = '#000';
  cb.defaultNodeLabelFont = '10px san-serif';
  cb.draggedNodeLabelFont = 'bold ' + cb.defaultNodeLabelFont;
  cb.highlightedNodeLabelFont = cb.draggedNodeLabelFont;

  cb.drawGrid = true;
  cb.zoomExtent = [0.1, 8]; // [min,max] zoom, min is also limited by screen size

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
    "link": "",
    "numberOfLinks": 0,
    "dragged": false,
    "highlighted": false
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

  // Initialize the graph object
  cbGraph = {"nodes": [], "links": []};

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
    node.radius = cb.baseNodeRadius + 2 * (node.numberOfLinks ? parseInt(node.numberOfLinks) : 1);
    return node.radius;
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
    return cbSimulation.find(transformed.x, transformed.y, 20);
  }

  function onDragStarted() {
    if (!d3.event.active) cbSimulation.alphaTarget(0.3).restart();
    d3.event.subject.fx = dragPosX = d3.event.subject.x;
    d3.event.subject.fy = dragPosY = d3.event.subject.y;

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

    if (node !== undefined) {
      if (node.link !== false) {
        //noinspection JSCheckFunctionSignatures
        parent.postMessage({'type': 'wiki_update', 'data': node.link}, "*");
      }
    }
  }

  function onDoubleClick() {
    moveToNode();
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

    // Calculate zoom identify
    var transform = d3.zoomIdentity
        .translate(halfCanvasWidth, halfCanvasHeight)
        .scale(3)
        .translate(-node.x, -node.y);

    // Move to it
    cbCanvas
        .transition()
        .duration(3000)
        .call(cbZoom.transform, transform)
        .on('end', function () {
          mouseMoveDisabled = false;
        });
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

    // Draw normal links
    context.beginPath();
    context.strokeStyle = isDragging ? cb.fadedLinksStrokeStyle : cb.defaultLinkStrokeStyle;
    cbGraph.links.forEach(drawNormalLink);
    context.stroke();

    // Draw dragged links
    if (isDragging) {
      context.beginPath();
      context.strokeStyle = cb.draggedLinkStrokeStyle;
      cbGraph.links.forEach(drawDraggedLink);
      context.stroke();
    }

    // Draw highlighted links
    if (highlightedNode !== null) {
      context.beginPath();
      context.strokeStyle = cb.highlightedLinkStrokeStyle;
      cbGraph.links.forEach(drawHighlightedLink);
      context.stroke();
    }

    // Draw normal nodes
    context.beginPath();
    context.lineWidth = cb.nodeLineWidth;
    context.fillStyle = isDragging ? cb.fadedNodeFillStyle : cb.defaultNodeFillStyle;
    context.strokeStyle = isDragging ? cb.fadedNodeStrokeStyle : cb.defaultNodeStrokeStyle;
    cbGraph.nodes.forEach(drawNormalNode);
    context.fill();
    context.stroke();

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

    // Draw highlighted nodes
    context.beginPath();
    context.lineWidth = cb.nodeLineWidth;
    context.fillStyle = cb.highlightedNodeFillStyle;
    context.strokeStyle = cb.highlightedNodeStrokeStyle;
    cbGraph.nodes.forEach(drawHighlightedNode);
    context.fill();
    context.stroke();


    // Draw normal link arrows
    context.fillStyle = isDragging ? cb.fadedLinksStrokeStyle : cb.defaultLinkStrokeStyle;
    cbGraph.links.forEach(drawNormalLinkArrow);

    // Draw dragged link arrows
    if (isDragging) {
      context.fillStyle = cb.draggedLinkStrokeStyle;
      cbGraph.links.forEach(drawDraggedLinkArrow);
    }

    // Draw highlighted link arrows
    if (highlightedNode !== null) {
      context.fillStyle = cb.highlightedLinkStrokeStyle;
      cbGraph.links.forEach(drawHighlightedLinkArrow);
    }

    // Draw node labels
    context.fillStyle = cb.defaultNodeLabelColor;
    context.font = cb.defaultNodeLabelFont;
    context.textBaseline = 'middle';
    context.textAlign = 'center';
    cbGraph.nodes.forEach(drawNodeText);

    // Draw link labels
    if (isDragging || highlightedNode !== null) {
      context.fillStyle = cb.defaultNodeLabelColor;
      context.font = cb.defaultNodeLabelFont;
      context.textBaseline = 'top';
      cbGraph.links.forEach(drawLinkText);
    }

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
    if (link.source.highlighted && link.target.highlighted) drawLink(link);
  }

  function drawLinkText(link) {
    if ((!isDragging && link.source.highlighted && link.target.highlighted) ||
        (link.source.dragged || link.target.dragged)) {
      if (link.relationName) {
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
          context.fillText(link.relationName, -(getNodeRadius(link.source) + 5), 0, getLinkLabelLength(link));
        } else {
          context.textAlign = 'left';
          context.fillText(link.relationName, getNodeRadius(link.source) + 5, 0, getLinkLabelLength(link));
        }

        // Restore context
        context.restore();
      }
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
    if (link.target.highlighted) drawLinkArrow(link);
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
    if (isDragging && node.dragged) {
      context.font = cb.draggedNodeLabelFont;
      context.fillText(node.label, node.x, node.y);
      return;
    }
    if ((highlightedNode !== null || isDragging) && node.highlighted) {
      context.font = cb.highlightedNodeLabelFont;
      context.fillText(node.label, node.x, node.y);
      return;
    }

    if (!isDragging && highlightedNode === null) context.fillText(node.label, node.x, node.y);
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
        .force("charge", d3.forceManyBody().distanceMin(20).strength(-90))
        .force("collide", d3.forceCollide().radius(getNodeRadius).strength(0.99).iterations(2))
        .force("link", d3.forceLink().distance(getLinkDistance).strength(0.5))
        .force("center", d3.forceCenter(cb.mapWidth / 2, cb.mapHeight / 2));

    d3.json(cb.data_source, function (error, data) {
      if (error) throw error;

      // Set graph global
      cbGraph = data;

      cbGraph.nodes.forEach(getNodeRadius);

      // Load data
      cbSimulation.nodes(cbGraph.nodes);
      cbSimulation.force("link").links(cbGraph.links);

      // Load handlers
      cbSimulation.on("tick", drawGraph);

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
          .on('contextmenu', function () {
            d3.event.preventDefault();
            d3.event.stopPropagation();
          });

      // Add window handler
      d3.select(window)
          .on("resize", resizeCanvas);
    });

    window.addEventListener('message', function (event) {
      var message = event.data;
      console.log(message);
      if (message.type === 'cb_update_opened') {
        cb.searchNode(message.data);
      }
    });

  });

}(window.cb = window.cb || {}, jQuery, d3));
