import * as d3 from 'd3';
import {ZoomTransform} from 'd3-zoom';
import BrowserConfigurationInstance, {BrowserConfiguration} from '../conceptBrowser/BrowserConfiguration';

export interface FlowThroughElement {
    id: number;
    uniqId: number;
    isEmpty: boolean;
    lpIndex?: number;
    name: string;
    numberOfLinks: number;
    relations: FlowThroughRelation[];
    sizeOfNode?: number;
    visits?: number;
}

interface SimpleBrowserElement {
    x: number;
    fx: number | null;
    ux: number | null; // The not clipped value
    y: number;
    fy: number | null;
    uy: number | null; // The not clipped value
    visible: boolean;
}

interface FlowThroughRelation {
    id: number;
    conceptInPath?: boolean;
    relationName: string;
    target: number;
}

interface BrowserElement extends FlowThroughElement, SimpleBrowserElement {
    label: string;
    isLp: boolean;
    lpId: number;
    expandedLabelStart: number;
    expandedLabel: string[];
    highlighted: boolean;
    proxyHighlighted: boolean;
}

interface DraggedBrowserElement extends BrowserElement {
    other: BrowserElement;
}

interface BrowserRelation {
    label: string;
    target: BrowserElement;
    source: BrowserElement;
    lpRelation: boolean;
}

interface LpBrowserRelation extends BrowserRelation {
    lpNext: boolean;
    lpCircular: boolean;
    lpForward: boolean;
    lpDistance: number;
    cpx: number;
    cpy: number;
    labelRadians: number;
    labelPosition: { x: number; y: number };
}

/**
 * The analytics browser class handles all analytics rendering
 *
 * Curve calculation sourced from
 * http://www.independent-software.com/determining-coordinates-on-a-html-canvas-bezier-curve.html
 */
export default class AnalyticsBrowser {

    private static drawLinkIfHighLight(link: any, func: (link: any) => void) {
        if (AnalyticsBrowser.linkIsHighlighted(link)) {
            func(link);
        }
    }

    private static linkIsHighlighted(link: BrowserRelation): boolean {
        return link.source.highlighted && (link.target.highlighted || link.target.proxyHighlighted);
    }

    private static getBezierXY(
        t: number, sx: number, sy: number, cp1x: number, cp1y: number, cp2x: number, cp2y: number, ex: number, ey: number)
        : { x: number, y: number } {
        return {
            x: Math.pow(1 - t, 3) * sx + 3 * t * Math.pow(1 - t, 2) * cp1x
                + 3 * t * t * (1 - t) * cp2x + t * t * t * ex,
            y: Math.pow(1 - t, 3) * sy + 3 * t * Math.pow(1 - t, 2) * cp1y
                + 3 * t * t * (1 - t) * cp2y + t * t * t * ey,
        };
    }

    private static getBezierAngle(
        t: number, sx: number, sy: number, cp1x: number, cp1y: number, cp2x: number, cp2y: number, ex: number, ey: number)
        : number {
        const dx = Math.pow(1 - t, 2) * (cp1x - sx) + 2 * t * (1 - t) * (cp2x - cp1x) + t * t * (ex - cp2x);
        const dy = Math.pow(1 - t, 2) * (cp1y - sy) + 2 * t * (1 - t) * (cp2y - cp1y) + t * t * (ey - cp2y);
        return -Math.atan2(dx, dy) + 0.5 * Math.PI;
    }

    private static getQuadraticXY(t: number, sx: number, sy: number, cpx: number, cpy: number, ex: number, ey: number)
        : { x: number, y: number } {
        return {
            x: (1 - t) * (1 - t) * sx + 2 * (1 - t) * t * cpx + t * t * ex,
            y: (1 - t) * (1 - t) * sy + 2 * (1 - t) * t * cpy + t * t * ey,
        };
    }

