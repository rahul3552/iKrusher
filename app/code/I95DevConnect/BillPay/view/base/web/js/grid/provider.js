/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 */

define([
    'jquery',
    'underscore',
    'mageUtils',
    'uiElement',
    'Magento_Ui/js/modal/alert',
    'mage/translate'
], function ($, _, utils, Element, alert, $t) {
    'use strict';
    alert("jksdhfjksf");
    return Element.extend({
        defaults: {
            listens: {
                params: 'reload'
            }
        },

        /**
         * Initializes provider component.
         *
         * @returns {Provider} Chainable.
         */
        initialize: function () {
            utils.limit(this, 'reload', 300);
            _.bindAll(this, 'onReload');

            return this._super();
        },

        /**
         * Initializes provider config.
         *
         * @returns {Provider} Chainable.
         */
        initConfig: function () {
            this._super();

            this.setData({
                items: [],
                totalRecords: 0
            });

            return this;
        },

        /**
         *
         * @param {Object} data
         * @returns {Provider} Chainable.
         */
        setData: function (data) {
            data = this.processData(data);

            this.set('data', data);

            return this;
        },

        /**
         * Reloads data with current parameters.
         */
        reload: function () {
            console.log("invoice_amount");
            this.trigger('reload');

            if (this.request && this.request.readyState !== 4) {
                this.request.abort();
            }

            this.request = $.ajax({
                url: this['update_url'],
                method: 'GET',
                data: this.get('params'),
                dataType: 'json'
            });

            this.request
                .done(this.onReload)
                .error(this.onError);
        },

        /* START MODIFIED CODE */
        processData: function (data) {
            var items = data.items;
            var invoice_amount = 0;
            _.each(items, function (record, index) {
                record._rowIndex = index;
                invoice_amount += parseFloat(record.invoice_amount);//price is column of custom table
            });
            console.log(invoice_amount);
            jQuery("#invoice_amount").text(invoice_amount);
            return data;
        },
        /* END MODIFIED CODE */
        /**
         * Handles reload error.
         */
        onError: function (xhr) {
            if (xhr.statusText === 'abort') {
                return;
            }

            alert({
                content: $t('Something went wrong.')
            });
        },

        /**
         * Handles successful data reload.
         *
         * @param {Object} data - Retrieved data object.
         */
        onReload: function (data) {
            this.setData(data)
                .trigger('reloaded');
        }
    });
});