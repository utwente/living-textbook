import * as d3 from 'd3';
import {ZoomTransform} from 'd3-zoom';
import '../../css/conceptBrowser/conceptBrowser.scss';
import BrowserConfigurationInstance, {BrowserConfiguration} from './BrowserConfiguration';
import ConceptBrowserRenderer from './ConceptBrowserRenderer';

export interface JsonType {
    numberOfLinks: number;
    id: number;
    name: string;
    instance: boolean;
    isEmpty: boolean;
    relations: Array<{
        id: number;
        target: number;
        relationName: string;
    }>;
    tags: Array<{
        id: number;
        color: string;
        name: string;
    }>;
}

export interface NodeType {
    id: number;
    index: number;
    x: number;
    y: number;
    vx: number;
    vy: number;
    fx: number | null;
    fy: number | null;
    color: number;
    radius: number;
    label: string;
    instance: boolean;
    expandedLabel: string[];
    expandedLabelStart: number;
    fontScale: number;
    link: string;
    numberOfLinks: number;
    individuals: string;
    dragged: boolean;
    highlighted: boolean;
    specialHilight: boolean;
    linkNode?: boolean;
    empty: boolean;
    tags: number[];
}

export interface LinkType {
    id: number;
    source: NodeType;
    target: NodeType;
    relationName: string;
}

export interface LinkNodeType extends NodeType, LinkType {
}


/* tslint:disable:variable-name member-ordering */
// noinspection JSMethodCanBeStatic
/**
 * The concept browser class
 */
export default class ConceptBrowser {
    private readonly config: BrowserConfiguration = BrowserConfigurationInstance;
    private readonly renderer!: ConceptBrowserRenderer;

    // Display settings
    private readonly mapWidth: number = 3000;
    private readonly mapWidthDragMargin: number = this.mapWidth / 30;
    private readonly mapHeight: number = 2000;
    private readonly mapHeightDragMargin: number = this.mapHeight / 20;
    private readonly zoomMargin: number = 25;

    // Force settings
    private readonly collideStrength: number = 0.6;
    private readonly collideIterations: number = 1;
    private readonly linkStrength: number = 0.9;
    private readonly manyBodyStrength: number = -70;
    private readonly manyBodyDistanceMin: number = 20;
    private readonly manyBodyDistanceMax: number = 1500;
    private readonly boundForceStrength: number = 80;
    private readonly linkNodeRadius: number = 20;
    private readonly nodeRadiusMargin: number = 10;

    // General settings
    private readonly zoomExtent: [number, number] = [0.1, 8]; // [min,max] zoom, min is also limited by screen size
    private readonly zoomButtonFactor: number = 1.5;

    /******************************************************************************************************
     * Control element references
     *****************************************************************************************************/

    private $filterBtn!: JQuery<HTMLElement>;
    private $select2Elements: Array<JQuery<HTMLElement>> = [];

    /******************************************************************************************************
     * Internal variables
     *****************************************************************************************************/

    private readonly canvas: HTMLCanvasElement;
    private _canvasWidth!: number;
    private _canvasHeight!: number;
    private _halfCanvasWidth!: number;
    private _halfCanvasHeight!: number;
    private halfMapWidth: number = this.mapWidth / 2;
    private halfMapHeight: number = this.mapHeight / 2;
    private cbCanvas: any;
    private cbSimulation: any;
    private cbZoom: any;
    private _cbTransform = d3.zoomIdentity;
    private cbDrag: any;
    private dragPosY: any;
    private dragPosX: any;
    private _isDragging = false;
    private _highlightedNode: NodeType | null = null;
    private _specialHighlightedNode: NodeType | null = null;
    private mouseMoveDisabled = false;
    private clickSend = false;
    private contextMenuNode: NodeType | null = null;
    private lastTransformed: any;
    private isLoaded = false;
    private isPaused = false;
    private _isVisible = false;

    // Initialize the graph object
    private cbGraph: { nodes: NodeType[], links: LinkType[], linkNodes: LinkNodeType[] } = {
        nodes: [],
        links: [],
        linkNodes: [],
    };

    private tags: {
        [id: number]: {
            id: number;
            name: string;
            color: string;
        },
    } = {};

    public get canvasWidth(): number {
        return this._canvasWidth;
    }

    public get canvasHeight(): number {
        return this._canvasHeight;
    }

    public get halfCanvasWidth(): number {
        return this._halfCanvasWidth;
    }

    public get halfCanvasHeight(): number {
        return this._halfCanvasHeight;
    }

    public get cbTransform(): ZoomTransform {
        return this._cbTransform;
    }

    public get nodes(): NodeType[] {
        return this.cbGraph.nodes;
    }

    public get links(): LinkType[] {
        return this.cbGraph.links;
    }

    public get linkNodes(): LinkNodeType[] {
        return this.cbGraph.linkNodes;
    }

    public get isDragging(): boolean {
        return this._isDragging;
    }

    public get highlightedNode(): NodeType | null {
        return this._highlightedNode;
    }

    public get specialHighlightedNode(): NodeType | null {
        return this._specialHighlightedNode;
    }

    public get isVisible(): boolean {
        return this._isVisible;
    }

    /******************************************************************************************************
     * Property getters for external access
     *****************************************************************************************************/

    constructor(elementId: string) {
        this.canvas = document.getElementById(elementId) as HTMLCanvasElement;
        this.renderer = new ConceptBrowserRenderer(this, this.canvas, this.config, this.mapWidth, this.mapHeight);
        this.cbCanvas = d3.select(this.canvas);
        this.resizeCanvas();
    }

    /******************************************************************************************************
     * Exposed functionality
     *****************************************************************************************************/

