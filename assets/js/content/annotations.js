/**
 * $ has been defined globally in the app.js
 */
(function (annotations, $) {

  /** Selection constants */
  const dataStudyAreaId = 'annotations-study-area-id';
  const dataConceptId = 'annotations-concept-id';
  const dataUserId = 'annotations-user-id';
  const dataIsStudyAreaOwner = 'annotations-is-study-area-owner';
  const showAnnotationsId = 'show-annotations-state';
  let $annotationsContainer = null, $annotationsToggle = null;

  /** State parameters */
  let studyAreaId = 0;
  let conceptId = 0;
  let userId = 0;
  let isStudyAreaOwner = false;
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
  const annotationHeaderContextData = {
    onButton: false
  };
  let showAnnotations = 'all';

  /******************************************************************************************************
   * Data types
   * Required for JS-hinting
   *****************************************************************************************************/
  const Types = {};

  Types.AnnotationType = {
    userId: 0,
    userName: '',
    authoredTime: ''
  };

  /** Annotations button */
  let $annotationsButtons = null, $annotationContextButtons = null, $annotationHeaderContextButton = null;
  const markerTextChar = '\ufeff';
  let hiddenMarkerElement, markerId = 'sel_' + new Date().getTime() + '_' + Math.random().toString().substr(2);
  let hideAnnotationsButtonTimeout = null, hideAnnotationContextButtonsTimeout = null,
      hideAnnotationHeaderContextButtonsTimeout = null;

  /** Annotations modals **/
  let $addModal = null;
  let $notesModal = null;
  let $noteProto = null;
  let $noteCollectionModal = null;
  let $collectionNoteProto = null;
  let $outdatedButtonProto = null;
  let $failedModal = null;
  let modalCss = '';

  /**
   * Annotation plugin loader, executed on page load.
   * This plugin does not work for IE<=8
   */
  $(function () {
    if (!window.Selection) {
      console.error('Selection not available, not loading annotations');
      return;
    }

    // Locate elements
    $annotationsContainer = $('.annotations-container').first();
    if ($annotationsContainer.length === 0) {
      console.info('Annotations container not found!');
      return;
    }
    $annotationsToggle = $('.annotations-toggle').first();
    if ($annotationsToggle.length === 0) {
      console.error('Annotations toggle not found!');
      return;
    }
    $annotationsButtons = $annotationsContainer.find('.annotations-buttons').first();
    if ($annotationsButtons.length === 0) {
      console.error('Annotation buttons not found!');
      return;
    }
    $annotationContextButtons = $annotationsContainer.find('.annotation-context-buttons').first();
    if ($annotationContextButtons.length === 0) {
      console.error('Annotation context button not found!');
      return;
    }
    $annotationHeaderContextButton = $annotationsContainer.find('.annotation-header-context-button').first();
    if ($annotationHeaderContextButton.length === 0) {
      console.error('Annotation header context button not found!');
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

    // Register close handler to restore 'modal-open' class if another modal is opened
    $failedModal.on('show.bs.modal', function () {
      setTimeout(function () {
        $('body').find('.modal-backdrop').last().addClass('failed-modal');
      }, 0); // Use 0 timeout to make sure the backdrop has been placed (which isn't the case during the event handling)
      return true;
    });
    $failedModal.on('hide.bs.modal', function () {
      modalCss = $('body').css('padding-right');
    });
    $failedModal.on('hidden.bs.modal', function () {
      if ($('.modal.show').length > 0) {
        $('body')
            .addClass('modal-open')
            .css('padding-right', modalCss)
            .find('.modal-backdrop')
            .removeClass('failed-modal');
      }
    });

    // Retrieve required concept id
    studyAreaId = parseInt($annotationsContainer.data(dataStudyAreaId));
    conceptId = parseInt($annotationsContainer.data(dataConceptId));
    userId = parseInt($annotationsContainer.data(dataUserId));
    const booleanValueIsStudyAreaOwner = $annotationsContainer.data(dataIsStudyAreaOwner);
    isStudyAreaOwner = booleanValueIsStudyAreaOwner || booleanValueIsStudyAreaOwner === 'true';

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

    // Register events on the annotation header context buttons
    $annotationHeaderContextButton
        .on('mousedown', function (e) {
          e.preventDefault();
          e.stopPropagation();
        })
        .hover(function () {
          annotationHeaderContextData.onButton = true;
        }, function () {
          annotationHeaderContextData.onButton = false;
        });
    $annotationHeaderContextButton.find('.annotation-header-note-button').on('click', function () {
      openCollectionAnnotationsModal($(this), false);
    });

    // Register events on the annotation modal
    $addModal.find('button.annotations-save').on('click', saveTextAnnotation);
    $addModal.find('button.annotations-cancel').on('click', function () {
      annotationsData.working = false;
      repositionAnnotationButtons();
    });

    // Register on selectionchange event
    document.addEventListener('selectionchange', renderAnnotationButtonForSelection);

    // Set annotations toggle button, based on local storage
    if (typeof (Storage) !== 'undefined') {
      // Set state
      const storageState = localStorage.getItem(showAnnotationsId + '.' + studyAreaId);
      showAnnotations = storageState !== null ? storageState : showAnnotations;
    }
    $annotationsToggle.click(toggleAnnotationVisibility);
    $annotationsToggle.removeClass('disabled');
    updateAnnotationVisibilityInDom();

    // Load current annotations from server
    loadAnnotations();
  });

  /**
   * Toggle the annotation visibility. Disabling the visibility will also disable all interaction.
   */
  function toggleAnnotationVisibility() {
    // Determine new state
    switch (showAnnotations) {
      case 'own':
        showAnnotations = 'all';
        break;
      case 'all':
        showAnnotations = 'off';
        break;
      default:
        showAnnotations = 'own';
        break;
    }

    // Save state in local storage
    if (typeof (Storage) !== 'undefined') {
      localStorage.setItem(showAnnotationsId + '.' + studyAreaId, showAnnotations);
    }

    updateAnnotationVisibilityInDom();
  }

  function updateAnnotationVisibilityInDom() {
    // Toggle spans
    $annotationsToggle.find('span').hide();
    $annotationsToggle.find('span.' + showAnnotations).show();

    // Clear state classes
    $annotationsContainer
        .removeClass('annotations-disabled')
        .removeClass('annotations-show-own');

    // Update disabled class on container
    switch (showAnnotations) {
      case 'own':
        $annotationsContainer.addClass('annotations-show-own');
        break;
      case 'all':
        // no-op
        break;
      default:
        $annotationsContainer.addClass('annotations-disabled');
        break;
    }

    updateHeaderMarking();
  }

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
          console.error('Error loading annotations');
          $failedModal.show();
        });
  }

  /**
   * Update the annotation data
   *
   * @param annotation
   */
  function updateAnnotation(annotation) {
    if (annotation.start === -1 || typeof annotation.selectedText == 'undefined') {
      const $context = $('[data-annotations-contains-text="false"][data-annotations-context="' + annotation.context + '"]');
      const annotations = [];
      ($context.data('annotations') || []).forEach(function (item) {
        if (item.id === annotation.id) {
          annotations.push(annotation);
        } else {
          annotations.push(item);
        }
      });
      $context.data('annotations', annotations);
    } else {
      // Update both data objects and attribute due to jQuery data caching
      // Outdated annotations cannot be updated, so no need to look for those
      $('[data-annotation-id="' + annotation.id + '"]')
          .data('annotation', annotation)
          .attr('data-annotation', JSON.stringify(annotation));
    }
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
      if (annotation.start === -1 || typeof annotation.selectedText == 'undefined') {
        renderHeaderAnnotation(annotation);
      } else {
        renderTextAnnotation(annotation);
      }
    }

    updateHeaderMarking();
  }

  /**
   * Rerender header annotations after annotation change
   * @param context
   * @param annotations
   */
  function rerenderHeaderAnnotations(context, annotations) {
    const $context = $('[data-annotations-contains-text="false"][data-annotations-context="' + context + '"]');
    if ($context.length === 0) {
      console.warn('Annotation context not found');
      return;
    }

    console.info('Rerendering header annotations');

    // Remove all markup
    $context.removeClass('mark');
    $context.removeClass('note');
    $context.removeClass('annotated');
    $context.data('annotations', []);

    // Rerender all
    for (let i = 0; i < annotations.length; i++) {
      renderHeaderAnnotation(annotations[i]);
    }

    updateHeaderMarking();
  }

  function updateHeaderMarking() {
    // Update header classes based on the annotations
    const $headers = $('[data-annotations-contains-text="false"]');
    $headers.each(function () {
      const $context = $(this);
      $context.removeClass('mark').removeClass('note');
      ($context.data('annotations') || []).forEach(function (annotation) {
        if (showAnnotations !== 'all' && annotation.userId !== userId) {
          return;
        }
        $context.addClass(typeof annotation.text == 'undefined' ? 'mark' : 'note');
      });
    });
  }

  /**
   * Render the header annotation
   * @param annotation
   */
  function renderHeaderAnnotation(annotation) {
    const $context = $('[data-annotations-contains-text="false"][data-annotations-context="' + annotation.context + '"]');
    if ($context.length === 0) {
      console.warn('Annotation context not found');
      return;
    }

    console.info('Rendering header annotation', annotation);

    // Update annotations data
    const annotations = $context.data('annotations') || [];
    annotations.push(annotation);
    $context.data('annotations', annotations);

    // Update styling, add hover
    if (!$context.hasClass('annotated')) {
      $context.addClass('annotated');
      $context.hover(function () {
        showAnnotationHeaderContextButton(annotation.context);
      }, function () {
        hideAnnotationHeaderContextButtonsTimeout = setTimeout(hideAnnotationHeaderContextButton, 200);
      });
    }
  }

  /**
   * Show the annotation header context button
   * @param context
   */
  function showAnnotationHeaderContextButton(context) {
    // Never open when disabled
    if (showAnnotations === 'off') {
      return;
    }

    // Do not show them when working
    if (annotationsData.working || annotationContextData.working) {
      return;
    }

    // Set data on the right places
    const $mark = $('h2.annotated[data-annotations-context="' + context + '"]').first();
    const $noteButton = $annotationHeaderContextButton.find('.annotation-header-note-button');
    const annotations = $mark.data('annotations') || [];
    $noteButton.data('annotations', annotations);
    $noteButton.data('annotations-context', context);
    let count = 0;
    annotations.forEach(function (annotation) {
      if (showAnnotations !== 'all' && annotation.userId !== userId) {
        return;
      }
      count++;
      count += annotation.comments.length;
    });
    $noteButton.find('.note-count').html(' ' + count);
    $noteButton.attr('data-original-title', annotations
        .filter(a => a.text).map(a => a.text.trunc(50)).join(' --- ').trunc(512));

    if (count === 0) {
      // Annotations are hidden, don't show button
      return;
    }

    clearTimeout(hideAnnotationHeaderContextButtonsTimeout);

    let obj = $mark[0];
    let top = obj.offsetHeight;

    // noinspection JSAssignmentUsedAsCondition This is the correct assignment for this loop
    do {
      top += obj.offsetTop;
    } while (obj = obj.offsetParent);

    $annotationHeaderContextButton.show();
    $annotationHeaderContextButton.offset({
      left: $mark[0].getBoundingClientRect().left,
      top: top
    });
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
      console.info('Rendering outdated annotation', annotation);

      // Find whether there already is a notification rendered
      const $container = $context.parent();
      let $outdatedNotification = $container.find('div.annotations-outdated-button[data-annotations-context="' + annotation.context + '"]');
      let $outdatedNotificationButton = $outdatedNotification.find('button');
      if ($outdatedNotification.length === 0) {
        // Create and insert node
        $outdatedNotification = $outdatedButtonProto.clone();
        $outdatedNotification.attr('data-annotations-context', annotation.context);
        $outdatedNotificationButton = $outdatedNotification.find('button');
        $outdatedNotificationButton.on('click', function () {
          openCollectionAnnotationsModal($(this), true);
        });
        $outdatedNotification.insertAfter($container.find('h2[data-annotations-context="' + annotation.context + '"]').first());
      }

      // Update the annotation context
      let annotationData = $outdatedNotificationButton.data('annotations') || [];
      annotationData.push(annotation);
      if (annotation.userId === userId) {
        $outdatedNotification.addClass('has-own');
      }
      $outdatedNotificationButton.data('annotations', annotationData);

      $(window).on('hide.bs.modal', function () {
        const annotationData = $outdatedNotificationButton.data('annotations') || [];
        if (annotationData.length === 0) {
          $outdatedNotification.remove();
          return;
        }
        $outdatedNotification.removeClass('has-own');
        annotationData.forEach(function (annotation) {
          if (annotation.userId === userId) {
            $outdatedNotification.addClass('has-own');
          }
        });
      });

    } else {
      console.info('Rendering text annotation', annotation);

      $context.markRanges([{
        start: annotation.start,
        length: annotation.end - annotation.start
      }], {
        className: 'ltb-annotation ' + (typeof annotation.text == 'undefined' ? 'mark' : 'note'),
        acrossElements: true,
        accuracy: 'exactly',
        caseSensitive: true,
        separateWordSearch: false,
        ignoreJoiners: true,
        each: function (node) {
          node.setAttribute('data-annotation', JSON.stringify(annotation));
          node.setAttribute('data-annotation-id', annotation.id);
          node.setAttribute('data-annotation-text', annotation.text);
          if (annotation.userId !== userId) {
            node.setAttribute('class', node.getAttribute('class') + ' other');
          }
          $(node).hover(function () {
            showAnnotationContextButtons(annotation.id);
          }, hideAnnotationContextButtons);
        },
        done: function () {
          $context.find('mark').each(function () {
            if (this.innerHTML.trim() === '') {
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
    // Never open when disabled
    if (showAnnotations === 'off') {
      return;
    }

    // Do not show them when working
    if (annotationsData.working || annotationContextData.working) {
      return;
    }

    const $mark = $('mark[data-annotation-id="' + annotationId + '"]').last();
    const $noteButton = $annotationContextButtons.find('.annotation-note-button');
    const annotation = $mark.data('annotation');

    // Do not show if only own should be shown
    if (annotation.userId !== userId && showAnnotations !== 'all') {
      return;
    }

    clearTimeout(hideAnnotationContextButtonsTimeout);

    annotationContextData.id = annotation.id;
    annotationContextData.context = annotation;
    if ($mark.hasClass('mark')) {
      annotationContextData.current = 'mark';
      $noteButton.hide();
    } else {
      annotationContextData.current = 'note';
      $noteButton.find('.note-count').html(' ' + (1 + annotation.comments.length));
      $noteButton.attr('data-original-title', annotation.text.trunc(512));
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

    // Remove remove button if not own annotation
    let $removeButton = $annotationContextButtons.find('.annotation-remove-button');
    if (annotation.userId !== userId && !isStudyAreaOwner) {
      $removeButton.hide();
    } else {
      $removeButton.show();
    }

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
    // Never open when disabled
    if (showAnnotations === 'off') {
      return;
    }

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
    // Never open when disabled
    if (showAnnotations === 'off') {
      return;
    }

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
    hiddenMarkerElement = document.createElement('span');
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
      };
    }

    let nodes = container.childNodes;
    if (nodes.length === 0) {
      return {
        start: context.start + (container.length !== undefined ? container.length : 0),
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
        }, 500);
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
        }, 500);
        return;
      }

      doHide();
    }
  }

  /**
   * Remove the annotation header context button, if any
   */
  function hideAnnotationHeaderContextButton() {
    clearTimeout(hideAnnotationHeaderContextButtonsTimeout);
    if ($annotationHeaderContextButton && !mouseDown) {
      const doHide = function () {
        $annotationHeaderContextButton.hide();
      };

      if (annotationHeaderContextData.onButton || (annotationsData.onButton && annotationsData.current === 'header')) {
        hideAnnotationHeaderContextButtonsTimeout = setTimeout(hideAnnotationHeaderContextButton, 500);
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

    // Set default visibility
    setInputVisibility($addModal.find('input[name="visibility"]'), 'private', 0);

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
   * @param annotationId
   * @param $visibilityInputs
   * @returns {*}
   */
  function createNote(context, annotationId, $visibilityInputs) {
    const $note = $noteProto.clone();
    $note.find('.author').text(context.userName);
    $note.find('.authored-time').text(new Date(context.authoredTime).toLocaleString());
    $note.find('.note').text(context.text);

    // Do not show remove icon for non-comments
    const $removeIcon = $note.find('.remove');
    if ((context.userId !== userId && !isStudyAreaOwner) || context.hasOwnProperty('context')) {
      $removeIcon.remove();
    } else {
      $removeIcon.on('click', function () {
        removeAnnotationComment($visibilityInputs, $note, annotationId, context.id);
      });
    }

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

    // Set item visibility accordingly
    let $ownerElements = $notesModal.find('.owner');
    let $ownerRemoveElements = $notesModal.find('.owner-remove');
    let $nonOwnerElements = $notesModal.find('.non-owner');
    $ownerElements.hide();
    $ownerRemoveElements.hide();
    $nonOwnerElements.hide();
    if (context.userId === userId) {
      $ownerElements.show();
      $ownerRemoveElements.show();
    } else if (isStudyAreaOwner) {
      $ownerRemoveElements.show();
      $nonOwnerElements.show();
    } else {
      $nonOwnerElements.show();
    }

    // Set selection in modal
    let $textContainer = $notesModal.find('#note-text');
    $textContainer.text(context.selectedText);

    // Set visibility in modal (use click to force buttons functionality)
    const $visibilityInputs = $notesModal.find('input[name="visibility"]');
    $visibilityInputs.off('change');
    if (context.userId === userId) {
      const otherComments = context.comments.filter(function (comment) {
        return comment.userId !== userId;
      });
      setInputVisibility($visibilityInputs, context.visibility, otherComments.length);
      if (otherComments.length === 0) {
        $visibilityInputs.on('change', function () {
          updateTextAnnotationVisibility($visibilityInputs, $notesModal, context.id);
        });
      }
    } else {
      $notesModal.find('.visibility-state').hide();
      $notesModal.find('.visibility-' + context.visibility).show();
    }
    $visibilityInputs.parent().find('.fa').show();
    $visibilityInputs.parent().find('.fa-spin').hide();

    // Clear existing notes in the modal
    let $container = $notesModal.find('.annotations-note-container');
    $container.empty();

    // Load annotation data in modal
    $container.append(createNote(context, context.id, $visibilityInputs));
    context.comments.forEach(function (comment) {
      $container.append(createNote(comment, context.id, $visibilityInputs));
    });

    // Register comment handler
    const $commentButton = $notesModal.find('button.annotations-comment-add');
    $notesModal.find('.annotation-comment')
        .val('')
        .off('input')
        .on('input', function () {
          $commentButton.prop('disabled', $(this).val() === '');
        });
    $commentButton.prop('disabled', true);
    $commentButton.off('click');
    $commentButton.find('.fa').show();
    $commentButton.find('.fa-spin').hide();
    $commentButton.on('click', function () {
      addAnnotationComment($notesModal, $(this), $container, $visibilityInputs, context.id);
    });

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
   * Show the collection annotation modal
   */
  function openCollectionAnnotationsModal($button, isOutdatedNotes) {
    const annotationsData = ($button.data('annotations') || []).sort(function (annotation) {
      return typeof annotation.text != 'undefined';
    });

    // Clear current annotations from modal
    const $modalBody = $noteCollectionModal.find('.modal-body');
    $modalBody.empty();

    // Place the annotations in the modal
    for (let i = 0; i < annotationsData.length; i++) {
      const annotation = annotationsData[i];
      if (annotation.userId !== userId && showAnnotations !== 'all') {
        continue;
      }

      const $annotationElement = $collectionNoteProto.clone();
      if (typeof annotation.text == 'undefined') {
        $annotationElement.find('.note-header').remove();
        $annotationElement.find('.annotations-note-container').parent().remove();
        $annotationElement.find('.annotations-visibility').parent().remove();
        $annotationElement.find('.annotations-comment-container').parent().remove();
      } else {
        $annotationElement.find('.mark-header').remove();

        const $visibilityInputs = $annotationElement.find('input[name="visibility"]');

        // Add textual notes
        const $container = $annotationElement.find('.annotations-note-container');
        $container.append(createNote(annotation, annotation.id, $visibilityInputs));
        annotation.comments.forEach(function (comment) {
          $container.append(createNote(comment, annotation.id, $visibilityInputs));
        });

        // Set item visibility accordingly
        let $ownerElements = $annotationElement.find('.owner');
        let $ownerRemoveElements = $annotationElement.find('.owner-remove');
        let $nonOwnerElements = $annotationElement.find('.non-owner');
        $ownerElements.hide();
        $ownerRemoveElements.hide();
        $nonOwnerElements.hide();
        if (annotation.userId === userId) {
          $ownerElements.show();
          $ownerRemoveElements.show();
        } else if (isStudyAreaOwner) {
          $ownerRemoveElements.show();
          $nonOwnerElements.show();
        } else {
          $nonOwnerElements.show();
        }

        // Set visibility (use click to force buttons functionality)
        $visibilityInputs.off('change');
        if (annotation.userId === userId) {
          const otherComments = annotation.comments.filter(function (comment) {
            return comment.userId !== userId;
          });
          setInputVisibility($visibilityInputs, annotation.visibility, otherComments.length);
          if (otherComments.length === 0) {
            $visibilityInputs.on('change', function () {
              updateTextAnnotationVisibility($visibilityInputs, $noteCollectionModal, annotation.id);
            });
          }
        } else {
          $annotationElement.find('.visibility-state').hide();
          $annotationElement.find('.visibility-' + annotation.visibility).show();
        }
        $visibilityInputs.parent().find('.fa').show();
        $visibilityInputs.parent().find('.fa-spin').hide();

        // Bind the comment button
        const $commentButton = $annotationElement.find('button.annotations-comment-add');
        $annotationElement.find('.annotation-comment')
            .val('')
            .off('input')
            .on('input', function () {
              $commentButton.prop('disabled', $(this).val() === '');
            });
        $commentButton.prop('disabled', true);
        $commentButton.find('.fa').show();
        $commentButton.find('.fa-spin').hide();
        $commentButton.on('click', function () {
          addAnnotationComment($noteCollectionModal, $(this), $container, $visibilityInputs, annotation.id);
        });
      }

      // Set selected text
      if (typeof annotation.selectedText !== 'undefined' && annotation.selectedText.length > 0) {
        $annotationElement.find('.selected-text').text(annotation.selectedText);
      } else {
        $annotationElement.find('.selected-text').closest('.row').remove();
      }

      // Bind remove button
      $annotationElement.find('button.annotations-remove').on('click', function () {
        removeAnnotationFromCollection($button, $(this), annotation.id);
      });

      $modalBody.append($annotationElement);
    }

    if (isOutdatedNotes === true) {
      $noteCollectionModal.find('.modal-title.outdated').show();
      $noteCollectionModal.find('.modal-title.normal').hide();
    } else {
      $noteCollectionModal.find('.modal-title.outdated').hide();
      $noteCollectionModal.find('.modal-title.normal').show();
    }

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
          type: 'POST',
          url: Routing.generate('app_annotation_add', {_studyArea: studyAreaId, concept: conceptId}),
          data: {
            'text': $addModal.find('textarea#annotation').val(),
            'context': annotationsData.context,
            'start': annotationsData.start,
            'end': annotationsData.end,
            'selectedText': annotationsData.containsText ? annotationsData.selectedText : null,
            'version': annotationsData.version,
            'visibility': $addModal.find('input[name="visibility"]:checked').val()
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
   * Update the annotation visibility
   */
  function updateTextAnnotationVisibility($visibilityInputs, $modal, updateId) {
    const $buttons = $modal.find('button');
    const visibility = $visibilityInputs.filter(':checked').val();

    // Show working spinners/disable buttons
    $buttons.prop('disabled', true);
    toggleInputVisibilities($modal, $visibilityInputs, false);

    // Update the data
    $.ajax(
        {
          type: 'POST',
          url: Routing.generate('app_annotation_editvisibility', {
            _studyArea: studyAreaId,
            concept: conceptId,
            annotation: updateId
          }),
          data: {
            'visibility': visibility
          }
        })
        .done(function (annotation) {
          // Update the annotation data after the update
          updateAnnotation(annotation);
        })
        .fail(function (err) {
          console.error('Error updating annotation visibility', err);
          $failedModal.modal();
        })
        .always(function () {
          // Remove working spinners/enable buttons
          $buttons.prop('disabled', false);
          toggleInputVisibilities($modal, $visibilityInputs, true);
        });
  }

  /**
   * Add a new comment to an annotation
   */
  function addAnnotationComment($container, $button, $noteContainer, $visibilityInputs, annotationId) {
    const $commentContainer = $button.closest('.annotations-comment-container');
    const $textArea = $commentContainer.find('textarea.annotation-comment');
    const $buttons = $container.find('button');

    // Show working spinners/disable buttons
    $textArea.prop('disabled', true);
    $buttons.prop('disabled', true);
    toggleInputVisibilities($container, $(), false);
    $button.find('.fa').hide();
    $button.find('.fa-spin').show();

    $.ajax(
        {
          type: 'POST',
          url: Routing.generate('app_annotation_addcomment', {
            _studyArea: studyAreaId,
            concept: conceptId,
            annotation: annotationId
          }),
          data: {
            'text': $textArea.val(),
          }
        })
        .done(function (data) {
          $textArea.val('');

          // Add the comment to the context
          $noteContainer.append(createNote(data.comment, annotationId, $visibilityInputs));

          // Update the visibility buttons
          const otherComments = data.annotation.comments.filter(function (comment) {
            return comment.userId !== userId;
          });
          setInputVisibility($visibilityInputs, data.annotation.visibility, otherComments.length);

          // Add the comment to the original data object
          updateAnnotation(data.annotation);
        })
        .fail(function (err) {
          console.error('Error saving comment', err);
          $failedModal.modal();
        })
        .always(function () {
          // Remove working spinners/enable buttons
          $textArea.prop('disabled', false);
          $buttons.prop('disabled', false);
          toggleInputVisibilities($container, $(), true);
          $button.find('.fa').show();
          $button.find('.fa-spin').hide();
        });
  }

  /**
   * Remove a comment from an annotation
   * Event driven
   */
  function removeAnnotationComment($visibilityInputs, $elem, annotationId, commentId) {
    // Set delete pending
    $elem.find('.remove .fa-trash')
        .removeClass('fa-trash')
        .addClass('fa-spin')
        .addClass('fa-circle-o-notch');
    $elem.css('opacity', 0.5);
    $elem.off('click');

    $.ajax(
        {
          type: 'DELETE',
          url: Routing.generate('app_annotation_removecomment', {
            _studyArea: studyAreaId,
            concept: conceptId,
            annotation: annotationId,
            comment: commentId
          })
        })
        .done(function (data) {
          // Remove the comment from the DOM
          $elem.remove();

          // Update the visibility buttons
          const otherComments = data.annotation.comments.filter(function (comment) {
            return comment.userId !== userId;
          });
          setInputVisibility($visibilityInputs, data.annotation.visibility, otherComments.length);

          // Update the original annotation
          updateAnnotation(data.annotation);
        })
        .fail(function (err) {
          console.error('Error removing comment', err);
          $failedModal.modal();

          // Rebind handlers/reshow icons
          $elem.find('.remove .fa-circle-o-notch')
              .addClass('fa-trash')
              .removeClass('fa-spin')
              .removeClass('fa-circle-o-notch');
          $elem.css('opacity', 1);
          $elem.on('click', function () {
            removeAnnotationComment($visibilityInputs, $elem, annotationId, commentId);
          });
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
          console.error('Error saving annotations', err);
          $failedModal.modal();
        })
        .always(function () {
          annotationsData.working = false;
          $('[data-toggle="tooltip"]').tooltip('hide');
          $annotationsButtons.find('.fa-flag').show();
          $annotationsButtons.find('.fa-spin').hide();
          $annotationsButtons.find('button').prop('disabled', false);
        });
  }

  /**
   * Remove the selected annotation
   */
  function removeAnnotation() {
    const removeId = annotationContextData.id;
    if (removeId === 0){
      // It is not possible to delete items without an id
      return;
    }

    console.info('Removing annotation', annotationContextData);

    $annotationContextButtons.find('button').prop('disabled', true);
    $annotationContextButtons.find('.fa-times').hide();
    $annotationContextButtons.find('.fa-spin').show();

    // Update state
    annotationContextData.working = true;
    annotationContextData.onButton = false;

    // Generate request
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
          $annotationContextButtons.hide();
        })
        .fail(function (err) {
          console.error('Error removing annotation', err);
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

  /**
   * Remove the selected annotation from the collection
   * @param $collectionContainer
   * @param $button
   * @param removeId
   */
  function removeAnnotationFromCollection($collectionContainer, $button, removeId) {
    console.info('Removing annotation from collection', removeId);

    // Disable the buttons, and show the loader
    $noteCollectionModal.find('button').prop('disabled', true);
    toggleInputVisibilities($noteCollectionModal, $(), false);
    $button.find('.fa-spin').addClass('show');
    $button.find('.fa-trash').hide();

    // Generate the request
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
          // Remove the removed annotation from the view
          $button.closest('.annotations-note-collection').remove();
          const newAnnotations = ($collectionContainer.data('annotations') || []).filter(function (annotation) {
            return annotation.id !== removeId;
          });

          // Rerender in case of header
          if ($collectionContainer.hasClass('annotation-header-note-button')) {
            rerenderHeaderAnnotations($collectionContainer.data('annotations-context'), newAnnotations);
          }
          $collectionContainer.data('annotations', newAnnotations);

          const ownNewAnnotations = newAnnotations.filter(function (annotation) {
            return annotation.userId === userId;
          });
          if (newAnnotations.length === 0 || (showAnnotations !== 'all' && ownNewAnnotations.length === 0)) {
            // Close modal
            $noteCollectionModal.modal('hide');
          }
        })
        .fail(function (err) {
          console.error('Error removing annotation', err);
          $failedModal.modal();
        })
        .always(function () {
          // Restore the buttons
          $noteCollectionModal.find('button').prop('disabled', false);
          toggleInputVisibilities($noteCollectionModal, $(), true);
          $button.find('.fa-spin').removeClass('show');
          $button.find('.fa-trash').show();
        });
  }

  /**
   * Set the input visibility value
   * @param $inputs
   * @param value
   * @param otherCommentCount
   */
  function setInputVisibility($inputs, value, otherCommentCount) {
    const $currentInputs = $inputs.filter('[value="' + value + '"]');
    $currentInputs.prop('checked', true);
    $inputs.closest('label').removeClass('active');
    $inputs.filter(':checked').closest('label').addClass('active');

    let $buttonsContainer = $inputs.closest('.visibility-buttons');
    $buttonsContainer.data('comment-count', otherCommentCount);
    toggleInputVisibilities($buttonsContainer.parent(), $currentInputs, otherCommentCount === 0);
  }

  /**
   * Set input visibility status
   * @param $container
   * @param $currentInputs
   * @param enabled
   */
  function toggleInputVisibilities($container, $currentInputs, enabled) {
    const $inputVisibilities = $container.find('.visibility-buttons');

    $inputVisibilities.each(function () {
      const $inputVisibility = $(this);
      const $radioButtons = $inputVisibility.find('.btn');
      const $currentButton = $currentInputs.filter(':checked').closest('.btn');
      $inputVisibility.find('.when-comments').hide();
      if ($inputVisibility.data('comment-count') !== 0) {
        $radioButtons.addClass('disabled');
        $currentButton.find('.fa').show();
        $currentButton.find('.fa-spin').hide();
        $inputVisibility.find('.overlay').addClass('show');
        $inputVisibility.find('.when-comments').show();
      } else if (enabled) {
        $radioButtons.removeClass('disabled');
        $currentButton.find('.fa').show();
        $currentButton.find('.fa-spin').hide();
        $inputVisibility.find('.overlay').removeClass('show');
      } else {
        $radioButtons.addClass('disabled');
        $currentButton.find('.fa').hide();
        $currentButton.find('.fa-spin').show();
        $inputVisibility.find('.overlay').addClass('show');
      }
    });
  }

}(window.annotations = window.annotations || {}, $));
