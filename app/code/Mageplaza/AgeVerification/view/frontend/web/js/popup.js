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
 * @package     Mageplaza_AgeVerification
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

define([
    'jquery',
    'jquery/jquery.cookie',
    'Mageplaza_Core/js/jquery.magnific-popup.min'
], function ($) {
    'use strict';

    $.widget('mageplaza.mpageverifyPopup', {
        /**
         * @inheritDoc
         */
        _create: function () {
            if (!this.checkVerified()) {
                this.purchaseVerify();
                this.pageVerify();
            } else {
                $('#mpageverify-popup').remove();
                this.removeMessage();
            }
        },

        pageVerify: function () {
            if (this.options.isVerifyPage) {
                this.showPopup();
                this.confirm();
            } else {
                $('#mpageverify-popup').remove();
            }
        },

        /**
         * Confirm popup
         * @param params
         * @param actionUrl
         */
        confirm: function (params = null, actionUrl = null) {
            var self            = this,
                mpageverifyForm = $('#mpageverify-form');

            $('input.mpageverify-cancel').on('click', function (e) {
                e.preventDefault();
                window.location = self.options.redirectUrl;
            });

            mpageverifyForm.submit(function (e) {
                e.preventDefault();
                var check = true;

                if (mpageverifyForm.find('.g-recaptcha').length > 0) {
                    var name = mpageverifyForm.find('.g-recaptcha').attr('id');

                    check = $("input[name='" + name + "']").prop('checked');
                    mpageverifyForm.find('.g-recaptcha-error').attr('style', 'margin-right:151px;color: red!important;');

                    if (check === false) {
                        var error = $('#mpageverify-form').find('.g-recaptcha-error').show();
                        setTimeout(function () {
                            error.hide();
                        }, 5000);
                    }
                }

                if (check === true) {
                    if (self.checkDob()) {
                        if (params) {
                            $.ajax({
                                url: actionUrl,
                                data: params,
                                type: 'post',
                                dataType: 'json',
                                showLoader: true,
                                success: function (res) {
                                    self.actionVerify();
                                    if (res.backUrl) {
                                        window.location = res.backUrl;
                                    }
                                }
                            });
                        } else {
                            self.actionVerify();
                        }
                    } else {
                        window.location = self.options.redirectUrl;
                    }
                }
            });
        },

        /**
         * Action when success verify
         */
        actionVerify: function () {
            var magnificPopup = $.magnificPopup.instance;

            this.setCookie();
            this.removeMessage();
            magnificPopup.close();
        },

        /**
         * show popup when click add to cart
         */
        purchaseVerify: function () {
            var self             = this,
                configProduct    = [];
            var productId,
                listProductIds   = this.options.productIds,
                isVerifyPurchase = this.options.isVerifyPurchase,
                isEnablePurchase = this.options.isEnablePurchase,
                formKey          = $('input[name="form_key"]').val();

            $('body').delegate('button.action.tocart', 'click', function (e) {
                var el        = $(this),
                    actionUrl = '',
                    params    = '',
                    form      = '',
                    dataPost  = el.data('post');

                if (dataPost) {
                    productId       = dataPost.data.product;
                    actionUrl       = dataPost.action;
                    params          = dataPost.data;
                    params.form_key = formKey;
                } else {
                    form      = el.closest('form');
                    actionUrl = form.attr('action');
                    params    = form.serialize();
                    if (form.find('input[name="product"]').val() === undefined) {
                        productId = el.closest('td').find('.price-box')[0].dataset.productId;
                    } else {
                        productId = form.find('input[name="product"]').val();
                    }
                }

                if ($('.swatch-opt').length !== 0) {
                    configProduct = self.checkChildProduct();
                }

                if (isVerifyPurchase ||
                    (isEnablePurchase && $.inArray(productId.toString(), listProductIds) !== -1) ||
                    configProduct.length > 0 && $.inArray(configProduct[0], listProductIds) !== -1
                ) {

                    /** check validation on product detail **/
                    if (el.closest('.product-info-main').length) {
                        var dataForm = $('form#product_addtocart_form'),
                            validate = dataForm.validation('isValid');

                        if (!validate) {
                            return false;
                        }
                    }

                    self.showPopup();
                    e.stopPropagation();
                    e.preventDefault();
                    self.confirm(params, actionUrl);
                }
            });
        },

        /**
         * get Child Configuarge Product Id
         */

        checkChildProduct: function () {
            var selected_options = {};

            $('div.swatch-attribute').each(function (k, v) {
                var attribute_id    = $(v).attr('attribute-id');
                var option_selected = $(v).attr('option-selected');

                if (!attribute_id || !option_selected) {
                    return;
                }
                selected_options[attribute_id] = option_selected;
            });

            var product_id_index = $('[data-role=swatch-options]').data('mageSwatchRenderer').options.jsonConfig.index;
            var child_ids        = [];

            $.each(product_id_index, function (product_id, attributes) {
                var productIsSelected = function (attributes, selected_options) {
                    return _.isEqual(attributes, selected_options);
                }
                if (productIsSelected(attributes, selected_options)) {
                    child_ids.push(product_id);
                }
            });
            return child_ids;
        },

        /**
         * Show popup verify
         */
        showPopup: function () {
            var popup = $('#mpageverify-popup');

            $.magnificPopup.open({
                items: {
                    src: popup.html()
                },
                type: 'inline',
                showCloseBtn: false,
                closeOnBgClick: false,
                closeOnContentClick: false,
                enableEscapeKey: false,
                callbacks: {
                    open: function () {
                        popup.remove();
                        $('body').css('overflow', 'hidden');
                    },
                    close: function () {
                        $('body').css('overflow', 'unset');
                    }
                }
            }, 0);
        },

        /**
         * Remove message
         */
        removeMessage: function () {
            var message = $('.mpageverify-message');
            message.remove();
        },

        /**
         * check verified
         * @returns {boolean}
         */
        checkVerified: function () {
            /** @namespace this.options.autoVerify */
            return !!(this.options.autoVerify || $.cookie('mp_isVerify'));
        },

        setCookie: function () {
            /** @namespace this.options.cookieTime */
            var cookieTime = parseInt(this.options.cookieTime) * 24 * 60 * 60;

            $.cookie('mp_isVerify', 1, {expires: cookieTime});
        },

        /**
         * Check dob
         * @returns {boolean}
         */
        checkDob: function () {
            var type = $('#mpageverify-popup-content').attr('data-type');

            if (parseInt(type) === this.options.dobType) {
                var day         = parseInt($('#mpageverify-day').val()),
                    month       = parseInt($('#mpageverify-month').val()),
                    year        = parseInt($('#mpageverify-year').val()),
                    age         = parseInt(this.options.age),
                    setDate     = new Date(year + age, month - 1, day),
                    currentDate = new Date();

                return currentDate >= setDate;
            }

            return true;
        }
    });

    return $.mageplaza.mpageverifyPopup;
});
