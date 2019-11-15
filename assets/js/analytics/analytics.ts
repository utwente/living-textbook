import 'regenerator-runtime/runtime';
import Routing from 'fos-routing';

export default class Analytics {
    private router: Routing;
    private $container?: JQuery;
    private $form?: JQuery;
    private $formBtn?: JQuery;
    private $formInputs?: JQuery;
    private $errorModal?: JQuery;

    constructor(router: Routing) {
        this.router = router;
        this.reload();
    }

    private reload() {
        // Select used elements
        this.$container = $('.analytics-dashboard');
        this.$form = this.$container.find('form');
        this.$formBtn = this.$form.find('button');
        this.$formInputs = this.$form.find('select, input, button');
        this.$errorModal = this.$container.find('#analytics-modal');

        // Bind event handler
        this.$formBtn.off();
        this.$formBtn.on('click', (e) => {
            e.preventDefault();
            e.stopPropagation();

            // noinspection JSIgnoredPromiseFromCall No need to wait for finished here
            this.loadData();
        });

        this.setState(false);
    }

    private async loadData() {
        // Retrieve form data before disabling form
        const formData = this.$form!.serialize();
        this.setState(true);
        this.hideResults();

        // Submit the data to the controller
        try {
            const data = await $.post({
                url: this.router.generate('app_analytics_generate', {
                    // @ts-ignore
                    _studyArea: currentStudyArea,
                }),
                data: formData,
            });

            this.loadImage('.heatmap', data.heatmap);
            this.loadImage('.path-visits', data.pathVisits);
            this.showResults();
        } catch (e) {
            if (400 === e.status && e.responseJSON) {
                this.showError(JSON.stringify(e.responseJSON));
            } else {
                this.showError();
            }
        }

        this.setState(false);
    }

    private showError(message?: string) {
        const $modalCustom = this.$errorModal!.find('.modal-body.custom');
        const $modalDefault = this.$errorModal!.find('.modal-body.default');
        if (message) {
            $modalCustom.text(message).show();
            $modalDefault.hide();
        } else {
            $modalCustom.hide();
            $modalDefault.show();
        }
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

    private loadImage(selector: string, image: string) {
        const $element = this.$container!.find(selector);
        $element.find('img').remove();
        $element.append('<img src="' + image + '" alt="visualisation" />');
    }

    private showResults() {
        this.$container!.find('.result').show();
    }

    private hideResults() {
        this.$container!.find('.result').hide();
    }
}
