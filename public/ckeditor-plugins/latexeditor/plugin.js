CKEDITOR.plugins.add('latexeditor', {
  availableLangs: {
    en: 1
  },
  lang: "en",
  requires: ['dialog'],
  icons: "latex-editor",

  init: function (editor) {
    var pluginCmd = 'latexeditorDialog';

    // Register the dialog
    editor.addCommand(pluginCmd, new CKEDITOR.dialogCommand(pluginCmd,
        {
          allowedContent: 'img[src,alt]',
          requiredContent: 'img[src,alt]'
        })
    );
    CKEDITOR.dialog.add(pluginCmd, this.path + "dialogs/latex-editor.js");

    // Add the menu button
    editor.ui.addButton('LatexEditor', {
      label: editor.lang.latexeditor.toolbar,
      command: pluginCmd,
      icon: this.path + 'icons/latex-editor.png',
      toolbar: 'insert'
    });

    // Add context-menu entry
    if (editor.contextMenu) {
      editor.addMenuGroup(editor.lang.latexeditor.menu);
      editor.addMenuItem('latexEditor', {
        label: editor.lang.latexeditor.edit,
        icon: this.path + 'icons/latex-editor.png',
        command: pluginCmd,
        group: editor.lang.latexeditor.menu
      });

      // if the selected item is image of class 'latex-image',
      // we should be interested in it
      editor.contextMenu.addListener(function (element) {

        // Check for cke span image wrapper
        if (element.getAscendant('span', true)){
          if (element.$.classList.contains('cke_widget_image')){
            element = element.getChild(0);
          }
        }

        element = element.getAscendant('img', true);
        if (element) {
          if (element.$.classList.contains('latex-image')) {
            editor.contextMenu.removeAll();
            return {
              latexEditor: CKEDITOR.TRISTATE_OFF
            };
          }
        }
      });
    }

    // Register on double click event to open the editor
    editor.on('doubleclick', function (evt) {
      var element = evt.data.element;
      if (element && element.is('img')) {
        if (element.$.classList.contains('latex-image')) {
          evt.data.dialog = pluginCmd;
        }
      }
    });
  }
});

	

