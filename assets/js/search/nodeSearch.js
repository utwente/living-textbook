(function (nodeSearch, $) {
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

    // Destroy previous typeahead, if any
    if (typeof $search.data('typeahead') === 'object') {
      $search.typeahead('destroy');
    }

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
      source: data,
      minLength: 0,
      items: 10,
      showHintOnFocus: "all"
    });
  };
}(window.nodeSearch = window.nodeSearch || {}, $));
