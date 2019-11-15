export interface Configuration {
    // Fixed node layout
    baseNodeRadius: number;
    extendNodeRatio: number;
    nodeLineWidth: number;

    // Fixed node label layout
    minCharCount: number;
    defaultNodeLabelFontSize: number;
    activeNodeLabelLineWidth: number;
    fontFamily: string;
    defaultNodeLabelFont: string;
    activeNodeLabelFont: string;
    nodeLabelFontScaleStep: number;
    nodeLabelFontScaleStepSize: number;

    // Node styles
    defaultNodeFillStyle: string;
    defaultNodeStrokeStyle: string;
    draggedNodeFillStyle: string;
    draggedNodeStrokeStyle: string;
    fadedNodeFillStyle: string;
    fadedNodeStrokeStyle: string;
    highlightedNodeFillStyle: string;
    highlightedNodeStrokeStyle: string;

    // Link styles
    linkLineWidth: number;
    defaultLinkStrokeStyle: string;
    draggedLinkStrokeStyle: string;
    fadedLinksStrokeStyle: string;
    highlightedLinkStrokeStyle: string;

    // Node label styles
    defaultNodeLabelColor: string;
    whiteNodeLabelColor: string;
    activeNodeLabelStrokeStyle: string;

    // Functions
    applyStyle: (style: number) => void;
    darkenedNodeColor: (style: number) => string;
    updateLabel: (node: any, scaleFactor: number) => void;
}