    private static getQuadraticAngle(t: number, sx: number, sy: number, cpx: number, cpy: number, ex: number, ey: number)
        : number {
        const dx = 2 * (1 - t) * (cpx - sx) + 2 * t * (ex - cpx);
        const dy = 2 * (1 - t) * (cpy - sy) + 2 * t * (ey - cpy);
        return -Math.atan2(dx, dy) + 0.5 * Math.PI;
    }

    private readonly config: BrowserConfiguration = BrowserConfigurationInstance;

    private readonly data: FlowThroughElement[];
    private readonly elements: SimpleBrowserElement[];
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

    private uniqIdCounter: number = 0;

    private readonly linkLength: number = 200;
    private readonly scaleExtent: [number, number] = [0.5, 4];
    private readonly circularControlOffsetX: number = 200;
    private readonly circularControlOffsetY: number = 150;
    private readonly quadraticControlOffsetX: number = 0;
    private readonly quadraticControlOffsetY: number = 40;

    private highlightedElement: BrowserElement | null = null;
    private draggingElement: DraggedBrowserElement | null = null;

    constructor(data: FlowThroughElement[]) {
        this.canvas = document.getElementById('analytics-canvas')! as HTMLCanvasElement;
        if (!this.canvas) {
            throw new Error('Error finding canvas element');
        }

        this.context = this.canvas.getContext('2d')!;

        // Set this lower to prevent horns on M/W letters
        // https://github.com/CreateJS/EaselJS/issues/781
        this.context.miterLimit = 2.5;

        this.initD3();
        window.onresize = () => {
            this.resizeCanvas();
        };
        this.resizeCanvas();

        // Determine sizes
        this.elementRadius = Math.round(this.canvas.height / 20);
        this.halfY = Math.round(this.canvas.height / 2);

        // Determine font/line sizes
        this.lineScale = Math.round(this.elementRadius / 25 * 100) / 100; // Round on 2 decimals
        this.lpLineScale = Math.round(1.5 * this.lineScale);
        this.textScale = this.lineScale;
        this.lpTextScale = Math.round(1.5 * this.textScale);
        this.fontSize = Math.round(this.config.defaultNodeLabelFontSize * this.textScale);
        this.lpFontSize = Math.round(this.config.defaultNodeLabelFontSize * this.textScale * 1.5);

        // Store the given data
        this.data = data;
        const lpData = data.filter((element) => typeof element.lpIndex !== 'undefined');

        // Find the learning path elements
        let interval = -this.halfY;
        this.elements = lpData
            .sort((a, b) => a.lpIndex! - b.lpIndex!)
            .map((el) => {
                interval += 2 * this.halfY;
                const elem = Object.assign(el, {
                    uniqId: this.uniqIdCounter++,
                    label: `${el.name} (${el.visits})`,
                    isLp: true,
                    lpId: el.id,
                    x: interval,
                    fx: interval,
                    ux: interval,
                    y: this.halfY,
                    fy: this.halfY,
                    uy: this.halfY,
                    expandedLabelStart: 0,
                    expandedLabel: [''],
                    highlighted: false,
                    proxyHighlighted: false,
                    visible: true,
                });
                this.config.updateLabel(elem, this.lpTextScale);
                return elem;
            });

        // Create the child elements
        this.lpElements.forEach((lpElem) => {
            lpElem.relations.forEach((r) => {
                const source = lpElem;

                if (r.conceptInPath) {
                    const target = this.lpElements.find((el) => el.id === r.target)!;

                    // Pre calculate parameters for static curves, so we won't need to do that in every frame
                    const lpForward = target.lpIndex! > source.lpIndex!;
                    const lpDistance = Math.abs(target.lpIndex! - source.lpIndex!);
                    const lpCircular = target.id === source.id;
                    const factor = (lpForward ? -1 : 1) * Math.pow(2, lpDistance);
                    const cpx = source.x + (factor * this.quadraticControlOffsetX);
                    const cpy = source.y + (factor * this.quadraticControlOffsetY);

                    const cpx1 = source.x - this.circularControlOffsetX;
                    const cpy1 = source.y + this.circularControlOffsetY;
                    const cpx2 = target.x + this.circularControlOffsetX;
                    const cpy2 = target.y + this.circularControlOffsetY;

                    // const t = 0.3 / Math.log(lpDistance + 1);
                    const t = 0.5; // Fixed times position for drawing
                    const labelRadians = lpCircular
                        ? AnalyticsBrowser.getBezierAngle(t, source.x, source.y, cpx1, cpy1, cpx2, cpy2, target.x, target.y)
                        : AnalyticsBrowser.getQuadraticAngle(t, source.x, source.y, cpx, cpy, target.x, target.y);
                    const labelPosition = lpCircular
                        ? AnalyticsBrowser.getBezierXY(t, source.x, source.y, cpx1, cpy1, cpx2, cpy2, target.x, target.y)
                        : AnalyticsBrowser.getQuadraticXY(t, source.x, source.y, cpx, cpy, target.x, target.y);

                    const relation: LpBrowserRelation = {
                        label: r.relationName,
                        target,
                        source,
                        lpRelation: true,
                        lpNext: target.lpIndex! - source.lpIndex! === 1,
                        lpCircular,
                        lpForward,
                        lpDistance,
                        cpx,
                        cpy,
                        labelRadians,
                        labelPosition,
                    };
                    this.relations.push(relation);
                } else {
                    // Force all elements to start on a specific side, iteration over the lpIndex
                    let side = 1;
                    if (source.lpIndex! % 2 === 0) {
                        side = -1;
                    }

                    // Create a real copy of the element
                    const foundElem = this.data.find((el) => el.id === r.target)!;
                    const childElement: BrowserElement = Object.assign(
                        JSON.parse(JSON.stringify(foundElem)),
                        {
                            uniqId: this.uniqIdCounter++,
                            label: foundElem.name,
                            isLp: false,
                            lpId: source.id,
                            x: source.x,
                            fx: null,
                            ux: null,
                            y: source.y + (side * 100),
                            fy: null,
                            uy: null,
                            highlighted: false,
                            proxyHighlighted: false,
                            visible: true,
                        });
                    delete childElement.lpIndex;

                    this.config.updateLabel(childElement, this.textScale);
                    this.elements.push(childElement);

                    // Create the relation object for it
                    this.relations.push({
                        label: r.relationName,
                        target: childElement,
                        source,
                        lpRelation: false,
                    });
                }
            });
        });

        // Create dummy child elements over the center line to prevent the actual child elements hovering over them
        const lpElements = this.lpElements;
        const first = lpElements[0];
        const last = lpElements[lpElements.length - 1];
        for (let x = first.x; x < last.x; x += this.elementRadius * 2) {
            this.elements.push({
                x,
                fx: x,
                ux: x,
                y: first.y,
                fy: first.y,
                uy: first.y,
                visible: false,
            });
        }

        // Create the simulation
        this.simulation =
            d3.forceSimulation(this.elements)
                .force('link', d3.forceLink(this.relations)
                    .distance(() => this.linkLength)
                    .strength(0.1))
                .force('collide', d3
                    .forceCollide()
                    .radius(this.elementRadius)
                    .strength(0.6))
                .on('tick', () => {
                    window.requestAnimationFrame(() => this.drawGraph());
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
        // LINKS            //
        //////////////////////

        // LP links
        this.context.beginPath();
        this.context.lineWidth = this.config.linkLineWidth;
        this.context.strokeStyle = this.hasHighlight
            ? this.config.fadedLinksStrokeStyle
            : this.config.defaultLinkStrokeStyle;
        this.lpRelations.forEach((r) => this.drawLinkLp(r));
        this.context.stroke();

        // Child links
        this.context.beginPath();
        this.childRelations.forEach((r) => this.drawLink(r.source, r.target));
        this.context.stroke();

        // Draw highlighted links
        if (this.hasHighlight) {
            this.context.beginPath();
            this.context.lineWidth = this.config.linkLineWidth * 2;
            this.context.strokeStyle = this.config.highlightedLinkStrokeStyle;
            this.lpRelations.forEach((r) =>
                AnalyticsBrowser.drawLinkIfHighLight(r, (link) => this.drawLinkLp(link)));
            this.context.stroke();

            this.context.beginPath();
            this.context.lineWidth = this.config.linkLineWidth;
            this.context.strokeStyle = this.config.draggedLinkStrokeStyle;
            this.childRelations.forEach((r) =>
                AnalyticsBrowser.drawLinkIfHighLight(r, (link) => this.drawLink(link.source, link.target)));
            this.context.stroke();
        }

        //////////////////////
        // NORMAL           //
        //////////////////////

        // LP nodes
        this.context.beginPath();
        this.context.fillStyle = this.hasHighlight
            ? this.config.fadedNodeFillStyle
            : this.config.defaultNodeFillStyle;
        this.lpElements.forEach((elem) => {
            if (elem.highlighted) {
                return;
            }
            this.drawElement(elem, this.elementRadius * 2);
        });
        this.context.fill();

        // Child nodes
        this.config.applyStyle(-1);
        this.context.beginPath();
        this.context.fillStyle = this.hasHighlight
            ? this.config.fadedNodeFillStyle
            : this.config.defaultNodeFillStyle;
        this.childElements.forEach((elem) => {
            if (elem.highlighted) {
                return;
            }
            this.drawElement(elem, this.elementRadius);
        });
        this.context.fill();

        // Labels
        if (!this.hasHighlight) {
            this.drawLabels(false);
            this.drawLinkLabels(false);
        }

        //////////////////////
        // HIGHLIGHT        //
        //////////////////////

        // LP nodes
        this.config.applyStyle(0);
        this.context.beginPath();
        this.context.lineWidth = this.config.nodeLineWidth * this.lpLineScale;
        this.context.fillStyle = this.config.highlightedNodeFillStyle;
        this.context.strokeStyle = this.config.highlightedNodeStrokeStyle;
        this.lpElements.forEach((elem) => {
            if (!elem.highlighted && !elem.proxyHighlighted) {
                return;
            }
            this.drawElement(elem, this.elementRadius * 2);
        });
        this.context.fill();
        this.context.stroke();

        this.drawLabels(true, true, false);
        this.drawLinkLabels(true, true, true);

        // Child nodes
        this.config.applyStyle(-1);
        this.context.beginPath();
        this.context.lineWidth = this.config.nodeLineWidth * this.lineScale;
        this.context.fillStyle = this.config.highlightedNodeFillStyle;
        this.context.strokeStyle = this.config.highlightedNodeStrokeStyle;
        this.childElements.forEach((elem) => {
            if (!elem.highlighted && !elem.proxyHighlighted) {
                return;
            }
            this.drawElement(elem, this.elementRadius);
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
        // Default text location
        this.context.textBaseline = 'middle';
        this.context.textAlign = 'center';
        this.context.fillStyle = this.config.defaultNodeLabelColor;
        this.context.strokeStyle = this.config.activeNodeLabelStrokeStyle;

        // Draw element labels
        if (lpElements) {
            this.context.lineWidth = this.config.activeNodeLabelLineWidth * this.lpTextScale;
            this.lpElements.forEach((elem) => {
                if (elem.highlighted !== highlighted && elem.proxyHighlighted !== highlighted) {
                    return;
                }

                this.drawElementText(elem, this.lpFontSize);
            });
        }

        if (childElements) {
            this.context.lineWidth = this.config.activeNodeLabelLineWidth * this.textScale;
            this.childElements.forEach((elem) => {
                if (elem.highlighted !== highlighted && elem.proxyHighlighted !== highlighted) {
                    return;
                }
                this.drawElementText(elem, this.fontSize);
            });
        }
    }

    private drawLinkLabels(highlighted: boolean, lpElements: boolean = true, childElements: boolean = true) {
        // this.context.textBaseline = 'bottom';
        this.context.lineWidth = this.config.activeNodeLabelLineWidth * this.lpTextScale;

        if (lpElements) {
            this.lpRelations.forEach((link) => {
                if (highlighted && !AnalyticsBrowser.linkIsHighlighted(link)) {
                    return;
                }

                if (link.lpNext) {
                    this.drawLinkLabel(link);
                } else if (link.lpCircular) {
                    this.drawLinkLabel(link, link.labelRadians, 0, link.labelPosition.x, link.labelPosition.y, true);
                } else {
                    this.drawLinkLabel(link, link.labelRadians, 0, link.labelPosition.x, link.labelPosition.y, true);
                }
            });
        }

        if (childElements) {
            this.childRelations.forEach((link) => this.drawLinkLabelHighlighted(link, highlighted));
        }
    }

    private drawLinkLabelHighlighted(link: BrowserRelation, highlighted: boolean) {
        if (highlighted) {
            AnalyticsBrowser.drawLinkIfHighLight(link, (l) => this.drawLinkLabel(l));
        } else {
            this.drawLinkLabel(link);
        }
    }

    private drawLinkLabel(link: BrowserRelation, startRadians?: number, offsetY?: number, x?: number, y?: number, ignoreRadius?: true) {
        this.context.font = this.lpFontSize + 'px ' + this.config.fontFamily;
        if (link.source.highlighted || link.target.proxyHighlighted) {
            this.context.font = 'bold ' + this.context.font;
        }

        // Calculate the label angle
        if (startRadians === undefined) {
            startRadians = Math.atan((link.source.y - link.target.y) / (link.source.x - link.target.x));
            startRadians += (link.source.x >= link.target.x) ? Math.PI : 0;
        }
        if (offsetY === undefined) {
            offsetY = 0;
        }
        if (x === undefined) {
            x = link.source.x;
        }
        if (y === undefined) {
            y = link.source.y;
        }


        // Transform the context
        this.context.save();
        this.context.translate(x, y);
        this.context.rotate(startRadians);

        // Calculate label positions
        const sourceRadius = ignoreRadius === true ? 0 : this.linkLength / 2 + 20;
        const labelLength = link.label.length * 5 + 10;

        // Disable circular arrow heads
        if (!(link as LpBrowserRelation).lpCircular) {
            // Draw the arrow head
            this.drawArrowHead(sourceRadius - 25, offsetY, false);
        }

        // Reset color for label
        this.context.fillStyle = this.config.defaultNodeLabelColor;

        // Check rotation and add extra if required
        if ((startRadians * 2) > Math.PI) {
            this.context.rotate(Math.PI);
            this.context.textAlign = 'right';
            this.context.strokeText(link.label, -sourceRadius, offsetY + 2, labelLength);
            this.context.fillText(link.label, -sourceRadius, offsetY + 2, labelLength);
        } else {
            this.context.textAlign = 'left';
            this.context.strokeText(link.label, sourceRadius, offsetY + 2, labelLength);
            this.context.fillText(link.label, sourceRadius, offsetY + 2, labelLength);
        }

        // Restore context
        this.context.restore();
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
            link.target.x, link.target.y);
    }

    private drawLinkLp(link: LpBrowserRelation) {
        if (link.lpNext) {
            this.drawLink(link.source, link.target);
        } else if (link.lpCircular) {
            this.drawLinkCircular(link);
        } else {
            this.drawLinkQuadratic(link);
        }
    }

    private drawLinkQuadratic(link: LpBrowserRelation) {
        this.context.moveTo(link.source.x, link.source.y);
        this.context.quadraticCurveTo(
            link.cpx, link.cpy,
            link.target.x, link.target.y);
    }

    private drawArrowHead(x: number, y: number, isCircular: boolean) {
        this.context.beginPath();
        this.context.fillStyle = this.hasHighlight
            ? this.config.highlightedLinkStrokeStyle
            : this.config.defaultLinkStrokeStyle;
        if (isCircular) {
            // Manually rotate arrow a bit due to offset
            this.context.moveTo(x, y - 9);
            this.context.lineTo(x + 20, y);
            this.context.lineTo(x - 2, y + 2);
            this.context.lineTo(x, y - 9);
        } else {
            this.context.moveTo(x, y - 6);
            this.context.lineTo(x + 20, y);
            this.context.lineTo(x, y + 6);
            this.context.lineTo(x, y - 6);
        }
        this.context.fill();
    }

    /**
     * Find an element base on the event location
     * @returns {*}
     */
    private findElement(event: MouseEvent): BrowserElement | null {
        // Retrieve the actual location
        const loc = this.getEventLocation(event);

        // Find the element
        let dx;
        let dy;
        let d2 = Infinity;
        let closest = null;
        let searchRadius = this.elementRadius * 2 * this.elementRadius * 2;

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
            this.draggingElement = element as DraggedBrowserElement;
            this.draggingElement.other = this.lpElements.find((lpElem) => lpElem.id === element.lpId)!;
            element.fx = element.x;
            element.ux = element.x;
            element.fy = element.y;
            element.uy = element.y;
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
            this.draggingElement.ux! += d3.event.dx / this.zoomTransform.k;
            this.draggingElement.uy! += d3.event.dy / this.zoomTransform.k;

            // Calculate distance to relation
            const distance = Math.hypot(
                this.draggingElement.ux! - this.draggingElement.other.x,
                this.draggingElement.uy! - this.draggingElement.other.y);

            const allowedDistance = this.linkLength - 50;
            if (allowedDistance <= distance) {
                this.draggingElement.fx = this.draggingElement.ux;
                this.draggingElement.fy = this.draggingElement.uy;
            } else {
                const radians = Math.atan2(
                    this.draggingElement.uy! - this.draggingElement.other.y,
                    this.draggingElement.ux! - this.draggingElement.other.x);
                this.draggingElement.fx = Math.cos(radians) * allowedDistance + this.draggingElement.other.x;
                this.draggingElement.fy = Math.sin(radians) * allowedDistance + this.draggingElement.other.y;
            }
        } else {
            this.d3Canvas.call(this.zoomBehaviour.transform, this.zoomTransform.translate(
                d3.event.dx / this.zoomTransform.k,
                d3.event.dy / this.zoomTransform.k));
        }
    }

    private onDragEnd() {
        if (!this.draggingElement) {
            return;
        }

        this.simulation!.alphaTarget(0);
        this.draggingElement.fx = null;
        this.draggingElement.ux = null;
        this.draggingElement.fy = null;
        this.draggingElement.uy = null;
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
            this.visibleElements.forEach((elem) => {
                elem.highlighted = false;
                elem.proxyHighlighted = false;
            });
        }

        if (null !== element) {
            // Do not set again
            if (this.hasHighlight && element.uniqId === this.highlightedElement!.uniqId) {
                return;
            }

            // Set element as highlighted
            this.visibleElements.forEach((elem) => {
                elem.highlighted = false;
                elem.proxyHighlighted = false;
            });

            this.highlightedElement = element;
            element.highlighted = true;
            if (element.isLp) {
                // Highlight all child elements
                this.childElements
                    .filter((el) => el.lpId === element.id)
                    .forEach((elem) => elem.highlighted = true);
                // Highlight all lp elements with a direct relation
                this.lpRelations
                    .filter((r) => r.source.id === element.id)
                    .forEach((r) => r.target.proxyHighlighted = true);
            } else {
                // Only highlight the lp element
                this.visibleElements
                    .find((lpElem) => lpElem.id === element.lpId)!.highlighted = true;
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
        return (this.elements as BrowserElement[]).filter((el) => el.isLp && el.visible);
    }

    private get childElements(): BrowserElement[] {
        return (this.elements as BrowserElement[]).filter((el) => !el.isLp && el.visible);
    }

    private get visibleElements(): BrowserElement[] {
        return this.elements.filter((el) => el.visible) as BrowserElement[];
    }

    private get lpRelations(): LpBrowserRelation[] {
        return this.relations.filter((r) => r.lpRelation) as LpBrowserRelation[];
    }

    private get childRelations(): BrowserRelation[] {
        return this.relations.filter((r) => !r.lpRelation);
    }

    private get hasHighlight(): boolean {
        return this.highlightedElement !== null;
    }
}
