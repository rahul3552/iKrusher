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
    './dependency'
], function ($, Element, dependency) {

    return Element.extend(dependency).extend({
        defaults: {
            elementTmpl: 'Mageplaza_CustomForm/form/field/rating'
        },
        initObservable: function () {
            this._super();
            this.addFieldToProvider();
            this.dependencyObs();

            if (this.default) {
                this.value(this.default);
            }

            return this;
        },
        mouseOverStar: function (event) {
            var onStar = parseInt($(event.currentTarget).data('value'), 10); // The star currently mouse on

            // Now highlight all the stars that's not after the current hovered star
            $(event.currentTarget).parent().children('li.star').each(function (e) {
                if (e < onStar) {
                    $(this).addClass('hover');
                } else {
                    $(this).removeClass('hover');
                }
            });
        },
        mouseOutStar: function (event) {
            $(event.currentTarget).children('li.star').removeClass('hover');
        },
        clickStar: function (event) {
            var inputEl = $(event.currentTarget).find('input');

            this.value(inputEl.val());
            $(event.currentTarget).siblings().removeClass('selected');
            $(event.currentTarget).addClass('selected').prevAll().addClass('selected');
        },
        getStarsObj: function () {
            var stars = [];
            var i;

            for (i = 1; i <= +this.numberStar; i++){
                stars.push({num:i,checked: this.value()});
            }
            return stars;
        },
        selectedStars: function (num) {
            var result = 'star';

            if (this.value() >= num) {
                result += ' selected';
            }
            return result;
        },
        checked: function (elem,star) {
            if(star.num === +star.checked){
                $(elem).find('input').prop('checked',true);
            }
        }
    });
});
