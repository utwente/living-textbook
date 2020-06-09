import {NodeType} from "@/conceptBrowser/ConceptBrowser";

// noinspection JSMethodCanBeStatic
export class BrowserConfiguration {

    // Fixed node layout
    public baseNodeRadius = 8; // Node base radius
    public extendNodeRatio = 2;
    public nodeLineWidth = 2;

    // Fixed node label layout
    public minCharCount = 12;
    public defaultNodeLabelFontSize = 12;
    public activeNodeLabelLineWidth = 1;
    public fontFamily = 'DroidSans, Arial, sans-serif';
    public defaultNodeLabelFont = this.defaultNodeLabelFontSize + 'px ' + this.fontFamily;
    public activeNodeLabelFont = 'bold ' + this.defaultNodeLabelFont;
    public nodeLabelFontScaleStep = 3; // Each step amount of links, the step size is subtracted from the font size
    public nodeLabelFontScaleStepSize = 2;

    // Node styles
    public defaultNodeFillStyle = '';
    public defaultNodeStrokeStyle = '';
    public draggedNodeFillStyle = '';
    public draggedNodeStrokeStyle = '';
    public fadedNodeFillStyle = '';
    public fadedNodeStrokeStyle = '';
    public highlightedNodeFillStyle = '';
    public highlightedNodeStrokeStyle = '';

    // Link styles
    public linkLineWidth = 1;
    public defaultLinkStrokeStyle = '#696969';
    public draggedLinkStrokeStyle = '#333';
    public fadedLinksStrokeStyle = '#E0E0E0';
    public highlightedLinkStrokeStyle = this.draggedLinkStrokeStyle;

    // Node label styles
    public defaultNodeLabelColor = '#000';
    public whiteNodeLabelColor = '#fff';
    public activeNodeLabelStrokeStyle = '#fff';

    // Instance defaults
    public instanceDefaultColor = 4;

    public applyStyle(style: number) {
        switch (style) {
            case -1: { // Grey 'empty' state
                // Node styles
                this.defaultNodeFillStyle = '#8e8e8e';
                this.defaultNodeStrokeStyle = '#d5d5d5';
                this.draggedNodeFillStyle = this.defaultNodeFillStyle;
                this.draggedNodeStrokeStyle = '#737373';
                this.fadedNodeFillStyle = '#bdbdbd';
                this.fadedNodeStrokeStyle = '#e1e1e1';
                this.highlightedNodeFillStyle = this.draggedNodeFillStyle;
                this.highlightedNodeStrokeStyle = this.draggedNodeStrokeStyle;

                break;
            }
            case 1: {
                // Node styles
                this.defaultNodeFillStyle = '#de5356';
                this.defaultNodeStrokeStyle = '#fff';
                this.draggedNodeFillStyle = this.defaultNodeFillStyle;
                this.draggedNodeStrokeStyle = '#ff2340';
                this.fadedNodeFillStyle = '#bc6d73';
                this.fadedNodeStrokeStyle = '#fff';
                this.highlightedNodeFillStyle = this.draggedNodeFillStyle;
                this.highlightedNodeStrokeStyle = this.draggedNodeStrokeStyle;

                break;
            }
            case 2: {
                // Node styles
                this.defaultNodeFillStyle = '#75de79';
                this.defaultNodeStrokeStyle = '#fff';
                this.draggedNodeFillStyle = this.defaultNodeFillStyle;
                this.draggedNodeStrokeStyle = '#1ac321';
                this.fadedNodeFillStyle = '#9ebc9d';
                this.fadedNodeStrokeStyle = '#fff';
                this.highlightedNodeFillStyle = this.draggedNodeFillStyle;
                this.highlightedNodeStrokeStyle = this.draggedNodeStrokeStyle;

                break;
            }
            case 3: {
                // Node styles
                this.defaultNodeFillStyle = '#a4a5fe';
                this.defaultNodeStrokeStyle = '#fff';
                this.draggedNodeFillStyle = this.defaultNodeFillStyle;
                this.draggedNodeStrokeStyle = '#1513ff';
                this.fadedNodeFillStyle = '#55557a';
                this.fadedNodeStrokeStyle = '#fff';
                this.highlightedNodeFillStyle = this.draggedNodeFillStyle;
                this.highlightedNodeStrokeStyle = this.draggedNodeStrokeStyle;

                break;
            }
            case 4: {
                // Node styles
                this.defaultNodeFillStyle = '#deaf6c';
                this.defaultNodeStrokeStyle = '#fff';
                this.draggedNodeFillStyle = this.defaultNodeFillStyle;
                this.draggedNodeStrokeStyle = '#ff5d00';
                this.fadedNodeFillStyle = '#bcac9b';
                this.fadedNodeStrokeStyle = this.fadedNodeFillStyle;
                this.highlightedNodeFillStyle = this.draggedNodeFillStyle;
                this.highlightedNodeStrokeStyle = this.draggedNodeStrokeStyle;

                break;
            }
            case 0:
            /* falls through */
            default: {
                // Node styles
                this.defaultNodeFillStyle = '#b1ded2';
                this.defaultNodeStrokeStyle = '#fff';
                this.draggedNodeFillStyle = this.defaultNodeFillStyle;
                this.draggedNodeStrokeStyle = '#2359ff';
                this.fadedNodeFillStyle = '#E6ECE4';
                this.fadedNodeStrokeStyle = '#fff';
                this.highlightedNodeFillStyle = this.draggedNodeFillStyle;
                this.highlightedNodeStrokeStyle = this.draggedNodeStrokeStyle;

                break;
            }
        }
    };

