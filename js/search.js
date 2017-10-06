/* globals cb */

/**
 * Execute after DOM load
 */
$(function () {
  // Watermark the search field
  var watermark = 'Search';
  var $search = $('#search');

  // Add 'Search' watermark to the search box
  $search.val(watermark).addClass('watermark')
      .blur(function () {
        if ($(this).val().length === 0) {
          $(this).val(watermark).addClass('watermark');
        }
      })
      .focus(function () {
        if ($(this).val() === watermark) {
          $(this).val('').removeClass('watermark');
        }
      });

  // Get graph data for search
  var graph = (function () {
    var json = null;
    $.ajax({
      'async': false,
      'global': false,
      'url': cb.data_source,
      'dataType': 'json',
      'success': function (data) {
        json = data;
      }
    });
    return json;
  })();

  // Create search source
  var optArray = [];
  for (var i = 0; i < graph.nodes.length; i++) {
    optArray.push(graph.nodes[i].label);
  }
  optArray = optArray.sort();

  // Create the actual search functionality, which is a bootstrap type ahead
  $search.typeahead({
    source: optArray,
    minLength: 0,
    items: 10,
    fitToElement: true,
    showHintOnFocus: true
  });

  // Bind search result changed handler
  $search.change(function () {
    var current = $search.typeahead('getActive');
    if (current === $search.val()) {
      cb.searchNode(current);
    }
  });
});
