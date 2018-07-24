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
            }
          }, {
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

      if (!latexImage) {
        latexImage = editor.document.createElement('figure');
        latexImage.append(editor.document.createElement('img'));
        latexImage.append(editor.document.createElement('caption'));
        this.insertMode = true;
      } else {
        this.insertMode = false;
      }

      // set-up the field values based on selected or newly created latexImage
      if (!this.insertMode) {
        this.setupContent(latexImage);
      }
    },

    onOk: function () {
      // Retrieve value
      var equation = this.getValueOf('latexEditor', 'equation');
      var captionText = this.getValueOf('latexEditor', 'caption');

      // Create container
      var latexFigure = editor.document.createElement('figure');
      latexFigure.setAttribute('class', 'latex-figure');

      // Create image
      var img = editor.document.createElement('img');
      img.setAttribute('class', 'latex-image');
      img.setAttribute('src', Routing.generate('app_latex_renderlatex', {content: equation}));
      img.setAttribute('alt', encodeURIComponent(equation));

      // Create caption
      var caption = editor.document.createElement('figcaption');
      caption.setAttribute('class', 'latex-caption');
      caption.setHtml(captionText);

      // Add parts to container and put it in the document
      latexFigure.append(img);
      latexFigure.append(caption);

      // Find the top level figure and select it
      var parent = editor.getSelection().getStartElement().getAscendant('figure', true);
      editor.getSelection().selectElement(parent);

      // Insert the new element by replacing the selection
      editor.insertElement(latexFigure);

      clearInterval(timer);
    },

    onCancel: function () {
      clearInterval(timer);
    }
  };
});