    /**
     * Search and focus on a node based on a concept id
     * @param id
     * @param nodeOnly Only set the actual node as highlighted
     */
    public moveToConceptById(id: number, nodeOnly?: boolean) {
        // Find the node by id
        const node = this.getNodeById(id);

        // If found, move to it
        if (node) {
            this.moveToNode(node, nodeOnly);
        }
    }

    /**
     * Recenter the viewport
     */
    public centerView(duration?: number) {
        // Find current locations of all nodes, and select max
        let minX = this.mapWidth;
        let maxX = 0;
        let minY = this.mapHeight;
        let maxY = 0;
        this.cbGraph.nodes.forEach((node) => {
            minX = Math.min(minX, node.x - node.radius);
            maxX = Math.max(maxX, node.x + node.radius);
            minY = Math.min(minY, node.y - node.radius);
            maxY = Math.max(maxY, node.y + node.radius);
        });

        this.moveToPosition(minX, maxX, minY, maxY, duration);
    }

    /**
     * Create an event to resize the canvas
     */
    public requestResizeCanvas() {
        d3.select(window).dispatch('custom_resize');
    }

    /**
     * Create an event to resize the canvas to a specific size
     */
    public requestResizeCanvasWithSizes(width?: number, height?: number) {
        d3.select(window).dispatch('custom_resize', {
            bubbles: true,
            cancelable: true,
            detail: {width, height},
        });
    }

    /**
     * Set the opened state
     */
    public setOpenedState(value: boolean) {
        this._isVisible = value;

        if (!this.isVisible) {
            this.closeFilters();
        } else {
            this.renderer.requestFrame();
        }
    }

    /******************************************************************************************************
     * Utility functions
     *****************************************************************************************************/

    /**
     * Get a node by id
     */
    private getNodeById(id: number): NodeType | null {
        // Find the node by id
        const result = this.cbGraph.nodes.filter((node) => {
            return node.id === id;
        });

        // Return result
        return result ? result[0] : null;
    }

    /**
     * Get a link by id
     */
    private getLinkById(id: number): LinkType | null {
        // Find the link by id
        const result = this.cbGraph.links.filter((link) => {
            return link.id === id;
        });

        // Return result
        return result ? result[0] : null;
    }

    /**
     * Resize the canvas (draw area)
     * This should be done on draggable window size changes, or browser window changes
     */
    public resizeCanvas() {
        // Get container size, and set sizes and zoom extent
        const $container = $('#graph_container_div');
        this.canvas.width = this._canvasWidth
            = ((d3.event && d3.event.detail && d3.event.detail.width) ? d3.event.detail.width : $container.innerWidth());
        this.canvas.height = this._canvasHeight
            = ((d3.event && d3.event.detail && d3.event.detail.height) ? d3.event.detail.height : $container.innerHeight());
        this._halfCanvasWidth = this.canvasWidth / 2;
        this._halfCanvasHeight = this.canvasHeight / 2;
        this.zoomExtent[0] = Math.max(this.canvasWidth / this.mapWidth, this.canvasHeight / this.mapHeight, 0.1);

        // Check if the event loop is running, if not, restart
        if (d3.event && !d3.event.active) {
            this.cbSimulation.restart();
        }
    }

    /**
     * Retrieve the node radius
     */
    public getNodeRadius(node: NodeType): number {
        // Check whether link node
        if (node.linkNode === true) {
            node.radius = 1;
            return this.linkNodeRadius;
        }

        // Update radius
        const linkCount = node.numberOfLinks ? node.numberOfLinks : 1;
        node.radius = this.config.baseNodeRadius + this.config.extendNodeRatio * linkCount;

        return node.radius + this.nodeRadiusMargin;
    }

    /**
     * Updated the node font scale
     */
    private updateNodeFontScale(node: NodeType) {
        // Check whether link node
        if (node.linkNode === true) {
            node.fontScale = 1;
            return;
        }

        // Calculate the new font scale
        const linkCount = node.numberOfLinks ? node.numberOfLinks : 1;
        const scaleFactor = Math.max(0, Math.min(Math.floor(linkCount / this.config.nodeLabelFontScaleStep), 3)) - 3;
        node.fontScale = (this.config.defaultNodeLabelFontSize + (scaleFactor * this.config.nodeLabelFontScaleStepSize))
            / this.config.defaultNodeLabelFontSize;
    }

    /**
     * Generate the link nodes to avoid overlap (http://bl.ocks.org/couchand/7190660)
     */
    private generateLinkNodes() {
        this.cbGraph.linkNodes = [];
        this.cbGraph.links.forEach((link) => {
            this.cbGraph.linkNodes.push({
                // We need to have the object, so select it if available
                source: link.source.hasOwnProperty('id')
                    ? link.source
                    : this.cbGraph.nodes.filter((node) => {
                        // @ts-ignore
                        return node.id === link.source;
                    })[0],
                target: link.target.hasOwnProperty('id')
                    ? link.target
                    : this.cbGraph.nodes.filter((node) => {
                        // @ts-ignore
                        return node.id === link.target;
                    })[0],
                x: 0,
                y: 0,
                vx: 0,
                vy: 0,
                linkNode: true,
            } as LinkNodeType);
        });
    }

    /**
     * Calculate the link node locations
     */
    private updateLinkNodeLocations() {
        this.cbGraph.linkNodes.forEach((linkNode) => {
            linkNode.x = (linkNode.source.x + linkNode.target.x) * 0.5;
            linkNode.y = (linkNode.source.y + linkNode.target.y) * 0.5;
        });
    }

