/**
 * $ has been defined globally in the app.js
 */
(function (annotations, $) {

  /** Selection constants */
  const dataStudyAreaId = 'annotations-study-area-id';
  const dataConceptId = 'annotations-concept-id';
  let $annotationsContainer = null;

  /** State parameters */
  let studyAreaId = 0;
  let conceptId = 0;
  let mouseDown = false;
  let currentSelection = null;
  let $currentSelectionContainer = null;

  /** Annotations button */
  let $annotationsButtons = null;
  const markerTextChar = "\ufeff";
  let hiddenMarkerElement, markerId = "sel_" + new Date().getTime() + "_" + Math.random().toString().substr(2);

  /** Annotations modal **/
  let $annotationsModal = null;

  /**
   * Annotation plugin loader, executed on page load.
   * This plugin does not work for IE<=8
   */
  $(function () {
    if (!window.Selection) {
      console.warn("Selection not available, not loading annotations");
      return;
    }
    currentSelection = window.getSelection();

    // Locate elements
    $annotationsContainer = $('.annotations-container').first();
    if ($annotationsContainer.length === 0) {
      return;
    }
    $annotationsButtons = $annotationsContainer.find('.annotations-buttons').first();
    if ($annotationsButtons.length === 0) {
      console.error("Annotation buttons not found!");
      return;
    }
    $annotationsModal = $annotationsContainer.find('.annotations-modal').first();
    if ($annotationsModal.length === 0) {
      console.error('Annotation modal not found!');
      return;
    }

    // Retrieve required concept id
    studyAreaId = parseInt($annotationsContainer.data(dataStudyAreaId));
    conceptId = parseInt($annotationsContainer.data(dataConceptId));

    // Register events on the window object
    $(window)
    // Hook into mouse events to show correct popup
        .on('mousedown', function () {
          mouseDown = true;
        })
        .on('mouseup', function () {
          mouseDown = false;
          renderAnnotationButton();
        })
        // Rerender on resize event, as the button will need to be repositioned if still shown
        .on('resize', renderAnnotationButton);

    // Register events on the annotation button
    $annotationsButtons
        .on('mousedown', function (e) {
          e.preventDefault();
          e.stopPropagation();
        });
    $annotationsButtons.find('.annotations-button-text').on('click', openTextAnnotationModel);
    $annotationsButtons.find('.annotations-button-mark').on('click', saveMarkAnnotation);

    // Register events on the annotation modal
    $annotationsModal.find('button.annotations-save').on('click', saveTextAnnotation);

    // Register on selectionchange event
    document.addEventListener('selectionchange', renderAnnotationButton);

    // Load current annotations from server
    loadAnnotations();
  });

  /**
   * Load all annotations for the current concept
   */
  function loadAnnotations() {
    // Retrieve the annotations
    $.ajax(
        {
          type: 'GET',
          url: Routing.generate('app_annotation_all', {_studyArea: studyAreaId, concept: conceptId}),
        })
        .done(renderAnnotations)
        .fail(function (err) {
          console.error("Error loading annotations");
        });
  }

  /**
   * Renders the annotation on the page on the correct places
   *
   * @param annotations Array of annotations
   */
  function renderAnnotations(annotations) {
    // Loop the annotations
    for (let i = 0; i < annotations.length; i++) {
      const annotation = annotations[i];
      console.info("Rendering annotation", annotation);

      // Find the context
      const $context = $('[data-annotations-context="' + annotation.context + '"]');
      if ($context.length === 0) {
        console.warn('Annotation context not found');
        continue;
      }

      // Check annotation version
      if (Date.parse($context.data('annotations-version')) > Date.parse(annotation.version)) {
        console.log("Outdated comment!");
      } else {
        // todo: render the annotation
      }
    }
  }

  /**
   * Render a annotation button
   * Based on https://stackoverflow.com/questions/1589721/how-can-i-position-an-element-next-to-user-text-selection
   */
  function renderAnnotationButton() {
    // Store current selection
    currentSelection = window.getSelection();

    // Check for range in selection. If there is none, nothing is selected
    if (currentSelection.rangeCount === 0) {
      $currentSelectionContainer = null;
      hideAnnotationButton();
      return;
    }
    let range = currentSelection.getRangeAt(0);

    // Only show when in annotations context, the selection is not collapsed and it actually contains text
    $currentSelectionContainer = $(range.commonAncestorContainer).closest('[data-annotations-context]');
    if ($currentSelectionContainer.length === 0 || currentSelection.isCollapsed || currentSelection.toString().trim().length === 0) {
      hideAnnotationButton();
      return;
    }

    // Don't update while the mouse is down
    if (mouseDown) {
      return;
    }

    // Clone range to not mess up user selection/make sure the pointer is at the end
    let clonedRange = range.cloneRange();
    clonedRange.collapse(false);

    // Create the marker element containing a single invisible character using DOM methods and insert it
    hiddenMarkerElement = document.createElement("span");
    hiddenMarkerElement.id = markerId;
    hiddenMarkerElement.appendChild(document.createTextNode(markerTextChar));
    hiddenMarkerElement.appendChild(document.createTextNode(markerTextChar));
    clonedRange.insertNode(hiddenMarkerElement);

    // If marker could not be created, ignore this selection
    if (!hiddenMarkerElement) {
      return
    }

    // Find hidden markerElement position http://www.quirksmode.org/js/findpos.html
    let obj = hiddenMarkerElement;
    let left = 0, top = obj.offsetHeight;

    // noinspection JSAssignmentUsedAsCondition This is the correct assignment for this loop
    do {
      left += obj.offsetLeft;
      top += obj.offsetTop;
    } while (obj = obj.offsetParent);

    // Move the button into place and show it
    $annotationsButtons.find('.fa').show();
    $annotationsButtons.find('.fa-spin').hide();
    $annotationsButtons.show();
    $annotationsButtons.offset({left: left - $annotationsButtons.width(), top: top});

    // Remove invisible marker
    hiddenMarkerElement.parentNode.removeChild(hiddenMarkerElement);
  }

  /**
   * Remove the annotation button, if any
   */
  function hideAnnotationButton() {
    if ($annotationsButtons && !mouseDown) {
      $annotationsButtons.hide();
    }
  }

  /**
   * Add a new annotation at the current selection
   */
  function openTextAnnotationModel() {
    $annotationsModal.modal();
    $annotationsModal.find('.annotations-selected-text').html(currentSelection.toString().trim());
    $annotationsModal.data('annotations-context', $currentSelectionContainer.data('annotations-context'));
    $annotationsModal.data('annotations-version', $currentSelectionContainer.data('annotations-version'));
    $annotationsModal.find('textarea').first().focus();
    $annotationsModal.find('.fa-plus').show();
    $annotationsModal.find('.fa-spin').hide();
  }

  /**
   * Save a new text annotation
   *
   * Note that the current selection is no longer set in this method!
   */
  function saveTextAnnotation() {
    let $modalButtons = $annotationsModal.find('button');
    $modalButtons.prop('disabled', true);
    $modalButtons.find('.fa-plus').hide();
    $modalButtons.find('.fa-spin').show();

    $.ajax(
        {
          type: "POST",
          url: Routing.generate('app_annotation_add', {_studyArea: studyAreaId, concept: conceptId}),
          data: {
            'text': $annotationsModal.find('textarea#annotation').val(),
            'context': $annotationsModal.data('annotations-context'),
            'start': 0, // todo: determine start/end
            'end': 1, // todo: determine start/end
            'selectedText': $annotationsModal.find('.annotations-selected-text').html(),
            'version': $annotationsModal.data('annotations-version')
          }
        })
        .done(function () {
          $annotationsModal.modal('hide');
        })
        .fail(function (err) {
          console.error('Error saving annotation');
        })
        .always(function () {
          $modalButtons.find('.fa-plus').show();
          $modalButtons.find('.fa-spin').hide();
          $modalButtons.prop('disabled', false);
        });
  }

  function saveMarkAnnotation() {
    $annotationsButtons.find('button').prop('disabled', true);
    $annotationsButtons.find('.fa-flag').hide();
    $annotationsButtons.find('.fa-spin').show();

    // Generate request
    $.ajax(
        {
          type: 'POST',
          url: Routing.generate('app_annotation_add', {_studyArea: studyAreaId, concept: conceptId}),
          data: {
            'context': $currentSelectionContainer.data('annotations-context'),
            'start': 0, // todo: determine start/end
            'end': 1, // todo: determine start/end
            'selectedText': currentSelection.toString().trim(),
            'version': $currentSelectionContainer.data('annotations-version')
          }
        })
        .done(function () {
          if (window.getSelection().empty) {  // Chrome
            window.getSelection().empty();
          } else if (window.getSelection().removeAllRanges) {  // Firefox
            window.getSelection().removeAllRanges();
          }
        })
        .fail(function (err) {
          console.error("Error saving annotations");
        })
        .always(function () {
          $('[data-toggle="tooltip"]').tooltip('hide');
          $annotationsButtons.find('.fa-flag').show();
          $annotationsButtons.find('.fa-spin').hide();
          $annotationsButtons.find('button').prop('disabled', false);
        })
  }
}(window.annotations = window.annotations || {}, $));
