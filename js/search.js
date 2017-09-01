/* globals data_source */
/* globals searchNode */

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
          searchNode(ui.item.label);
        }
      }
  );
});
