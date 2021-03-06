(function (conceptSearch) {
  // Create function to create and/or update the searcher
  conceptSearch.createSearch = function ($search, data) {
    if (typeof $search === 'undefined') {
      throw new Error('Search field not given!');
    }
    if (typeof data === 'undefined') {
      throw new Error('No data given!');
    }

    // Destroy previous typeahead, if any
    if (typeof $search.data('typeahead') === 'object') {
      $search.typeahead('destroy');
    }

    // Bind search result changed handler
    $search.change(function () {
      const current = $search.typeahead('getActive');
      if (current && current.name === $search.val()) {
        cb.moveToConceptById(current.id);
      }
    });

    // Bind click handler
    $search.on('click', function () {
      cb.closeFilters();
    });

    // Create the actual search functionality, which is a bootstrap typeahead
    $search.typeahead({
      source: data,
      minLength: 0,
      items: 10,
      showHintOnFocus: 'all',
      displayText: function (item) {
        // Fix js error when name is empty (013-LIVING-TEXTBOOK-K, #281)
        // Use space character to render span height correctly
        return item.name || ' ';
      }
    });
  };
}(window.conceptSearch = window.conceptSearch || {}));