    /**
     * Limit the node position based on the map dimensions
     */
    private limitNodes() {
        this.cbGraph.nodes.forEach((node) => {
            node.x = Math.max(node.radius, Math.min(this.mapWidth - node.radius, node.x));
            node.y = Math.max(node.radius, Math.min(this.mapHeight - node.radius, node.y));
        });
    }

    /**
     * Reload the simulation nodes/links
     */
    private reloadSimulation() {
        this.cbSimulation.nodes(this.cbGraph.nodes.concat(this.cbGraph.linkNodes));
        this.cbSimulation.force('link').links(this.cbGraph.links);
    }

    /**
     * Limits the transformation struct, by the map size with a small white margin
     */
    private limitTransform(transform: any): ZoomTransform {
        transform.x =
            Math.max(-(((this.mapWidth + this.mapWidthDragMargin) * transform.k) - this.canvasWidth),
                Math.min(this.mapWidthDragMargin * transform.k, transform.x));
        transform.y =
            Math.max(-(((this.mapHeight + this.mapHeightDragMargin) * transform.k) - this.canvasHeight),
                Math.min(this.mapHeightDragMargin * transform.k, transform.y));

        return transform;
    }

    /**
     * Mark the given node as being dragged
     */
    private setNodeAsDragged(node: NodeType) {
        this._isDragging = true;
        node.dragged = true;
        this.clearNodeHighlight();
        this.setHighlightsByNode(node);
    }

    /**
     * Unmark the given node as being dragged
     * @param node
     */
    private clearNodeAsDragged(node: NodeType) {
        this._isDragging = false;
        node.dragged = false;
        this.clearHighlightsByNode(node);
    }

    /**
     * Mark the given node as being highlighted
     * @param node
     * @param nodeOnly If true, only the supplied node is marked as highlighted
     */
    private setNodeAsHighlight(node: NodeType, nodeOnly?: boolean) {
        // Check if node the same
        if (this.highlightedNode && this.highlightedNode.index === node.index) {
            return;
        }
        if (nodeOnly && this.specialHighlightedNode && this.specialHighlightedNode.index === node.index) {
            return;
        }

        // Check for previous highlight
        this.clearNodeHighlight();

        // Check whether the given node exists
        if (node === undefined) {
            return;
        }

        // Only set other highlight when nodeOnly is not set
        if (!nodeOnly) {
            // Set as highlighted
            this._highlightedNode = node;
            node.highlighted = true;
            this.setHighlightsByNode(node);
        } else {
            this._specialHighlightedNode = node;
            node.specialHilight = true;
        }

        this.renderer.requestStateRefresh();
    }

    /**
     * Unmark the given node as being highlighted
     */
    private clearNodeHighlight() {
        if (this.highlightedNode !== null) {
            this.highlightedNode.highlighted = false;
            this.clearHighlightsByNode(this.highlightedNode);
            this._highlightedNode = null;

            this.renderer.requestStateRefresh();
        }
        if (this.specialHighlightedNode !== null) {
            this.specialHighlightedNode.specialHilight = false;
            this._specialHighlightedNode = null;

            this.renderer.requestStateRefresh();
        }
    }

    /**
     * Mark relations of the given node as highlighted
     * @param node
     */
    private setHighlightsByNode(node: NodeType) {
        this.cbGraph.links.forEach((link) => {
            if (link.target.index === node.index) {
                link.source.highlighted = true;
            }
            if (link.source.index === node.index) {
                link.target.highlighted = true;
            }
        });
    }

    /**
     * Unmark relations of the given node as highlighted
     * @param node
     */
    private clearHighlightsByNode(node: NodeType) {
        this.cbGraph.links.forEach((link) => {
            if (link.target.index === node.index) {
                link.source.highlighted = false;
            }
            if (link.source.index === node.index) {
                link.target.highlighted = false;
            }
        });
    }

    /**
     * Retrieve the link minimum length
     */
    private getLinkDistance(link: LinkType): number {
        return this.getNodeRadius(link.source) +
            this.getNodeRadius(link.target) +
            this.getLinkLabelLength(link);
    }

    /**
     * Estimate the length of the link label
     */
    public getLinkLabelLength(link: LinkType): number {
        return link.relationName.length * 5 + 10;
    }

    /**
     * Transform a location with the current transformation
     * @param loc
     */
    private transformLocation(loc: any) {
        return {
            x: (loc.clientX - (this.canvas.getBoundingClientRect() as DOMRect).x - this.cbTransform.x) / this.cbTransform.k,
            y: (loc.clientY - this.cbTransform.y) / this.cbTransform.k,
        };
    }

    /******************************************************************************************************
     * Event handlers
     *****************************************************************************************************/

    /**
     * Find a node based on the event location
     * @returns {undefined}
     */
    private findNode() {
        if (!this.isLoaded) {
            return;
        }

        let transformed;
        if (typeof d3.event.clientX === 'undefined' || typeof d3.event.clientY === 'undefined') {
            if (!this.lastTransformed) {
                return;
            }
            transformed = this.lastTransformed;
        } else {
            transformed = this.lastTransformed = this.transformLocation(d3.event);
        }

        const node = this.cbSimulation.find(transformed.x, transformed.y, 20);

        if (!node) {
            return undefined;
        }

        if (node.linkNode || this.renderer.isFiltered(node)) {
            return undefined;
        }

        return node;
    }

    /**
     * Event fired when the drag action starts
     */
    private onDragStarted() {
        if (!d3.event.active) {
            this.cbSimulation.alphaTarget(0.3).restart();
        }
        d3.event.subject.fx = this.dragPosX = d3.event.subject.x;
        d3.event.subject.fy = this.dragPosY = d3.event.subject.y;

        this.mouseMoveDisabled = false;
        this.setNodeAsDragged(d3.event.subject);
    }

