CKEDITOR.dialog.add('latexeditorDialog', function (editor) {

  var timer;
  var previousVal;

  var updateImage = function () {
    // Get the current dialog
    var dialog = CKEDITOR.dialog.getCurrent();
    var previewContainer = dialog.getContentElement('latexEditor', 'preview').getElement();
    var image = previewContainer.findOne('img');
    var enterMessage = previewContainer.findOne('.initial');
    var loadingMessage = previewContainer.findOne('.loader');

    // Remove all messages
    enterMessage.hide();
    loadingMessage.hide();

    // Retrieve the new value
    var content = dialog.getValueOf('latexEditor', 'equation');
    if (content === '') {
      // If empty, show message and remove image
      enterMessage.show();
      image.hide();
      return;
    }

    // Check if it is really an update
    if (previousVal === content || content === image.getAttribute('alt')) return;
    previousVal = content;

    // Update the preview
    loadingMessage.show();
    image.hide();
    image
        .setAttribute('alt', content) // Update values
        .setAttribute('src', Routing.generate('app_latex_renderlatex', {content: content}));
  };

  return {
    title: editor.lang.latexeditor.title,
    minWidth: 550,
    minHeight: 300,
    resizable: CKEDITOR.DIALOG_RESIZE_NONE,
    contents: [
      {
        id: 'latexEditor',
        label: 'LatexEditor',
        elements: [
          {
            id: 'caption',
            type: 'text',
            label: editor.lang.latexeditor.caption,
            default: '',
            setup: function (element) {
              // Retrieve caption value
              var valueElement = element.findOne('figcaption');
              if (valueElement) {
                this.setValue(valueElement.getHtml());
              }
            }
          },
          {
            id: 'equation',
            type: 'textarea',
            label: editor.lang.latexeditor.equation,
            default: '',
            setup: function (element) {
              // Retrieve image alt value
              var valueElement = element.findOne('img');
              if (valueElement) {
                this.setValue(decodeURIComponent(valueElement.getAttribute('alt')));
              }
              previousVal = null;
            },
            onShow: function () {
              updateImage();
              this.getElement().on('keyup', function () {
                clearTimeout(timer);
                timer = setTimeout(function () {
                  updateImage();
                }, 500);
              });
            },
            validate: function () {
              if (this.getValue().length < 1) {
                alert('You will need to enter a latex expression');
                return false;
              }
            }
          },
          {
            id: 'inline',
            type: 'checkbox',
            label: editor.lang.latexeditor.inline,
            default: false,
            onClick: function () {
              var checked = this.getValue();
              var captionElement = this.getDialog().getContentElement('latexEditor', 'caption');
              if (checked) {
                captionElement.disable();
              } else {
                captionElement.enable();
              }
            },
            setup: function (element) {
              var inline = element.getAttribute('class').includes('latex-figure-inline');
              this.setValue(inline);
              if (inline) {
                this.getDialog().getContentElement('latexEditor', 'caption').disable();
                this.getDialog().getContentElement('latexEditor', 'inline').disable();
              }
            }
          },
          {
            type: 'html',
            html: '<label>' + editor.lang.latexeditor.preview + ':</label>'
          },
          {
            id: 'preview',
            type: 'html',
            html: '<div>' +
                '<img class="latex-image" onload="$(this).parent().find(\'.initial,.loader\').hide(); $(this).show();"></img>' +
                '<span class="initial">Enter a equation</span>' +
                '<span class="loader">Loading...</span>' +
                '</div>'
          }
        ]
      }
    ],

    onShow: function () {
      var selection = editor.getSelection();
      var latexImage = selection.getStartElement().getAscendant('figure', true);
      var inlineImage = selection.getStartElement().getAscendant('span', true);
      if (inlineImage && (!inlineImage.getAttribute('class') || !inlineImage.getAttribute('class').includes('latex-figure'))) {
        inlineImage = null;
      }

      if (!latexImage && !inlineImage) {
        latexImage = editor.document.createElement('figure');
        latexImage.append(editor.document.createElement('img'));
        latexImage.append(editor.document.createElement('caption'));
        this.insertMode = true;
      } else {
        this.insertMode = false;
      }

      // set-up the field values based on selected or newly created latexImage
      if (!this.insertMode) {
        this.setupContent(inlineImage ? inlineImage : latexImage);
      }
    },

    onOk: function () {
      editor.undoManager.lock();

      // Retrieve value
      var equation = this.getValueOf('latexEditor', 'equation');
      var captionText = this.getValueOf('latexEditor', 'caption');
      var inline = this.getValueOf('latexEditor', 'inline');

      // Create container
      var latexFigure = editor.document.createElement('figure');
      latexFigure.setAttribute('class', inline ? 'latex-figure latex-figure-inline' : 'latex-figure');

      // Create image
      var img = editor.document.createElement('img');
      img.setAttribute('contenteditable', false);
      img.setAttribute('class', 'latex-image');
      img.setAttribute('src', Routing.generate('app_latex_renderlatex', {content: equation}));
      img.setAttribute('alt', encodeURIComponent(equation));
      latexFigure.append(img);

      if (!inline) {
        // Only set when not inline
        var caption = editor.document.createElement('figcaption');
        caption.setAttribute('class', 'latex-caption');
        caption.setHtml(captionText);
        latexFigure.append(caption);
      }

      // Find the top level figure and select it
      var parent = editor.getSelection().getStartElement().getAscendant('figure', true);
      if (parent) {
        editor.getSelection().selectElement(parent);
      } else {
        // Find span with specific class in case of inline
        var spanParent = editor.getSelection().getStartElement().getAscendant('span', true);
        if (spanParent) {
          var spanParentClasses = spanParent.getAttribute('class');
          if (spanParentClasses && spanParentClasses.includes('latex-figure-inline')) {
            // If found, remove it
            editor.getSelection().selectElement(spanParent);
          } else {
            spanParent = null;
          }
        }
      }

      // Insert the new element
      if (inline) {
        if (!spanParent) {
          var span = editor.document.createElement('span');
          span.setAttribute('class', 'latex-figure latex-figure-inline');
          span.append(img);
          editor.insertElement(span);
        } else {
          spanParent.setHtml(img.getOuterHtml());
        }
      } else {
        editor.insertElement(latexFigure);
      }

      clearInterval(timer);
      editor.undoManager.unlock();
    },

    onCancel: function () {
      clearInterval(timer);
    }
  };
});

