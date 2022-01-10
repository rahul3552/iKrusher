/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
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
    'underscore',
    'Magento_Ui/js/grid/provider',
    'uiRegistry',
    'mage/translate',
    'chartBundle'
], function ($, _, Provider, Registry, $t) {
    'use strict';

    return Provider.extend({
        chartElement: 'mp-custom-form-chart',
        dateFormat: '',

        /**
         * @param data
         * @returns {*}
         */
        processData: function (data) {
            this.buildChart(data);

            return this._super(data);
        },

        /**
         * Build chart when Mp Reports enable
         */
        buildChart: function (data) {
            var items  = data.items,
                labels = [],
                nor    = [],
                views  = [],
                ctr    = [];

            _.each(items, function (record) {
                labels.push(record.name);
                nor.push(record.number_of_responses);
                views.push(record.views);
                ctr.push(record.ctr.replace('%', ''));
            });

            this.createChart(labels, nor, views, ctr);
            $('#' + this.chartElement).show();
        },

        createChart: function (labels, nor, views, ctr) {
            var config = {
                animationEnabled: true,
                type: 'bar',
                data: {
                    datasets: [
                        {
                            type: 'bar',
                            label: 'Number of Responses',
                            data: nor,
                            fill: true,
                            yAxisID: 'y-axis-1',
                            backgroundColor: '#4285F4',
                            borderWidth: 1
                        },
                        {
                            type: 'bar',
                            label: 'Views',
                            data: views,
                            fill: false,
                            yAxisID: 'y-axis-1',
                            backgroundColor: '#DB4437',
                            borderWidth: 1
                        },
                        {
                            type: 'bar',
                            label: 'CTR',
                            data: ctr,
                            fill: false,
                            yAxisID: 'y-axis-1',
                            backgroundColor: '#F1C232',
                            borderWidth: 1
                        }
                    ],
                    labels: labels
                },
                options: {
                    legend: {
                        display: true,
                        position: 'right'
                    },
                    tooltips: {
                        mode: 'index',
                        callbacks: {}
                    },
                    scales: {
                        yAxes: [
                            {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                ticks: {
                                    beginAtZero: true
                                },
                                id: 'y-axis-1'
                            }
                        ]
                    }
                }
            };

            if (typeof window[this.chartElement] !== 'undefined' &&
                typeof window[this.chartElement].destroy === 'function'
            ) {
                window[this.chartElement].destroy();
            }

            /* global Chart */
            window[this.chartElement] = new Chart($('#' + this.chartElement), config);
        }
    });
});
