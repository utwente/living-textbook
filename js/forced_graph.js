// Initialize viewport size
var graphContainer = $('#graph_container');
var w = graphContainer.width();
var h = graphContainer.height();

// Initialize variables
var keyC = true, keyS = true, keyT = true, keyR = true, keyX = true, keyD = true, keyL = true, keyM = true, keyH = true,
    key1 = true, key2 = true, key3 = true, key0 = true;
var focus_node = null, highlight_node = null;
var text_center = false;
var outline = false;
var min_score = 0;
var max_score = 1;
var color = d3.scale.linear()
    .domain([min_score, (min_score + max_score) / 2, max_score])
    .range(["lime", "yellow", "red"]);
var highlight_color = "blue";
var highlight_trans = 0.1;
var size = d3.scale.pow().exponent(1)
    .domain([1, 100])
    .range([10, 24]);
var force = d3.layout.force()
    .linkDistance(20)
    .charge(-3000)
    .size([w, h]);
var default_node_color = "#b1ded2";
var default_link_color = "#696969";
var nominal_base_node_size = 10;
var nominal_text_size = 12;
var max_text_size = 24;
var nominal_stroke = 1.5;
var max_stroke = 4.5;
var max_base_node_size = 36;
var min_zoom = 0.15;
var max_zoom = 2;

// Create the svg image containing the graph
var svg = d3.select("#graph_container").append("svg");
var zoom = d3.behavior.zoom().scaleExtent([min_zoom, max_zoom]);
var g = svg.append("g");
svg.style("cursor", "move");

