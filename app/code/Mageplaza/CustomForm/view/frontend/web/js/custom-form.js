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
        'uiComponent',
        'Magento_Ui/js/modal/modal'
    ], function ($, ko, Component, modal) {

        return Component.extend({
            initObservable: function () {
                var formEl = $('#mp-custom-form-' + this.formId);

                this._super();
                this.visible = ko.observable(false);
                this.initCalendar();
                this.checkCustomerGroup();
                this.openModalFormObs();
                formEl.on('submit', function (e) {
                    if (!e.isTrigger) {
                        return false;
                    }
                });

                return this;
            },
            openModalFormObs: function () {
                var self   = this,
                    formEl = $('#mp-custom-form-' + this.formId);

                formEl.on('click', '#mp-cf-open-form-' + this.formId, function () {
                    var modalEl = $(this).parent().find('.mp-custom-form-popup'),
                        options = {
                            'type': self.popupType,
                            'responsive': true,
                            'appendTo': $(this).parent(),
                            'innerScroll': true,
                            'buttons': []
                        };

                    if (modalEl.data('mageModal')) {
                        modalEl.data('mageModal').openModal();
                    } else {
                        modal(options, modalEl).openModal();
                    }
                });
            },
            initCalendar: function () {
                $.widget('mage.calendar', $.extend({
                    _picker: function () {
                        if (this.options.mpDateTimeType) {
                            switch (this.options.mpDateTimeType){
                                case 'time':
                                    return 'timepicker';
                                case 'datetime-local':
                                    return 'datetimepicker';
                                default:
                                    return 'datepicker';
                            }
                        } else {
                            return this.options.showsTime ? 'datetimepicker' : 'datepicker';
                        }
                    }
                }));
            },
            checkCustomerGroup: function () {
                var self = this;

                if (this.isPreview) {
                    this.visible(true);
                    $('#mp-cf-open-form-' + this.formId).show();
                    return;
                }
                $.ajax({
                    url: this.checkCustomerGroupUrl,
                    method: 'post',
                    showLoader: true,
                    success: function (res) {
                        if (self.customerGroupIds.indexOf(res.customerGroupId) !== -1) {
                            self.visible(true);
                            $('#mp-cf-open-form-' + self.formId).show();
                        }
                    }
                });
            }
        });
    }
);
