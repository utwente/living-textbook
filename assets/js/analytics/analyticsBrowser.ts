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

interface BrowserRelation {
    lpRelation: boolean;
    target: BrowserElement;
    source: BrowserElement;
}

interface LpBrowserRelation extends BrowserRelation {
    lpNext: boolean;
    lpCircular: boolean;
    lpForward: boolean;
}

require('../conceptBrowser/configuration.js');

/**
 * The analytics browser class handles all analytics rendering
 */
export default class AnalyticsBrowser {

    private readonly data: FlowThroughElement[];
    private readonly elements: BrowserElement[];
    private readonly relations: BrowserRelation[] = [];
    private readonly simulation: d3.Simulation<any, any> | null;

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
    private readonly circularControlOffsetX: number = 200;
    private readonly circularControlOffsetY: number = 150;
    private readonly quadraticControlOffsetX: number = 50;
    private readonly quadraticControlOffsetY: number = 300;

    private highlightedElement: BrowserElement | null = null;
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
        const lpData = data.filter((element) => typeof element.lpIndex !== 'undefined');

        // Find the learning path elements
        let interval = -this.halfY;
        this.elements = lpData
            .sort((a, b) => a.lpIndex! - b.lpIndex!)
            .map(el => {
                interval += 2 * this.halfY;
                const elem = Object.assign(el, {
                    label: `${el.name} (${el.visits})`,
                    isLp: true,
                    lpId: el.id,
                    x: interval,
                    fx: interval,
                    y: this.halfY,
                    fy: this.halfY,
                    expandedLabelStart: 0,
                    expandedLabel: [''],
                    highlighted: false,
                });
                this.config.updateLabel(elem, this.lpTextScale);
                return elem;
            });

