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
        "underscore",
        'ko',
        'uiComponent',
    ], function ($, _, ko, Component) {

        return Component.extend({
            defaults: {
                template: 'Mageplaza_CustomForm/progress-bar',
                visible: true,
                stepCodes: [],
                validCodes: []
            },
            initialize: function () {
                this._super();
                window.addEventListener('hashchange',_.bind(this.handleHash, this));
            },
            initObservable: function () {
                var href         = window.location.href;

                this.originalUrl = href.substring(0, href.indexOf('#'));
                this._super();
                this.steps = ko.observableArray();
                return this;
            },

            /**
             * @return {Boolean}
             */
            handleHash: function () {
                var hashString = window.location.hash.replace('#', ''),
                    isRequestedStepVisible,
                    self       = this,
                    formName,
                    sortedItems;

                if (hashString === '') {
                    return false;
                }

                formName = hashString.split('-')[0];
                if (this.formName !== formName) {
                    return false;
                }
                if ($.inArray(hashString, this.validCodes) === -1) {
                    window.location.href = self.originalUrl + '/noroute';

                    return false;
                }

                isRequestedStepVisible = this.steps.sort(this.sortItems).some(function (element) {
                    return (element.code == hashString || element.alias == hashString) && element.isVisible(); //eslint-disable-line
                });

                //if requested step is visible, then we don't need to load step data from server
                if (isRequestedStepVisible) {
                    return false;
                }
                sortedItems = this.steps.sort(this.sortItems);
                if(!_.isArray(sortedItems)){
                    sortedItems = sortedItems();
                }

                sortedItems.forEach(function (element) {
                    if (element.code == hashString || element.alias == hashString) { //eslint-disable-line eqeqeq
                        element.navigate(element);
                    } else {
                        element.isVisible(false);
                    }

                });

                return false;
            },

            /**
             * @param {String} code
             * @param {*} alias
             * @param {*} title
             * @param {Function} isVisible
             * @param {*} navigate
             * @param {*} sortOrder
             */
            registerStep: function (code, alias, title, isVisible, navigate, sortOrder) {
                var hash, active, formName;

                if ($.inArray(code, this.validCodes) !== -1) {
                    throw new DOMException('Step code [' + code + '] already registered in step navigator');
                }

                if (alias != null) {
                    if ($.inArray(alias, this.validCodes) !== -1) {
                        throw new DOMException('Step code [' + alias + '] already registered in step navigator');
                    }
                    this.validCodes.push(alias);
                }
                this.validCodes.push(code);
                this.steps.push({
                    code: code,
                    alias: alias != null ? alias : code,
                    title: title,
                    isVisible: isVisible,
                    navigate: navigate,
                    sortOrder: sortOrder
                });
                active = this.getActiveItemIndex();
                this.steps.each(function (elem, index) {
                    if (active !== index) {
                        elem.isVisible(false);
                    }
                });
                this.stepCodes.push(code);
                hash         = window.location.hash.replace('#', '');
                formName = hash.split('-')[0];
                if (hash != '' && hash != code && formName === this.formName) { //eslint-disable-line eqeqeq
                    //Force hiding of not active step
                    isVisible(false);
                }
            },

            /**
             * @param {Object} itemOne
             * @param {Object} itemTwo
             * @return {Number}
             */
            sortItems: function (itemOne, itemTwo) {
                return itemOne.sortOrder > itemTwo.sortOrder ? 1 : -1;
            },

            /**
             * @return {Number}
             */
            getActiveItemIndex: function () {
                var activeIndex = 0;

                this.steps.sort(this.sortItems).some(function (element, index) {
                    if (element.isVisible()) {
                        activeIndex = index;

                        return true;
                    }

                    return false;
                });

                return activeIndex;
            },

            /**
             * @return {Boolean}
             * @param code
             */
            isProcessed: function (code) {
                var activeItemIndex    = this.getActiveItemIndex(),
                    sortedItems        = this.steps.sort(this.sortItems),
                    requestedItemIndex = -1;

                if(!_.isArray(sortedItems)){
                    sortedItems = sortedItems();
                }
                sortedItems.forEach(function (element, index) {
                    if (element.code == code) { //eslint-disable-line eqeqeq
                        requestedItemIndex = index;
                    }
                });

                return activeItemIndex > requestedItemIndex;
            },

            /**
             * @param code
             * @param {*} scrollToElementId
             */
            navigateTo: function (code, scrollToElementId) {
                var sortedItems = this.steps.sort(this.sortItems),
                    bodyElem    = $.browser.safari || $.browser.chrome ? $('body') : $('html');

                scrollToElementId = scrollToElementId || null;

                if (!this.isProcessed(code)) {
                    return;
                }
                if(!_.isArray(sortedItems)){
                    sortedItems = sortedItems();
                }
                sortedItems.forEach(function (element) {
                    if (element.code == code) { //eslint-disable-line eqeqeq
                        element.isVisible(true);
                        bodyElem.animate({
                            scrollTop: $('#' + code).offset().top
                        }, 0, function () {
                            var href        = window.location.href;

                            window.location = href.substring(0, href.indexOf('#')) + '#' + code;
                        });

                        if (scrollToElementId && $('#' + scrollToElementId).length) {
                            bodyElem.animate({
                                scrollTop: $('#' + scrollToElementId).offset().top
                            }, 0);
                        }
                    } else {
                        element.isVisible(false);
                    }

                });
            },

            /**
             * Sets window location hash.
             *
             * @param {String} hash
             */
            setHash: function (hash) {
                window.location.hash = hash;
            },

            /**
             * Next step.
             */
            next: function () {
                var activeIndex = 0,
                    sortedItems = this.steps.sort(this.sortItems),
                    code;

                if(!_.isArray(sortedItems)){
                    sortedItems = sortedItems();
                }
                sortedItems.forEach(function (element, index) {
                    if (element.isVisible()) {
                        element.isVisible(false);
                        activeIndex = index;
                    }
                });

                if (this.steps().length > activeIndex + 1) {
                    code = this.steps()[activeIndex + 1].code;
                    this.steps()[activeIndex + 1].isVisible(true);
                    this.setHash(code);
                    document.body.scrollTop = document.documentElement.scrollTop;
                }
            }
        });
    }
);
