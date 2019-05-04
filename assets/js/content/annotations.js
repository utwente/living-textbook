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
  const annotationsData = {
    current: null,
    working: false,
    onButton: false,
    containsText: false,
    selectedText: '', // When containsText is set to false, this holds the title
    start: 0,
    end: 0,
    context: null,
    version: null
  };

  /** Annotations button */
  let $annotationsButtons = null;
  const markerTextChar = "\ufeff";
  let hiddenMarkerElement, markerId = "sel_" + new Date().getTime() + "_" + Math.random().toString().substr(2);
  let hideAnnotationsButtonTimeout = null;

  /** Annotations modal **/
  let $annotationsModal = null;
  let $failedModal = null;

  /**
   * Annotation plugin loader, executed on page load.
   * This plugin does not work for IE<=8
   */
  $(function () {
    if (!window.Selection) {
      console.warn("Selection not available, not loading annotations");
      return;
    }

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
    $failedModal = $annotationsContainer.find('.failed-modal').first();
    if ($failedModal.length === 0) {
      console.error('Failed modal not found!');
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
          renderAnnotationButtonForSelection();
        })
        // Reposition on resize event, as the button will need to be repositioned if still shown
        .on('resize', repositionAnnotationButtons);

    // Find headers which can be annotated, to register hover action
    $annotationsContainer.find('h2[data-annotations-context]')
        .hover(renderAnnotationButtonForHeader, hideAnnotationButtons);

    // Register events on the annotation button
    $annotationsButtons
        .on('mousedown', function (e) {
          e.preventDefault();
          e.stopPropagation();
        })
        .hover(function () {
          annotationsData.onButton = true;
        }, function () {
          annotationsData.onButton = false;
        });
    $annotationsButtons.find('.annotations-button-text').on('click', openTextAnnotationModel);
    $annotationsButtons.find('.annotations-button-mark').on('click', saveMarkAnnotation);

    // Register events on the annotation modal
    $annotationsModal.find('button.annotations-save').on('click', saveTextAnnotation);
    $annotationsModal.find('button.annotations-cancel').on('click', function () {
      annotationsData.working = false;
      repositionAnnotationButtons();
    });

    // Register on selectionchange event
    document.addEventListener('selectionchange', renderAnnotationButtonForSelection);

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

      if (annotation.start === -1 || typeof annotation.selectedText == "undefined") {
        // todo Header annotation
      } else {
        renderTextAnnotation(annotation);
      }
    }
  }

  /**
   * Renders the text annotation in the text on the correct place
   *
   * @param annotation
   */
  function renderTextAnnotation(annotation) {
    const $context = $('[data-annotations-contains-text="true"][data-annotations-context="' + annotation.context + '"]');
    if ($context.length === 0) {
      console.warn('Annotation context not found');
      return;
    }

    // Check annotation version
    if (Date.parse($context.data('annotations-version')) > Date.parse(annotation.version)) {
      console.log("Outdated comment!");

      // todo: render outdated annotations at top?
    } else {
      $context.markRanges([{
        start: annotation.start,
        length: annotation.end - annotation.start
      }], {
        className: 'ltb-annotation-' + annotation.id + ' ' + (typeof annotation.text == "undefined" ? "mark" : "note"),
        acrossElements: true,
        accuracy: "exactly",
        caseSensitive: true,
        separateWordSearch: false,
        ignoreJoiners: true,
        each: function (node) {
          node.setAttribute('data-annotation', JSON.stringify(annotation));
          node.setAttribute('data-annotation-id', annotation.id);
          node.setAttribute('data-annotation-text', annotation.text);
        },
        done: function () {
          $context.find('mark').each(function () {
            if (this.innerHTML.trim() === "") {
              $(this).remove();
            }
          });
        }
      });
    }
  }

  /**
   * Reposition the rendered buttons
   */
  function repositionAnnotationButtons() {
    if (annotationsData.current === 'text') {
      renderAnnotationButtonForSelection();
    } else if (annotationsData.current === 'header') {
      // Reposition data is not available for header buttons, so close it directly
      annotationsData.current = null;
      hideAnnotationButtons();
    }
  }

  /**
   * Render an annotation button for a context header
   */
  function renderAnnotationButtonForHeader() {
    // Don't do anything while working
    if (annotationsData.working) {
      return;
    }

    // Find hidden markerElement position http://www.quirksmode.org/js/findpos.html
    const $hoveredHeader = $(this);
    if ($hoveredHeader.length === 0) {
      return;
    }

    // Clear current selection, if any
    window.getSelection().removeAllRanges();

    // Find hidden markerElement position http://www.quirksmode.org/js/findpos.html
    let obj = $hoveredHeader.find('.header-marker')[0];
    let left = 0, top = obj.offsetHeight;

    // noinspection JSAssignmentUsedAsCondition This is the correct assignment for this loop
    do {
      left += obj.offsetLeft;
      top += obj.offsetTop;
    } while (obj = obj.offsetParent);

    // Update state
    annotationsData.current = 'header';
    annotationsData.containsText = false;
    annotationsData.selectedText = $hoveredHeader.text().trim();
    annotationsData.start = -1;
    annotationsData.end = 0;
    annotationsData.context = $hoveredHeader.data('annotations-context');
    annotationsData.version = null;

    // Move the button into place and show it
    clearTimeout(hideAnnotationsButtonTimeout);
    $annotationsButtons.find('.fa').show();
    $annotationsButtons.find('.fa-spin').hide();
    $annotationsButtons.show();
    $annotationsButtons.offset({left: left, top: top - $annotationsButtons.height()});
  }

  /**
   * Render an annotation button for a user selection.
   * The selection must be within a single context
   *
   * Based on https://stackoverflow.com/questions/1589721/how-can-i-position-an-element-next-to-user-text-selection
   */
  function renderAnnotationButtonForSelection() {
    // Store current selection
    const currentSelection = window.getSelection();

    // Check for range in selection. If there is none, nothing is selected
    if (currentSelection.rangeCount === 0) {
      hideAnnotationButtons();
      return;
    }

    // Don't continue while working
    if (annotationsData.working) {
      return;
    }

    let range = currentSelection.getRangeAt(0);

    // Only show when in annotations context, the selection is not collapsed and it actually contains text
    const $currentSelectionContainer = $(range.commonAncestorContainer).closest('[data-annotations-context]');
    if ($currentSelectionContainer.length === 0 || !$currentSelectionContainer.data('annotations-contains-text')
        || currentSelection.isCollapsed || currentSelection.toString().trim().length === 0) {
      hideAnnotationButtons();
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
    clonedRange.insertNode(hiddenMarkerElement);

    // If marker could not be created, ignore this selection
    if (!hiddenMarkerElement) {
      return;
    }

    // Clear hide timeout
    clearTimeout(hideAnnotationsButtonTimeout);

    // Find hidden markerElement position http://www.quirksmode.org/js/findpos.html
    let obj = hiddenMarkerElement;
    let left = 0, top = obj.offsetHeight;

    // noinspection JSAssignmentUsedAsCondition This is the correct assignment for this loop
    do {
      left += obj.offsetLeft;
      top += obj.offsetTop;
    } while (obj = obj.offsetParent);

    // Determine selection start
    let selectedTextOffsetStart = findNode($currentSelectionContainer[0], range.startContainer).start
        + range.startOffset;

    // Determine selection end
    let selectedTextOffsetEnd = findNode($currentSelectionContainer[0], range.endContainer).start
        + range.endOffset;

    // Compensate the start element for the trimmed characters
    let selectedText = currentSelection.toString();
    let originalLength = selectedText.length;
    selectedTextOffsetStart = selectedTextOffsetStart + originalLength - selectedText.trimLeft().length;
    selectedTextOffsetEnd = selectedTextOffsetEnd - originalLength + selectedText.trimRight().length;

    // Update state
    annotationsData.current = 'text';
    annotationsData.containsText = true;
    annotationsData.selectedText = selectedText.trim();
    annotationsData.start = selectedTextOffsetStart;
    annotationsData.end = selectedTextOffsetEnd;
    annotationsData.context = $currentSelectionContainer.data('annotations-context');
    annotationsData.version = $currentSelectionContainer.data('annotations-version');

    // Move the button into place and show it
    $annotationsButtons.find('.fa').show();
    $annotationsButtons.find('.fa-spin').hide();
    $annotationsButtons.show();
    $annotationsButtons.offset({
      left: Math.max($currentSelectionContainer[0].getBoundingClientRect().left, left - $annotationsButtons.width()),
      top: top
    });

    // Remove invisible marker
    hiddenMarkerElement.parentNode.removeChild(hiddenMarkerElement);
  }

  /**
   * Find the start of the node in the given container
   * @param container
   * @param node
   * @param context
   * @returns {{found: boolean, start: number}}
   */
  function findNode(container, node, context) {
    // Set default context
    context = context || {start: 0, found: false};

    // Skip marker nodes
    if (container.isSameNode(hiddenMarkerElement)) {
      return context;
    }

    // Check if this is the correct node
    if (container.isSameNode(node)) {
      return {
        start: context.start,
        found: true
      }
    }

    let nodes = container.childNodes;
    if (nodes.length === 0) {
      return {
        start: context.start + container.length,
        found: false
      };
    }

    // Loop node list
    for (let i = 0; i < nodes.length; i++) {
      context = findNode(nodes[i], node, context);
      if (context.found) break;
    }

    return context;
  }

  /**
   * Remove the annotation button, if any
   */
  function hideAnnotationButtons() {
    clearTimeout(hideAnnotationsButtonTimeout);
    if ($annotationsButtons && !mouseDown) {

      const doHide = function () {
        $annotationsButtons.hide();
        annotationsData.current = null;
      };

      if (annotationsData.onButton || annotationsData.working) {
        hideAnnotationsButtonTimeout = setTimeout(hideAnnotationButtons, 500);
        return;
      }

      if (annotationsData.current === 'text'
          && annotationsData.selectedText !== window.getSelection().toString().trim()) {
        doHide();
      }

      if (annotationsData.current === 'header') {
        hideAnnotationsButtonTimeout = setTimeout(function () {
          annotationsData.current = null;
          hideAnnotationButtons()
        }, 2000);
        return;
      }

      doHide();
    }
  }

  /**
   * Add a new annotation at the current selection
   */
  function openTextAnnotationModel() {
    // Update text field in modal
    let $selectedTextField = $annotationsModal.find('.annotations-selected-text');
    if (annotationsData.containsText) {
      $selectedTextField.html(annotationsData.selectedText);
    } else {
      $selectedTextField.html('Complete "' + annotationsData.selectedText + '"');
    }

    // Update state
    annotationsData.working = true;
    annotationsData.onButton = false;

    // Focus and show
    $annotationsModal.find('textarea#annotation').val('');
    $annotationsModal.find('.fa-plus').show();
    $annotationsModal.find('.fa-spin').hide();
    $annotationsModal.one('shown.bs.modal', function () {
      $annotationsModal.find('textarea').first().focus();
    });
    $annotationsModal.modal({
      backdrop: 'static',
      keyboard: false
    });
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

    annotationsData.working = true;
    $.ajax(
        {
          type: "POST",
          url: Routing.generate('app_annotation_add', {_studyArea: studyAreaId, concept: conceptId}),
          data: {
            'text': $annotationsModal.find('textarea#annotation').val(),
            'context': annotationsData.context,
            'start': annotationsData.start,
            'end': annotationsData.end,
            'selectedText': annotationsData.containsText ? annotationsData.selectedText : null,
            'version': annotationsData.version
          }
        })
        .done(function (annotation) {
          $annotationsModal.modal('hide');
          hideAnnotationButtons();
          renderAnnotations([annotation]);
        })
        .fail(function (err) {
          console.error('Error saving annotation', err);
          $failedModal.modal();
        })
        .always(function () {
          annotationsData.working = false;
          $modalButtons.find('.fa-plus').show();
          $modalButtons.find('.fa-spin').hide();
          $modalButtons.prop('disabled', false);
        });
  }

  function saveMarkAnnotation() {
    $annotationsButtons.find('button').prop('disabled', true);
    $annotationsButtons.find('.fa-flag').hide();
    $annotationsButtons.find('.fa-spin').show();

    // Update state
    annotationsData.working = true;
    annotationsData.onButton = false;

    // Generate request
    $.ajax(
        {
          type: 'POST',
          url: Routing.generate('app_annotation_add', {_studyArea: studyAreaId, concept: conceptId}),
          data: {
            'context': annotationsData.context,
            'start': annotationsData.start,
            'end': annotationsData.end,
            'selectedText': annotationsData.containsText ? annotationsData.selectedText : null,
            'version': annotationsData.version
          }
        })
        .done(function (annotation) {
          if (window.getSelection().empty) {  // Chrome
            window.getSelection().empty();
          } else if (window.getSelection().removeAllRanges) {  // Firefox
            window.getSelection().removeAllRanges();
          }
          hideAnnotationButtons();
          renderAnnotations([annotation]);
        })
        .fail(function (err) {
          console.error("Error saving annotations", err);
          $failedModal.modal();
        })
        .always(function () {
          annotationsData.working = false;
          $('[data-toggle="tooltip"]').tooltip('hide');
          $annotationsButtons.find('.fa-flag').show();
          $annotationsButtons.find('.fa-spin').hide();
          $annotationsButtons.find('button').prop('disabled', false);
        })
  }
}(window.annotations = window.annotations || {}, $));
