define([
    'uiRegistry'
], function (registry) {
    "use strict";

    return {

        /**
         * Get configure item url
         *
         * @return {String}
         */
        getConfigureItemUrl: function () {
            var url = '';

            if (this._getConfig()) {
                url = this._getConfig().configureItemUrl;
            }

            return url;
        },

        /**
         * Get update item option url
         *
         * @return {String}
         */
        getUpdateItemOptionUrl: function () {
            var url = '';

            if (this._getConfig()) {
                url = this._getConfig().updateItemOptionUrl;
            }

            return url;
        },

        /**
         * Get update item qty url
         *
         * @return {String}
         */
        getUpdateItemQtyUrl: function () {
            var url = '';

            if (this._getConfig()) {
                url = this._getConfig().updateItemQtyUrl;
            }

            return url;
        },

        /**
         * Get remove item url
         *
         * @return {String}
         */
        getRemoveItemUrl: function () {
            var url = '';

            if (this._getConfig()) {
                url = this._getConfig().removeItemUrl;
            }

            return url;
        },

        /**
         * Get config
         *
         * @return {*}
         * @private
         */
        _getConfig: function () {
            return registry.get('aw_quick_order_config');
        }
    }
});
