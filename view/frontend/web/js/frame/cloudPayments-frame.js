define([
    'jquery',
    'mage/url',
    'https://widget.cloudpayments.ru/bundles/cloudpayments'
], function ($, url) {

    $.widget('mygento.cloudPaymentsFrame', {
        options: {
            steps: 'charge',
            params: '',
            baseUrl: '',
            successPageUrl: null,
            errorPageUrl: null,
        },

        _create: function () {
            this.init();
        },

        init: function () {
            url.setBaseUrl(this.options.baseUrl);

            var self = this;

            var widget = new cp.CloudPayments();

            widget[this.options.steps](self.options.params,
                function(option) {
                    self.successPayment(option)
                },
                function(reason, option) {
                    self.errorPayment(reason, option)
                });
        },

        successPayment: function (option) {
            //действие при успешном платеже
            if (this.options.successPageUrl) {
                window.location.replace(url.build(this.options.successPageUrl));
                return;
            }

            window.location.replace(url.build('checkout/onepage/success'));
        },

        errorPayment: function (reason, option) {
            if (this.options.errorPageUrl) {
                window.location.replace(url.build(this.options.errorPageUrl));
                return;
            }

            window.location.replace(url.build('checkout/onepage/success'));
        }
    });

    return $.mygento.cloudPaymentsFrame;
});
