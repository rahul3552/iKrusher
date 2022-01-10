/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'mage/translate',
    'jquery-ui-modules/accordion',
    'jquery-ui-modules/autocomplete',
    'jquery-ui-modules/button',
    'jquery-ui-modules/datepicker',
    'jquery-ui-modules/dialog',
    'jquery-ui-modules/draggable',
    'jquery-ui-modules/droppable',
    'jquery-ui-modules/effect-blind',
    'jquery-ui-modules/effect-bounce',
    'jquery-ui-modules/effect-clip',
    'jquery-ui-modules/effect-drop',
    'jquery-ui-modules/effect-explode',
    'jquery-ui-modules/effect-fade',
    'jquery-ui-modules/effect-fold',
    'jquery-ui-modules/effect-highlight',
    'jquery-ui-modules/effect-scale',
    'jquery-ui-modules/effect-pulsate',
    'jquery-ui-modules/effect-shake',
    'jquery-ui-modules/effect-slide',
    'jquery-ui-modules/effect-transfer',
    'jquery-ui-modules/effect',
    'jquery-ui-modules/menu',
    'jquery-ui-modules/mouse',
    'jquery-ui-modules/position',
    'jquery-ui-modules/progressbar',
    'jquery-ui-modules/resizable',
    'jquery-ui-modules/selectable',
    'jquery-ui-modules/slider',
    'jquery-ui-modules/sortable',
    'jquery-ui-modules/spinner',
    'jquery-ui-modules/tabs',
    'jquery-ui-modules/timepicker',
    'jquery-ui-modules/tooltip',
    'jquery-ui-modules/widget'
], function($, $t) {
    "use strict";

    $.widget('mage.catalogAddToCart', {

        options: {
            processStart: null,
            processStop: null,
            bindSubmit: true,
            minicartSelector: '[data-block="minicart"]',
            messagesSelector: '[data-placeholder="messages"]',
            productStatusSelector: '.stock.available',
            addToCartButtonSelector: '.action.tocart',
            addToCartButtonDisabledClass: 'disabled',
            addToCartButtonTextWhileAdding: '',
            addToCartButtonTextAdded: '',
            addToCartButtonTextDefault: ''
        },

        _create: function() {
            if (this.options.bindSubmit) {
                this._bindSubmit();
            }
        },

        _bindSubmit: function() {
            var self = this;
            this.element.on('submit', function(e) {
                e.preventDefault();
                self.submitForm($(this));
            });
        },

        isLoaderEnabled: function() {
            return this.options.processStart && this.options.processStop;
        },

        /**
         * Handler for the form 'submit' event
         *
         * @param {Object} form
         */
        submitForm: function (form) {
            var addToCartButton, self = this;

            if (form.has('input[type="file"]').length && form.find('input[type="file"]').val() !== '') {
                self.element.off('submit');
                // disable 'Add to Cart' button
                addToCartButton = $(form).find(this.options.addToCartButtonSelector);
                addToCartButton.prop('disabled', true);
                addToCartButton.addClass(this.options.addToCartButtonDisabledClass);
                form.submit();
            } else {
                self.ajaxSubmit(form);
            }
        },

        ajaxSubmit: function(form) {
            var self = this;
            $(self.options.minicartSelector).trigger('contentLoading');
            self.disableAddToCartButton(form);

            $.ajax({
                url: form.attr('action'),
                data: form.serialize(),
                type: 'post',
                dataType: 'json',
                beforeSend: function() {
                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStart);
                    }
                },
                success: function(res) {
                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStop);
                    }

                    if (res.backUrl) {
                        window.location = res.backUrl;
                        return;
                    }
                    if (res.messages) {
                        $(self.options.messagesSelector).html(res.messages);
                    }
                    if (res.minicart) {
                        $(self.options.minicartSelector).replaceWith(res.minicart);
                        $(self.options.minicartSelector).trigger('contentUpdated');
                    }
                    if (res.product && res.product.statusText) {
                        $(self.options.productStatusSelector)
                            .removeClass('available')
                            .addClass('unavailable')
                            .find('span')
                            .html(res.product.statusText);
                    }
                    self.enableAddToCartButton(form);
                }
            });
        },

        disableAddToCartButton: function(form) {
            var addToCartButtonTextWhileAdding = this.options.addToCartButtonTextWhileAdding || $t('Adding...');
            var addToCartButton = $(form).find(this.options.addToCartButtonSelector);
            addToCartButton.addClass(this.options.addToCartButtonDisabledClass);
			addToCartButton.find('.fa').addClass('fa-spin');
            addToCartButton.find('span:not(.fa)').text(addToCartButtonTextWhileAdding);
            addToCartButton.attr('title', addToCartButtonTextWhileAdding);
        },

        enableAddToCartButton: function(form) {
            var addToCartButtonTextAdded = this.options.addToCartButtonTextAdded || $t('Added');
            var self = this,
                addToCartButton = $(form).find(this.options.addToCartButtonSelector);

            addToCartButton.find('span:not(.fa)').text(addToCartButtonTextAdded);
			addToCartButton.find('.fa').removeClass('fa-spin');
			addToCartButton.find('.fa').addClass('fa-added');
            addToCartButton.attr('title', addToCartButtonTextAdded);

            setTimeout(function() {
                var addToCartButtonTextDefault = self.options.addToCartButtonTextDefault || $t('Add to cart');
                addToCartButton.removeClass(self.options.addToCartButtonDisabledClass);
                addToCartButton.find('span:not(.fa)').text(addToCartButtonTextDefault);
				addToCartButton.find('.fa').removeClass('fa-added');
                addToCartButton.attr('title', addToCartButtonTextDefault);
            }, 1000);
        }
    });

    return $.mage.catalogAddToCart;
});
