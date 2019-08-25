import uuid from 'uuid/v1';

export default class Tracker {
    // Event types
    private static readonly CONCEPT_BROWSER_OPEN = 'concept_browser_open';
    private static readonly CONCEPT_BROWSER_OPEN_CONCEPT = 'concept_browser_open_concept';
    private static readonly CONCEPT_BROWSER_CLOSE = 'concept_browser_close';
    private static readonly LEARNING_PATH_BROWSER_OPEN = 'learning_path_browser_open';
    private static readonly LEARNING_PATH_BROWSER_OPEN_CONCEPT = 'learning_path_browser_open_concept';
    private static readonly LEARNING_PATH_BROWSER_CLOSE = 'learning_path_browser_close';
    private static readonly GENERAL_LINK_CLICK = 'general_link_click';

    private readonly sessionId = uuid();
    private readonly studyArea: number;
    private readonly trackUser: boolean;

    private processQueueTimer: any;

    private trackingConsent: 'true' | 'false' | null = null;

    private pageloadQueue: any[] = [];
    private eventQueue: any[] = [];

    /**
     * Constructor
     * @param studyArea
     * @param trackUser
     */
    constructor(studyArea: number, trackUser: boolean) {
        this.studyArea = studyArea;
        this.trackUser = trackUser;

        // Process the event queue every 30 seconds
        this.processQueueTimer = setInterval(() => this.processQueues(), 30000);
        // Make sure the clear the queues on page unload
        window.onbeforeunload = () => this.processQueues();
    }

    /**
     * Save tracking consent
     *
     * @param agree
     */
    public saveTrackingConsent(agree: boolean) {
        this.trackingConsent = agree ? 'true' : 'false';

        // Store in browser if possible
        if (typeof (Storage) !== 'undefined') {
            localStorage.setItem('tracking-consent.' + this.studyArea, this.trackingConsent);
        }

        // Create event
        this.eDispatch.trackingConsentUpdated(this.trackingConsent);
    }

    /**
     * Get tracking consent state
     */
    public getTrackingConsent(): 'true' | 'false' | null {
        return this.trackingConsent;
    }


    /**
     * Track the user' page load
     * @param request
     */
    public trackPageload(request: any) {
        // Verify whether tracking is enabled
        if (!this.trackUser) {
            return;
        }

        // Retrieve consent status, trackingConsent holds the session cached value
        if (this.trackingConsent === null && typeof (Storage) !== 'undefined') {
            // Retrieve from local storage for this study area
            this.trackingConsent = localStorage.getItem('tracking-consent.' + this.studyArea) as 'true' | 'false' | null;
        }

        // Check whether consent is granted
        if (this.trackingConsent === 'false') {
            // Denied, return;
            return;
        }

        if (this.trackingConsent === null) {
            // Opt-in question not yet asked, ask now. Disabled backdrop and keyboard modal exit.
            const $trackingModal = $('#tracking-modal');
            $trackingModal.modal({
                backdrop: 'static',
                keyboard: false,
            });

            // Register event handlers to modal buttons. Use off to ensure they are not bound multiple times
            // when a previous consent is reset.
            const $agreeButton = $('#tracking-modal-agree');
            $agreeButton.off('click');
            $agreeButton.on('click', () => {
                // Save and send tracking data
                this.saveTrackingConsent(true);
                $trackingModal.modal('hide');
                this.sendPageLoadTrackingData(request);
            });
            const $disagreeButton = $('#tracking-modal-disagree');
            $disagreeButton.off('click');
            $disagreeButton.on('click', () => {
                // Save only
                this.saveTrackingConsent(false);
                $trackingModal.modal('hide');
            });

            return;
        }

        // Try to send data
        this.sendPageLoadTrackingData(request);
    }

    /**
     * Track concept browser state
     * @param opened
     */
    public trackConceptBrowser(opened: boolean) {
        this.sendTrackingEventData(
            opened ? Tracker.CONCEPT_BROWSER_OPEN : Tracker.CONCEPT_BROWSER_CLOSE,
        );
    }

