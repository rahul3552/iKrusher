define([
    'Aheadworks_OneStepCheckout/js/view/actions-toolbar/renderer/default'
], function (Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Aheadworks_OneStepCheckout/actions-toolbar/renderer/adyen/payment/pos'
        },

        /**
         * Redirect to Adyen
         */
        continueToAdyen: function () {
            var self = this;

            this._beforeAction().done(function () {
                self._getMethodRenderComponent().continueToAdyen();
            });
        }
    });
});
