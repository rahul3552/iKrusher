define([
    'Magento_Checkout/js/model/quote',
    'underscore'
], function (quote, _) {
    "use strict";

    return function (paymentData, checkoutTotal) {
        var grandTotal = !_.isUndefined(checkoutTotal)
            ? checkoutTotal
            : quote.totals()['grand_total'];

        return paymentData.credit_available >= grandTotal;
    }
});
