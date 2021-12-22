define(
    [
        'ko',
        'uiRegistry',
        'Magento_Checkout/js/model/quote',
        'Aheadworks_OneStepCheckout/js/model/estimation-data-resolver',
        'Aheadworks_OneStepCheckout/js/model/same-as-shipping-flag',
        'Aheadworks_OneStepCheckout/js/action/get-sections-details',
        'Aheadworks_OneStepCheckout/js/model/totals-service',
        'Aheadworks_OneStepCheckout/js/model/shipping-address/new-address-form-state'
    ],
    function (
        ko,
        registry,
        quote,
        estimationDataResolver,
        sameAsShippingFlag,
        getSectionsDetailsAction,
        totalsService,
        newAddressFormState
    ) {
        'use strict';

        var isLoading = ko.observable(false);

        /**
         * Update payment methods
         */
        function updatePaymentMethods() {
            if (estimationDataResolver.resolveBillingAddress()
                && estimationDataResolver.resolveShippingAddress()
            ) {
                if (!isLoading() && !newAddressFormState.isShown()) {
                    isLoading(true);
                    totalsService.isLoading(true);

                    getSectionsDetailsAction(['paymentMethods', 'totals'], true).always(function () {
                        isLoading(false);
                        totalsService.isLoading(false);
                    });
                }
            }
        }

        quote.billingAddress.subscribe(updatePaymentMethods);
        sameAsShippingFlag.sameAsShipping.subscribe(function () {
            if (!quote.isQuoteVirtual()) {
                updatePaymentMethods();
            }
        });

        return {
            isLoading: isLoading,

            /**
             * Bind address fields
             *
             * @param {string} path
             */
            bindAddressFields: function (path) {
                registry.async(path)(function (element) {
                    element.on('value', updatePaymentMethods);
                });
            }
        };
    }
);
