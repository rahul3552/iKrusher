define([
    'uiComponent',
    'Magento_Checkout/js/model/payment/renderer-list'
], function (Component, rendererList) {
    'use strict';

    rendererList.push(
        {
            type: 'aw_credit_limit',
            component: 'Aheadworks_CreditLimit/js/payment/method-renderer/aw-credit-limit-method'
        }
    );

    return Component;
});