// Load json data
d3.json("data/GIS_RS.json", function (error, graph) {
  var linkedByIndex = {};
  graph.links.forEach(function (d) {
    linkedByIndex[d.source + "," + d.target] = true;
  });

  function isConnected(a, b) {
    return linkedByIndex[a.index + "," + b.index] || linkedByIndex[b.index + "," + a.index] || a.index === b.index;
  }

  function hasConnections(a) {
    for (var property in linkedByIndex) {
      var s = property.split(",");
      if ((s[0] === a.index || s[1] === a.index) && linkedByIndex[property])          return true;
    }
    return false;
  }

  force
      .nodes(graph.nodes)
      .links(graph.links)
      .start();

  var link = g.selectAll(".link")
      .data(graph.links)
      .enter().append("line")
      .attr("class", "link")
      .style("pointer-events", "none")
      .attr('marker-end', 'url(#arrowhead)')
      .style("stroke-width", nominal_stroke)
      .style("stroke", default_link_color);

  var node = g.selectAll(".node")
      .data(graph.nodes)
      .enter().append("g")
      .attr("class", "node")
      .call(force.drag);

  // On doubleclick, move the node to the center of the screen
  node.on("dblclick.zoom", function (d) {
    d3.event.stopPropagation();
    var dcx = graphContainer.width() / 2 - d.x * zoom.scale();
    var dcy = graphContainer.height() / 2 - d.y * zoom.scale();
    zoom.translate([dcx, dcy]);
    g.transition()
        .duration(2000)
        .attr("transform", "translate(" + dcx + "," + dcy + ")scale(" + zoom.scale() + ")");
  });

  // Notify other screen of click
  node.on("click", function (d) {
    if (d.link !== false) {
      crosstab.broadcast('wiki_update', d.link);
    }
  });

  var tocolor = "fill";
  var towhite = "stroke";
  if (outline) {
    tocolor = "stroke";
    towhite = "fill"
  }

  var circle = node
      .append("path")
      .attr("d", d3.svg.symbol()
          .size(function (d) {
            return Math.PI * Math.pow(size(d.size) || nominal_base_node_size, 2);
          })
          .type(function (d) {
            return d.type;
          }))

      .style(tocolor, function (d) {
        if (isNumber(d.score) && d.score >= 0) return color(d.score);
        else return default_node_color;
      })
      //.attr("r", function(d) { return size(d.size)||nominal_base_node_size; })
      .style("stroke-width", nominal_stroke)
      .style(towhite, "white");

  var linkpaths = g
      .selectAll(".linkpath")
      .data(graph.links)
      .enter()
      .append('path')
      .attr({
        'd': function (d) {
          return 'M ' + d.source.x + ' ' + d.source.y + ' L ' + d.target.x + ' ' + d.target.y
        },
        'class': 'linkpath',
        'fill-opacity': 0,
        'stroke-opacity': 0,
        'id': function (d, i) {
          return 'linkpath' + i
        }
      })
      .style("pointer-events", "none");

  var text = g
      .selectAll(".text")
      .data(graph.nodes)
      .enter().append("text")
      .attr("dy", ".35em")
      .attr("class", "nodeLabel")
      .style("font-size", nominal_text_size + "px");

  // Center text
  if (text_center) {
    text
        .text(function (d) {
          return d.label;
        })
        .style("text-anchor", "middle");
  } else {
    text
        .attr("dx", function (d) {
          return (size(d.size) || nominal_base_node_size);
        })
        .text(function (d) {
          return '\u2002' + d.label;
        });
  }

  var linklabels = g
      .selectAll(".linklabel")
      .data(graph.links)
      .enter()
      .append('text')
      .style("pointer-events", "none")
      .attr({
        'class': 'linklabel',
        'id': function (d, i) {
          return 'linklabel' + i
        },
        'dx': 30,
        'dy': -3,
        'font-size': 10,
        'fill': '#100c14'
      });

  svg
      .append('defs')
      .append('marker')
      .attr({
        'id': 'arrowhead',
        'viewBox': '-0 -5 10 10',
        'refX': 23,
        'refY': 0,
        'orient': 'auto',
        'markerWidth': 5,
        'markerHeight': 5,
        'xoverflow': 'visible'
      })
      .append('path')
      .attr('d', 'M 0,-5 L 10 ,0 L 0,5')
      .attr('fill', '#696969')
      .attr('stroke', '#696969');

  svg
      .append('defs')
      .append('marker')
      .attr({
        'id': 'arrowheadSelected',
        'viewBox': '-0 -5 10 10',
        'refX': 23,
        'refY': 0,
        //'markerUnits':'strokeWidth',
        'orient': 'auto',
        'markerWidth': 5,
        'markerHeight': 5,
        'xoverflow': 'visible'
      })
      .append('path')
      .attr('d', 'M 0,-5 L 10 ,0 L 0,5')
      .attr('fill', 'blue')
      .attr('stroke', 'blue');


  // Disable contextmenu (right click)
  node.on("contextmenu", function (d) {
    d3.event.preventDefault();
    d.fixed = true;
  });

  // Node actions
  node
      .on("mouseover", function (d) {
        // When the mouse is over the node, set the highlight
        set_highlight(d);
      })

      .on("mousedown", function (d) {
        // On mousedown, set the focus on the node.
        d3.event.stopPropagation();
        focus_node = d;
        set_focus(d);

        // Set the highlight if not already set
        if (highlight_node === null) set_highlight(d);
      })

      .on("mouseout", function () {
        // When the mouse is no longer over the node, clear the highlight
        exit_highlight();
      });


  // On mouseup, reset the highlight
  d3.select(window).on("mouseup",
      function () {
        if (focus_node !== null) {
          focus_node = null;
          if (highlight_trans < 1) {
            circle.style("opacity", 1);
            text.style("opacity", 1);
            link.style("opacity", 1);
          }
        }

        if (highlight_node === null) exit_highlight();
      });


  function set_focus(d) {
    if (highlight_trans < 1) {
      circle.style("opacity", function (o) {
        return isConnected(d, o) ? 1 : highlight_trans;
      });

      text.style("opacity", function (o) {
        return isConnected(d, o) ? 1 : highlight_trans;
      });

      link.style("opacity", function (o) {
        return o.source.index === d.index || o.target.index === d.index ? 1 : highlight_trans;
      });
    }
  }

  function set_highlight(d) {
    svg.style("cursor", "pointer");

    if (focus_node !== null) d = focus_node;
    highlight_node = d;
    if (highlight_color !== "white") {

      circle
          .style(towhite, function (o) {
            return isConnected(d, o) ? highlight_color : "white";
          })
          .style("opacity", function (o) {
            return isConnected(d, o) ? 1 : 0.3
          });

      text
          .style("font-weight", function (o) {
            return isConnected(d, o) ? "bold" : "normal";
          })
          .style("opacity", function (o) {
            return isConnected(d, o) ? 1 : 0.3
          });

      link
          .style("stroke", function (o) {
            return o.source.index === d.index || o.target.index === d.index ? highlight_color : default_link_color;
          })
          .style("opacity", function (o) {
            return o.source.index === d.index || o.target.index === d.index ? 1 : 0.3
          })
          .attr('marker-end', function (o) {
            return o.source.index === d.index || o.target.index === d.index ? 'url(#arrowheadSelected)' : 'url(#arrowhead)'
          });

      linklabels
          .append('textPath')
          .attr('xlink:href', function (d, i) {
            return '#linkpath' + i
          })
          .style("pointer-events", "none")
          .text(function (d) {
            return d.relationName
          })
          .style("visibility", function (o) {
            if (o.source.index === d.index || o.target.index === d.index) {
              return "visible";
            }
            else {
              return "hidden";
            }
          });
    }
  }

  function exit_highlight() {
    highlight_node = null;
    if (focus_node === null) {
      svg.style("cursor", "move");
      if (highlight_color !== "white") {
        circle
            .style(towhite, "white")
            .style("opacity", 1);

        text
            .style("font-weight", "normal")
            .style("opacity", 1);

        link
            .style("stroke", function (o) {
              return (isNumber(o.score) && o.score >= 0) ? color(o.score) : default_link_color
            })
            .style("opacity", 1)
            .attr('marker-end', 'url(#arrowhead)');

        linklabels
            .text(function () {
              return ""
            });
      }
    }
  }

  // On zoom
  zoom.on("zoom", function () {

    var stroke = nominal_stroke;
    if (nominal_stroke * zoom.scale() > max_stroke) stroke = max_stroke / zoom.scale();
    link.style("stroke-width", stroke);
    circle.style("stroke-width", stroke);

    var base_radius = nominal_base_node_size;
    if (nominal_base_node_size * zoom.scale() > max_base_node_size) base_radius = max_base_node_size / zoom.scale();
    circle
        .attr("d", d3.svg.symbol()
        .size(function (d) {
          return Math.PI * Math.pow(size(d.size) * base_radius / nominal_base_node_size || base_radius, 2);
        })
        .type(function (d) {
          return d.type;
        }));

    //circle.attr("r", function(d) { return (size(d.size)*base_radius/nominal_base_node_size||base_radius); })
    if (!text_center) text.attr("dx", function (d) {
      return (size(d.size) * base_radius / nominal_base_node_size || base_radius);
    });

    var text_size = nominal_text_size;
    if (nominal_text_size * zoom.scale() > max_text_size) text_size = max_text_size / zoom.scale();
    text.style("font-size", text_size + "px");
    g.attr("transform", "translate(" + d3.event.translate + ")scale(" + d3.event.scale + ")");
  });

  svg.call(zoom);
  svg.on("mouseover", function () {
    updateLinks();
  });


  /**
   * Update the links
   */
  function updateLinks() {
    node
        .attr("transform", function (d) {
          return "translate(" + d.x + "," + d.y + ")";
        });
    text
        .attr("transform", function (d) {
          return "translate(" + d.x + "," + d.y + ")";
        });

    link
        .attr("x1", function (d) {
          return d.source.x;
        })
        .attr("y1", function (d) {
          return d.source.y;
        })
        .attr("x2", function (d) {
          return d.target.x;
        })
        .attr("y2", function (d) {
          return d.target.y;
        });

    node
        .attr("cx", function (d) {
          return d.x;
        })
        .attr("cy", function (d) {
          return d.y;
        });

    linkpaths
        .attr('d', function (d) {
          // Return the path
          return 'M ' + d.source.x + ' ' + d.source.y + ' L ' + d.target.x + ' ' + d.target.y;
        });

    linklabels
        .attr('transform', function (d) {
          if (d.target.x < d.source.x) {
            var bbox = this.getBBox();
            var rx = bbox.x + bbox.width / 2;
            var ry = bbox.y + bbox.height / 2;
            return 'rotate(180 ' + rx + ' ' + ry + ')';
          }
          else {
            return 'rotate(0)';
          }
        });
  }

  // Resize window
  onResize();

  // Bind window handlers
  d3.select(window)
      .on("resize", onResize)
      .on("keydown", onKeyDown);

  // Force link update on tick
  force.on("tick", function () {
    updateLinks();
  });

  function onResize() {
    var width = graphContainer.width(),
        height = graphContainer.height();
    svg.attr("width", width).attr("height", height);

    force.size([force.size()[0] + (width - w) / zoom.scale(), force.size()[1] + (height - h) / zoom.scale()]).resume();
    w = width;
    h = height;
  }

  /**
   * Function to handle the keydown event
   */
  function onKeyDown() {
    // Force movement stop with spacebar
    if (d3.event.keyCode === 32) {
      force.stop();
    }

    // Check other keycodes for show/hide actions
    // @note Currenlty not functional, except for the 0 key
    else if (d3.event.keyCode >= 48 && d3.event.keyCode <= 90 && !d3.event.ctrlKey && !d3.event.altKey && !d3.event.metaKey) {
      switch (String.fromCharCode(d3.event.keyCode)) {

          // Display node based on type
        case "C":
          keyC = !keyC;
          break;
        case "S":
          keyS = !keyS;
          break;
        case "T":
          keyT = !keyT;
          break;
        case "R":
          keyR = !keyR;
          break;
        case "X":
          keyX = !keyX;
          break;
        case "D":
          keyD = !keyD;
          break;

          // Display node by score
        case "L":
          keyL = !keyL;
          break;
        case "M":
          keyM = !keyM;
          break;
        case "H":
          keyH = !keyH;
          break;

          // Display links by score
        case "1":
          key1 = !key1;
          break;
        case "2":
          key2 = !key2;
          break;
        case "3":
          key3 = !key3;
          break;

          // Display node/node text
        case "0":
          key0 = !key0;
          break;
      }

      // Hide/show links based on types and scores
      link.style("display", function (d) {
        var flag = vis_by_type(d.source.type) && vis_by_type(d.target.type) && vis_by_node_score(d.source.score) && vis_by_node_score(d.target.score) && vis_by_link_score(d.score);
        linkedByIndex[d.source.index + "," + d.target.index] = flag;
        return flag ? "inline" : "none";
      });

      // Hide/show node and node text based on the 0 key
      node.style("display", function (d) {
        return (key0 || hasConnections(d)) && vis_by_type(d.type) && vis_by_node_score(d.score) ? "inline" : "none";
      });
      text.style("display", function (d) {
        return (key0 || hasConnections(d)) && vis_by_type(d.type) && vis_by_node_score(d.score) ? "inline" : "none";
      });

      // Check highlight state
      if (highlight_node !== null) {
        if ((key0 || hasConnections(highlight_node)) && vis_by_type(highlight_node.type) && vis_by_node_score(highlight_node.score)) {
          if (focus_node !== null) set_focus(focus_node);
          set_highlight(highlight_node);
        }
        else {
          exit_highlight();
        }
      }
    }
  }
});

