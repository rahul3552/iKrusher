define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Aheadworks_OneStepCheckout/js/model/customer-email-validator'
    ],
    function (Component, additionalValidators, agreementValidator) {
        'use strict';

        additionalValidators.registerValidator(agreementValidator);

        return Component.extend({});
    }
);
