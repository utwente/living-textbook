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
  const annotationContextData = {
    current: null,
    working: false,
    onButton: false,
    context: null,
    id: 0
  };

  /** Annotations button */
  let $annotationsButtons = null, $annotationContextButtons = null;
  const markerTextChar = "\ufeff";
  let hiddenMarkerElement, markerId = "sel_" + new Date().getTime() + "_" + Math.random().toString().substr(2);
  let hideAnnotationsButtonTimeout = null, hideAnnotationContextButtonsTimeout = null;

  /** Annotations modals **/
  let $addModal = null;
  let $notesModal = null;
  let $noteProto = null;
  let $noteCollectionModal = null;
  let $collectionNoteProto = null;
  let $outdatedButtonProto = null;
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
      console.error('Annotations container not found!');
      return;
    }
    $annotationsButtons = $annotationsContainer.find('.annotations-buttons').first();
    if ($annotationsButtons.length === 0) {
      console.error('Annotation buttons not found!');
      return;
    }
    $annotationContextButtons = $annotationsContainer.find('.annotation-context-buttons').first();
    if ($annotationContextButtons.length === 0) {
      console.error('Annotation discuss button not found!');
      return;
    }
    $addModal = $annotationsContainer.find('.annotations-modal.add').first();
    if ($addModal.length === 0) {
      console.error('Add modal not found!');
      return;
    }
    $notesModal = $annotationsContainer.find('.annotations-modal.notes').first();
    if ($notesModal.length === 0) {
      console.error('Notes modal not found!');
      return;
    }
    $noteProto = $annotationsContainer.find('.annotations-note').first();
    if ($noteProto.length === 0) {
      console.error('Note prototype not found!');
      return;
    }
    $noteCollectionModal = $annotationsContainer.find('.annotations-modal.collection').first();
    if ($noteCollectionModal.length === 0) {
      console.error('Note collection modal not found!');
      return;
    }
    $collectionNoteProto = $annotationsContainer.find('.annotations-note-collection').first();
    if ($collectionNoteProto.length === 0) {
      console.error('Collection note prototype not found!');
      return;
    }
    $outdatedButtonProto = $annotationsContainer.find('.annotations-outdated-button').first();
    if ($outdatedButtonProto.length === 0) {
      console.error('Outdated button prototype not found!');
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
        // Reposition on resize event, as the buttons will need to be repositioned if still shown
        .on('resize', function () {
          repositionAnnotationButtons();
          repositionAnnotationContextButtons();
        });

    // Find headers which can be annotated, to register hover action
    $annotationsContainer.find('h2[data-annotations-context]')
        .hover(renderAnnotationButtonForHeader, hideAnnotationButtons);

    // Register events on the annotation buttons
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
    $annotationsButtons.find('.annotations-button-text').on('click', openTextAnnotationModal);
    $annotationsButtons.find('.annotations-button-mark').on('click', saveMarkAnnotation);

    // Register events on the annotation context buttons
    $annotationContextButtons
        .on('mousedown', function (e) {
          e.preventDefault();
          e.stopPropagation();
        })
        .hover(function () {
          annotationContextData.onButton = true;
        }, function () {
          annotationContextData.onButton = false;
        });
    $annotationContextButtons.find('.annotation-note-button').on('click', openNotesModal);
    $annotationContextButtons.find('.annotation-remove-button').on('click', removeAnnotation);

    // Register events on the annotation modal
    $addModal.find('button.annotations-save').on('click', saveTextAnnotation);
    $addModal.find('button.annotations-cancel').on('click', function () {
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
        .fail(function () {
          console.error("Error loading annotations");
          $failedModal.show();
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
      if (annotation.start === -1 || typeof annotation.selectedText == "undefined") {
        // todo Header annotation
        console.log("Header annotation", annotation);
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
      console.log("Rendering outdated annotation", annotation);

      // Find whether there already is a notification rendered
      const $container = $context.parent();
      let $outdatedNotification = $container.find('div.annotations-outdated-button[data-annotations-context="' + annotation.context + '"]');
      if ($outdatedNotification.length === 0) {
        // Create and insert node
        $outdatedNotification = $outdatedButtonProto.clone();
        $outdatedNotification.attr('data-annotations-context', annotation.context);
        $outdatedNotification.insertAfter($container.find('h2[data-annotations-context="' + annotation.context + '"]').first());
        $outdatedNotification.on('click', openOutdatedAnnotationsModal);
      }

      // Update the annotation context
      let annotationData = $outdatedNotification.data('annotations') || [];
      annotationData.push(annotation);
      $outdatedNotification.data('annotations', annotationData);

    } else {
      console.info("Rendering text annotation", annotation);

      $context.markRanges([{
        start: annotation.start,
        length: annotation.end - annotation.start
      }], {
        className: 'ltb-annotation ' + (typeof annotation.text == "undefined" ? "mark" : "note"),
        acrossElements: true,
        accuracy: "exactly",
        caseSensitive: true,
        separateWordSearch: false,
        ignoreJoiners: true,
        each: function (node) {
          node.setAttribute('data-annotation', JSON.stringify(annotation));
          node.setAttribute('data-annotation-id', annotation.id);
          node.setAttribute('data-annotation-text', annotation.text);
          $(node).hover(function () {
            showAnnotationContextButtons(annotation.id);
          }, hideAnnotationContextButtons);
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
   * Show the annotation context buttons
   * @param annotationId
   */
  function showAnnotationContextButtons(annotationId) {
    // Do not show them when working
    if (annotationsData.working || annotationContextData.working) {
      return;
    }

    clearTimeout(hideAnnotationContextButtonsTimeout);
    const $mark = $('mark[data-annotation-id="' + annotationId + '"]').last();
    const $noteButton = $annotationContextButtons.find('.annotation-note-button');

    annotationContextData.id = annotationId;

    annotationContextData.context = $mark.data('annotation');
    if ($mark.hasClass('mark')) {
      annotationContextData.current = 'mark';
      $noteButton.hide();
    } else {
      annotationContextData.current = 'note';
      $noteButton.find('.note-count').html(" 1"); // Todo: set actual value once we support discussions
      $noteButton.show();
    }

    let obj = $mark[0];
    let left = 0, top = obj.offsetHeight;

    // noinspection JSAssignmentUsedAsCondition This is the correct assignment for this loop
    do {
      left += obj.offsetLeft;
      top += obj.offsetTop;
    } while (obj = obj.offsetParent);

    $annotationContextButtons.find('.fa-spin').hide();
    $annotationContextButtons.show();
    $annotationContextButtons.offset({
      left: Math.max($mark[0].getBoundingClientRect().left, left + $mark.innerWidth() - $annotationContextButtons.width()),
      top: top
    });
  }

  /**
   * Reposition the rendered selection buttons
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
   * Reposition the rendered context buttons
   */
  function repositionAnnotationContextButtons() {
    if (annotationContextData.current !== null && annotationContextData.id !== 0) {
      showAnnotationContextButtons(annotationContextData.id);
    }
  }

  /**
   * Render an annotation button for a context header
   */
  function renderAnnotationButtonForHeader() {
    // Don't do anything while working
    if (annotationsData.working || annotationContextData.working) {
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
    if (annotationsData.working || annotationContextData.working) {
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

    // Remove context buttons if any
    clearTimeout(hideAnnotationContextButtonsTimeout);
    annotationContextData.current = null;
    annotationContextData.id = 0;
    annotationContextData.onButton = false;
    hideAnnotationContextButtons();

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
   * Remove the annotation buttons, if any
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
        return;
      }

      if (annotationsData.current === 'header') {
        hideAnnotationsButtonTimeout = setTimeout(function () {
          annotationsData.current = null;
          hideAnnotationButtons();
        }, 2000);
        return;
      }

      doHide();
    }
  }

  /**
   * Remove the annotation context buttons, if any
   */
  function hideAnnotationContextButtons() {
    clearTimeout(hideAnnotationContextButtonsTimeout);
    if ($annotationContextButtons && !mouseDown) {
      const doHide = function () {
        $annotationContextButtons.hide();
        annotationContextData.current = null;
        annotationContextData.id = 0;
      };

      if (annotationContextData.onButton || annotationContextData.working) {
        hideAnnotationContextButtonsTimeout = setTimeout(hideAnnotationContextButtons, 500);
        return;
      }

      if (annotationContextData.current !== null) {
        hideAnnotationContextButtonsTimeout = setTimeout(function () {
          annotationContextData.current = null;
          hideAnnotationContextButtons();
        }, 2000);
        return;
      }

      doHide();
    }
  }

  /**
   * Add a new annotation at the current selection
   */
  function openTextAnnotationModal() {
    // Update text field in modal
    let $selectedTextField = $addModal.find('.annotations-selected-text');
    if (annotationsData.containsText) {
      $selectedTextField.html(annotationsData.selectedText);
    } else {
      $selectedTextField.html('Complete "' + annotationsData.selectedText + '"');
    }

    // Update state
    annotationsData.working = true;
    annotationsData.onButton = false;

    // Focus and show
    $addModal.find('textarea#annotation').val('');
    $addModal.find('.fa-plus').show();
    $addModal.find('.fa-spin').hide();
    $addModal.one('shown.bs.modal', function () {
      $addModal.find('textarea').first().focus();
    });
    $addModal.modal({
      backdrop: 'static',
      keyboard: false
    });
  }

  /**
   * Create a note element
   * @param context
   * @returns {*}
   */
  function createNote(context) {
    const $note = $noteProto.clone();
    $note.find('.author').text(context.userName);
    $note.find('.authored-time').text(new Date(context.authoredTime).toLocaleString());
    $note.find('.note').text(context.text);

    return $note;
  }

  /**
   * Opens the notes modal
   */
  function openNotesModal() {
    const context = annotationContextData.context;

    // Update state
    annotationContextData.working = true;
    annotationContextData.onButton = false;

    // Set selection in modal
    let $textContainer = $notesModal.find('#note-text');
    $textContainer.text(context.selectedText);

    // Clear existing notes in the modal
    let $container = $notesModal.find('.annotations-note-container');
    $container.empty();

    // Load annotation data in modal
    $container.append(createNote(context));

    // Show modal
    $notesModal.one('hide.bs.modal', function () {
      annotationContextData.working = false;
    });
    $notesModal.modal({
      backdrop: 'static',
      keyboard: false
    });
  }

  /**
   * Show the outdated annotations modal, which displays all outdated annotations
   */
  function openOutdatedAnnotationsModal() {
    let $button = $(this);
    let annotationsData = $button.data('annotations').sort(function (annotation) {
      return typeof annotation.text != "undefined";
    });

    // Clear current annotations from modal
    let $modalBody = $noteCollectionModal.find('.modal-body');
    $modalBody.empty();

    // Place the annotations in the modal
    for (let i = 0; i < annotationsData.length; i++) {
      const annotation = annotationsData[i];
      const $annotationElement = $collectionNoteProto.clone();
      if (typeof annotation.text == "undefined") {
        $annotationElement.find('.note-header').remove();
        $annotationElement.find('.annotations-note-container').parent().remove();
      } else {
        $annotationElement.find('.mark-header').remove();

        // Add textual notes
        const $container = $annotationElement.find('.annotations-note-container');
        $container.append(createNote(annotation));
      }

      $annotationElement.find('.selected-text').text(annotation.selectedText);
      $annotationElement.find('button').on('click', function () {
        // Todo: implement removal of outdated annotation
        console.info("remove outdated annotation", annotation.id);
      });

      $modalBody.append($annotationElement);
    }

    $noteCollectionModal.find('.modal-title.outdated').show();
    $noteCollectionModal.find('.modal-title.normal').hide();
    $noteCollectionModal.modal({
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
    let $modalButtons = $addModal.find('button');
    $modalButtons.prop('disabled', true);
    $modalButtons.find('.fa-plus').hide();
    $modalButtons.find('.fa-spin').show();

    annotationsData.working = true;
    $.ajax(
        {
          type: "POST",
          url: Routing.generate('app_annotation_add', {_studyArea: studyAreaId, concept: conceptId}),
          data: {
            'text': $addModal.find('textarea#annotation').val(),
            'context': annotationsData.context,
            'start': annotationsData.start,
            'end': annotationsData.end,
            'selectedText': annotationsData.containsText ? annotationsData.selectedText : null,
            'version': annotationsData.version
          }
        })
        .done(function (annotation) {
          $addModal.modal('hide');
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

  /**
   * Save a new mark annotation
   */
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

  /**
   * Remove the selected annotation
   */
  function removeAnnotation() {
    $annotationContextButtons.find('button').prop('disabled', true);
    $annotationContextButtons.find('.fa-times').hide();
    $annotationContextButtons.find('.fa-spin').show();

    // Update state
    annotationContextData.working = true;
    annotationContextData.onButton = false;

    // Generate request
    let removeId = annotationContextData.id;
    $.ajax(
        {
          type: 'DELETE',
          url: Routing.generate('app_annotation_remove', {
            _studyArea: studyAreaId,
            concept: conceptId,
            annotation: removeId
          }),
        })
        .done(function () {
          $('mark[data-annotation-id="' + removeId + '"]').contents()
              .unwrap('mark[data-annotation-id="' + removeId + '"]');
          annotationContextData.id = 0;
          annotationContextData.current = null;
        })
        .fail(function (err) {
          console.error("Error removing annotation", err);
          $failedModal.modal();
        })
        .always(function () {
          annotationContextData.working = false;
          $('[data-toggle="tooltip"]').tooltip('hide');
          $annotationContextButtons.find('button').prop('disabled', false);
          $annotationContextButtons.find('.fa-times').show();
          $annotationContextButtons.find('.fa-spin').hide();
        });
  }
}(window.annotations = window.annotations || {}, $));
