/**
 * Register slp namespace in the browser, for usage of the sortable learning paths plugin
 *
 * $ has been defined globally in the app.js
 */
(function (slp, $) {

  let _sortables = {};

  /**
   * Registers the sortable behavior for the given selector
   *
   * @param sortableId Unique identifer
   * @param formId Symfony form id
   */
  slp.registerSortable = function (sortableId, formId) {
    let $elem = $('#' + sortableId + '_sortable');
    $elem.sortable({
      axis: "y",
      handle: '.handle'
    });
    _sortables[sortableId] = {
      formId: formId,
      elem: $elem
    };

    // Register sort update handler
    $elem.on('sortupdate', function () {
      // Update the item numbering for every item
      let counter = 0;
      $elem.find('li').each(function () {
        $(this).find(':input').each(function () {
          const value = $(this).attr('name').replace(/\[(\d+)](\[[^\[\]]+])$/, '[' + counter + ']$2');
          $(this).attr('name', value)
        });
        counter++;
      })
    });
  };

  slp.addLearningPathConcepts = function (sortableId, conceptsFieldId, learningOutcomesFieldId) {
    const $conceptField = $('#' + conceptsFieldId);
    const $learningOutcomesField = $('#' + learningOutcomesFieldId);

    // Todo: retrieve learning path concepts

    // Retrieve concepts
    $($conceptField.val()).each(function () {
      const conceptId = parseInt(this);
      const conceptName = $('#' + conceptsFieldId + ' option[value="' + conceptId + '"]').html();
      slp.addConcept(sortableId, conceptId, conceptName);
    });

    // Clear current selection
    $conceptField.val(null).trigger('change');
    $learningOutcomesField.val(null).trigger('change');
  };

  slp.addConcept = function (sortableId, conceptId, name) {
    // Create prototype element
    let $elem = _sortables[sortableId].elem;
    const index = parseInt($elem.data('index'));
    const prototype = $($elem.data('prototype').replace(/__name__/g, index));

    // Set some properties
    prototype.find('#' + _sortables[sortableId].formId + '_' + index + '_id').val(-1);
    prototype.find('#' + _sortables[sortableId].formId + '_' + index + '_conceptId').val(conceptId);
    prototype.find('#' + _sortables[sortableId].formId + '_' + index + '_concept').val(name);
    $elem.append(prototype);

    // Update index for next
    $elem.data('index', index + 1);
  };

}(window.slp = window.slp || {}, $));
