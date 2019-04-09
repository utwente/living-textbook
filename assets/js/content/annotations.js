/**
 * $ has been defined globally in the app.js
 */
(function (annotations, $) {

  /** Selection constants */
  const dataStudyAreaId = 'annotations-study-area-id';
  const dataConceptId = 'annotations-concept-id';
  let $annotationsContainer;

  /** State parameters */
  let studyAreaId;
  let conceptId;
  let mouseDown = false;

  /** Annotations button */
  let $annotationButton = null;
  const markerTextChar = "\ufeff";
  let hiddenMarkerElement, markerId = "sel_" + new Date().getTime() + "_" + Math.random().toString().substr(2);

  /**
   * Annotation plugin loader, executed on page load.
   * This plugin does not work for IE<=8
   */
  $(function () {
    if (!window.Selection) {
      console.warn("Selection not available, not loading annotations");
      return;
    }

    // Locate element
    $annotationsContainer = $('.annotations-container').first();
    if ($annotationsContainer.length === 0) {
      return;
    }

    // Retrieve required concept id
    studyAreaId = parseInt($annotationsContainer.data(dataStudyAreaId));
    conceptId = parseInt($annotationsContainer.data(dataConceptId));

    // Rerender annotation button if required
    $(window)
        .on('resize', renderAnnotationButton)
        .on('mousedown', function () {
          mouseDown = true;
        })
        .on('mouseup', function () {
          mouseDown = false;
          renderAnnotationButton();
        });

    // Register mouseup behavior
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
    const doc = window.document;
    let selection = window.getSelection();
    let range = selection.getRangeAt(0);

    // Only show when in annotations context, the selection is not collapsed and it actually contains text
    if ($(range.commonAncestorContainer).closest('[data-annotations-context]').length === 0
        || selection.isCollapsed || selection.toString().trim().length === 0) {
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
    hiddenMarkerElement = doc.createElement("span");
    hiddenMarkerElement.id = markerId;
    hiddenMarkerElement.appendChild(doc.createTextNode(markerTextChar));
    clonedRange.insertNode(hiddenMarkerElement);

    // If marker could not be created, ignore this selection
    if (!hiddenMarkerElement) {
      return
    }

    // Lazily create element to be placed next to the selection
    if (!$annotationButton) {
      $annotationButton = $('<div>');
      $annotationButton.html("selection");
      $annotationButton.css('background-color', 'yellow');
      $annotationButton.css('position', 'absolute');
      $annotationButton.appendTo($annotationsContainer);
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
    $annotationButton.show();
    $annotationButton.offset({left: left - $annotationButton.width(), top: top});

    // Remove invisible marker
    hiddenMarkerElement.parentNode.removeChild(hiddenMarkerElement);
  }

  /**
   * Remove the annotation button, if any
   */
  function hideAnnotationButton() {
    if ($annotationButton) {
      $annotationButton.hide();
    }
  }
}(window.annotations = window.annotations || {}, $));
