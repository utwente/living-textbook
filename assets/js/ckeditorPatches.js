/**
 * Run these patches when CKEDITOR is detected
 */
if (window.CKEDITOR) {
  // Update h1 & h2 styles to use h3 & h4 tags
  CKEDITOR.config.format_h1 = {element: 'h3'};
  CKEDITOR.config.format_h2 = {element: 'h4'};

  // // Add extra styles
  CKEDITOR.config.format_highlight = {name: "Highlight", element: 'div', attributes: { 'class': 'highlight'}};
  CKEDITOR.config.format_symbol = {name: "Symbol", element: 'div', attributes: { 'class': 'symbol'}};
  CKEDITOR.config.format_citation = {name: "Citation", element: 'div', attributes: { 'class': 'citation'}};
  CKEDITOR.config.format_reference = {name: "Reference", element: 'div', attributes: { 'class': 'reference'}};

  if (ckeditorCss) {
    CKEDITOR.config.contentsCss = ckeditorCss;
  }

  // Set caption checkbox by default in image dialog
  CKEDITOR.on('dialogDefinition', (evt) => {
    // Only parse image properties
    if (evt.data.name !== 'image2') return;

    let definition = evt.data.definition;
    let info = definition.getContents('info');

    let captionBox = info.get('hasCaption');
    let origSetup = captionBox['setup'];

    // This needs to be a function in order to scope this correctly!
    captionBox['setup'] = function (widget) {
      // Set hasCaption to true if there is no src
      if (widget.data.src === "") {
        widget.data.hasCaption = true;
      }
      origSetup.bind(this)(widget);
    };
  });

  // Fix table dialog definition
  CKEDITOR.on('dialogDefinition', (evt) => {
    // Only parse table properties
    if (evt.data.name !== 'table' && evt.data.name !== 'tableProperties') return;

    let definition = evt.data.definition;
    let info = definition.getContents('info');

    // Move txtCols to other column
    let txtCols = info.get('txtCols');
    info.remove('txtCols');
    info.add(txtCols, 'txtWidth');

    // Remove unwanted fields
    info.remove('txtCellSpace');
    info.remove('txtCellPad');
    info.remove('txtHeight');
    info.remove('txtWidth');
    info.remove('txtBorder');
    info.remove('cmbAlign');
    info.remove('txtSummary');

    // Fix dialog size
    definition.minHeight = 185;
  });
}