    /**
     * Event fired during dragging progress
     */
    private onDragged() {
        this.dragPosX += d3.event.dx / this.cbTransform.k;
        this.dragPosY += d3.event.dy / this.cbTransform.k;
        d3.event.subject.fx = Math.max(0, Math.min(this.mapWidth, this.dragPosX));
        d3.event.subject.fy = Math.max(0, Math.min(this.mapHeight, this.dragPosY));
    }

    /**
     * Event fired when the drag action stops
     */
    private onDragEnded() {
        if (!d3.event.active) {
            this.cbSimulation.alphaTarget(0);
        }
        if (!this.isPaused) {
            d3.event.subject.fx = null;
            d3.event.subject.fy = null;
        }

        this.clearNodeAsDragged(d3.event.subject);
    }

    /**
     * Event for mouse move, to select a node to highlight
     */
    private onMouseMove() {
        if (this.mouseMoveDisabled) {
            return;
        }
        this.highlightNode(this.findNode());
    }

    /**
     * Left mouse button click, in order to fix node highlight
     * Communicates with the content in order to open the correct page
     */
    private onClick() {
        this.closeFilters();

        const node = this.findNode();
        if (node && !this.mouseMoveDisabled) {
            this.setNodeAsHighlight(node);
        }
        this.mouseMoveDisabled = !!node;

        if (!this.clickSend && node !== undefined) {
            if (typeof node.id !== 'undefined' && node.id !== '') {
                this.clickSend = true;
                // @ts-ignore
                window.eDispatch.conceptSelected(node.id);
                setTimeout(() => {
                    this.clickSend = false;
                }, 250);
            }
        }
    }

    /**
     * Right click event.
     * On empty space, shows context menu for styling
     */
    private onRightClick() {
        d3.event.preventDefault();

        const node = this.findNode();
        this.contextMenuNode = typeof node !== 'undefined' ? node : null;
        $('#graph_container_div')
            // @ts-ignore
            .contextMenu({x: d3.event.clientX, y: d3.event.clientY});
    }

    /**
     * Double click event, move to the clicked node
     */
    private onDoubleClick() {
        this.moveToNode(this.findNode());
    }

    /**
     * Pause/play the animation
     */
    private pausePlayAnimation() {
        const $pauseButton = $('#pause-button');
        const $playButton = $('#play-button');

        if (this.isPaused) {
            // Reset the fixed position for each node
            this.cbGraph.nodes.forEach((node) => {
                node.fx = null;
                node.fy = null;
            });
            $playButton.hide();
            $pauseButton.show();

            this.cbSimulation.restart();
        } else {
            // Set the fixed position for each node, to ensure it keeps frozen
            this.cbSimulation.stop();
            this.cbGraph.nodes.forEach((node) => {
                node.fx = node.x;
                node.fy = node.y;
            });
            $pauseButton.hide();
            $playButton.show();

            this.cbSimulation.stop();
        }

        $pauseButton.blur();
        $playButton.blur();

        this.isPaused = !this.isPaused;
    }

    /**
     * Start concept creation from the selected concept
     * @param concept
     */
    private createInstance(concept: NodeType) {
        // @ts-ignore
        window.eDispatch.createInstance(concept.id);
    }

    /**
     * Keyboard event handler
     * space -> Stop simulation
     */
    private onKeyDown() {
        const node = this.findNode();

        switch (d3.event.keyCode) {
            case 32: // Space
                this.pausePlayAnimation();

                return;

            case 73: // I
            /* falls through */
            case 48: // 0
                if (node !== undefined) {
                    this.colorNode(node, 0);
                }
                break;

            case 82: // R
            /* falls through */
            case 49: // 1
                if (node !== undefined) {
                    this.colorNode(node, 1);
                }
                break;

            case 71: // G
            /* falls through */
            case 50: // 2
                if (node !== undefined) {
                    this.colorNode(node, 2);
                }
                break;

            case 66: // B
            /* falls through */
            case 51: // 3
                if (node !== undefined) {
                    this.colorNode(node, 3);
                }
                break;

            case 79: // O
            /* falls through */
            case 52: // 4
                if (node !== undefined) {
                    this.colorNode(node, 4);
                }
                break;
        }

        // Check if the event loop is running, if not, restart
        if (!d3.event.active) {
            this.cbSimulation.restart();
        }
    }

    /**
     * Highlight the current event location node
     * @param node
     */
    private highlightNode(node: NodeType) {
        if (node) {
            this.setNodeAsHighlight(node);
        } else {
            this.clearNodeHighlight();
        }

        // Check if the event loop is running, if not, restart
        if (!d3.event.active) {
            this.cbSimulation.restart();
        }
    }

    /**
     * Move the view to the given node
     * It keeps the relations inside the view
     * @param node
     * @param nodeOnly
     */
    private moveToNode(node: NodeType, nodeOnly?: boolean) {
        // Check for node existence
        if (node === undefined) {
            return;
        }

        // Stop simulation for now to prevent node walking
        this.mouseMoveDisabled = true;
        this.cbSimulation.stop();

        // Set clicked node as highlighted
        this.setNodeAsHighlight(node, nodeOnly);

        let minX = this.mapWidth;
        let maxX = 0;
        let minY = this.mapHeight;
        let maxY = 0;
        if (!nodeOnly) {
            const zoomMargin = this.zoomMargin;
            // Find current locations of highlighted nodes
            this.cbGraph.nodes.forEach((n) => {
                if (!n.highlighted) {
                    return;
                }
                minX = Math.min(minX, n.x - n.radius - zoomMargin);
                maxX = Math.max(maxX, n.x + n.radius + zoomMargin);
                minY = Math.min(minY, n.y - n.radius - zoomMargin);
                maxY = Math.max(maxY, n.y + n.radius + zoomMargin);
            });

            // Do the actual move
            this.moveToPosition(minX, maxX, minY, maxY);
        } else {
            // Calculate transform for move without zoom change
            const transform = d3.zoomIdentity
                .translate(this.halfCanvasWidth, this.halfCanvasHeight)
                .scale(this.cbTransform.k)
                .translate(-node.x, -node.y);
            this.moveToTransform(transform);
        }
    }

