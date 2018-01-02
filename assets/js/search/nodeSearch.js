(function (nodeSearch, $) {
  // Watermark the search field
  var watermark = 'Search';

  // Create function to create and/or update the searcher
  nodeSearch.createSearch = function ($search, data, isUrl) {
    if (typeof data === 'undefined') {
      throw 'No data given!';
    }
    isUrl = typeof isUrl !== 'undefined' ? isUrl : false;

    if (isUrl) {
      // Get graph data for search
      data = (function () {
        var json = null;
        $.ajax({
          'async': false,
          'global': false,
          'url': data,
          'dataType': 'json',
          'success': function (data) {
            json = data;
          }
        });
        return json;
      })();
    }

    // Create search source
    var optArray = [];
    for (var i = 0; i < data.nodes.length; i++) {
      optArray.push(data.nodes[i].label);
    }
    optArray = optArray.sort();

    // Destroy previous typeahead, if any
    if (typeof $search.data('typeahead') === 'object') {
      $search.typeahead('destroy');
    }

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

    // Bind search result changed handler
    $search.change(function () {
      var current = $search.typeahead('getActive');
      if (current === $search.val()) {
        // @todo Reimplement this module
        cb.searchNode(current);
      }
    });

    // Create the actual search functionality, which is a bootstrap typeahead
    $search.typeahead({
      source: optArray,
      minLength: 0,
      items: 10,
      fitToElement: true,
      showHintOnFocus: true
    });
  };
}(window.nodeSearch = window.nodeSearch || {}, $));
