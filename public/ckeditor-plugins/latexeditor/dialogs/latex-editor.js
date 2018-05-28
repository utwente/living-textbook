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
    minHeight: 250,
    resizable: CKEDITOR.DIALOG_RESIZE_NONE,
    contents: [
      {
        id: 'latexEditor',
        label: 'LatexEditor',
        elements: [
          {
            id: 'equation',
            type: 'textarea',
            label: editor.lang.latexeditor.equation,
            default: '',
            setup: function (element) {
              this.setValue(element.getAttribute('alt'));
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
      var image = selection.getStartElement().getAscendant('img', true);

      if (!image) {
        image = editor.document.createElement('img');
        this.insertMode = true;
      } else {
        this.insertMode = false;
      }

      // set-up the field values based on selected or newly created image
      if (!this.insertMode) {
        this.setupContent(image);
      }
    },

    onOk: function () {
      // Retrieve value
      var content = this.getValueOf('latexEditor', 'equation');

      // Create image
      var img = editor.document.createElement('img');
      img.setAttribute('class', 'latex-image');
      img.setAttribute('src', Routing.generate('app_latex_renderlatex', {content: content}));
      img.setAttribute('alt', content);
      editor.insertElement(img);

      clearInterval(timer);
    },

    onCancel: function () {
      clearInterval(timer);
    }
  };
});

