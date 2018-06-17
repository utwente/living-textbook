CKEDITOR.plugins.add('conceptselector', {
  availableLangs: {
    en: 1
  },
  lang: "en",
  requires: ['dialog'],
  icons: "concept-selector",
  init: function (editor) {

    var pluginCmd = 'conceptSelectorDialog';

    // Register the dialog
    editor.addCommand(pluginCmd, new CKEDITOR.dialogCommand(pluginCmd, {
      allowedContent: 'a[href]',
      requiredContent: 'a[href]'
    }));
    CKEDITOR.dialog.add(pluginCmd, this.path + 'dialogs/concept-selector.js');

    // Add the menu button
    editor.ui.addButton('ConceptSelector', {
      label: editor.lang.conceptselector.toolbar,
      command: pluginCmd,
      icon: this.path + 'icons/concept-selector.png',
      toolbar: 'insert'
    });

    // Add context-menu entry
    if (editor.contextMenu) {
      editor.addMenuGroup(editor.lang.conceptselector.menu);
      editor.addMenuItem('conceptSelector', {
        label: editor.lang.conceptselector.edit,
        icon: this.path + 'icons/concept-selector.png',
        command: pluginCmd,
        group: editor.lang.latexeditor.menu
      });

      // if the selected item is url of class 'concept-link',
      // we should be interested in it
      editor.contextMenu.addListener(function (element) {
        if (element.getAscendant('a', true)) {
          if (element.$.classList.contains('concept-link')) {
            editor.contextMenu.removeAll();
            return {
              conceptSelector: CKEDITOR.TRISTATE_OFF
            };
          }
        }
      });

      // Register on double click event to open the editor
      editor.on('doubleclick', function (evt) {
        var element = evt.data.element;
        if (element && element.is('a')) {
          if (element.$.classList.contains('concept-link')) {
            evt.data.dialog = pluginCmd;
          }
        }
      });
    }
  }
});