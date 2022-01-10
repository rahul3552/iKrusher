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
        'underscore',
        'Magento_Ui/js/form/form',
        'ko',
        'uiRegistry',
    ], function ($, _, Component, ko, registry) {

        return Component.extend({
            defaults: {
                template: 'Mageplaza_CustomForm/form/page'

            },

            /**
             * @return {exports}
             */
            initialize: function () {
                var self = this;

                if (!this.smButtonText) {
                    this.smButtonText = 'Next';
                }

                this._super();
                registry.async(this.ns + '.progressBar')(function (stepNavigator) {
                    stepNavigator.registerStep(
                        self.formName + '-' + self._id,
                        null,
                        self.title,
                        self.visible,
                        _.bind(self.navigate, self),
                        self.sortOrder
                    );
                    if (registry.get(self.parentName).getRegion('pages')().length === stepNavigator.steps().length) {
                        stepNavigator.handleHash();
                    }
                });

                $.ajax({
                    url: self.viewsUrl,
                    data: {identifier: self.identifier},
                    method: 'POST',
                    success: function (res) {
                        console.log(res.message);
                    }
                });

                return this;
            },

            initObservable: function () {
                this._super();
                this.visible = ko.observable(true);

                return this;
            },

            /**
             * Tries to set focus on first invalid form field.
             *
             * @returns {Object}
             * Compatible with 2.1.x
             */
            focusInvalid: function () {
                var invalidField = _.find(this.delegate('checkInvalid'));

                if (!_.isUndefined(invalidField) && _.isFunction(invalidField.focused)) {
                    invalidField.focused(true);
                }

                return this;
            },

            /**
             * Navigator change hash handler.
             *
             * @param {Object} step - navigation step
             */
            navigate: function (step) {
                step && step.isVisible(true);
            },
            submitForm: function () {
                var self = this;

                registry.async(this.ns + '.progressBar')(function (stepNavigator) {
                    var parentElem = self.containers[0];
                    var invalid    = false;

                    if (stepNavigator.getActiveItemIndex() === stepNavigator.steps().length - 1) {

                        _.each(parentElem.getRegion('pages')(), function (page) {
                            if (invalid) {
                                return;
                            }
                            page.validate();
                            if (page.source.get('params.invalid')) {
                                stepNavigator.navigateTo(page.formName + '-' + page._id);
                                page.focusInvalid();
                                invalid = true;
                                return false;
                            }
                        });
                        if (invalid) {
                            return;
                        }

                        if (self.actionAfterSubmit === 'current') {
                            $.ajax({
                                url: self.submitUrl,
                                data: $('#mp-custom-form-' + parentElem.formId).serialize(),
                                showLoader: true,
                                method: 'POST',
                                success: function (res) {
                                    if (res.success) {
                                        //reset form
                                        _.each(parentElem.getRegion('pageProvider')(), function (provider) {
                                            _.each(provider.fields, function (fieldElement) {
                                                // ignore validate after reset field
                                                var validation = _.clone(fieldElement.validation);

                                                fieldElement.validation = false;
                                                fieldElement.resetField();
                                                fieldElement.validation = validation;
                                            });
                                        });

                                        //navigate to first step
                                        stepNavigator.navigateTo(stepNavigator.stepCodes[0]);

                                        //hide popup
                                        if(self.formStyle === 'popup'){
                                            $('#mp-custom-form-' + self.formId)
                                            .find('.mp-custom-form-popup').data('mageModal').closeModal();
                                        }
                                    }

                                    $("html, body").animate({ scrollTop: 0 }, "slow");
                                }
                            });
                        } else {
                            $('#mp-custom-form-' + parentElem.formId).submit();
                        }

                    } else {
                        self.validate();
                        if (self.source.get('params.invalid')) {
                            self.focusInvalid();
                            return;
                        }
                        stepNavigator.next();
                    }
                });
            }
        });
    }
);
