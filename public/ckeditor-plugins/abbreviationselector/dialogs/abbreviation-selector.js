CKEDITOR.dialog.add('abbreviationSelectorDialog', function (editor) {

  // noinspection JSUnresolvedVariable
  var lang = editor.lang.abbreviationselector;
  var currentAbbreviationTextValue = '';
  var currentAbbreviationId = null;

  return {
    title: lang.title,
    minWidth: 400,
    minHeight: 50,
    resizable: CKEDITOR.DIALOG_RESIZE_NONE,
    contents: [
      {
        id: 'abbreviationSelector',
        label: 'AbbreviationSelector',
        elements: [
          {
            type: 'html',
            html: '<label>' + lang.select + ':</label>'
          },
          {
            id: 'selector-abbreviations',
            type: 'html',
            label: lang.selector,
            html: '<select></select>',
            setup: function(element) {
              currentAbbreviationId = element.getAttribute('data-abbr-id');
            },
            onShow: function () {
              // noinspection JSUnresolvedVariable
              var selectElem = $('#' + this.domId);
              selectElem.children().remove();

              // Re-use values from page below as hack
              $('#edit_concept_abbreviations').children().clone().appendTo(selectElem);

              // Restore val if set
              if (currentAbbreviationId){
                selectElem.val(currentAbbreviationId);
              }
              currentAbbreviationTextValue = selectElem.find('option:selected').text();

              // Create select2 element
              selectElem.select2({
                width: '100%',
                theme: 'bootstrap'
              });

              // On change, update text field if necessary
              selectElem.on('change', function () {
                currentAbbreviationTextValue = $(this).find('option:selected').text();
              });
            }
          }
        ]
      }
    ],

    onShow: function() {
      // Reset state
      currentAbbreviationId = null;
      currentAbbreviationTextValue = '';

      var selection = editor.getSelection();
      var abbrElem = selection.getStartElement().getAscendant('ltb-abbr', true);

      // If the abbrElem is found, insertMode needs to be false
      this.insertMode = !abbrElem;

      if (!this.insertMode) {
        // Select whole element
        editor.getSelection().selectElement(abbrElem);
        // set-up the field values based on selected or newly created abbrElem
        this.setupContent(abbrElem);
      }
    },

    onOk: function () {
      var abbreviationId = parseInt(this.getValueOf('abbreviationSelector', 'selector-abbreviations'));

      var abbrElem = editor.document.createElement('ltb-abbr');
      abbrElem.setAttribute('data-abbr-id', abbreviationId);
      abbrElem.appendText(currentAbbreviationTextValue);
      editor.insertElement(abbrElem);
    }
  }
});
