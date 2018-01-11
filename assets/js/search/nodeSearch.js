(function (nodeSearch) {
  // Create function to create and/or update the searcher
  nodeSearch.createSearch = function ($search, data) {
    if (typeof $search === 'undefined') {
      throw 'Search field not given!';
    }
    if (typeof data === 'undefined') {
      throw 'No data given!';
    }

    // Destroy previous typeahead, if any
    if (typeof $search.data('typeahead') === 'object') {
      $search.typeahead('destroy');
    }

    // Bind search result changed handler
    $search.change(function () {
      var current = $search.typeahead('getActive');
      if (current.name === $search.val()) {
        cb.searchNodeById(current.id);
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
}(window.nodeSearch = window.nodeSearch || {}));
