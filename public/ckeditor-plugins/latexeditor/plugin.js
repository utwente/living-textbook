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
        var res = {};
        if (element.getAscendant('img', true)) {
          if (element.getAttribute('class') === 'latex-image') {
            res['latexEditor'] = CKEDITOR.TRISTATE_OFF;
            return res;
          }
        }
      });

    }

    // Register on double click event to open the editor
    editor.on('doubleclick', function (evt) {
      var element = evt.data.element;
      if (element && element.is('img')) {
        if (element.getAttribute('class') === 'latex-image') {
          evt.data.dialog = pluginCmd;
        }
      }
    });
  }
});

	

