CKEDITOR.plugins.add('latexeditor', {
  availableLangs: {
    en: 1
  },
  lang: 'en',
  requires: ['dialog'],
  icons: 'latex-editor',

  init: function (editor) {
    var pluginCmd = 'latexeditorDialog';

    // Register the dialog
    editor.addCommand(pluginCmd, new CKEDITOR.dialogCommand(pluginCmd,
        {
          allowedContent: 'span;figure;img[src,alt];figcaption',
          requiredContent: 'figure;img;figcaption',
          canUndo: false
        })
    );
    CKEDITOR.dialog.add(pluginCmd, this.path + 'dialogs/latex-editor.js');

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
            || (element.name === 'figure' && element.hasClass('latex-figure'))
            || (element.name === 'span' && element.hasClass('latex-figure'))) {
          return false;
        }
      });
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

        var parentSpan = element.getAscendant('span', true);
        if (parentSpan) {

          // Check for cke span image wrapper
          if (element.$.classList.contains('cke_widget_image')) {
            element = element.getChild(0);
          }

          // Check for our own wrapper
          if (parentSpan.$.classList.contains('latex-figure-inline')) {
            return {
              latexEditor: CKEDITOR.TRISTATE_OFF
            };
          }
        }

        element = element.getAscendant('figure', true);
        if (element && element.$.classList.contains('latex-figure')) {
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
          || (element.is('span') && element.hasClass('latex-figure'))
          || (element.is('img') && element.hasClass('latex-image'))
          || (element.is('caption') && element.hasClass('latex-caption'))) {
        editor.getSelection().selectElement(element);
        evt.data.dialog = pluginCmd;
      }
    });
  }
});

	

