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
    'Magento_Ui/js/form/element/abstract',
    './dependency',
    'Magento_Ui/js/modal/modal'
], function ($, Element, dependency, modal) {

    return Element.extend(dependency).extend({
        defaults: {
            elementTmpl: 'Mageplaza_CustomForm/form/field/agreement'
        },
        initObservable: function () {
            var self = this;

            this._super();
            this.addFieldToProvider();
            this.dependencyObs();
            this.value(!!this.default);
            $('body').on('click', '#agreement-modal-' + this.uid, function () {
                var options = {
                    type: 'popup',
                    responsive: true,
                    innerScroll: true,
                    title: self.agreementTitle,
                };

                modal(options, '<div>' + self.agreementContent + '</div>').openModal();
            });
            return this;
        },
        getAgreement: function () {
            var result, anchor;

            if (this.anchorType === 'popup') {
                anchor = '<a href="#" id="agreement-modal-' + this.uid + '">' + this.anchorText + '</a>';
            } else {
                anchor = '<a href="' + this.url + '" target="_blank">' + this.anchorText + '</a>';
            }
            result = this.checkboxLabel.replace('{anchor}', anchor);
            return result;
        }
    });
});