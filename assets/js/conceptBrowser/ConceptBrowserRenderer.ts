import {BrowserConfiguration} from '@/conceptBrowser/BrowserConfiguration';
import ConceptBrowser, {LinkType, NodeType} from '@/conceptBrowser/ConceptBrowser';

interface NodesPerColor {
    [color: number]: NodeType[];
}

/* tslint:disable:variable-name */
// noinspection JSMethodCanBeStatic
export default class ConceptBrowserRenderer {
    // Concept browser reference
    private readonly cb: ConceptBrowser;
    private readonly config: BrowserConfiguration;

    // Display settings
    private readonly mapWidth: number;
    private readonly mapHeight: number;

    // Render settings
    private _drawGrid: boolean = false;
    private _drawLinkNodes: boolean = false;

    // Render context
    private context: CanvasRenderingContext2D;

    private nodesToRender!: {
        all: NodeType[];
        normal: NodesPerColor;
        dragged: NodesPerColor;
        highlight: NodesPerColor;
    };

    private linksToRender!: {
        all: LinkType[];
        normal: LinkType[];
        dragged: LinkType[];
        highlight: LinkType[];
        text: LinkType[];
    };

    private shouldRefreshState: boolean = true;
    private filteredNodeIds: number[] = [];

    private readonly filters: {
        showInstances: boolean;
        tags: number[];
        tagOr: boolean;
    } = {
        showInstances: true,
        tags: [],
        tagOr: true,
    };

    constructor(cb: ConceptBrowser, canvas: HTMLCanvasElement, config: BrowserConfiguration, width: number, height: number) {
        this.cb = cb;
        this.config = config;
        this.context = canvas.getContext('2d')!;
        this.mapWidth = width;
        this.mapHeight = height;
    }

    public setShowInstances(show: boolean) {
        this.filters.showInstances = show;
        this.requestStateRefresh();
    }

    public setFilterTags(tagIds: number[]) {
        this.filters.tags = tagIds;
        this.requestStateRefresh();
    }

    public setFilterTagsOr(orState: boolean) {
        this.filters.tagOr = orState;
        this.requestStateRefresh();
    }

    public requestFrame() {
        window.requestAnimationFrame(() => this.draw());
    }

    public requestStateRefresh() {
        this.shouldRefreshState = true;
        this.requestFrame();
    }

    public isFiltered(node: NodeType) {
        return this.filteredNodeIds.includes(node.id);
    }

    /**
     * Rebuilds the internal state, which uses cached filtering for the nodes
     */
    private refreshState() {
        // Clear state
        this.nodesToRender = {
            all: [],
            normal: {},
            dragged: {},
            highlight: {},
        };
        this.linksToRender = {
            all: [],
            normal: [],
            dragged: [],
            highlight: [],
            text: [],
        };

        let state: 'normal' | 'dragged' | 'highlight' = 'normal';
        let color: number = 0;

        this.filteredNodeIds = [];
        this.cb.nodes.forEach((node) => {
            if (!this.filters.showInstances && node.instance) {
                this.filteredNodeIds.push(node.id);
                return;
            }

            if (this.filters.tags.length > 0) {
                if (this.filters.tagOr) {
                    if (!node.tags.some((t) => this.filters.tags.includes(t))) {
                        this.filteredNodeIds.push(node.id);
                        return;
                    }
                } else {
                    if (node.tags.filter((t) => this.filters.tags.includes(t)).length !== this.filters.tags.length) {
                        this.filteredNodeIds.push(node.id);
                        return;
                    }
                }
            }

            if (node.highlighted || node.specialHilight) {
                state = 'highlight';
            } else if (node.dragged) {
                state = 'dragged';
            } else {
                state = 'normal';
            }

            color = node.empty ? -1 : node.color;

            if (!this.nodesToRender[state][color]) {
                this.nodesToRender[state][color] = [];
            }

            this.nodesToRender.all.push(node);
            this.nodesToRender[state][color].push(node);
        });

        this.cb.links.forEach((link) => {
            if (this.isFiltered(link.target) || this.isFiltered(link.source)) {
                return;
            }

            if (this.cb.highlightedNode
                && (link.source.index === this.cb.highlightedNode.index || link.target.index === this.cb.highlightedNode.index)) {
                state = 'highlight';
            } else if (link.source.dragged || link.target.dragged) {
                state = 'dragged';
            } else {
                state = 'normal';
            }

            this.linksToRender.all.push(link);
            this.linksToRender[state].push(link);

            // Only draw the text when the link is active
            if (link.relationName &&
                (!this.cb.isDragging
                    && (this.cb.highlightedNode
                        && (link.source.index === this.cb.highlightedNode.index || link.target.index === this.cb.highlightedNode.index)))
                || (link.source.dragged || link.target.dragged)) {
                this.linksToRender.text.push(link);
            }
        });
    }

