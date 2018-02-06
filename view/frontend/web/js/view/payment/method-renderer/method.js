/*browser:true*/
/*global define*/
define(
    [
        'Mygento_Payment/js/view/payment/redirect-method'
    ],
    function (Component) {
        'use strict';
        return Component.extend({
            redirectUrl: 'cloudpayments/payment/process/'
        });
    }
);