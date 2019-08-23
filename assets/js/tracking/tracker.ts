import uuid from 'uuid/v1';

export default class Tracker {
    private readonly sessionId = uuid();
    private readonly studyArea: number;
    private readonly trackUser: boolean;

    private processQueueTimer: any;

    private trackingConsent: 'true' | 'false' | null = null;

    private pageloadQueue: any[] = [];
    private eventQueue: any[] = [];

    // Event types
    private static readonly concept_browser_open = 'concept_browser_open';
    private static readonly concept_browser_open_concept = 'concept_browser_open_concept';
    private static readonly concept_browser_close = 'concept_browser_close';
    private static readonly learning_path_browser_open = 'learning_path_browser_open';
    private static readonly learning_path_browser_open_concept = 'learning_path_browser_open_concept';
    private static readonly learning_path_browser_close = 'learning_path_browser_close';
    private static readonly general_link_click = 'general_link_click';

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
    public getTrackingConsent(): "true" | "false" | null {
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
            let $trackingModal = $('#tracking-modal');
            $trackingModal.modal({
                backdrop: 'static',
                keyboard: false,
            });

            // Register event handlers to modal buttons. Use off to ensure they are not bound multiple times
            // when a previous consent is reset.
            let $agreeButton = $('#tracking-modal-agree');
            $agreeButton.off('click');
            $agreeButton.on('click', () => {
                // Save and send tracking data
                this.saveTrackingConsent(true);
                $trackingModal.modal('hide');
                this.sendPageLoadTrackingData(request);
            });
            let $disagreeButton = $('#tracking-modal-disagree');
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
            opened ? Tracker.concept_browser_open : Tracker.concept_browser_close
        );
    }

    /**
     * Track concept open from concept browser
     * @param conceptId
     */
    public trackConceptBrowserConceptOpened(conceptId: number) {
        this.sendTrackingEventData(Tracker.concept_browser_open_concept, {conceptId});
    }

    /**
     * Track learning path browser state
     * @param opened
     * @param learningPathId
     */
    public trackLearningPathBrowser(opened: boolean, learningPathId?: number) {
        this.sendTrackingEventData(
            opened ? Tracker.learning_path_browser_open : Tracker.learning_path_browser_close,
            learningPathId ? {learningPathId} : {}
        );
    }

    /**
     * Track concept open from learning patch browser
     * @param conceptId
     */
    public trackLearningPathConceptOpened(conceptId: number) {
        this.sendTrackingEventData(Tracker.learning_path_browser_open_concept, {conceptId});
    }

    /**
     * Track general link clicks
     * @param link
     * @param blank
     */
    public trackLinkClick(link: string, blank?: boolean) {
        this.sendTrackingEventData(Tracker.general_link_click, {
            link: link,
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
            origin: request ? null : this.eHandler.previousUrl
        })
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
            event: event,
            context: context || {}
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
        let pageloadData = [];

        // Shift from array to ensure exclusive access
        while ((item = this.pageloadQueue.shift()) !== undefined) {
            pageloadData.push(item);
        }

        // Send the data
        window.navigator.sendBeacon(
            this.routing.generate('app_tracking_pageload', {_studyArea: this.studyArea}),
            JSON.stringify(pageloadData)
        );
    }

    /**
     * Process the event queue
     */
    private processEventQueue(): void {
        let item;
        let eventData = [];

        // Shift from array to ensure exclusive access
        while ((item = this.eventQueue.shift()) !== undefined) {
            eventData.push(item);
        }

        // Send the data
        window.navigator.sendBeacon(
            this.routing.generate('app_tracking_event', {_studyArea: this.studyArea}),
            JSON.stringify(eventData)
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
