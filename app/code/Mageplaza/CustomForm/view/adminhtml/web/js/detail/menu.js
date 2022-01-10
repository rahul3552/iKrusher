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

define([
    'jquery',
    'moment',
    'uiRegistry',
    'daterangepicker'
], function ($, moment, uiRegistry) {
    'use strict';
    var dateRangeEl = $('#daterange');

    $.widget('mageplaza.customformmenu', {
        _create: function () {
            var self = this;

            this.initNowDateRange(moment(this.options.date[0]), moment(this.options.date[1]));
            this.initDateRangeApply();

            $('body').on('click', function (e) {
                var dateRangElement = $('#daterange'),
                    grid, params;

                if ($('.daterangepicker').is(':visible')) {
                    $('.drp-calendar.left').show();
                    $('.drp-calendar.right').show();
                }

                if ($(e.target).parents().hasClass('daterangepicker')) {
                    if (!$('.daterangepicker').is(':visible')) {
                        grid   = uiRegistry.get(self.options.gridName);
                        params = grid.get('params');
                        if (typeof params.mpFilter === 'undefined') {
                            params.mpFilter = {};
                        }

                        params.mpFilter.startDate = dateRangElement.data().startDate.format('');
                        params.mpFilter.endDate   = dateRangElement.data().endDate.format('');
                        params.dateRange          = [params.mpFilter.startDate, params.mpFilter.endDate, null, null];
                        grid.reload();
                        $('.drp-calendar.left').show();
                        $('.drp-calendar.right').show();
                    }
                }
            });
        },
        initDateRange: function (el, start, end, data) {
            function cb (cbStart, cbEnd) {
                el.find('span').html(cbStart.format('MMM DD, YYYY') + ' - ' + cbEnd.format('MMM DD, YYYY'));
            }

            el.daterangepicker(data, cb);
            cb(start, end);
        },
        initNowDateRange: function (start, end) {
            var dateRangeData = {
                startDate: start,
                endDate: end,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [
                        moment().subtract(1, 'month').startOf('month'),
                        moment().subtract(1, 'month').endOf('month')
                    ],
                    'YTD': [moment().subtract(1, 'year'), moment()],
                    '2YTD': [moment().subtract(2, 'year'), moment()]
                }
            };

            this.initDateRange(dateRangeEl, start, end, dateRangeData);
        },
        initDateRangeApply: function () {
            var self = this;

            dateRangeEl.on('apply.daterangepicker', function (ev, picker) {
                var grid, params;

                self.initNowDateRange(picker.startDate, picker.endDate);
                self.initDateRangeApply();
                grid   = uiRegistry.get(self.options.gridName);
                params = grid.get('params');
                if (typeof params.mpFilter === 'undefined') {
                    params.mpFilter = {};
                }

                params.mpFilter.startDate = picker.startDate.format('Y-MM-DD');
                params.mpFilter.endDate   = picker.endDate.format('Y-MM-DD');
                params.dateRange          = [params.mpFilter.startDate, params.mpFilter.endDate, null, null];
                grid.reload();

            });
        }
    });

    return $.mageplaza.customformmenu;
});
