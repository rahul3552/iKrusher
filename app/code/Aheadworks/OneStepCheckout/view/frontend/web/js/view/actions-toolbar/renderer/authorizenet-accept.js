define([
    'Aheadworks_OneStepCheckout/js/view/actions-toolbar/renderer/default'
], function (Component) {
    'use strict';

    return Component.extend({

        /**
         * @inheritdoc
         */
        placeOrder: function (data, event) {
            var self = this;

            if (event) {
                event.preventDefault();
            }
            this._beforeAction().done(function () {
                self._getMethodRenderComponent().beforePlaceOrder();
            });
        }
    });
});