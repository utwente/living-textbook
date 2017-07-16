// Watermark the search field
var watermark = "Search";
$("#search")
    .val(watermark).addClass("watermark")
    .blur(function () {
      if ($(this).val().length === 0) {
        $(this).val(watermark).addClass("watermark");
      }
    })
    .focus(function () {
      if ($(this).val() === watermark) {
        $(this).val("").removeClass("watermark");
      }
    });

// Get graph data
var graph = (function () {
  var json = null;
  $.ajax({
    'async': false,
    'global': false,
    'url': data_source,
    'dataType': "json",
    'success': function (data) {
      json = data;
    }
  });
  return json;
})();
console.log(graph);

// Create search source
var optArray = [];
for (var i = 0; i < graph.nodes.length; i++) {
  optArray.push(graph.nodes[i].label);
}
optArray = optArray.sort();

// Create search functionality, which is a jquery-ui autocomplete
$(function () {
  $("#search").autocomplete(
      {
        source: optArray
      },
      {
        select: function (e, ui) {
          $("#search").val(ui.item.label);
          searchNode();
        }
      }
  )
});

/**
 * Finds the node selected by the autocomplete search tool
 */
function searchNode() {
  var selectedVal = $('#search').val();

  // Get all nodes
  var node = g.selectAll(".node");

  // Check if something was selected, if not, clear the stroke styles
  if (selectedVal === "none") {
    node.style("stroke", "white").style("stroke-width", "1");
    return;
  }

  // Get all link, linklabels and text
  var link = svg.selectAll(".link");
  var linklabel = svg.selectAll(".linklabel");
  var text = svg.selectAll("text");

  // Find the nodes and label that are not selected
  var selectedNode = node.filter(function (d) {
    return d.label !== selectedVal;
  });
  var selectedLabel = text.filter(function (d) {
    return d.label !== selectedVal;
  });

  // Find the selected node
  var focusNode = node.filter(function (d) {
    return d.label === selectedVal;
  });
  console.log(focusNode);

  // Find location in screen
  var graphContainer = $("#graph_container");
  var tr_x = graphContainer.width() / 2 - focusNode["0"]["0"].__data__.px;
  var tr_y = graphContainer.height() / 2 - focusNode["0"]["0"].__data__.py;

  // Transition the SVG in a 3 second transform to the correct location
  g.transition()
      .duration(3000)
      .attr("transform", "translate (" + tr_x + "," + tr_y + ")");

  // Set the opacity of all non-selected nodes, links and labels to 0.2
  selectedNode.style("opacity", "0.2");
  selectedLabel.style("opacity", "0.2");
  link.style("opacity", "0.2");
  linklabel.style("opacity", "0.2");

  // Reset the opacity to zero with a 7 second transition
  svg.selectAll(".node, .link, .linklabel, text").transition()
      .duration(7000)
      .style("opacity", 1);

  zoom.translate([tr_x, tr_y]);
  zoom.scale(1);
}
