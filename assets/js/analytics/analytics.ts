import 'regenerator-runtime/runtime';
import {RoutingInterface} from '@/routing';
import AnalyticsBrowser, {FlowThroughElement} from './analyticsBrowser';

interface AnalyticsData {
    heatMap: string;
    pathVisits: string;
    pathUsers: string;
    flowThrough: FlowThroughElement[];
    metadata: {
        hitsPerLearningPathPerDay: { [lpId: string]: { [date: string]: number } },
        hitsPerDay: { [date: string]: number },
        typeOfHitsPerDay: {
            [date: string]: {
                general: number,
                conceptBrowser: number,
                learningPathBrowser: number,
                external: number,
            },
        },
        totalHitsPerPath: { [lpId: string]: number },
        totalUsersInPeriod: number,
    };
}

export default class Analytics {
    public data?: AnalyticsData;

    private router: RoutingInterface;
    private browser?: AnalyticsBrowser;

    private $container?: JQuery;
    private $form?: JQuery;
    private $formBtn?: JQuery;
    private $formInputs?: JQuery;
    private $errorModal?: JQuery;
    private $title?: JQuery;
    private $tableResults?: JQuery;
    private $imgResults?: JQuery;
    private $browserResults?: JQuery;

    constructor(router: RoutingInterface, data?: AnalyticsData) {
        this.router = router;
        this.data = data;

        // Select used elements
        this.$container = $('.analytics-dashboard');
        this.$form = this.$container.find('form');
        this.$formBtn = this.$form.find('button');
        this.$formInputs = this.$form.find('select, input, button');
        this.$errorModal = this.$container.find('#analytics-modal');
        this.$title = this.$container.find('.result-title');
        this.$tableResults = this.$container.find('.table-results');
        this.$imgResults = this.$container.find('.img-results');
        this.$browserResults = this.$container.find('.browser-results');

        // Bind event handler
        this.$formBtn.off();
        this.$formBtn.on('click', (e) => {
            e.preventDefault();
            e.stopPropagation();

            // noinspection JSIgnoredPromiseFromCall No need to wait for finished here
            this.loadData();
        });

        // Show the results. Hide first to clear existing data.
        this.hideResults();
        this.showResults();

        // Reset the form state
        this.setState(false);
    }

    private async loadData() {
        // Retrieve form data before disabling form
        const formData = this.$form!.serialize();
        this.setState(true);
        this.hideResults();

        // Submit the data to the controller
        try {
            this.data = await $.post({
                url: this.router.generate('app_analytics_generate', {
                    // @ts-ignore
                    _studyArea: currentStudyArea,
                }),
                data: formData,
            });

            this.showResults();
        } catch (e) {
            const axiosError = e as JQuery.jqXHR;
            if (400 === axiosError.status && axiosError.responseJSON) {
                this.showError(e, JSON.stringify(axiosError.responseJSON));
            } else {
                this.showError(e);
            }
        }

        this.setState(false);
    }

    private showError(e: any, message?: string) {
        const $modalCustom = this.$errorModal!.find('.modal-body.custom');
        const $modalDefault = this.$errorModal!.find('.modal-body.default');
        if (message) {
            $modalCustom.text(message).show();
            $modalDefault.hide();
        } else {
            $modalCustom.hide();
            $modalDefault.show();
        }
        console.error(e);
        this.$errorModal!.modal();
    }

    private setState(disabled: boolean) {
        if (disabled) {
            this.$formInputs!.attr('disabled', 'disabled');
            this.$formBtn!.find('.fa').addClass('fa-spin');
        } else {
            this.$formInputs!.removeAttr('disabled');
            this.$formBtn!.find('.fa').removeClass('fa-spin');
        }
    }

    private loadText(selector: string, text: string) {
        this.$container!
            .find(selector)
            .text(text);
    }

    private loadImage(selector: string, image: string) {
        this.$container!
            .find(selector)
            .append('<img src="' + image + '" alt="visualisation" />');
    }

    private showResults() {
        if (!this.data) {
            return;
        }

        // Title
        this.$title!.show();

        // Browser
        this.browser = new AnalyticsBrowser(this.data.flowThrough);
        this.$browserResults!.show();

        // Images
        this.loadImage('.heatmap', this.data.heatMap);
        this.loadImage('.path-visits', this.data.pathVisits);
        this.loadImage('.path-users', this.data.pathUsers);
        this.$imgResults!.show();

        // Table
        this.loadText('#metadata-users-in-period', this.data.metadata.totalUsersInPeriod.toString());
        const totalHitsPerPath = this.data.metadata.totalHitsPerPath;
        const totalHitsPerPathItems = Object.keys(totalHitsPerPath);
        this.loadText('#metadata-total-hits-on-path',
            totalHitsPerPathItems.length !== 0 ? totalHitsPerPath[totalHitsPerPathItems[0]].toString() : '0');
        this.$tableResults!.show();
    }

    private hideResults() {
        // Title
        this.$title!.hide();

        // Table
        this.$tableResults!.hide();

        // Images
        this.$imgResults!.hide();
        this.$imgResults!.find('img').remove();

        // Browser
        this.$browserResults!.hide();
    }
}
