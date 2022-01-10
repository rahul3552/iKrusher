/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_CustomForm
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

'use strict';
define([
    'moment',
    'jquery',
    'Magento_Ui/js/form/element/date',
    './dependency',
    'touchPunch',
    'jquery-ui-modules/datepicker',
    'jquery-ui-modules/slider'
], function (moment, $, Element, dependency) {

    $.widget('ui.slider', $.ui.slider, {
        _setOptionDisabled: function (value) {
            if (value) {
                this._removeClass(this.hoverable, null, 'ui-state-hover');
                this._removeClass(this.focusable, null, 'ui-state-focus');
            }
        }
    });

    return Element.extend(dependency).extend({
        defaults: {
            options: {
                showOn: 'both',
                showsTime: true,
                timeFormat: 'HH:mm'
            }
        },
        initObservable: function () {
            this._super();
            this.addFieldToProvider();
            this.dependencyObs();
            this.validationParams = false;

            return this;
        },
        resetField: function () {
            this.value(null);
            $('[name="' + this.inputName + '"]').val('');
        },
        onShiftedValueChange: function (shiftedValue) {
            var value,
                formattedValue,
                momentValue;

            if (shiftedValue) {
                momentValue = moment(shiftedValue, this.pickerDateTimeFormat);

                if (this.options.showsTime) {
                    formattedValue = moment(momentValue).format(this.timezoneFormat);
                    value = moment.tz(formattedValue, this.storeTimeZone).tz('UTC').toISOString();
                } else {
                    value = momentValue.format(this.outputDateFormat);
                }
            } else {
                value = '';
            }

            if (value !== moment.tz(this.value(), this.storeTimeZone).tz('UTC').toISOString()) {
                this.value(this.value());
            }
        },
    });
});