    /**
     * Darken theme node color
     */
    public darkenedNodeColor(color: number): string {
        let colorCode;
        switch (color) {
            case -1: { // Grey 'empty' state
                colorCode = '#8e8e8e';
                break;
            }
            case 1: {
                colorCode = '#de5356';
                break;
            }
            case 2: {
                colorCode = '#75de79';
                break;
            }
            case 3: {
                colorCode = '#a4a5fe';
                break;
            }
            case 4: {
                colorCode = '#deaf6c';
                break;
            }
            case 0:
            /* falls through */
            default: {
                colorCode = '#b1ded2';
            }
        }

        return this.shadeHexColor(colorCode, -0.2);
    };


    /**
     * Shade color
     * Source: https://stackoverflow.com/questions/5560248/programmatically-lighten-or-darken-a-hex-color-or-rgb-and-blend-colors
     */
    private shadeHexColor(color: string, percent: number): string {
        const f = parseInt(color.slice(1), 16), t = percent < 0 ? 0 : 255, p = percent < 0 ? percent * -1 : percent,
            R = f >> 16, G = f >> 8 & 0x00FF, B = f & 0x0000FF;
        return '#' + (0x1000000 + (Math.round((t - R) * p) + R) * 0x10000 + (Math.round((t - G) * p) + G) * 0x100 + (Math.round((t - B) * p) + B)).toString(16).slice(1);
    }

    /**
     * Load the node label
     */
    public updateLabel(node: NodeType | any, scaleFactor: number) {
        // Set default label values
        node.expandedLabelStart = 0;
        node.expandedLabel = [];
        if (node.label === '') return;

        // Calculate node text lines
        const lines = node.label.split(' ');
        if (lines.length <= 2 && node.label.length <= (this.minCharCount + 1)) {
            node.expandedLabel = lines;
        } else {
            // Check if next line can be combined with the last line
            node.expandedLabel.push(lines[0]);
            for (let i = 1; i < lines.length; i++) {
                if (node.expandedLabel[node.expandedLabel.length - 1].length + lines[i].length <= this.minCharCount) {
                    node.expandedLabel[node.expandedLabel.length - 1] += ' ' + lines[i];
                } else {
                    node.expandedLabel.push(lines[i]);
                }
            }
        }

        // Calculate offset for the amount of lines
        node.expandedLabelStart = (node.expandedLabel.length - 1) * (0.5 * this.defaultNodeLabelFontSize * scaleFactor);
    };
}

const instance = new BrowserConfiguration();

export default instance;