    /**
     * Move the view to the given view bounds
     * @param minX
     * @param maxX
     * @param minY
     * @param maxY
     * @param duration
     */
    private moveToPosition(minX: number, maxX: number, minY: number, maxY: number, duration?: number) {
        if (!this.isLoaded) {
            return;
        }

        // Calculate scale
        let scale = 0.9 / Math.max((maxX - minX) / this.canvasWidth, (maxY - minY) / this.canvasHeight);
        scale = Math.min(this.zoomExtent[1], Math.max(1, scale));

        // Calculate zoom identity
        const transform = d3.zoomIdentity
            .translate(this.halfCanvasWidth, this.halfCanvasHeight)
            .scale(scale)
            .translate(-(minX + maxX) / 2, -(minY + maxY) / 2);

        // Move to it
        this.moveToTransform(transform, duration);
    }

    /**
     * Move the view to the given transform in the given duration
     * @param transform
     * @param duration
     */
    private moveToTransform(transform: ZoomTransform, duration?: number) {
        // Check duration
        duration = typeof duration !== 'undefined' ? duration : 3000;

        this.cbCanvas
            .transition()
            .duration(duration)
            .call(this.cbZoom.transform, transform);
    }

    /**
     * Zoom event handler
     * Limits the transformation and calls the draw function
     */
    private zoomGraph() {
        this._cbTransform = this.limitTransform(d3.event.transform);
        this.renderer.requestFrame();
    }

    /**
     * Zoom to the new scale, which is limited
     */
    private zoomFromButton(newScale: number) {
        const transform = d3.zoomIdentity
            .translate(this.halfCanvasWidth, this.halfCanvasHeight)
            .scale(Math.max(this.zoomExtent[0], Math.min(this.zoomExtent[1], newScale)))
            .translate(
                (this.cbTransform.x - this.halfCanvasWidth) / this.cbTransform.k,
                (this.cbTransform.y - this.halfCanvasHeight) / this.cbTransform.k,
            );

        this.cbCanvas
            .call(this.cbZoom.transform, transform);
    }

    /******************************************************************************************************
     * Color functions
     *****************************************************************************************************/

    /**
     * Color the given node and save in the local storage
     */
    private colorNode(node: NodeType, color: number) {
        node.color = color === 0 && node.instance ? this.config.instanceDefaultColor : color;

        if (typeof (Storage) !== 'undefined') {
            if (color === 0) {
                localStorage.removeItem('nodeColor.' + node.id);
            } else {
                localStorage.setItem('nodeColor.' + node.id, String(color));
            }
        }

        this.renderer.requestStateRefresh();
    }

    /**
     * Resets all node colors and clears the local storage
     */
    private resetNodeColors() {
        this.cbGraph.nodes.forEach((node) => {
            node.color = node.instance ? this.config.instanceDefaultColor : 0;
        });

        // Clear local storage for loaded nodes only
        if (typeof (Storage) !== 'undefined') {
            this.cbGraph.nodes.forEach((node) => {
                localStorage.removeItem('nodeColor.' + node.id);
            });
        }

        this.renderer.requestStateRefresh();
    }

    /**
     * Load node colors from the local storage
     */
    private loadNodeColor(node: NodeType) {
        node.color = 0;
        if (typeof (Storage) !== 'undefined') {
            const color = localStorage.getItem('nodeColor.' + node.id);
            if (color !== null) {
                node.color = parseInt(color, 10);
            } else if (node.instance) {
                node.color = this.config.instanceDefaultColor;
            }
        }
    }

    /******************************************************************************************************
     * Force functions
     *****************************************************************************************************/

    /**
     * Force to keep the nodes inside the map box
     * @param alpha
     */
    private keepInBoxForce(alpha: number) {
        for (let i = 0, n = this.cbGraph.nodes.length,
                 node, kx = (alpha * this.boundForceStrength) / this.mapWidth,
                 ky = (alpha * this.boundForceStrength) / this.mapHeight; i < n; ++i) {
            // Set variables
            node = this.cbGraph.nodes[i];

            // Calculate forces
            node.vx -= (node.x - this.halfMapWidth) * kx;
            node.vy -= (node.y - this.halfMapHeight) * ky;
        }
    }

    /******************************************************************************************************
     * Register update functions
     *****************************************************************************************************/

    public updateNodeName(id: number, name: string) {
        const node = this.getNodeById(id);

        if (node) {
            node.label = name;
            this.updateNodeFontScale(node);
            this.config.updateLabel(node, node.fontScale);
            this.cbSimulation.restart();
        }
    }

