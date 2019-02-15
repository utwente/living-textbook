/**
 * Register browser configuration
 */
(function (bConfig) {

  // Fixed node layout
  bConfig.baseNodeRadius = 8; // Node base radius
  bConfig.extendNodeRatio = 3;
  bConfig.nodeLineWidth = 2;

  // Fixed node label layout
  bConfig.minCharCount = 12;
  bConfig.defaultNodeLabelFontSize = 10;
  bConfig.activeNodeLabelLineWidth = 1.5;
  bConfig.defaultNodeLabelFont = bConfig.defaultNodeLabelFontSize + 'px "DroidSans, arial, serif"';
  bConfig.activeNodeLabelFont = 'bold ' + bConfig.defaultNodeLabelFont;

  // Node styles
  bConfig.defaultNodeFillStyle = '';
  bConfig.defaultNodeStrokeStyle = '';
  bConfig.draggedNodeFillStyle = '';
  bConfig.draggedNodeStrokeStyle = '';
  bConfig.fadedNodeFillStyle = '';
  bConfig.fadedNodeStrokeStyle = '';
  bConfig.highlightedNodeFillStyle = '';
  bConfig.highlightedNodeStrokeStyle = '';

  // Link styles
  bConfig.linkLineWidth = 1;
  bConfig.defaultLinkStrokeStyle = '#696969';
  bConfig.draggedLinkStrokeStyle = '#333';
  bConfig.fadedLinksStrokeStyle = '#E0E0E0';
  bConfig.highlightedLinkStrokeStyle = bConfig.draggedLinkStrokeStyle;

  // Node label styles
  bConfig.defaultNodeLabelColor = '#000';
  bConfig.whiteNodeLabelColor = '#fff';
  bConfig.activeNodeLabelStrokeStyle = '#fff';

  bConfig.applyStyle = function (style) {
    switch (style) {
      case -1: { // Grey 'empty' state
        // Node styles
        bConfig.defaultNodeFillStyle = '#8e8e8e';
        bConfig.defaultNodeStrokeStyle = '#d5d5d5';
        bConfig.draggedNodeFillStyle = bConfig.defaultNodeFillStyle;
        bConfig.draggedNodeStrokeStyle = '#737373';
        bConfig.fadedNodeFillStyle = '#bdbdbd';
        bConfig.fadedNodeStrokeStyle = '#e1e1e1';
        bConfig.highlightedNodeFillStyle = bConfig.draggedNodeFillStyle;
        bConfig.highlightedNodeStrokeStyle = bConfig.draggedNodeStrokeStyle;

        break;
      }
      case 1: {
        // Node styles
        bConfig.defaultNodeFillStyle = '#de5356';
        bConfig.defaultNodeStrokeStyle = '#fff';
        bConfig.draggedNodeFillStyle = bConfig.defaultNodeFillStyle;
        bConfig.draggedNodeStrokeStyle = '#ff2340';
        bConfig.fadedNodeFillStyle = '#bc6d73';
        bConfig.fadedNodeStrokeStyle = '#fff';
        bConfig.highlightedNodeFillStyle = bConfig.draggedNodeFillStyle;
        bConfig.highlightedNodeStrokeStyle = bConfig.draggedNodeStrokeStyle;

        break;
      }
      case 2: {
        // Node styles
        bConfig.defaultNodeFillStyle = '#75de79';
        bConfig.defaultNodeStrokeStyle = '#fff';
        bConfig.draggedNodeFillStyle = bConfig.defaultNodeFillStyle;
        bConfig.draggedNodeStrokeStyle = '#1ac321';
        bConfig.fadedNodeFillStyle = '#9ebc9d';
        bConfig.fadedNodeStrokeStyle = '#fff';
        bConfig.highlightedNodeFillStyle = bConfig.draggedNodeFillStyle;
        bConfig.highlightedNodeStrokeStyle = bConfig.draggedNodeStrokeStyle;

        break;
      }
      case 3: {
        // Node styles
        bConfig.defaultNodeFillStyle = '#a4a5fe';
        bConfig.defaultNodeStrokeStyle = '#fff';
        bConfig.draggedNodeFillStyle = bConfig.defaultNodeFillStyle;
        bConfig.draggedNodeStrokeStyle = '#1513ff';
        bConfig.fadedNodeFillStyle = '#55557a';
        bConfig.fadedNodeStrokeStyle = '#fff';
        bConfig.highlightedNodeFillStyle = bConfig.draggedNodeFillStyle;
        bConfig.highlightedNodeStrokeStyle = bConfig.draggedNodeStrokeStyle;

        break;
      }
      case 4: {
        // Node styles
        bConfig.defaultNodeFillStyle = '#deaf6c';
        bConfig.defaultNodeStrokeStyle = '#fff';
        bConfig.draggedNodeFillStyle = bConfig.defaultNodeFillStyle;
        bConfig.draggedNodeStrokeStyle = '#ff5d00';
        bConfig.fadedNodeFillStyle = '#bcac9b';
        bConfig.fadedNodeStrokeStyle = bConfig.fadedNodeFillStyle;
        bConfig.highlightedNodeFillStyle = bConfig.draggedNodeFillStyle;
        bConfig.highlightedNodeStrokeStyle = bConfig.draggedNodeStrokeStyle;

        break;
      }
      case 0:
        /* falls through */
      default: {
        // Node styles
        bConfig.defaultNodeFillStyle = '#b1ded2';
        bConfig.defaultNodeStrokeStyle = '#fff';
        bConfig.draggedNodeFillStyle = bConfig.defaultNodeFillStyle;
        bConfig.draggedNodeStrokeStyle = '#2359ff';
        bConfig.fadedNodeFillStyle = '#E6ECE4';
        bConfig.fadedNodeStrokeStyle = '#fff';
        bConfig.highlightedNodeFillStyle = bConfig.draggedNodeFillStyle;
        bConfig.highlightedNodeStrokeStyle = bConfig.draggedNodeStrokeStyle;

        break;
      }
    }
  };

  /**
   * Load the node label
   *
   * @param node
   * @param scaleFactor
   */
  bConfig.updateLabel = function (node, scaleFactor) {
    // Set default label values
    node.expandedLabelStart = 0;
    node.expandedLabel = [];
    if (node.label === '') return;

    // Calculate node text lines
    const lines = node.label.split(' ');
    if (lines.length <= 2 && node.label.length <= (bConfig.minCharCount + 1)) {
      node.expandedLabel = lines;
    } else {
      // Check if next line can be combined with the last line
      node.expandedLabel.push(lines[0]);
      for (let i = 1; i < lines.length; i++) {
        if (node.expandedLabel[node.expandedLabel.length - 1].length + lines[i].length <= bConfig.minCharCount) {
          node.expandedLabel[node.expandedLabel.length - 1] += ' ' + lines[i];
        } else {
          node.expandedLabel.push(lines[i]);
        }
      }
    }

    // Calculate offset for the amount of lines
    node.expandedLabelStart = (node.expandedLabel.length - 1) * (0.5 * bConfig.defaultNodeLabelFontSize * scaleFactor);
  };
}(window.bConfig = window.bConfig || {}));
