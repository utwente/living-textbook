import Color from 'color';
import {NodeType} from './ConceptBrowser';

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
    public defaultNodeLabelColor = '';
    public whiteNodeLabelColor = '';
    public activeNodeLabelStrokeStyle = '';

    // Instance defaults
    public instanceDefaultColor = 4;

    private customColorCache: {
        [color: string]: {
            defaultNodeFillStyle: string;
            defaultNodeStrokeStyle: string;
            draggedNodeFillStyle: string;
            draggedNodeStrokeStyle: string;
            fadedNodeFillStyle: string;
            fadedNodeStrokeStyle: string;
            highlightedNodeFillStyle: string;
            highlightedNodeStrokeStyle: string;
            defaultNodeLabelColor: string;
            whiteNodeLabelColor: string;
            activeNodeLabelStrokeStyle: string;
        };
    } = {};

    public applyStyle(style: string | number) {
        if (typeof style === 'number') {
            style = String(style);
        }

        switch (style) {
            case '-1': { // Grey 'empty' state
                // Node styles
                this.defaultNodeFillStyle = '#a2a2a2';
                this.defaultNodeStrokeStyle = '#d5d5d5';
                this.draggedNodeFillStyle = this.defaultNodeFillStyle;
                this.draggedNodeStrokeStyle = '#999999';
                this.fadedNodeFillStyle = '#bdbdbd';
                this.fadedNodeStrokeStyle = '#e1e1e1';
                this.highlightedNodeFillStyle = this.draggedNodeFillStyle;
                this.highlightedNodeStrokeStyle = this.draggedNodeStrokeStyle;
                this.defaultNodeLabelColor = '#000';
                this.whiteNodeLabelColor = '#fff';
                this.activeNodeLabelStrokeStyle = '#fff';

                break;
            }
            case '1': {
                // Node styles
                this.defaultNodeFillStyle = '#de5356';
                this.defaultNodeStrokeStyle = '#fff';
                this.draggedNodeFillStyle = this.defaultNodeFillStyle;
                this.draggedNodeStrokeStyle = '#ff2340';
                this.fadedNodeFillStyle = '#bc6d73';
                this.fadedNodeStrokeStyle = '#fff';
                this.highlightedNodeFillStyle = this.draggedNodeFillStyle;
                this.highlightedNodeStrokeStyle = this.draggedNodeStrokeStyle;
                this.defaultNodeLabelColor = '#000';
                this.whiteNodeLabelColor = '#fff';
                this.activeNodeLabelStrokeStyle = '#fff';

                break;
            }
            case '2': {
                // Node styles
                this.defaultNodeFillStyle = '#75de79';
                this.defaultNodeStrokeStyle = '#fff';
                this.draggedNodeFillStyle = this.defaultNodeFillStyle;
                this.draggedNodeStrokeStyle = '#1ac321';
                this.fadedNodeFillStyle = '#9ebc9d';
                this.fadedNodeStrokeStyle = '#fff';
                this.highlightedNodeFillStyle = this.draggedNodeFillStyle;
                this.highlightedNodeStrokeStyle = this.draggedNodeStrokeStyle;
                this.defaultNodeLabelColor = '#000';
                this.whiteNodeLabelColor = '#fff';
                this.activeNodeLabelStrokeStyle = '#fff';

                break;
            }
            case '3': {
                // Node styles
                this.defaultNodeFillStyle = '#a4a5fe';
                this.defaultNodeStrokeStyle = '#fff';
                this.draggedNodeFillStyle = this.defaultNodeFillStyle;
                this.draggedNodeStrokeStyle = '#1513ff';
                this.fadedNodeFillStyle = '#55557a';
                this.fadedNodeStrokeStyle = '#fff';
                this.highlightedNodeFillStyle = this.draggedNodeFillStyle;
                this.highlightedNodeStrokeStyle = this.draggedNodeStrokeStyle;
                this.defaultNodeLabelColor = '#000';
                this.whiteNodeLabelColor = '#fff';
                this.activeNodeLabelStrokeStyle = '#fff';

                break;
            }
            case '4': {
                // Node styles
                this.defaultNodeFillStyle = '#deaf6c';
                this.defaultNodeStrokeStyle = '#fff';
                this.draggedNodeFillStyle = this.defaultNodeFillStyle;
                this.draggedNodeStrokeStyle = '#ff5d00';
                this.fadedNodeFillStyle = '#bcac9b';
                this.fadedNodeStrokeStyle = this.fadedNodeFillStyle;
                this.highlightedNodeFillStyle = this.draggedNodeFillStyle;
                this.highlightedNodeStrokeStyle = this.draggedNodeStrokeStyle;
                this.defaultNodeLabelColor = '#000';
                this.whiteNodeLabelColor = '#fff';
                this.activeNodeLabelStrokeStyle = '#fff';

                break;
            }
            case '0': {
                // Node styles
                this.defaultNodeFillStyle = '#b1ded2';
                this.defaultNodeStrokeStyle = '#fff';
                this.draggedNodeFillStyle = this.defaultNodeFillStyle;
                this.draggedNodeStrokeStyle = '#2359ff';
                this.fadedNodeFillStyle = '#E6ECE4';
                this.fadedNodeStrokeStyle = '#fff';
                this.highlightedNodeFillStyle = this.draggedNodeFillStyle;
                this.highlightedNodeStrokeStyle = this.draggedNodeStrokeStyle;
                this.defaultNodeLabelColor = '#000';
                this.whiteNodeLabelColor = '#fff';
                this.activeNodeLabelStrokeStyle = '#fff';

                break;
            }
            default: {
                // Node styles need to be calculated from the supplied color
                // We use an in memory cache to speed up the selection in case of repetition
                if (!(style in this.customColorCache)) {
                    style = String(style);
                    const color = Color(style);
                    const isDark = color.isDark();
                    this.customColorCache[style] = {
                        defaultNodeFillStyle: style,
                        defaultNodeStrokeStyle: '#fff',
                        draggedNodeFillStyle: style,
                        draggedNodeStrokeStyle: color.darken(0.4).hex(),
                        fadedNodeFillStyle: color.lighten(0.4).hex(),
                        fadedNodeStrokeStyle: '#fff',
                        highlightedNodeFillStyle: style,
                        highlightedNodeStrokeStyle: color.darken(0.4).hex(),
                        defaultNodeLabelColor: isDark ? '#fff' : '#000',
                        whiteNodeLabelColor: isDark ? '#000' : '#fff',
                        activeNodeLabelStrokeStyle: isDark ? '#000' : '#fff',
                    };
                }

                const customColor = this.customColorCache[style];
                this.defaultNodeFillStyle = customColor.defaultNodeFillStyle;
                this.defaultNodeStrokeStyle = customColor.defaultNodeStrokeStyle;
                this.draggedNodeFillStyle = customColor.draggedNodeFillStyle;
                this.draggedNodeStrokeStyle = customColor.draggedNodeStrokeStyle;
                this.fadedNodeFillStyle = customColor.fadedNodeFillStyle;
                this.fadedNodeStrokeStyle = customColor.fadedNodeStrokeStyle;
                this.highlightedNodeFillStyle = customColor.highlightedNodeFillStyle;
                this.highlightedNodeStrokeStyle = customColor.highlightedNodeStrokeStyle;
                this.defaultNodeLabelColor = customColor.defaultNodeLabelColor;
                this.whiteNodeLabelColor = customColor.whiteNodeLabelColor;
                this.activeNodeLabelStrokeStyle = customColor.activeNodeLabelStrokeStyle;
                break;
            }
        }
    }

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
    }

    /**
     * Load the node label
     */
    public updateLabel(node: NodeType | any, scaleFactor: number) {
        // Set default label values
        node.expandedLabelStart = 0;
        node.expandedLabel = [];
        if (node.label === '') {
            return;
        }

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
    }

    /**
     * Shade color
     * Source: https://stackoverflow.com/questions/5560248/programmatically-lighten-or-darken-a-hex-color-or-rgb-and-blend-colors
     */
    private shadeHexColor(color: string, percent: number): string {
        const f = parseInt(color.slice(1), 16);
        const t = percent < 0 ? 0 : 255;
        const p = percent < 0 ? percent * -1 : percent;

        /* tslint:disable:no-bitwise */
        const R = f >> 16;
        const G = f >> 8 & 0x00FF;
        const B = f & 0x0000FF;
        /* tslint:enable:no-bitwise */

        return '#'
            + (0x1000000 + (Math.round((t - R) * p) + R) * 0x10000
                + (Math.round((t - G) * p) + G) * 0x100
                + (Math.round((t - B) * p) + B)).toString(16).slice(1);
    }
}

const instance = new BrowserConfiguration();

export default instance;
