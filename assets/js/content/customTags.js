/**
 * Custom tag parsing
 */
$(function () {
  /**
   * Abbreviations tags
   */
  let $abbreviations = $('ltb-abbr');
  if ($abbreviations.length > 0) {
    // Load abbreviation data
    let ids = $abbreviations.map((index, elem) => {
      return $(elem).data('abbr-id');
    }).toArray();

    $.get(Routing.generate('app_abbreviation_data', {_studyArea: currentStudyArea}), {
      ids: ids
    }).done((data) => {
      let dataIds = data.map((abbreviation) => {
        return abbreviation['id'];
      });
      let missingIds = $(ids).not(dataIds).get();

      $abbreviations.each((index, elem) => {
        let $elem = $(elem);

        let id = parseInt($elem.data('abbr-id'));
        if (-1 !== $.inArray(id, missingIds)){
          $elem.attr('title', 'Meaning unavailable...');
        } else {
          let abbreviation = data.filter((abbreviation) => {
            return abbreviation['id'] === id;
          })[0];

          if (abbreviation && abbreviation.hasOwnProperty('meaning')) {
            $elem.attr('title', abbreviation['meaning']);
          } else {
            $elem.attr('title', 'Meaning unavailable...');
          }
        }
      });
    }).fail(() => {
      $abbreviations.each((index, elem) => {
        $(elem).attr('title', 'Meaning unavailable...');
      });
    }).always(() => {
      $abbreviations.each((index, elem) => {
        $(elem).tooltip('dispose').tooltip();
      });
    });

    $abbreviations.each((index, elem) => {
      let $elem = $(elem);
      $elem.attr('title', 'Loading...');
    });

    $abbreviations.tooltip();
  }
});