    /**
     * Track concept open from concept browser
     * @param conceptId
     */
    public trackConceptBrowserConceptOpened(conceptId: number) {
        this.sendTrackingEventData(Tracker.CONCEPT_BROWSER_OPEN_CONCEPT, {conceptId});
    }

    /**
     * Track learning path browser state
     * @param opened
     * @param learningPathId
     */
    public trackLearningPathBrowser(opened: boolean, learningPathId?: number) {
        this.sendTrackingEventData(
            opened ? Tracker.LEARNING_PATH_BROWSER_OPEN : Tracker.LEARNING_PATH_BROWSER_CLOSE,
            learningPathId ? {learningPathId} : {},
        );
    }

    /**
     * Track concept open from learning patch browser
     * @param conceptId
     */
    public trackLearningPathConceptOpened(conceptId: number) {
        this.sendTrackingEventData(Tracker.LEARNING_PATH_BROWSER_OPEN_CONCEPT, {conceptId});
    }

    /**
     * Track general link clicks
     * @param link
     * @param blank
     */
    public trackLinkClick(link: string, blank?: boolean) {
        this.sendTrackingEventData(Tracker.GENERAL_LINK_CLICK, {
            link,
            blank: typeof blank !== 'undefined' ? blank : false,
        });
    }

    /**
     * Sends the actual tracking data
     *
     * @param request
     */
    private sendPageLoadTrackingData(request: any) {
        // Validate consent before sending
        if (this.trackingConsent !== 'true') {
            return;
        }

        // Place data in the queue
        this.pageloadQueue.push({
            sessionId: this.sessionId,
            timestamp: this.timestamp,
            path: this.eHandler.currentUrl,
            origin: request ? null : this.eHandler.previousUrl,
        });
    }

    /**
     * Send a tracking event with context
     *
     * @param event
     * @param context
     */
    private sendTrackingEventData(event: string, context?: object): void {
        // Validate consent before sending
        if (this.trackingConsent !== 'true') {
            return;
        }

        // Place data in the queue
        this.eventQueue.push({
            sessionId: this.sessionId,
            timestamp: this.timestamp,
            event,
            context: context || {},
        });
    }

    /**
     * Process the queues
     */
    private processQueues(): void {
        console.info('Processing tracking queues');
        this.processPageloadQueue();
        this.processEventQueue();
    }

    /**
     * Process the page load queue
     */
    private processPageloadQueue(): void {
        let item;
        const pageloadData = [];

        // Shift from array to ensure exclusive access
        // tslint:disable-next-line:no-conditional-assignment
        while ((item = this.pageloadQueue.shift()) !== undefined) {
            pageloadData.push(item);
        }

        // Send the data
        window.navigator.sendBeacon(
            this.routing.generate('app_tracking_pageload', {_studyArea: this.studyArea}),
            JSON.stringify(pageloadData),
        );
    }

    /**
     * Process the event queue
     */
    private processEventQueue(): void {
        let item;
        const eventData = [];

        // Shift from array to ensure exclusive access
        // tslint:disable-next-line:no-conditional-assignment
        while ((item = this.eventQueue.shift()) !== undefined) {
            eventData.push(item);
        }

        // Send the data
        window.navigator.sendBeacon(
            this.routing.generate('app_tracking_event', {_studyArea: this.studyArea}),
            JSON.stringify(eventData),
        );
    }

    // noinspection JSMethodCanBeStatic
    /**
     * Retrieve the current timestamp, without milliseconds
     */
    private get timestamp(): string {
        return new Date().toISOString().split('.')[0] + 'Z';
    }

    // noinspection JSMethodCanBeStatic
    /**
     * Retrieve the event handler from the window object
     */
    private get eHandler(): any {
        // @ts-ignore
        return window.eHandler;
    }

    // noinspection JSMethodCanBeStatic
    /**
     * Retrieve the event dispatcher from the window object
     */
    private get eDispatch(): any {
        // @ts-ignore
        return window.eDispatch;
    }

    // noinspection JSMethodCanBeStatic
    /**
     * Retrieve the routing from the window object
     */
    private get routing(): any {
        // @ts-ignore
        return window.Routing;
    }
}
