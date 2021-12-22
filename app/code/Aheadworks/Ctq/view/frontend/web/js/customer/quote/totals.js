define([
    'Magento_Checkout/js/view/cart/totals',
    'Magento_Checkout/js/action/select-shipping-method'
], function (Component, selectShippingMethodAction) {
    'use strict';

    return Component.extend({

        /**
         * @override
         */
        initialize: function () {
            this._super();
            if (window.checkoutConfig.selectedShippingMethod) {
                selectShippingMethodAction(window.checkoutConfig.selectedShippingMethod);
            }
        }
    });
});
