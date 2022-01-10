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
    'underscore',
    'uiRegistry'
], function (_, registry) {
    return {
        dependencyObs: function () {
            var self = this;

            if (this.depends) {
                this.checkDepend = {};
                _.each(this.depends, function (depend, key) {
                    var field        = depend.field.split('-'),
                        fieldGroupId = field[0],
                        fieldId      = field[1];

                    registry.async(self.ns + '.steps.' + self.pageName + '.field-group-' + fieldGroupId + '.field-' + fieldId)(function (fieldElement) {
                        self.checkDependency(key, fieldElement, fieldElement.value(), depend.value);
                        fieldElement.value.subscribe(function (newValue) {
                            self.checkDependency(key, fieldElement, newValue, depend.value);
                        });
                        fieldElement.visible.subscribe(function (newValue) {
                            self.checkDependency(key, fieldElement, newValue, depend.value);
                        });
                    });
                });
            }
        },
        checkDependency: function (key, fieldElement, newValue, dependValue) {
            var fieldType = fieldElement.fieldType,
                isFieldVisible,
                value;

            if (newValue === false) {
                this.checkDepend[key] = false;
            } else {
                if(newValue === true){
                    newValue = fieldElement.value();
                }
                value                 = fieldType === 'grid' || fieldType === 'dropdown'
                    ? dependValue : fieldElement.optionsData[dependValue].value;
                this.checkDepend[key] = newValue === value;
            }

            isFieldVisible = this.isFieldVisible(fieldType);
            this.visible(isFieldVisible);
            this.disabled(!isFieldVisible);
        },
        isFieldVisible: function (fieldType) {
            if (fieldType === 'radio' || fieldType === 'dropdown') {
                return !!_.filter(this.checkDepend, function (e) {
                    return e === true;
                }).length;
            }

            return !_.filter(this.checkDepend, function (e) {
                return e === false;
            }).length;

        },
        addFieldToProvider: function () {
            var self = this;

            registry.async(this.provider)(function (pageProvider) {
                if (!pageProvider.fields) {
                    pageProvider.fields = [];
                }
                pageProvider.fields.push(self);
            });
        },
        resetField: function () {
            if (this.default) {
                this.value(this.default);
            } else {
                this.value('');
            }
        }
    };
});
