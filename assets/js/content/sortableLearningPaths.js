/**
 * Register slp namespace in the browser, for usage of the sortable learning paths plugin
 *
 * $ has been defined globally in the app.js
 */
(function (slp, $) {

  let _sortables = {};

  /**
   * Updates the sortable indexes to make sure the browser submits it in the correct order
   *
   * @param sortableId
   */
  function updateSortableIndexes(sortableId) {
    let $elem = $(_sortables[sortableId].elemId);

    // Update the item numbering for every item
    let counter = 0;
    $elem.find('li').each(function () {
      $(this).find(':input').each(function () {
        let nameAttr = $(this).attr('name');
        if (typeof nameAttr !== 'undefined') {
          const value = nameAttr.replace(/\[(\d+)](\[[^\[\]]+])$/, '[' + counter + ']$2');
          $(this).attr('name', value)
        }
      });
      counter++;
    });
  }

  /**
   * Registers the sortable behavior for the given selector
   *
   * @param sortableId Unique identifer
   * @param formId Symfony form id
   */
  slp.registerSortable = function (sortableId, formId) {
    const elemId = '#' + sortableId + '_sortable';
    let $elem = $(elemId);

    $elem.sortable({
      axis: 'y',
      handle: '.handle',
      helper: 'clone',
    });
    _sortables[sortableId] = {
      formId: formId,
      elemId: elemId
    };

    // Register sort update handler
    $elem.on('sortupdate', function () {
      updateSortableIndexes(sortableId);
    });
  };

  /**
   * Add new concepts to the learning path, based on the input form
   *
   * @param sortableId
   * @param conceptsFieldId
   * @param learningOutcomesFieldId
   */
  slp.addLearningPathConcepts = function (sortableId, conceptsFieldId, learningOutcomesFieldId) {
    const $conceptField = $('#' + conceptsFieldId);
    const $learningOutcomesField = $('#' + learningOutcomesFieldId);

    const addConceptId = function (conceptId) {
      conceptId = parseInt(conceptId);
      const conceptName = $('#' + conceptsFieldId + ' option[value="' + conceptId + '"]').html();
      slp.addConcept(sortableId, conceptId, conceptName);
    };

    // Retrieve concepts
    $($conceptField.val()).each(function () {
      addConceptId(this);
    });

    // Add learning outcome concepts
    const learningOutcomeConceptIds = JSON.parse($('#' + learningOutcomesFieldId + 'Concepts').val());
    $($learningOutcomesField.val()).each(function () {
      const learningOutcomeId = parseInt(this);
      $(learningOutcomeConceptIds[learningOutcomeId]).each(function () {
        addConceptId(this);
      })
    });

    // Clear current selection
    $conceptField.val(null).trigger('change');
    $learningOutcomesField.val(null).trigger('change');
  };

  /**
   * Add a single concept to the bottom of the learning path
   *
   * @param sortableId
   * @param conceptId
   * @param name
   */
  slp.addConcept = function (sortableId, conceptId, name) {
    // Create prototype element
    let $elem = $(_sortables[sortableId].elemId);
    const index = parseInt($elem.data('index'));
    const prototype = $($elem.data('prototype').replace(/__name__/g, index));

    // Set some properties
    prototype.find('#' + _sortables[sortableId].formId + '_' + index + '_id').val(-1);
    prototype.find('#' + _sortables[sortableId].formId + '_' + index + '_conceptId').val(conceptId);
    prototype.find('#' + _sortables[sortableId].formId + '_' + index + '_concept').val(name);
    prototype.find('[data-toggle="tooltip"]').tooltip();
    $elem.append(prototype);

    // Update index for next
    $elem.data('index', index + 1);
  };

  /**
   * Remove a concept from the learning path
   *
   * @param removeButton
   * @param sortableId
   */
  slp.removeConcept = function (removeButton, sortableId) {
    // Remove element
    $(removeButton).tooltip('dispose');
    $(removeButton).closest('li').remove();

    // Update indexes
    updateSortableIndexes(sortableId);
  };

}(window.slp = window.slp || {}, $));
