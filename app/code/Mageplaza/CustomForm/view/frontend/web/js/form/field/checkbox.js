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
    './dependency'
], function ($, ko, _, utils, Element, dependency) {

    function indexOptions (data, result) {
        var value;

        result = result || {};
        data.forEach(function (item) {
            value = item.value;
            if (_.isArray(value)) {
                indexOptions(value, result);
            } else {
                result[value] = item;
            }
        });

        return result;
    }

    return Element.extend(dependency).extend({
        defaults: {
            elementTmpl: 'Mageplaza_CustomForm/form/field/checkbox'
        },
        initObservable: function () {
            var defaultValue = $.extend([], this.default);

            this._super();
            this.addFieldToProvider();
            this.dependencyObs();
            this.value       = ko.observableArray([]);
            this.value(this.normalizeData(defaultValue));
            this.indexedOptions = indexOptions(this.options);

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
        getOptionsByRow: function () {
            var countPerRow = +this.countPerRow || this.options.length;
            var options     = _.groupBy(this.options, function (element, index) {
                return Math.floor(index / countPerRow);
            });

            options         = _.toArray(options);
            return options;
        },
        resetField: function () {
            if (this.default) {
                this.value(this.normalizeData(this.default));
            } else {
                this.value([]);
            }
        }
    });
});
