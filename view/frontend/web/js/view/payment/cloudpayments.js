/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (Component, rendererList) {
        'use strict';
        rendererList.push(
            {
                type: 'cloudpayments',
                component: 'Mygento_Cloudpayments/js/view/payment/method-renderer/method'
            }
        );
        return Component.extend({});
    }
);