        // Create the child elements
        this.lpElements.forEach((lpElement) => {
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
                this.elements.push(childElement);
            })
        });

        // Create the static relations
        this.lpElements.forEach((lpElem) => {
            lpElem.relations.forEach((r) => {
                if (!r.conceptInPath) return;

                const target = this.lpElements.find((el) => el.id === r.target)!;
                const relation: LpBrowserRelation = {
                    lpRelation: true,
                    lpNext: target.lpIndex! - lpElem.lpIndex! === 1,
                    lpCircular: target.id === lpElem.id,
                    lpForward: target.lpIndex! > lpElem.lpId!,
                    target: target,
                    source: lpElem,
                };
                this.relations.push(relation);
            })
        });

        // Create the simulation
        this.simulation =
            d3.forceSimulation(this.elements)
                .force('charge', d3
                    .forceManyBody()
                    .strength(0.3))
                .force('collide', d3
                    .forceCollide()
                    .radius(this.elementRadius))
                .on('tick', () => {
                    window.requestAnimationFrame(() => this.drawGraph())
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

        // LP links
        this.context.beginPath();
        this.context.lineWidth = this.config.linkLineWidth;
        this.context.strokeStyle = this.isDragging || this.hasHighlight
            ? this.config.fadedLinksStrokeStyle
            : this.config.defaultLinkStrokeStyle;
        this.lpRelations.forEach((link) => this.drawLinkLp(link));
        this.context.stroke();

        // LP nodes
        this.context.beginPath();
        this.context.fillStyle = this.config.defaultNodeFillStyle;
        this.lpElements.forEach((elem) => {
            if (elem.highlighted) return;
            this.drawElement(elem, this.elementRadius * 2)
        });
        this.context.fill();

        // Child nodes
        this.config.applyStyle(-1);
        this.context.beginPath();
        this.context.fillStyle = this.config.defaultNodeFillStyle;
        this.childElements.forEach((elem) => {
            if (elem.highlighted) return;
            this.drawElement(elem, this.elementRadius)
        });
        this.context.fill();

        // Labels
        this.drawLabels(false);

        //////////////////////
        // HIGHLIGHT        //
        //////////////////////

        // LP nodes
        this.config.applyStyle(0);
        this.context.beginPath();
        this.context.lineWidth = this.config.nodeLineWidth * this.lineScale;
        this.context.fillStyle = this.config.highlightedNodeFillStyle;
        this.context.strokeStyle = this.config.highlightedNodeStrokeStyle;
        this.lpElements.forEach((elem) => {
            if (!elem.highlighted) return;
            this.drawElement(elem, this.elementRadius * 2);
        });
        this.context.fill();
        this.context.stroke();

        this.drawLabels(true, true, false);

        // Child nodes
        this.config.applyStyle(-1);
        this.context.beginPath();
        this.context.lineWidth = this.config.nodeLineWidth * this.lineScale;
        this.context.fillStyle = this.config.highlightedNodeFillStyle;
        this.context.strokeStyle = this.config.highlightedNodeStrokeStyle;
        this.childElements.forEach((elem) => {
            if (!elem.highlighted) return;
            this.drawElement(elem, this.elementRadius)
        });
        this.context.fill();
        this.context.stroke();

        // Labels
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
            this.lpElements.forEach(elem => {
                if (elem.highlighted == highlighted) {
                    this.drawElementText(elem, this.lpFontSize);
                }
            });
        }

        if (childElements) {
            this.context.lineWidth = this.config.activeNodeLabelLineWidth * this.textScale;
            this.childElements.forEach(elem => {
                if (elem.highlighted != highlighted) return;
                this.drawElementText(elem, this.fontSize)
            });
        }
    }

    private drawLink(source: BrowserElement, target: BrowserElement) {
        this.context.moveTo(source.x, source.y);
        this.context.lineTo(target.x, target.y);
    }

    private drawLinkCircular(link: LpBrowserRelation) {
        this.context.moveTo(link.source.x, link.source.y);
        this.context.bezierCurveTo(
            link.source.x - this.circularControlOffsetX, link.source.y + this.circularControlOffsetY,
            link.target.x + this.circularControlOffsetX, link.target.y + this.circularControlOffsetY,
            link.target.x, link.target.y
        );
    }

    private drawLinkLp(link: LpBrowserRelation) {
        if (link.lpNext) {
            this.drawLink(link.source, link.target);
        } else if (link.lpCircular) {
            this.drawLinkCircular(link)
        } else {
            this.drawLinkQuadratic(link);
        }
    }

    private drawLinkQuadratic(link: LpBrowserRelation) {
        this.context.moveTo(link.source.x, link.source.y);
        const factor = link.lpForward ? 1 : -1;
        this.context.quadraticCurveTo(
            link.source.x + (factor * -1 * this.quadraticControlOffsetX),
            link.source.y + (factor * this.quadraticControlOffsetY),
            link.target.x, link.target.y);
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

        const findFunction: (element: BrowserElement) => void = (element) => {
            dx = loc.ox - element.x;
            dy = loc.oy - element.y;
            d2 = dx * dx + dy * dy;
            if (d2 < searchRadius) {
                closest = element;
                searchRadius = d2;
            }
        };

        this.lpElements.forEach(findFunction);

        if (d2 > this.elementRadius * this.elementRadius) {
            searchRadius = this.elementRadius * this.elementRadius;
        }

        this.childElements.forEach(findFunction);

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
            this.simulation!.alphaTarget(0.3).restart();
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

        this.simulation!.alphaTarget(0);
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
            this.highlightedElement = null;

            // Set element as highlighted
            this.elements.forEach(elem => {
                elem.highlighted = false;
            });
        }

        if (null !== element) {
            // Do not set again
            if (this.hasHighlight && element.id == this.highlightedElement!.id) {
                return;
            }

            // Set element as highlighted
            this.elements.forEach(elem => {
                elem.highlighted = false;
            });

            this.highlightedElement = element;
            element.highlighted = true;
            if (element.isLp) {
                // Highlight al child element
                this.childElements
                    .filter((el) => el.lpId == element.id)
                    .forEach((elem) => elem.highlighted = true);
            } else {
                // Only highlight the lp element
                const lpId = element.lpId;
                this.elements.find(lpElement => lpElement.id == lpId)!.highlighted = true;
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

    private get lpElements(): BrowserElement[] {
        return this.elements.filter((el) => el.isLp);
    }

    private get childElements(): BrowserElement[] {
        return this.elements.filter((el) => !el.isLp);
    }

    private get lpRelations(): LpBrowserRelation[] {
        return <LpBrowserRelation[]>this.relations.filter((r) => r.lpRelation);
    }

    private get childRelations(): BrowserRelation[] {
        return this.relations.filter((r) => !r.lpRelation);
    }

    private get isDragging(): boolean {
        return this.draggingElement !== null;
    }

    private get hasHighlight(): boolean {
        return this.highlightedElement !== null;
    }
}
