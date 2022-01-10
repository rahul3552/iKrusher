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
        'Magento_Ui/js/form/form',
        'ko'
    ], function (_, Component, ko) {

        return Component.extend({
            defaults: {
                template: 'Mageplaza_CustomForm/form/field-group'
            },
            visible: ko.observable(true),
            initialize: function () {
                this._super();
                return this;
            }
        });
    }
);
