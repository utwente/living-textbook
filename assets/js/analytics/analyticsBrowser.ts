import {Configuration} from "@/analytics/configuration";
import * as d3 from 'd3';
import {ZoomTransform} from "d3-zoom";

export interface FlowThroughElement {
    id: number;
    isEmpty: boolean;
    lpIndex?: number;
    name: string;
    numberOfLinks: number;
    relations: FlowThroughRelation[],
    sizeOfNode?: number;
    visits?: number;
}

interface FlowThroughRelation {
    id: number;
    conceptInPath?: boolean;
    relationName: string;
    target: number;
}

interface BrowserElement extends FlowThroughElement {
    label: string;
    isLp: boolean;
    lpId: number;
    x: number;
    fx: number | null;
    y: number;
    fy: number | null;
    expandedLabelStart: number;
    expandedLabel: string[];
    highlighted: boolean;
}

require('../conceptBrowser/configuration.js');

/**
 * The analytics browser class handles all analytics rendering
 */
export default class AnalyticsBrowser {

    private readonly data: FlowThroughElement[];
    private readonly lpElements: BrowserElement[];
    private readonly lpChildElements: { [lpElementId: number]: BrowserElement[] } = {};
    private readonly lpChildSimulations: { [LpElementId: number]: d3.Simulation<any, any> } = {};

    private readonly canvas: HTMLCanvasElement;
    private readonly context: CanvasRenderingContext2D;

    private d3Canvas: any;
    private dragBehaviour: d3.DragBehavior<any, any, any> = d3.drag();
    private zoomBehaviour: d3.ZoomBehavior<any, any> = d3.zoom();
    private zoomTransform: ZoomTransform = d3.zoomIdentity;

    private readonly elementRadius: number;
    private readonly halfY: number;
    private twoPi: number = 2 * Math.PI;

    private readonly lpLineScale: number;
    private readonly lineScale: number;
    private readonly lpTextScale: number;
    private readonly textScale: number;
    private readonly lpFontSize: number;
    private readonly fontSize: number;

    private readonly scaleExtent: [number, number] = [0.5, 4];

    private hasHighlight: boolean = false;
    private draggingElement: BrowserElement | null = null;

    constructor(data: FlowThroughElement[]) {
        this.canvas = document.getElementById('analytics-canvas')! as HTMLCanvasElement;
        this.context = this.canvas.getContext("2d")!;

        if (!this.canvas) {
            throw new Error("Error finding required elements");
        }

        this.initD3();
        window.onresize = () => {
            this.resizeCanvas();
        };
        this.resizeCanvas();

        // Determine sizes
        this.elementRadius = Math.round(this.canvas.height / 20);
        this.halfY = Math.round(this.canvas.height / 2);

        // Determine font/line sizes
        this.lineScale = Math.round(this.elementRadius / 30 * 100) / 100; // Round on 2 decimals
        this.lpLineScale = 2 * this.lineScale;
        this.textScale = this.lineScale;
        this.lpTextScale = 2 * this.textScale;
        this.fontSize = Math.round(this.config.defaultNodeLabelFontSize * this.textScale);
        this.lpFontSize = Math.round(this.config.defaultNodeLabelFontSize * this.textScale * 2);

        // Store the given data
        this.data = data;

        // Find the learning path elements
        let interval = -this.halfY;
        this.lpElements = data
            .filter((element) => typeof element.lpIndex != 'undefined')
            .sort((a, b) => a.lpIndex! - b.lpIndex!)
            .map(el => {
                interval += 2 * this.halfY;
                const elem = Object.assign(el, {
                    label: el.name,
                    isLp: true,
                    lpId: el.id,
                    x: interval,
                    fx: null,
                    y: this.halfY,
                    fy: null,
                    expandedLabelStart: 0,
                    expandedLabel: [''],
                    highlighted: false,
                });
                this.config.updateLabel(elem, this.lpTextScale);
                return elem;
            });

        this.lpElements.forEach(lpElement => {
            this.lpChildElements[lpElement.id] = [];
            lpElement.relations.forEach(relation => {
                // Skip concept which are in the path
                if (relation.conceptInPath) return;
                if (relation.target == lpElement.id) return;

                // Create a real copy of the element
                const childElement = JSON.parse(JSON.stringify(this.data.find(el => el.id == relation.target)));
                delete childElement.lpIndex;
                childElement.isLp = false;
                childElement.lpId = lpElement.id;
                childElement.label = childElement.name;
                childElement.x = lpElement.x;
                childElement.fx = null;
                childElement.y = lpElement.y;
                childElement.fy = null;
                childElement.highlighted = false;
                this.config.updateLabel(childElement, this.textScale);
                this.lpChildElements[lpElement.id].push(childElement);
            });

            this.lpChildSimulations[lpElement.id] =
                d3.forceSimulation(this.lpChildElements[lpElement.id])
                    .force('radial', d3
                        .forceRadial(this.halfY / 2 + this.elementRadius, lpElement.x, lpElement.y)
                        .strength(0.2))
                    .force('charge', d3
                        .forceManyBody()
                        .strength(0.3))
                    .force('collide', d3
                        .forceCollide()
                        .radius(this.elementRadius))
                    .on('tick', () => {
                        window.requestAnimationFrame(() => this.drawGraph())
                    });
        });

        // Draw the graph
        this.d3Canvas.call(this.zoomBehaviour.transform, d3.zoomIdentity);
        window.requestAnimationFrame(() => this.drawGraph());
    }

