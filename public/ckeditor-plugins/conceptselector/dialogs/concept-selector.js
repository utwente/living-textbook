CKEDITOR.dialog.add('conceptSelectorDialog', function (editor) {

  // noinspection JSUnresolvedVariable
  var lang = editor.lang.conceptselector;
  var currentConceptTextValue = '';
  var currentConceptId = null;

  return {
    title: lang.title,
    minWidth: 400,
    minHeight: 50,
    resizable: CKEDITOR.DIALOG_RESIZE_NONE,
    contents: [
      {
        id: 'conceptSelector',
        label: 'ConceptSelector',
        elements: [
          {
            id: 'selector-concept-text',
            type: 'text',
            label: lang.selectorText,
            setup: function (element) {
              this.setValue(element.getText());
            }
          },
          {
            type: 'html',
            html: '<label>' + lang.select + ':</label>'
          },
          {
            id: 'selector-concepts',
            type: 'html',
            label: lang.selector,
            html: '<select></select>',
            setup: function (element) {
              currentConceptId = element.getAttribute('data-concept-id');
            },
            onShow: function () {
              // noinspection JSUnresolvedVariable
              var selectElem = $('#' + this.domId);
              selectElem.children().remove();

              // Re-use values from page below as hack
              $('[data-ckeditor-selector="concepts"]').children().clone().each(function () {
                var elem = $(this);
                if (elem.val() !== "") {
                  elem.appendTo(selectElem);
                }
              });

              // Restore val if set
              if (currentConceptId) {
                selectElem.val(currentConceptId);
              }
              currentConceptTextValue = selectElem.find('option:selected').text();

              // Create select2 element
              selectElem.select2({
                width: '100%',
                theme: 'bootstrap',
              });

              // On change, update text field if necessary
              selectElem.on('change', function () {
                var currentTextFieldVal = CKEDITOR.dialog.getCurrent().getValueOf('conceptSelector', 'selector-concept-text');
                // noinspection EqualityComparisonWithCoercionJS
                if (currentTextFieldVal == '' || currentTextFieldVal == currentConceptTextValue) {
                  currentConceptTextValue = $(this).find('option:selected').text();
                  CKEDITOR.dialog.getCurrent().setValueOf('conceptSelector', 'selector-concept-text', currentConceptTextValue);
                }
              });

              // Update the text elem
              var textElemVal = CKEDITOR.dialog.getCurrent().getValueOf('conceptSelector', 'selector-concept-text');
              // noinspection EqualityComparisonWithCoercionJS
              if (textElemVal == '') {
                CKEDITOR.dialog.getCurrent().setValueOf('conceptSelector', 'selector-concept-text', currentConceptTextValue);
              }
            }
          },
        ]
      }
    ],

    onShow: function () {
      // Reset state
      currentConceptId = null;
      currentConceptTextValue = '';

      var selection = editor.getSelection();
      var link = selection.getStartElement().getAscendant('a', true);

      // If the link is found, insertMode needs to be false
      this.insertMode = !link;

      if (!this.insertMode) {
        // Select whole element
        editor.getSelection().selectElement(link);
        // set-up the field values based on selected or newly created link
        this.setupContent(link);
      }
    },

    onOk: function () {
      var conceptId = parseInt(this.getValueOf('conceptSelector', 'selector-concepts'));
      var conceptText = this.getValueOf('conceptSelector', 'selector-concept-text');
      var href = Routing.generate('app_concept_show', {_studyArea: currentStudyArea, concept: conceptId});

      var link = editor.document.createElement('a');
      link.setAttribute('href', href);
      link.setAttribute('alt', conceptText);
      link.setAttribute('class', 'concept-link');
      link.setAttribute('data-concept-id', conceptId);
      link.appendText(conceptText);
      editor.insertElement(link);
    }
  }
});
