import * as d3 from 'd3';
import {ZoomTransform} from 'd3-zoom';
import BrowserConfigurationInstance, {BrowserConfiguration} from './BrowserConfiguration';

require('../../css/conceptBrowser/conceptBrowser.scss');

export interface JsonType {
    numberOfLinks: number,
    id: number,
    name: string,
    instance: boolean;
    isEmpty: boolean,
    relations: Array<{
        id: number,
        target: number,
        relationName: number
    }>,
}

export interface NodeType {
    id: number;
    index: number,
    x: number,
    y: number,
    vx: number,
    vy: number,
    fx: number | null,
    fy: number | null,
    color: number,
    radius: number,
    label: string,
    instance: boolean;
    expandedLabel: string[],
    expandedLabelStart: number,
    fontScale: number,
    link: string,
    numberOfLinks: number,
    individuals: string,
    dragged: boolean,
    highlighted: boolean,
    specialHilight: boolean;
    linkNode?: boolean,
    empty: boolean
}

export interface LinkType {
    id: number,
    source: NodeType,
    target: NodeType,
    relationName: string
}

export interface LinkNodeType extends NodeType, LinkType {
}

// noinspection JSMethodCanBeStatic
/**
 * The concept browser class
 */
export default class ConceptBrowser {
    private readonly config: BrowserConfiguration = BrowserConfigurationInstance;

    // Display settings
    private mapWidth: number = 3000;
    private mapWidthDragMargin: number = this.mapWidth / 30;
    private mapHeight: number = 2000;
    private mapHeightDragMargin: number = this.mapHeight / 20;
    private zoomMargin: number = 25;

    // Force settings
    private collideStrength: number = 0.6;
    private collideIterations: number = 1;
    private linkStrength: number = 0.9;
    private manyBodyStrength: number = -70;
    private manyBodyDistanceMin: number = 20;
    private manyBodyDistanceMax: number = 1500;
    private boundForceStrenght: number = 80;
    private linkNodeRadius: number = 20;
    private nodeRadiusMargin: number = 10;

    // General settings
    private drawGrid: boolean = false;
    private drawLinkNodes: boolean = false;
    private zoomExtent: [number, number] = [0.1, 8]; // [min,max] zoom, min is also limited by screen size
    private zoomButtonFactor: number = 1.5;

    /******************************************************************************************************
     * Style configuration variables
     *****************************************************************************************************/

    public applyStyle(style: number) {
        this.config.applyStyle(style);

        if (d3.event && !d3.event.active) this.cbSimulation.restart();
    };

    /******************************************************************************************************
     * Internal variables
     *****************************************************************************************************/

    private readonly canvas: HTMLCanvasElement;
    private context!: CanvasRenderingContext2D;
    private canvasWidth: any;
    private canvasHeight: any;
    private halfCanvasWidth: any;
    private halfCanvasHeight: any;
    private halfMapWidth: number = this.mapWidth / 2;
    private halfMapHeight: number = this.mapHeight / 2;
    private cbCanvas: any;
    private cbSimulation: any;
    private cbZoom: any;
    private cbTransform = d3.zoomIdentity;
    private cbDrag: any;
    private dragPosY: any;
    private dragPosX: any;
    private isDragging = false;
    private highlightedNode: NodeType | null = null;
    private specialHighlightedNode: NodeType | null = null;
    private mouseMoveDisabled = false;
    private clickSend = false;
    private contextMenuNode: NodeType | null = null;
    private lastTransformed: any;
    private isLoaded = false;
    private isPaused = false;

    private readonly filters = {
        showInstances: true,
    };

    // Initialize the graph object
    private cbGraph: { nodes: NodeType[], links: LinkType[], linkNodes: LinkNodeType[] } = {
        nodes: [],
        links: [],
        linkNodes: [],
    };