    /**
     * Draws the complete concept browser, refreshes the view on every iteration
     * @note Order in this function is important!
     */
    private draw() {
        // Refresh state when requested
        if (this.shouldRefreshState) {
            this.shouldRefreshState = false;
            this.refreshState();
        }

        // Save state
        this.context.save();

        // Clear canvas
        this.context.clearRect(0, 0, this.cb.canvasWidth, this.cb.canvasHeight);

        // Adjust scaling
        this.context.translate(this.cb.cbTransform.x, this.cb.cbTransform.y);
        this.context.scale(this.cb.cbTransform.k, this.cb.cbTransform.k);

        // Draw grid lines
        this.drawGrid();

        //////////////////////
        // NORMAL           //
        //////////////////////

        // Draw normal links
        this.context.beginPath();
        this.context.lineWidth = this.config.linkLineWidth;
        this.context.strokeStyle = this.cb.isDragging || this.cb.highlightedNode !== null
            ? this.config.fadedLinksStrokeStyle : this.config.defaultLinkStrokeStyle;
        this.linksToRender.normal.forEach((l) => this.drawLink(l));
        this.context.stroke();

        // Draw normal nodes
        Object.keys(this.nodesToRender.normal).forEach((key) => {
            const color = Number(key);
            this.config.applyStyle(color);
            this.context.beginPath();
            this.context.fillStyle = this.cb.isDragging || this.cb.highlightedNode !== null
                ? this.config.fadedNodeFillStyle : this.config.defaultNodeFillStyle;
            this.nodesToRender.normal[color].forEach((n) => this.drawNode(n));
            this.context.fill();
        });

        // Draw link nodes
        if (this._drawLinkNodes) {
            this.context.beginPath();
            this.context.fillStyle = this.config.fadedNodeFillStyle;
            this.cb.linkNodes.forEach((n) => this.drawNode(n));
            this.context.fill();
        }

        // Draw normal link arrows
        this.context.fillStyle = this.cb.isDragging || this.cb.highlightedNode !== null
            ? this.config.fadedLinksStrokeStyle : this.config.defaultLinkStrokeStyle;
        this.linksToRender.normal.forEach((l) => this.drawLinkArrow(l));

        //////////////////////
        // DRAGGED          //
        //////////////////////

        if (this.cb.isDragging) {
            // Draw dragged links
            this.context.beginPath();
            this.context.lineWidth = this.config.linkLineWidth;
            this.context.strokeStyle = this.config.draggedLinkStrokeStyle;
            this.linksToRender.dragged.forEach((l) => this.drawLink(l));
            this.context.stroke();

            // Draw dragged nodes
            Object.keys(this.nodesToRender.dragged).forEach((key) => {
                const color = Number(key);
                this.config.applyStyle(color);
                this.context.beginPath();
                this.context.lineWidth = this.config.nodeLineWidth;
                this.context.fillStyle = this.config.draggedNodeFillStyle;
                this.context.strokeStyle = this.config.draggedNodeStrokeStyle;
                this.nodesToRender.dragged[color].forEach((n) => this.drawNode(n));
                this.context.fill();
                this.context.stroke();
            });

            // Draw dragged link arrows
            this.context.fillStyle = this.config.draggedLinkStrokeStyle;
            this.linksToRender.dragged.forEach((l) => this.drawLinkArrow(l));
        }

        //////////////////////
        // HIGHLIGHT        //
        //////////////////////

        // Draw highlighted links
        if (this.linksToRender.highlight.length > 0) {
            this.context.beginPath();
            this.context.lineWidth = this.config.linkLineWidth;
            this.context.strokeStyle = this.config.highlightedLinkStrokeStyle;
            this.linksToRender.highlight.forEach((l) => this.drawLink(l));
            this.context.stroke();
        }

        // Draw highlighted nodes
        Object.keys(this.nodesToRender.highlight).forEach((key) => {
            const color = Number(key);
            this.config.applyStyle(color);
            this.context.beginPath();
            this.context.lineWidth = this.config.nodeLineWidth;
            this.context.fillStyle = this.config.highlightedNodeFillStyle;
            this.context.strokeStyle = this.config.highlightedNodeStrokeStyle;
            this.nodesToRender.highlight[color].forEach((n) => this.drawNode(n));
            this.context.fill();
            this.context.stroke();
        });

        if (this.linksToRender.highlight.length > 0) {
            // Draw highlighted link arrows
            this.context.fillStyle = this.config.highlightedLinkStrokeStyle;
            this.linksToRender.highlight.forEach((l) => this.drawLinkArrow(l));
        }

        //////////////////////
        // LABELS           //
        //////////////////////

        // Set this lower to prevent horns on M/W letters
        // https://github.com/CreateJS/EaselJS/issues/781
        this.context.miterLimit = 2.5;

        // Draw link labels
        if (this.cb.isDragging || this.cb.highlightedNode !== null) {
            this.context.fillStyle = this.config.defaultNodeLabelColor;
            this.context.textBaseline = 'top';
            this.context.strokeStyle = this.config.activeNodeLabelStrokeStyle;
            this.linksToRender.text.forEach((l) => this.drawLinkText(l));
        }

        // Draw node labels
        this.context.fillStyle = this.config.defaultNodeLabelColor;
        this.context.textBaseline = 'middle';
        this.context.textAlign = 'center';
        this.context.strokeStyle = this.config.activeNodeLabelStrokeStyle;
        this.nodesToRender.all.forEach((n) => this.drawNodeText(n));

        // Restore state
        this.context.restore();
    }