    public update(data: JsonType[]) {
        // First, stop de simulation
        this.cbSimulation.stop();

        // Reset the link nodes
        this.cbGraph.linkNodes = [];

        // Reset the tags
        this.tags = {};

        // Map the new data
        const availConcepts: any[] = [];
        const availLinks: any[] = [];
        data.forEach((concept) => {
            availConcepts.push(concept.id);
            let node = this.getNodeById(concept.id);
            if (!node) {
                // Create
                node = {
                    id: concept.id,
                    link: '',
                    x: this.halfMapWidth,
                    y: this.halfMapHeight,
                    vx: 0,
                    vy: 0,
                } as NodeType;
                this.cbGraph.nodes.push(node);
            }

            // Update properties
            node.label = concept.name;
            node.instance = concept.instance;
            node.numberOfLinks = concept.numberOfLinks;
            node.empty = concept.isEmpty;
            node.tags = concept.tags.map((t) => t.id);
            this.getNodeRadius(node);
            this.loadNodeColor(node);
            this.updateNodeFontScale(node);
            this.config.updateLabel(node, node.fontScale);

            // Rebuild the tag data
            concept.tags.forEach((tag) => {
                if (tag.id in this.tags) {
                    return;
                }
                this.tags[tag.id] = tag;
            });
        });

        // Loop data again, as now we have all nodes available to create the links
        data.forEach((concept) => {
            // Update relations
            concept.relations.forEach((relation: any) => {
                availLinks.push(relation.id);
                let link = this.getLinkById(relation.id);
                if (!link) {
                    // Create
                    link = {
                        id: relation.id,
                        source: this.getNodeById(concept.id),
                        target: this.getNodeById(relation.target),
                    } as LinkType;
                    this.cbGraph.links.push(link);
                }

                // Update properties
                link.relationName = relation.relationName;
            });
        });

        // Remove missing nodes
        this.cbGraph.nodes = this.cbGraph.nodes.filter((node) => {
            return availConcepts.indexOf(node.id) !== -1;
        });
        // Remove missing links
        this.cbGraph.links = this.cbGraph.links.filter((link) => {
            return availLinks.indexOf(link.id) !== -1;
        });

        // Recreate the link nodes
        this.generateLinkNodes();
        this.updateLinkNodeLocations();

        // Reload the nodes and links
        this.reloadSimulation();

        // Reload the filters
        this.initFilters();

        // Reload the renderer state
        this.renderer.requestStateRefresh();

        // Restart simulation
        this.cbSimulation.alpha(0.01);
        this.cbSimulation.restart();
    }

    /******************************************************************************************************
     * Register init function
     *****************************************************************************************************/

    public init(data: JsonType[]) {

        /******************************************************************************************************
         * Convert the data to be suitable for the concept browser
         *****************************************************************************************************/
        this.cbGraph = {
            nodes: [],
            links: [],
            linkNodes: [],
        };
        this.tags = {};

        // Map the nodes and relations to their js equivalent
        data.forEach((concept) => {
            // Node mapping
            this.cbGraph.nodes.push({
                id: concept.id,
                label: concept.name,
                instance: concept.instance,
                empty: concept.isEmpty,
                tags: concept.tags.map((t) => t.id),
                link: '',
                numberOfLinks: concept.numberOfLinks,
            } as NodeType);

            // Relation mapping
            concept.relations.forEach((relation) => {
                this.cbGraph.links.push({
                    id: relation.id,
                    // @ts-ignore
                    source: concept.id,
                    // @ts-ignore
                    target: relation.target,
                    relationName: relation.relationName,
                });
            });

            // Tag mappings
            concept.tags.forEach((tag) => {
                if (tag.id in this.tags) {
                    return;
                }
                this.tags[tag.id] = tag;
            });
        });

        /******************************************************************************************************
         * Start simulation
         *****************************************************************************************************/

        this.cbSimulation = d3.forceSimulation()
            .force('sidedetection', (a) => this.keepInBoxForce(a))     // To keep nodes in the map view
            .force('charge', d3.forceManyBody()         // To keep nodes grouped
                .distanceMin(this.manyBodyDistanceMin)
                .distanceMax(this.manyBodyDistanceMax)
                .strength(this.manyBodyStrength))
            .force('collide', d3.forceCollide()         // To prevent nodes from overlapping
                // @ts-ignore
                .radius((n) => this.getNodeRadius(n))
                .strength(this.collideStrength)
                .iterations(this.collideIterations))
            .force('link', d3.forceLink()               // To force a certain link distance
                // @ts-ignore
                .distance((l) => this.getLinkDistance(l))
                .strength(this.linkStrength)
                .id((d: any) => {
                    return d.id;
                }))
            .force('center',                            // To force the node to move around the map center
                d3.forceCenter(this.mapWidth / 2, this.mapHeight / 2));

        // Calculate some one-time values before rendering starts
        this.cbGraph.nodes.forEach((node) => {
            this.getNodeRadius(node);
            this.loadNodeColor(node);
            this.updateNodeFontScale(node);
            this.config.updateLabel(node, node.fontScale);
        });

        this.generateLinkNodes();

        // Load data (nodes and links)
        this.reloadSimulation();

        // Load handlers for the tick event
        this.cbSimulation.on('tick', () => {
            // Limit nodes
            this.limitNodes();

            // Update link node positions
            this.updateLinkNodeLocations();

            // Draw the actual graph
            this.renderer.requestFrame();
        });

        // Create zoom handler
        this.cbZoom = d3.zoom()
            .scaleExtent(this.zoomExtent)
            .on('zoom', () => this.zoomGraph());

        // Create pause/play handler for button
        $('#pause-button, #play-button').on('click', () => {
            this.pausePlayAnimation();
        });

        // Create zoom handlers for buttons
        $('#zoom-in-button').on('click', () => {
            this.zoomFromButton(this.cbTransform.k * this.zoomButtonFactor);
        });
        $('#zoom-out-button').on('click', () => {
            this.zoomFromButton(this.cbTransform.k / this.zoomButtonFactor);
        });

        // Create filter button handler
        this.$filterBtn = $('#filter-button');
        this.$filterBtn
            .popover({
                html: true,
                trigger: 'manual',
                placement: 'bottom',
                // tslint:disable-next-line:max-line-length
                template: '<div class="popover filter-popover" role="tooltip"><div class="arrow"></div><h3 class="popover-header"></h3><div class="popover-body"></div></div>',
                content: document.getElementById('filter-content')!,
            })
            .on('click', () => {
                this.$filterBtn.popover('toggle');
            })
            .on('shown.bs.popover', () => {
                this.initFilters();
            })
            .on('show.bs.popover', () => {
                this.$filterBtn.tooltip('hide');
                this.$filterBtn.tooltip('disable');
            })
            .on('hidden.bs.popover', () => {
                this.$filterBtn.tooltip('enable');
            });

        // Initialize the filters
        this.initFilters();

        // Create drag handlers
        this.cbDrag = d3.drag()
            .container(this.canvas)
            .subject(() => this.findNode())
            .on('start', () => this.onDragStarted())
            .on('drag', () => this.onDragged())
            .on('end', () => this.onDragEnded());

        // Add handlers to canvas
        this.cbCanvas
            .call(this.cbDrag)
            .call(this.cbZoom)
            .on('mousemove', () => this.onMouseMove())
            .on('click', () => this.onClick())
            .on('dblclick.zoom', () => this.onDoubleClick())
            .on('contextmenu', () => this.onRightClick());

        // Add window handlers
        d3.select(window)
            .on('resize', () => this.resizeCanvas())
            .on('custom_resize', () => this.resizeCanvas())
            .on('keydown', () => this.onKeyDown());

        // Define context menu
        // @ts-ignore
        $.contextMenu({
            selector: '#graph_container_div',
            trigger: 'none',
            build: () => {
                //noinspection JSUnusedGlobalSymbols
                return {
                    callback: (key: string) => {
                        if (key === 'quit') {
                            return;
                        }
                        if (key.startsWith('style')) {
                            this.colorNode(this.contextMenuNode!, parseInt(key.substr(6), 10));
                        }
                        if (key === 'reset') {
                            this.resetNodeColors();
                        }
                        if (key === 'create-instance') {
                            this.createInstance(this.contextMenuNode!);
                        }
                        if (key === 'center') {
                            this.centerView();
                        }
                        this.cbSimulation.restart();
                    },
                    items: this.getContextMenuItems(),
                };
            },
        });

        // Center view and center image
        this.isLoaded = true;
        this.resizeCanvas();
        setTimeout(() => {
            this.centerView(500);
        }, 500);
    }