    /**
     * Resizes the canvas on window resize
     */
    public resizeCanvas() {
        const containerSize = document.getElementsByClassName('analytics-canvas-div')[0].getBoundingClientRect();
        this.canvas.setAttribute('width', Math.round(containerSize.width).toFixed());
        this.canvas.setAttribute('height', Math.round(window.innerHeight / 2).toFixed());

        window.requestAnimationFrame(() => this.drawGraph());
    }

    // noinspection JSMethodCanBeStatic
    private get config(): Configuration {
        // @ts-ignore
        return window.bConfig;
    }

    private drawGraph() {
        // Save the current state
        this.context.save();

        // Clear the canvas
        this.context.clearRect(0, 0, this.canvas.width, this.canvas.height);

        // Adjust scaling
        this.context.translate(this.zoomTransform.x, this.zoomTransform.y);
        this.context.scale(this.zoomTransform.k, this.zoomTransform.k);

        // Draw the learning path nodes
        this.config.applyStyle(0);

        //////////////////////
        // NORMAL           //
        //////////////////////

        this.context.beginPath();
        this.context.fillStyle = this.config.defaultNodeFillStyle;
        this.lpElements.forEach(element => {
            if (element.highlighted) return;
            this.drawElement(element, this.elementRadius * 2)
        });
        this.context.fill();

        // Exit nodes
        this.config.applyStyle(-1);
        this.context.beginPath();
        this.context.fillStyle = this.config.defaultNodeFillStyle;
        this.lpElements.forEach(lpElement => {
            this.lpChildElements[lpElement.id].forEach(childElement => {
                if (childElement.highlighted) return;
                this.drawElement(childElement, this.elementRadius)
            });
        });
        this.context.fill();

        this.drawLabels(false);

        //////////////////////
        // HIGHLIGHT        //
        //////////////////////

        this.config.applyStyle(0);
        this.context.beginPath();
        this.context.lineWidth = this.config.nodeLineWidth * this.lineScale;
        this.context.fillStyle = this.config.highlightedNodeFillStyle;
        this.context.strokeStyle = this.config.highlightedNodeStrokeStyle;
        this.lpElements.forEach(element => {
            if (!element.highlighted) return;
            this.drawElement(element, this.elementRadius * 2);
        });
        this.context.fill();
        this.context.stroke();

        this.drawLabels(true, true, false);

        // Exit nodes
        this.config.applyStyle(-1);
        this.context.beginPath();
        this.context.lineWidth = this.config.nodeLineWidth * this.lineScale;
        this.context.fillStyle = this.config.highlightedNodeFillStyle;
        this.context.strokeStyle = this.config.highlightedNodeStrokeStyle;
        this.lpElements.forEach(lpElement => {
            this.lpChildElements[lpElement.id].forEach(childElement => {
                if (!childElement.highlighted) return;
                this.drawElement(childElement, this.elementRadius)
            });
        });
        this.context.fill();
        this.context.stroke();

        this.drawLabels(true, false);

        // Restore state
        this.context.restore();
    }

    private drawElement(element: BrowserElement, radius: number) {
        this.context.moveTo(element.x + radius, element.y);
        this.context.arc(element.x, element.y, radius, 0, this.twoPi);
    }

    private drawElementText(element: BrowserElement, fontSize: number) {
        // Set font accordingly
        this.context.font = fontSize + 'px ' + this.config.fontFamily;
        if (element.highlighted) {
            this.context.font = 'bold ' + this.context.font;
        }

        // Draw the actual text (which can be multiple lines)
        let yStart = element.y - element.expandedLabelStart;
        element.expandedLabel.forEach((line) => {
            if (element.highlighted) {
                this.context.strokeText(line, element.x, yStart);
            }
            this.context.fillText(line, element.x, yStart);
            yStart += fontSize;
        });
    }

    private drawLabels(highlighted: boolean, lpElements: boolean = true, childElements: boolean = true) {
        // Set this lower to prevent horns on M/W letters
        // https://github.com/CreateJS/EaselJS/issues/781
        this.context.miterLimit = 2.5;

        // Default text location
        this.context.textBaseline = 'middle';
        this.context.textAlign = 'center';
        this.context.fillStyle = this.config.defaultNodeLabelColor;
        this.context.strokeStyle = this.config.activeNodeLabelStrokeStyle;

        // Draw element labels
        if (lpElements) {
            this.context.lineWidth = this.config.activeNodeLabelLineWidth * this.lpTextScale;
            this.lpElements.forEach(lpElement => {
                if (lpElement.highlighted == highlighted) {
                    this.drawElementText(lpElement, this.lpFontSize);
                }
            });
        }

        if (childElements) {
            this.context.lineWidth = this.config.activeNodeLabelLineWidth * this.textScale;
            this.lpElements.forEach(lpElement => {
                this.lpChildElements[lpElement.id].forEach(childElement => {
                    if (childElement.highlighted != highlighted) return;
                    this.drawElementText(childElement, this.fontSize)
                });
            });
        }
    }

