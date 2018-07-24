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
          allowedContent: 'figure;img[src,alt];figcaption',
          requiredContent: 'figure;img;figcaption'
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

    // Images with the 'latex-image' class will not be upcasted by the image widget
    if (editor.widgets) {
      editor.widgets.addUpcastCallback(function (element) {
        if ((element.name === 'img' && element.hasClass('latex-image'))
            || (element.name === 'figure' && element.hasClass('latex-figure'))) {
          return false;
        }
      })
    }

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
        if (element.getAscendant('span', true)) {
          if (element.$.classList.contains('cke_widget_image')) {
            element = element.getChild(0);
          }
        }

        element = element.getAscendant('figure', true);
        if (element) {
          return {
            latexEditor: CKEDITOR.TRISTATE_OFF
          };
        }
      });
    }

    // Register on double click event to open the editor
    editor.on('doubleclick', function (evt) {
      var element = evt.data.element;
      if (!element) return;

      if ((element.is('figure') && element.hasClass('latex-figure'))
          || (element.is('img') && element.hasClass('latex-image'))
          || (element.is('caption') && element.hasClass('latex-caption'))) {
        evt.data.dialog = pluginCmd;
      }
    });
  }
});

	