    /**
     * Context menu builder
     * @returns {*}
     */
    private getContextMenuItems() {
        if (this.contextMenuNode === null) {
            // Global
            return {
                reset: {name: 'Reset node colors', icon: 'fa-undo'},
                sep1: '---------',
                center: {name: 'Back to center', icon: 'fa-sign-in'},
                sep2: '---------',
                quit: {name: 'Close', icon: 'fa-times'},
            };
        } else {
            // Node
            let nodeItems = {};

            // Generate individuals, if any
            if (this.contextMenuNode.individuals !== undefined) {
                const individualsItems: { [key: string]: any } = {};
                const individualsTextItems = this.contextMenuNode.individuals.split(';');
                individualsTextItems.forEach((item) => {
                    item = item.trim();
                    individualsItems['individual-' + item] = {
                        name: item,
                    };
                });

                nodeItems = {
                    individuals: {
                        name: 'Examples',
                        items: individualsItems,
                    },
                    sep1: '---------',
                };
            }

            // Color data
            if (!this.contextMenuNode!.empty) {
                const colorData = {
                    styles: {
                        name: 'Change color',
                        icon: 'fa-paint-brush',
                        items: {
                            'style-1': {
                                name: 'Red',
                                icon: this.contextMenuNode.color === 1 ? 'fa-check' : '',
                                visible: !this.contextMenuNode.instance || this.config.instanceDefaultColor !== 1,
                            },
                            'style-2': {
                                name: 'Green',
                                icon: this.contextMenuNode.color === 2 ? 'fa-check' : '',
                                visible: !this.contextMenuNode.instance || this.config.instanceDefaultColor !== 2,
                            },
                            'style-3': {
                                name: 'Blue',
                                icon: this.contextMenuNode.color === 3 ? 'fa-check' : '',
                                visible: !this.contextMenuNode.instance || this.config.instanceDefaultColor !== 3,
                            },
                            'style-4': {
                                name: 'Orange',
                                icon: this.contextMenuNode.color === 4 ? 'fa-check' : '',
                                visible: !this.contextMenuNode.instance || this.config.instanceDefaultColor !== 4,
                            },
                            'sep1': '---------',
                            'style-0': {
                                name: 'Default',
                                icon:
                                    this.contextMenuNode.color === 0
                                    || (this.contextMenuNode.color === this.config.instanceDefaultColor && this.contextMenuNode.instance)
                                        ? 'fa-check' : 'fa-undo',
                            },
                        },
                    },
                    sep2: '---------',
                };

                nodeItems = $.extend(nodeItems, colorData);
            }

            // Merge with default data
            const defaultData = {
                'create-instance': {name: 'Instantiate', icon: 'fa-code-fork'},
                'sep3': '---------',
                'quit': {name: 'Close', icon: 'fa-times'},
            };

            return $.extend(nodeItems, defaultData);
        }
    }

