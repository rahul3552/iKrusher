define([
    'uiElement',
    'jquery',
    'jquery-ui-modules/tabs'
], function (Element, $) {
    'use strict';

    return Element.extend({
        defaults: {
            imports: {
                toolbarId: '${ $.parentName }:toolbarId'
            }
        },

        /**
         * Tabs are getting initialized once this last child is loaded
         */
        onToolbarRender: function () {
            window.FORM_KEY = $.mage.cookies.get('form_key');
            $(function() {
                $("#" + this.toolbarId).tabs();
            }.bind(this));
        }
    });
});