    /**
     * Draw the link line
     */
    private drawLink(link: LinkType) {
        this.context.moveTo(link.target.x, link.target.y);
        this.context.lineTo(link.source.x, link.source.y);
    }

    /**
     * Draw the link text
     */
    private drawLinkText(link: LinkType) {
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
        const linkLabelLength = this.cb.getLinkLabelLength(link);
        const sourceRadius = this.cb.getNodeRadius(link.source) + 5;
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

    /**
     * Draw the link arrow
     */
    private drawLinkArrow(link: LinkType) {
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
     * Draw the node
     */
    private drawNode(node: NodeType) {
        if (node.instance) {
            this.context.moveTo(node.x - node.radius, node.y - node.radius);
            this.context.rect(node.x - node.radius, node.y - node.radius, node.radius * 2, node.radius * 2);
        } else {
            this.context.moveTo(node.x + node.radius, node.y);
            this.context.arc(node.x, node.y, node.radius, 0, 2 * Math.PI);
        }
    }

    /**
     * Draw the node text
     */
    private drawNodeText(node: NodeType) {
        // Calculate font(size)
        const scaledFontSize = Math.ceil(this.config.defaultNodeLabelFontSize * node.fontScale);
        this.context.font = 'bold ' + scaledFontSize + 'px ' + this.config.fontFamily;
        this.context.lineWidth = this.config.activeNodeLabelLineWidth * node.fontScale;

        // Set font if accordingly, or skip if not
        if (!((this.cb.isDragging && node.dragged)
            || ((this.cb.highlightedNode !== null || this.cb.isDragging) && node.highlighted)
            || (this.cb.specialHighlightedNode !== null && node.specialHilight))) {

            // Skip this text if not required to render
            if (this.cb.isDragging || this.cb.highlightedNode !== null) {
                return;
            }
        }

        // Draw the actual text (which can be multiple lines)
        let yStart = node.y - node.expandedLabelStart;
        node.expandedLabel.forEach((line) => {
            if (node.dragged || node.highlighted || node.specialHilight) {
                this.context.strokeText(line, node.x, yStart);
            }

            this.context.fillText(line, node.x, yStart);
            yStart += scaledFontSize;
        });
    }

    /**
     * Draw the debug grid
     */
    private drawGrid() {
        if (!this._drawGrid) {
            return;
        }

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
        this.context.lineTo(this.cb.canvasWidth, 0);
        this.context.lineTo(this.cb.canvasWidth, this.cb.canvasHeight);
        this.context.lineTo(0, this.cb.canvasHeight);
        this.context.lineTo(0, 0);
        this.context.strokeStyle = 'blue';
        this.context.stroke();

        this.context.beginPath();
        this.context.fillStyle = '#ff0000';
        this.context.arc(this.cb.halfCanvasWidth, this.cb.halfCanvasHeight, 10, 0, 2 * Math.PI);
        this.context.fill();
    }
}