    /**
     * Find an element base on the event location
     * @returns {*}
     */
    private findElement(event: MouseEvent): BrowserElement | null {
        // Retrieve the actual location
        const loc = this.getEventLocation(event);

        // Find the element
        let dx,
            dy,
            d2 = Infinity,
            closest = null,
            searchRadius = this.elementRadius * 2 * this.elementRadius * 2;

        this.lpElements.forEach(element => {
            dx = loc.ox - element.x;
            dy = loc.oy - element.y;
            d2 = dx * dx + dy * dy;
            if (d2 < searchRadius) {
                closest = element;
                searchRadius = d2;
            }
        });

        if (d2 > this.elementRadius * this.elementRadius) {
            searchRadius = this.elementRadius * this.elementRadius;
        }

        let elements: BrowserElement[] = [];
        this.lpElements.forEach(lpElement => {
            elements.push(...this.lpChildElements[lpElement.id]);
        });

        elements.forEach(element => {
            dx = loc.ox - element.x;
            dy = loc.oy - element.y;
            d2 = dx * dx + dy * dy;
            if (d2 < searchRadius) {
                closest = element;
                searchRadius = d2;
            }
        });

        return closest;
    }

    /**
     * Retrieve the actual event location
     */
    private getEventLocation(event: MouseEvent): { ox: number, oy: number } {
        const rect = this.canvas.getBoundingClientRect();
        return {
            ox: (event.pageX - rect.left - this.canvas.clientLeft - window.pageXOffset - this.zoomTransform.x) / this.zoomTransform.k,
            oy: (event.pageY - rect.top - this.canvas.clientTop - window.pageYOffset - this.zoomTransform.y) / this.zoomTransform.k,
        };
    }

    private onDragStart() {
        const element = this.findElement(d3.event.sourceEvent);
        if (element && !element.isLp) {
            this.draggingElement = element;
            element.fx = element.x;
            element.fy = element.y;
            this.lpChildSimulations[element.lpId].alphaTarget(0.3).restart();
        } else {
            this.draggingElement = null;
        }
    }

    /**
     * Drag event handler
     */
    private onDrag() {
        if (this.draggingElement) {
            this.draggingElement.fx! += d3.event.dx / this.zoomTransform.k;
            this.draggingElement.fy! += d3.event.dy / this.zoomTransform.k;
        } else {
            this.d3Canvas.call(this.zoomBehaviour.transform, this.zoomTransform.translate(
                d3.event.dx / this.zoomTransform.k,
                d3.event.dy / this.zoomTransform.k
            ));
        }
    }

    private onDragEnd() {
        if (!this.draggingElement) return;

        this.lpChildSimulations[this.draggingElement.lpId].alphaTarget(0);
        this.draggingElement.fx = null;
        this.draggingElement.fy = null;
        this.draggingElement = null;
    }

    /**
     * Mouse move event handler
     */
    private onMouseMove() {
        const element = this.findElement(d3.event);

        if (null === element && this.hasHighlight) {
            this.hasHighlight = false;

            // Set element as highlighted
            this.lpElements.forEach(lpElement => {
                lpElement.highlighted = false;
                this.lpChildElements[lpElement.id].forEach(childElement => childElement.highlighted = false);
            });
        }

        if (null !== element) {
            // Do not set again
            if (element.highlighted) {
                return;
            }

            // Set element as highlighted
            this.lpElements.forEach(lpElement => {
                lpElement.highlighted = false;
                this.lpChildElements[lpElement.id].forEach(childElement => childElement.highlighted = false);
            });

            this.hasHighlight = true;
            element.highlighted = true;
            if (element.isLp) {
                // Highlight al child element
                this.lpChildElements[element.id].forEach(childElement => childElement.highlighted = true);
            } else {
                // Only highlight the lp element
                const lpId = element.lpId;
                this.lpElements.find(lpElement => lpElement.id == lpId)!.highlighted = true;
            }
        }

        window.requestAnimationFrame(() => this.drawGraph());
    }

    /**
     * Zoom event handler
     */
    private onZoom() {
        this.zoomTransform = d3.event.transform;
        window.requestAnimationFrame(() => this.drawGraph());
    }

    /**
     * Initializes D3 for rendering
     */
    private initD3() {
        this.dragBehaviour
            .container(this.canvas)
            .on('start', () => this.onDragStart())
            .on('drag', () => this.onDrag())
            .on('end', () => this.onDragEnd());

        this.zoomBehaviour
            .scaleExtent(this.scaleExtent)
            .on('zoom', () => this.onZoom());

        this.d3Canvas = d3.select(this.canvas as Element);
        this.d3Canvas
            .on('mousemove', () => this.onMouseMove())
            .call(this.dragBehaviour)
            .call(this.zoomBehaviour);
    }
}
