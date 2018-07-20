CKEDITOR.plugins.add('abbreviationselector', {
  availableLangs: {
    en: 1
  },
  lang: "en",
  requires: ['dialog'],
  icons: "abbreviation-selector",
  init: function (editor) {

    var pluginCmd = 'abbreviationSelectorDialog';

    // Register the dialog
    editor.addCommand(pluginCmd, new CKEDITOR.dialogCommand(pluginCmd, {
      allowedContent: 'ltb-abbr[abbr-id]',
      requiredContent: 'ltb-abbr[abbr-id]'
    }));
    CKEDITOR.dialog.add(pluginCmd, this.path + 'dialogs/abbreviation-selector.js');

    // Add the menu button
    editor.ui.addButton('AbbreviationSelector', {
      label: editor.lang.abbreviationselector.toolbar,
      command: pluginCmd,
      icon: this.path + 'icons/abbreviation-selector.png',
      toolbar: 'insert'
    });

    // Add context-menu entry
    if (editor.contextMenu) {
      editor.addMenuGroup(editor.lang.abbreviationselector.menu);
      editor.addMenuItem('abbreviationSelector', {
        label: editor.lang.abbreviationselector.edit,
        icon: this.path + 'icons/abbreviation-selector.png',
        command: pluginCmd,
        group: editor.lang.abbreviationselector.menu
      });

      // if the selected item is url of class 'concept-link',
      // we should be interested in it
      editor.contextMenu.addListener(function (element) {
        if (element.getAscendant('ltb-abbr', true)) {
          editor.contextMenu.removeAll();
          return {
            abbreviationSelector: CKEDITOR.TRISTATE_OFF
          };
        }
      });

      // Register on double click event to open the editor
      editor.on('doubleclick', function (evt) {
        var element = evt.data.element;
        if (element && element.is('ltb-abbr')) {
          evt.data.dialog = pluginCmd;
        }
      });
    }
  }
});