/**
 * Return whether or not the type should be visible.
 * Is based on the state of the C/S/T/R/X/D keys
 *
 * @param type
 * @returns {boolean}
 */
function vis_by_type(type) {
  // Check if undefined
  if (type === undefined) {
    return true;
  }

  switch (type) {
    case "circle":
      return keyC;
    case "square":
      return keyS;
    case "triangle-up":
      return keyT;
    case "diamond":
      return keyR;
    case "cross":
      return keyX;
    case "triangle-down":
      return keyD;
    default:
      return true;
  }
}

/**
 * Return whether or not the node should be visible
 * Is based on the state of the H/M/L keys
 * @param score
 * @returns {boolean}
 */
function vis_by_node_score(score) {
  // Check score if it is a number
  if (isNumber(score)) {
    if (score >= 0.666) return keyH;
    else if (score >= 0.333) return keyM;
    else if (score >= 0) return keyL;
  }

  return true;
}

/**
 * Return whether or not the link should be visible
 * Is based on the state of the 1/2/3 keys
 *
 * @param score
 * @returns {boolean}
 */
function vis_by_link_score(score) {
  // Check score if it is a number
  if (isNumber(score)) {
    if (score >= 0.666) return key3;
    else if (score >= 0.333) return key2;
    else if (score >= 0) return key1;
  }

  return true;
}

/**
 * Check whether the given parameter is a valid number
 * @param n
 * @returns {boolean}
 */
function isNumber(n) {
  // Check for undefined first
  if (n === undefined) return false;

  // Try to parse the number
  return !isNaN(parseFloat(n)) && isFinite(n);
}
