import uuid from 'uuid/v1';

export default class Tracker {
    private readonly sessionId = uuid();
    private readonly studyArea: number;
    private readonly trackUser: boolean;

    private trackingConsent: 'true' | 'false' | null = null;

    /**
     * Constructor
     * @param studyArea
     * @param trackUser
     */
    constructor(studyArea: number, trackUser: boolean) {
        this.studyArea = studyArea;
        this.trackUser = trackUser;
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

    public getTrackingConsent(): "true" | "false" | null {
        return this.trackingConsent;
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

        // Post page load back to server
        // noinspection JSIgnoredPromiseFromCall We do need to wait for this to be completed
        $.ajax({
            type: 'POST',
            url: this.routing.generate('app_tracking_pageload', {_studyArea: this.studyArea}),
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
            data: JSON.stringify({
                sessionId: this.sessionId,
                timestamp: new Date().toISOString().split('.')[0] + 'Z', // remove milliseconds
                path: this.eHandler.currentUrl,
                origin: request ? null : this.eHandler.previousUrl
            })
        });
    }

    // noinspection JSMethodCanBeStatic
    private get eHandler(): any {
        // @ts-ignore
        return window.eHandler;
    }

    // noinspection JSMethodCanBeStatic
    private get eDispatch(): any {
        // @ts-ignore
        return window.eDispatch;
    }

    // noinspection JSMethodCanBeStatic
    private get routing(): any {
        // @ts-ignore
        return window.Routing;
    }
}
