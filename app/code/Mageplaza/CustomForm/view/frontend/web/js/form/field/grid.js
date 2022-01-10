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
    'jquery',
    'ko',
    'underscore',
    'mageUtils',
    'Magento_Ui/js/form/element/abstract',
    './dependency',
    'Magento_Ui/js/lib/validation/validator',
    'mage/translate'

], function ($,ko, _, utils, Element, dependency, validator, $t) {

    return Element.extend(dependency).extend({
        defaults: {
            elementTmpl: 'Mageplaza_CustomForm/form/field/grid',
            template: 'Mageplaza_CustomForm/form/grid-field'
        },

        initialize: function () {
            var self = this;

            this._super();
            validator.addRule(
                'required-entry-' + this._id,
                function (value) {
                    var count = 0;

                    _.each(self.rows, function (obj) {
                        if (_.find(value, function (val) {
                            return val.split('-')[0] === obj.value;
                        })) {
                            count++;
                        }
                    });
                    return count === self.rows.length;

                },
                $t('This field requires at least one per row')
            );

            return this;
        },

        initObservable: function () {
            var self = this;
            var defaultValue;

            this._super();
            this.addFieldToProvider();
            this.dependencyObs();
            if (this.selectType === 'radio') {
                self.rowValueObs = [];
                _.each(this.rows, function (row) {
                    self['value-' + row.value] = ko.observable();
                    self.rowValueObs.push(self['value-' + row.value]);
                    self['value-' + row.value].subscribe(function () {
                        var rowValues = [];

                        _.each(self.rowValueObs, function (obj) {
                            if (obj()) {
                                rowValues.push(obj());
                            }
                        });
                        self.value(rowValues);
                    });
                });

                _.each(this.default, function (defaultVal) {
                    var rowId = defaultVal.split('-')[0];

                    self['value-' + rowId](defaultVal);
                });
            } else {
                defaultValue = $.extend([], this.default);
                this.value   = ko.observableArray([]);
                this.value(this.normalizeData(defaultValue));
            }

            return this;
        },

        normalizeData: function (value) {
            if (utils.isEmpty(value)) {
                value = [];
            }

            return _.isString(value) ? value.split(',') : value;
        },

        hasChanged: function () {
            var value   = this.value(),
                initial = this.initialValue;

            return !utils.equalArrays(value, initial);
        },
        resetField: function () {
            var self = this;

            if (this.selectType === 'radio') {
                _.each(this.rowValueObs, function (rowValue) {
                    rowValue('');
                });
                if (this.default) {
                    _.each(this.default, function (defaultVal) {
                        var rowId = defaultVal.split('-')[0];

                        self['value-' + rowId](defaultVal);
                    });
                }
            } else if (this.default) {
                this.value(this.normalizeData(this.default));
            } else {
                this.value([]);
            }
        }
    });
});