    /**
     * Initialize the filters
     */
    private initFilters() {
        // Clear state
        this.$select2Elements = [];
        const tagValues = Object.values(this.tags);

        // Create instance filter
        const $filterInstances = $('#filter-show-instances');
        $filterInstances
            .off('change')
            .on('change', () => {
                this.renderer.setShowInstances($filterInstances.is(':checked'));
            });

        // Create tag filter
        const $filterTagsEnabled = $('#filter-tags-enabled');
        $filterTagsEnabled
            .off('change')
            .on('change', () => {
                this.renderer.setFilterTagsEnabled($filterTagsEnabled.is(':checked'));
            });

        const $filterTags = $('#filter-tags');
        const currentValue = $filterTags.val() as string[] || [];
        if ($filterTags.data('select2') !== undefined) {
            // @ts-ignore
            $filterTags.select2('destroy');
        }
        $filterTags.off('change');
        $filterTags.val([]);

        // Rebuild options
        $filterTags.find('option').remove();
        tagValues.sort((a, b) => a.name.localeCompare(b.name)).forEach((tag) => {
            $filterTags.append(new Option(tag.name, String(tag.id), false, currentValue.includes(String(tag.id))));
        });

        // Create select2 for tag filter
        $filterTags
            // @ts-ignore
            .select2({
                width: '100%',
                theme: 'bootstrap',
                allowClear: true,
                placeholder: '',
            })
            .on('change', () => {
                this.renderer.setFilterTags(($filterTags.val() as string[]).map((t) => Number(t)));
            });
        this.$select2Elements.push($filterTags);

        // Create tag filter and/or handler
        const $filterTagsOr = $('#filter-tags-or');
        $filterTagsOr.off('change');
        $filterTagsOr.on('change', () => {
            this.renderer.setFilterTagsOr($filterTagsOr.is(':checked'));
        });

        const $filterTagColorEnabled = $('#filter-tag-colors-enabled');
        $filterTagColorEnabled
            .off('change')
            .on('change', () => {
                this.renderer.setFilterTagColorsEnabled($filterTagColorEnabled.is(':checked'));
            });

        // Create tag color selectors
        this.buildTagColorSelectors();

        if (tagValues.length === 0) {
            $('#filter-content-tags').hide();
        } else {
            $('#filter-content-tags').show();
        }
    }

    /**
     * Close the filter popover
     */
    public closeFilters() {
        if (this.$filterBtn) {
            this.$filterBtn.popover('hide');
        }

        this.$select2Elements.forEach(($element) => {
            if ($element.data('select2') !== undefined) {
                // @ts-ignore
                $element.select2('close');
            }
        });
    }

    /**
     * Build/update the tag color selectors
     */
    private buildTagColorSelectors() {
        let sortedTags = Object.values(this.tags).sort((a, b) => a.name.localeCompare(b.name));

        const $prototype = $('#filter-tag-color-prototype > div');
        const $container = $('#filter-tag-color-container');
        const currentValues: Array<{ tag: any, color: any }> = [];
        $container.children('div')
            .each((index, element) => {
                const $row = $(element);
                const $select = $row.find('select');
                const $color = $row.find('input[type="color"]');
                currentValues.push({
                    tag: $select.val(),
                    color: $color.length === 1 ? $color.val() : null,
                });

                if ($select.data('select2') !== undefined) {
                    // @ts-ignore
                    $select.select2('destroy');
                }
            })
            .remove();

        const rows: Array<{ $elem: JQuery<HTMLElement>; tag: string | null; color: string | null }> = [];
        currentValues.forEach((item) => {
            if (!item.tag) {
                return;
            }

            rows.push({
                $elem: $prototype.clone(),
                tag: String(item.tag),
                color: item.color ? String(item.color) : null,
            });
        });

        rows.push({
            $elem: $prototype.clone(),
            tag: null,
            color: null,
        });

        rows.forEach((row) => {
            if (sortedTags.length === 0) {
                return;
            }

            const selectedTag = sortedTags.find((t) => String(t.id) === row.tag);

            if (row.tag && !selectedTag) {
                return;
            }

            $container.append(row.$elem);

            const $select = row.$elem.find('select');
            $select.append(new Option());
            sortedTags.forEach((tag) => {
                $select.append(new Option(tag.name, String(tag.id), false, false));
            });

            if (selectedTag) {
                row.color = row.color || selectedTag.color;

                $select.val(String(selectedTag.id));
                $select.data('default-color', selectedTag.color);
                row.$elem.find('input[type="color"]')
                    .val(row.color)
                    .on('change', () => this.buildTagColorSelectors());

                sortedTags = sortedTags.filter((t) => t.id !== selectedTag.id);
            } else {
                row.$elem.find('.color-picker').remove();
            }

            $select
                // @ts-ignore
                .select2({
                    width: '100%',
                    theme: 'bootstrap',
                    placeholder: 'Select a tag',
                    allowClear: true,
                })
                .on('change', (event: Event) => {
                    // Test whether the default color is still selected
                    const $selectTarget = $(event.target!);
                    const $row = $selectTarget.closest('.filter-tag-color');
                    const $colorPicker = $row.find('input[type="color"]');
                    if ($colorPicker.length === 1) {
                        const defaultColor = $selectTarget.data('default-color');
                        if (defaultColor && defaultColor === $colorPicker.val()) {
                            $colorPicker.remove();
                        }
                    }

                    this.buildTagColorSelectors();
                });
            this.$select2Elements.push($select);
        });

        this.renderer.setFilterTagColors(rows
            .filter((row) => row.tag !== null)
            .map((row) => {
                return {
                    tag: Number(row.tag),
                    color: row.color!,
                };
            }));
    }
}
