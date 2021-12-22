// todo: temporary, probably need component with isLoading flag for sidebar block
define(
    [
        'uiComponent',
        'Aheadworks_OneStepCheckout/js/model/totals-service'
    ],
    function (Component, totalsService) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Aheadworks_OneStepCheckout/sidebar/totals'
            },
            isLoading: totalsService.isLoading
        });
    }
);
