define(
    [
        'Aheadworks_OneStepCheckout/js/view/form/element/validation-enabled-flag'
    ],
    function (validationEnabledFlag) {
        'use strict';

        return function (component) {
            return component.extend({

                /**
                 * @inheritdoc
                 */
                validate: function () {
                    return validationEnabledFlag()
                        ? this._super()
                        : {valid: true, target: this};
                }
            });
        }
    }
);