    constructor() {
        this.canvas = document.getElementById('graph_container_canvas') as HTMLCanvasElement;
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
    public moveToConceptById(id: number, nodeOnly: boolean) {
        // Find the node by id
        const node = this.getNodeById(id);

        // If found, move to it
        if (node) {
            this.moveToNode(node, nodeOnly);
        }
    };

    /**
     * Recenter the viewport
     */
    public centerView(duration?: number) {
        // Find current locations of all nodes, and select max
        let minX = this.mapWidth, maxX = 0, minY = this.mapHeight, maxY = 0;
        this.cbGraph.nodes.map((node) => {
            minX = Math.min(minX, node.x - node.radius);
            maxX = Math.max(maxX, node.x + node.radius);
            minY = Math.min(minY, node.y - node.radius);
            maxY = Math.max(maxY, node.y + node.radius);
        });

        this.moveToPosition(minX, maxX, minY, maxY, duration);
    };

    /**
     * Create an event to resize the canvas
     */
    public requestResizeCanvas() {
        d3.select(window).dispatch('custom_resize');
    };

    /**
     * Create an event to resize the canvas to a specific size
     */
    public requestResizeCanvasWithSizes(width?: number, height?: number) {
        d3.select(window).dispatch('custom_resize', {
            bubbles: true,
            cancelable: true,
            detail: {width: width, height: height},
        });
    };

    /******************************************************************************************************
     * Utility functions
     *****************************************************************************************************/

    /**
     * Get a node by id
     */
    private getNodeById(id: number): NodeType | null {
        // Find the node by id
        const result = this.cbGraph.nodes.filter(function (node) {
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
        const result = this.cbGraph.links.filter(function (link) {
            return link.id === id;
        });

        // Return result
        return result ? result[0] : null;
    }

    /**
     * Resize the canvas (draw area)
     * This should be done on draggable window size changes, or browser window changes
     */
    private resizeCanvas() {
        // Get container size, and set sizes and zoom extent
        const $container = $('#graph_container_div');
        this.canvas.width = this.canvasWidth = ((d3.event && d3.event.detail && d3.event.detail.width) ? d3.event.detail.width : $container.innerWidth());
        this.canvas.height = this.canvasHeight = ((d3.event && d3.event.detail && d3.event.detail.height) ? d3.event.detail.height : $container.innerHeight());
        this.halfCanvasWidth = this.canvasWidth / 2;
        this.halfCanvasHeight = this.canvasHeight / 2;
        this.zoomExtent[0] = Math.max(this.canvasWidth / this.mapWidth, this.canvasHeight / this.mapHeight, 0.1);

        // Get context if not available
        if (this.context === undefined) {
            this.context = this.canvas.getContext('2d')!;
        }

        // Check if the event loop is running, if not, restart
        if (d3.event && !d3.event.active) this.cbSimulation.restart();
    }

    /**
     * Retrieve the node radius
     */
    private getNodeRadius(node: NodeType): number {
        // Check whether link node
        if (node.linkNode === true) {
            node.radius = 1;
            return this.linkNodeRadius;
        }

        // Update radius
        let linkCount = node.numberOfLinks ? node.numberOfLinks : 1;
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
        let linkCount = node.numberOfLinks ? node.numberOfLinks : 1;
        let scaleFactor = Math.max(0, Math.min(Math.floor(linkCount / this.config.nodeLabelFontScaleStep), 3)) - 3;
        node.fontScale = (this.config.defaultNodeLabelFontSize + (scaleFactor * this.config.nodeLabelFontScaleStepSize)) / this.config.defaultNodeLabelFontSize;
    }

    /**
     * Generate the link nodes to avoid overlap (http://bl.ocks.org/couchand/7190660)
     */
    private generateLinkNodes() {
        this.cbGraph.linkNodes = [];
        this.cbGraph.links.map((link) => {
            this.cbGraph.linkNodes.push({
                // We need to have the object, so select it if available
                source: link.source.hasOwnProperty('id')
                    ? link.source
                    : this.cbGraph.nodes.filter(function (node) {
                        // @ts-ignore
                        return node.id === link.source;
                    })[0],
                target: link.target.hasOwnProperty('id')
                    ? link.target
                    : this.cbGraph.nodes.filter(function (node) {
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
        this.cbGraph.linkNodes.map((linkNode) => {
            linkNode.x = (linkNode.source.x + linkNode.target.x) * 0.5;
            linkNode.y = (linkNode.source.y + linkNode.target.y) * 0.5;
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
     * Limit the node position based on the map dimensions
     */
    private limitNode(node: NodeType) {
        node.x = Math.max(node.radius, Math.min(this.mapWidth - node.radius, node.x));
        node.y = Math.max(node.radius, Math.min(this.mapHeight - node.radius, node.y));
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
        this.isDragging = true;
        node.dragged = true;
        this.clearNodeHighlight();
        this.setHighlightsByNode(node);
    }

    /**
     * Unmark the given node as being dragged
     * @param node
     */
    private clearNodeAsDragged(node: NodeType) {
        this.isDragging = false;
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
        if (this.highlightedNode && this.highlightedNode.index === node.index) return;
        if (nodeOnly && this.specialHighlightedNode && this.specialHighlightedNode.index === node.index) return;

        // Check for previous highlight
        this.clearNodeHighlight();

        // Check whether the given node exists
        if (node === undefined) return;

        // Only set other highlight when nodeOnly is not set
        if (!nodeOnly) {
            // Set as highlighted
            this.highlightedNode = node;
            node.highlighted = true;
            this.setHighlightsByNode(node);
        } else {
            this.specialHighlightedNode = node;
            node.specialHilight = true;
        }
    }

    /**
     * Unmark the given node as being highlighted
     */
    private clearNodeHighlight() {
        if (this.highlightedNode !== null) {
            this.highlightedNode.highlighted = false;
            this.clearHighlightsByNode(this.highlightedNode);
            this.highlightedNode = null;
        }
        if (this.specialHighlightedNode !== null) {
            this.specialHighlightedNode.specialHilight = false;
            this.specialHighlightedNode = null;
        }
    }

    /**
     * Mark relations of the given node as highlighted
     * @param node
     */
    private setHighlightsByNode(node: NodeType) {
        this.cbGraph.links.map((link) => {
            if (link.target.index === node.index) link.source.highlighted = true;
            if (link.source.index === node.index) link.target.highlighted = true;
        });
    }

    /**
     * Unmark relations of the given node as highlighted
     * @param node
     */
    private clearHighlightsByNode(node: NodeType) {
        this.cbGraph.links.map((link) => {
            if (link.target.index === node.index) link.source.highlighted = false;
            if (link.source.index === node.index) link.target.highlighted = false;
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
    private getLinkLabelLength(link: LinkType): number {
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
        if (!this.isLoaded) return;

        let transformed;
        if (typeof d3.event.clientX === 'undefined' || typeof d3.event.clientY === 'undefined') {
            if (!this.lastTransformed) return;
            transformed = this.lastTransformed;
        } else {
            transformed = this.lastTransformed = this.transformLocation(d3.event);
        }

        const node = this.cbSimulation.find(transformed.x, transformed.y, 20);

        if (!node) {
            return undefined;
        }

        if (node.linkNode || (node.instance && !this.filters.showInstances)) {
            return undefined;
        }

        return node;
    }

    /**
     * Event fired when the drag action starts
     */
    private onDragStarted() {
        if (!d3.event.active) this.cbSimulation.alphaTarget(0.3).restart();
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
        if (!d3.event.active) this.cbSimulation.alphaTarget(0);
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
        if (this.mouseMoveDisabled) return;
        this.highlightNode(this.findNode());
    }

    /**
     * Left mouse button click, in order to fix node highlight
     * Communicates with the content in order to open the correct page
     */
    private onClick() {
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
                if (node !== undefined) this.colorNode(node, 0);
                break;

            case 82: // R
            /* falls through */
            case 49: // 1
                if (node !== undefined) this.colorNode(node, 1);
                break;

            case 71: // G
            /* falls through */
            case 50: // 2
                if (node !== undefined) this.colorNode(node, 2);
                break;

            case 66: // B
            /* falls through */
            case 51: // 3
                if (node !== undefined) this.colorNode(node, 3);
                break;

            case 79: // O
            /* falls through */
            case 52: // 4
                if (node !== undefined) this.colorNode(node, 4);
                break;
        }

        // Check if the event loop is running, if not, restart
        if (!d3.event.active) this.cbSimulation.restart();
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
        if (!d3.event.active) this.cbSimulation.restart();
    }

    /**
     * Move the view to the given node
     * It keeps the relations inside the view
     * @param node
     * @param nodeOnly
     */
    private moveToNode(node: NodeType, nodeOnly?: boolean) {
        // Check for node existence
        if (node === undefined) return;

        // Stop simulation for now to prevent node walking
        this.mouseMoveDisabled = true;
        this.cbSimulation.stop();

        // Set clicked node as highlighted
        this.setNodeAsHighlight(node, nodeOnly);

        let minX = this.mapWidth, maxX = 0, minY = this.mapHeight, maxY = 0;
        if (!nodeOnly) {
            const zoomMargin = this.zoomMargin;
            // Find current locations of highlighted nodes
            this.cbGraph.nodes.map((node) => {
                if (!node.highlighted) return;
                minX = Math.min(minX, node.x - node.radius - zoomMargin);
                maxX = Math.max(maxX, node.x + node.radius + zoomMargin);
                minY = Math.min(minY, node.y - node.radius - zoomMargin);
                maxY = Math.max(maxY, node.y + node.radius + zoomMargin);
            });

            // Do the actual move
            this.moveToPosition(minX, maxX, minY, maxY);
        } else {
            // Calculate transform for move without zoom change
            let transform = d3.zoomIdentity
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
        if (!this.isLoaded) return;

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
        this.cbTransform = this.limitTransform(d3.event.transform);
        window.requestAnimationFrame(() => this.drawGraph());
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
     * Canvas draw methods
     *****************************************************************************************************/

    /**
     * Draws the complete concept browser, refreshes the view on every iteration
     * @note Order in this function is important!
     */
    private drawGraph() {
        // Limit the nodes
        this.cbGraph.nodes.map((n) => this.limitNode(n));

        // Save state
        this.context.save();

        // Clear canvas
        this.context.clearRect(0, 0, this.canvasWidth, this.canvasHeight);

        // Adjust scaling
        this.context.translate(this.cbTransform.x, this.cbTransform.y);
        this.context.scale(this.cbTransform.k, this.cbTransform.k);

        // Draw grid lines
        if (this.drawGrid) {
            this.context.beginPath();
            for (let i = 0; i <= this.mapWidth; i += 100) {
                this.context.moveTo(i, 0);
                this.context.lineTo(i, this.mapHeight);
            }
            for (let j = 0; j <= this.mapHeight; j += 100) {
                this.context.moveTo(0, j);
                this.context.lineTo(this.mapWidth, j);
            }
            this.context.strokeStyle = 'black';
            this.context.stroke();

            // Draw canvas size rectangle
            this.context.beginPath();
            this.context.moveTo(0, 0);
            this.context.lineTo(this.canvasWidth, 0);
            this.context.lineTo(this.canvasWidth, this.canvasHeight);
            this.context.lineTo(0, this.canvasHeight);
            this.context.lineTo(0, 0);
            this.context.strokeStyle = 'blue';
            this.context.stroke();

            this.context.beginPath();
            this.context.fillStyle = '#ff0000';
            this.context.arc(this.halfCanvasWidth, this.halfCanvasHeight, 10, 0, 2 * Math.PI);
            this.context.fill();
        }

        //////////////////////
        // NORMAL           //
        //////////////////////

        // Draw normal links
        this.context.beginPath();
        this.context.lineWidth = this.config.linkLineWidth;
        this.context.strokeStyle = this.isDragging || this.highlightedNode !== null
            ? this.config.fadedLinksStrokeStyle : this.config.defaultLinkStrokeStyle;
        this.cbGraph.links.map((l) => this.drawNormalLink(l));
        this.context.stroke();

        // Draw normal nodes
        for (let nn = -1; nn <= 4; nn++) {
            this.applyStyle(nn);
            this.context.beginPath();
            this.context.fillStyle = this.isDragging || this.highlightedNode !== null
                ? this.config.fadedNodeFillStyle : this.config.defaultNodeFillStyle;
            this.cbGraph.nodes.filter(this.filterNodeOnColor(nn)).map((n) => this.drawNormalNode(n));
            this.context.fill();
        }

        // Draw link nodes
        if (this.drawLinkNodes) {
            this.context.beginPath();
            this.context.fillStyle = this.config.fadedNodeFillStyle;
            this.cbGraph.linkNodes.map((n) => this.drawNormalNode(n));
            this.context.fill();
        }

        // Draw normal link arrows
        this.context.fillStyle = this.isDragging || this.highlightedNode !== null
            ? this.config.fadedLinksStrokeStyle : this.config.defaultLinkStrokeStyle;
        this.cbGraph.links.map((l) => this.drawNormalLinkArrow(l));

        //////////////////////
        // DRAGGED          //
        //////////////////////

        // Draw dragged links
        if (this.isDragging) {
            this.context.beginPath();
            this.context.lineWidth = this.config.linkLineWidth;
            this.context.strokeStyle = this.config.draggedLinkStrokeStyle;
            this.cbGraph.links.map((l) => this.drawDraggedLink(l));
            this.context.stroke();
        }

        // Draw dragged nodes
        if (this.isDragging) {
            for (let dn = -1; dn <= 4; dn++) {
                this.applyStyle(dn);
                this.context.beginPath();
                this.context.lineWidth = this.config.nodeLineWidth;
                this.context.fillStyle = this.config.draggedNodeFillStyle;
                this.context.strokeStyle = this.config.draggedNodeStrokeStyle;
                this.cbGraph.nodes.filter(this.filterNodeOnColor(dn)).map((n) => this.drawDraggedNode(n));
                this.context.fill();
                this.context.stroke();
            }
        }

        // Draw dragged link arrows
        if (this.isDragging) {
            this.context.fillStyle = this.config.draggedLinkStrokeStyle;
            this.cbGraph.links.map((l) => this.drawDraggedLinkArrow(l));
        }

        //////////////////////
        // HIGHLIGHT        //
        //////////////////////

        // Draw highlighted links
        if (this.highlightedNode !== null) {
            this.context.beginPath();
            this.context.lineWidth = this.config.linkLineWidth;
            this.context.strokeStyle = this.config.highlightedLinkStrokeStyle;
            this.cbGraph.links.map((l) => this.drawHighlightedLink(l));
            this.context.stroke();
        }

        // Draw highlighted nodes
        for (let hn = -1; hn <= 4; hn++) {
            this.applyStyle(hn);
            this.context.beginPath();
            this.context.lineWidth = this.config.nodeLineWidth;
            this.context.fillStyle = this.config.highlightedNodeFillStyle;
            this.context.strokeStyle = this.config.highlightedNodeStrokeStyle;
            this.cbGraph.nodes.filter(this.filterNodeOnColor(hn)).map((n) => this.drawHighlightedNode(n));
            this.context.fill();
            this.context.stroke();
        }

        // Draw highlighted link arrows
        if (this.highlightedNode !== null) {
            this.context.fillStyle = this.config.highlightedLinkStrokeStyle;
            this.cbGraph.links.map((l) => this.drawHighlightedLinkArrow(l));
        }

        //////////////////////
        // LABELS           //
        //////////////////////

        // Set this lower to prevent horns on M/W letters
        // https://github.com/CreateJS/EaselJS/issues/781
        this.context.miterLimit = 2.5;

        // Draw link labels
        if (this.isDragging || this.highlightedNode !== null) {
            this.context.fillStyle = this.config.defaultNodeLabelColor;
            this.context.textBaseline = 'top';
            this.context.strokeStyle = this.config.activeNodeLabelStrokeStyle;
            this.cbGraph.links.map((l) => this.drawLinkText(l));
        }

        // Draw node labels
        this.context.fillStyle = this.config.defaultNodeLabelColor;
        this.context.textBaseline = 'middle';
        this.context.textAlign = 'center';
        this.context.strokeStyle = this.config.activeNodeLabelStrokeStyle;
        this.cbGraph.nodes.map((n) => this.drawNodeText(n));

        // Restore state
        this.context.restore();
    }

    /**
     * Draw the link line
     */
    private drawLink(link: LinkType) {
        if (!this.filters.showInstances && (link.target.instance || link.source.instance)) {
            return;
        }

        this.context.moveTo(link.target.x, link.target.y);
        this.context.lineTo(link.source.x, link.source.y);
    }

    /**
     * Draw a link when in normal state
     */
    private drawNormalLink(link: LinkType) {
        if ((link.target.dragged && link.source.dragged) || (link.target.highlighted && link.source.highlighted)) return;
        this.drawLink(link);
    }

    /**
     * Draw a link when in dragged state
     */
    private drawDraggedLink(link: LinkType) {
        if (link.source.dragged || link.target.dragged) this.drawLink(link);
    }

    /**
     * Draw a link when in highlight state
     */
    private drawHighlightedLink(link: LinkType) {
        if (link.source.index === this.highlightedNode!.index || link.target.index === this.highlightedNode!.index) this.drawLink(link);
    }

    /**
     * Draw the link text
     * @param link
     */
    private drawLinkText(link: LinkType) {
        if (!this.filters.showInstances && (link.target.instance || link.source.instance)) {
            return;
        }

        // Only draw the text when the link is active
        if (link.relationName &&
            (!this.isDragging && (link.source.index === this.highlightedNode!.index || link.target.index === this.highlightedNode!.index)) ||
            (link.source.dragged || link.target.dragged)) {

            // Calculate font(size)
            const fontScale = Math.min(link.source.fontScale, link.target.fontScale);
            const scaledFontSize = Math.ceil(this.config.defaultNodeLabelFontSize * fontScale);
            this.context.font = scaledFontSize + 'px ' + this.config.fontFamily;
            this.context.lineWidth = this.config.activeNodeLabelLineWidth * fontScale;

            // Calculate angle of label
            let startRadians = Math.atan((link.source.y - link.target.y) / (link.source.x - link.target.x));
            startRadians += (link.source.x >= link.target.x) ? Math.PI : 0;

            // Transform the context
            this.context.save();
            this.context.translate(link.source.x, link.source.y);
            this.context.rotate(startRadians);

            // Check rotation and add extra if required
            let linkLabelLength = this.getLinkLabelLength(link);
            let sourceRadius = this.getNodeRadius(link.source) + 5;
            if ((startRadians * 2) > Math.PI) {
                this.context.rotate(Math.PI);
                this.context.textAlign = 'right';
                this.context.strokeText(link.relationName, -sourceRadius, 0, linkLabelLength);
                this.context.fillText(link.relationName, -sourceRadius, 0, linkLabelLength);
            } else {
                this.context.textAlign = 'left';
                this.context.strokeText(link.relationName, sourceRadius, 0, linkLabelLength);
                this.context.fillText(link.relationName, sourceRadius, 0, linkLabelLength);
            }

            // Restore context
            this.context.restore();
        }
    }

    /**
     * Draw the link arrow
     */
    private drawLinkArrow(link: LinkType) {
        if (!this.filters.showInstances && (link.target.instance || link.source.instance)) {
            return;
        }

        const xDistance = link.source.x - link.target.x;
        const yDistance = link.source.y - link.target.y;

        // Calculate head rotation
        const radians = Math.atan(yDistance / xDistance);
        const startRadians = radians + ((link.source.x >= link.target.x) ? -1 : 1) * Math.PI / 2;

        // Calculate the arrow head starting point
        let arrowHeadStart;
        if (link.target.instance) {
            // Calculate arrow head start for instance, as they are not circles
            if (radians > Math.PI / 4) {
                arrowHeadStart = link.target.radius / Math.sin(radians);
            } else if (radians < -Math.PI / 4) {
                arrowHeadStart = -link.target.radius / Math.sin(radians);
            } else {
                arrowHeadStart = link.target.radius / Math.cos(radians);
            }
        } else {
            arrowHeadStart = link.target.radius;
        }

        // Draw the triangle
        this.context.save();
        this.context.beginPath();
        this.context.translate(link.target.x, link.target.y);
        this.context.rotate(startRadians);
        this.context.moveTo(0, arrowHeadStart - 1);
        this.context.lineTo(3, 9 + arrowHeadStart);
        this.context.lineTo(-3, 9 + arrowHeadStart);
        this.context.closePath();
        this.context.restore();
        this.context.fill();
    }

    /**
     * Draw a link arrow when in normal state
     */
    private drawNormalLinkArrow(link: LinkType) {
        if ((link.target.dragged && link.source.dragged) || (link.target.highlighted && link.source.highlighted)) return;
        this.drawLinkArrow(link);
    }

    /**
     * Draw a link arrow when in dragged state
     */
    private drawDraggedLinkArrow(link: LinkType) {
        if (link.target.dragged || link.source.dragged) this.drawLinkArrow(link);
    }

    /**
     * Draw a link arrow when in highlighted state
     */
    private drawHighlightedLinkArrow(link: LinkType) {
        if (link.target.index === this.highlightedNode!.index || link.source.index === this.highlightedNode!.index) this.drawLinkArrow(link);
    }


    /**
     * Draw the node
     * @param node
     */
    private drawNode(node: NodeType) {
        if (node.instance) {
            if (!this.filters.showInstances) {
                return;
            }
            this.context.moveTo(node.x - node.radius, node.y - node.radius);
            this.context.rect(node.x - node.radius, node.y - node.radius, node.radius * 2, node.radius * 2);
        } else {
            this.context.moveTo(node.x + node.radius, node.y);
            this.context.arc(node.x, node.y, node.radius, 0, 2 * Math.PI);
        }
    }

    /**
     * Draw a node when in normal state
     * @param node
     */
    private drawNormalNode(node: NodeType) {
        if (node.highlighted || node.dragged) return;
        this.drawNode(node);
    }

    /**
     * Draw a node when in dragged state
     * @param node
     */
    private drawDraggedNode(node: NodeType) {
        if (node.dragged) this.drawNode(node);
    }

    /**
     * Draw a node when in highlighted state
     * @param node
     */
    private drawHighlightedNode(node: NodeType) {
        if (node.highlighted || node.specialHilight) this.drawNode(node);
    }


    /**
     * Draw the node text
     * @param node
     */
    private drawNodeText(node: NodeType) {
        if (!this.filters.showInstances && node.instance) {
            return;
        }

        // Calculate font(size)
        let scaledFontSize = Math.ceil(this.config.defaultNodeLabelFontSize * node.fontScale);
        this.context.font = 'bold ' + scaledFontSize + 'px ' + this.config.fontFamily;
        this.context.lineWidth = this.config.activeNodeLabelLineWidth * node.fontScale;

        // Set font if accordingly, or skip if not
        if (!((this.isDragging && node.dragged)
            || ((this.highlightedNode !== null || this.isDragging) && node.highlighted)
            || (this.specialHighlightedNode !== null && node.specialHilight))) {

            // Skip this text if not required to render
            if (this.isDragging || this.highlightedNode !== null) return;
        }

        // Draw the actual text (which can be multiple lines)
        let yStart = node.y - node.expandedLabelStart;
        node.expandedLabel.map((line) => {
            if (node.dragged || node.highlighted || node.specialHilight) this.context.strokeText(line, node.x, yStart);
            this.context.fillText(line, node.x, yStart);
            yStart += scaledFontSize;
        });
    }


    /******************************************************************************************************
     * Color functions
     *****************************************************************************************************/

    /**
     * Function to filter nodes on a given color index
     */
    private filterNodeOnColor(color: number) {
        return function (node: NodeType) {
            if (color === -1) {
                return node.empty;
            }
            return !node.empty && node.color === color;
        };
    }

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
    }

    /**
     * Resets all node colors and clears the local storage
     */
    private resetNodeColors() {
        this.cbGraph.nodes.map((node) => {
            node.color = node.instance ? this.config.instanceDefaultColor : 0;
        });

        // Clear local storage for loaded nodes only
        if (typeof (Storage) !== 'undefined') {
            this.cbGraph.nodes.map((node) => {
                localStorage.removeItem('nodeColor.' + node.id);
            });
        }
    }

    /**
     * Load node colors from the local storage
     */
    private loadNodeColor(node: NodeType) {
        node.color = 0;
        if (typeof (Storage) !== 'undefined') {
            const color = localStorage.getItem('nodeColor.' + node.id);
            if (color !== null) {
                node.color = parseInt(color);
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
                 node, kx = (alpha * this.boundForceStrenght) / this.mapWidth,
                 ky = (alpha * this.boundForceStrenght) / this.mapHeight; i < n; ++i) {
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
    };

    public update(data: JsonType[]) {
        // First, stop de simulation
        this.cbSimulation.stop();

        // Reset the link nodes
        this.cbGraph.linkNodes = [];

        // Map the new data
        const availConcepts: any[] = [];
        const availLinks: any[] = [];
        data.map((concept) => {
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
            this.getNodeRadius(node);
            this.loadNodeColor(node);
            this.updateNodeFontScale(node);
            this.config.updateLabel(node, node.fontScale);
        });

        // Loop data again, as now we have all nodes available to create the links
        data.map((concept) => {
            // Update relations
            concept.relations.map((relation: any) => {
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
        this.cbGraph.nodes = this.cbGraph.nodes.filter(function (node) {
            return availConcepts.indexOf(node.id) !== -1;
        });
        // Remove missing links
        this.cbGraph.links = this.cbGraph.links.filter(function (link) {
            return availLinks.indexOf(link.id) !== -1;
        });

        // Recreate the link nodes
        this.generateLinkNodes();
        this.updateLinkNodeLocations();

        // Reload the nodes and links
        this.reloadSimulation();

        // Restart simulation
        this.cbSimulation.alpha(0.01);
        this.cbSimulation.restart();
    };

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

        // Map the nodes and relations to their js equivalent
        data.map((concept: any) => {
            // Node mapping
            this.cbGraph.nodes.push({
                id: concept.id,
                label: concept.name,
                instance: concept.instance,
                empty: concept.isEmpty,
                link: '',
                numberOfLinks: concept.numberOfLinks,
            } as NodeType);

            // Relation mapping
            concept.relations.map((relation: any) => {
                this.cbGraph.links.push({
                    id: relation.id,
                    source: concept.id,
                    target: relation.target,
                    relationName: relation.relationName,
                });
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
                .id(function (d: any) {
                    return d.id;
                }))
            .force('center',                            // To force the node to move around the map center
                d3.forceCenter(this.mapWidth / 2, this.mapHeight / 2));

        // Calculate some one-time values before rendering starts
        this.cbGraph.nodes.map((node) => {
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
            // Update link node positions
            this.updateLinkNodeLocations();

            // Draw the actual graph
            window.requestAnimationFrame(() => this.drawGraph());
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
        const $filterBtn = $('#filter-button');
        // @ts-ignore
        $filterBtn.popover({
            html: true,
            trigger: 'manual',
            placement: 'bottom',
            template: '<div class="popover filter-popover" role="tooltip"><div class="arrow"></div><h3 class="popover-header"></h3><div class="popover-body"></div></div>',
            content: $('#filter-content'),
        });
        $filterBtn.on('click', function () {
            $filterBtn.popover('toggle');
        });
        $filterBtn.on('show.bs.popover', function () {
            $filterBtn.tooltip('hide');
            $filterBtn.tooltip('disable');
        });
        $filterBtn.on('hidden.bs.popover', function () {
            $filterBtn.tooltip('enable');
        });

        // Create handlers for filters
        $('#filter-show-instances').on('change', () => {
            this.filters.showInstances = $(this).is(':checked');
            window.requestAnimationFrame(() => this.drawGraph());
        });

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
                        if (key === 'quit') return;
                        if (key.startsWith('style')) this.colorNode(this.contextMenuNode!, parseInt(key.substr(6)));
                        if (key === 'reset') this.resetNodeColors();
                        if (key === 'create-instance') this.createInstance(this.contextMenuNode!);
                        if (key === 'center') this.centerView();
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
    };

    /**
     * Context menu builder
     * @returns {*}
     */
    private getContextMenuItems() {
        if (this.contextMenuNode === null) {
            // Global
            return {
                'reset': {name: 'Reset node colors', icon: 'fa-undo'},
                'sep1': '---------',
                'center': {name: 'Back to center', icon: 'fa-sign-in'},
                'sep2': '---------',
                'quit': {name: 'Close', icon: 'fa-times'},
            };
        } else {
            // Node
            let nodeItems = {};

            // Generate individuals, if any
            if (this.contextMenuNode.individuals !== undefined) {
                const individualsItems: { [key: string]: any } = {};
                const individualsTextItems = this.contextMenuNode.individuals.split(';');
                individualsTextItems.map((item) => {
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
                                icon: this.contextMenuNode.color === 0 || (this.contextMenuNode.color === this.config.instanceDefaultColor && this.contextMenuNode.instance) ? 'fa-check' : 'fa-undo',
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
                sep3: '---------',
                quit: {name: 'Close', icon: 'fa-times'},
            };

            return $.extend(nodeItems, defaultData);
        }
    }
}
