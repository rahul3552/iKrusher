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
 * @category  Mageplaza
 * @package   Mageplaza_CustomForm
 * @copyright Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license   https://www.mageplaza.com/LICENSE.txt
 */

'use strict';
define(
    [
        'jquery',
        'underscore',
        'mage/translate',
        'chartBundle'
    ], function ($, _, $t) {

        $.widget(
            'mageplaza.responsesSummary', {
                _create: function () {
                    var self = this;

                    require([
                        'https://maps.googleapis.com/maps/api/js?key='
                        + self.options.mpGoogleMapKey + '&libraries=places'
                    ], function () {
                        self.renderField();
                    });
                },
                renderField: function () {
                    switch (this.options.type){
                        case 'radio':
                        case 'dropdown':
                            this.renderPieChart();
                            break;
                        case 'checkbox':
                            this.renderHorizontalBarChart();
                            break;
                        case 'grid':
                            this.renderVerticalBarChart();
                            break;
                        case 'map':
                            this.renderMap();
                            break;
                    }
                },
                renderPieChart: function () {
                    var chartEl = $(this.element);
                    var self    = this;

                    new Chart(chartEl, {
                        type: 'pie',
                        data: {
                            labels: self.options.chartData.labels,
                            datasets: [
                                {
                                    data: self.options.chartData.data,
                                    fill: true,
                                    backgroundColor: self.options.chartData.backgroundColors,
                                    borderWidth: 1,
                                    total: self.options.chartData.total
                                }
                            ]
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
                            events: false,
                            animation: {
                                duration: 500,
                                easing: "easeOutQuart",
                                onComplete: function () {
                                    var ctx          = this.chart.ctx;
                                    var i;
                                    var model;
                                    var total;
                                    var midRadius;
                                    var startAngle;
                                    var endAngle;
                                    var midAngle;
                                    var x;
                                    var y;
                                    var val;
                                    var percent;

                                    ctx.font         = Chart.helpers.fontString(
                                        Chart.defaults.global.defaultFontFamily,
                                        'normal',
                                        Chart.defaults.global.defaultFontFamily
                                    );
                                    ctx.textAlign    = 'center';
                                    ctx.textBaseline = 'bottom';

                                    this.data.datasets.forEach(function (dataset) {
                                        for (i = 0; i < dataset.data.length; i++){
                                            model     = dataset._meta[Object.keys(dataset._meta)[0]].data[i]._model;
                                            total     = dataset._meta[Object.keys(dataset._meta)[0]].total;
                                            midRadius = model.innerRadius + (model.outerRadius - model.innerRadius) / 2;

                                            startAngle    = model.startAngle;
                                            endAngle      = model.endAngle;
                                            midAngle      = startAngle + (endAngle - startAngle) / 2;
                                            x             = midRadius * Math.cos(midAngle);
                                            y             = midRadius * Math.sin(midAngle);
                                            ctx.fillStyle = '#fff';
                                            val           = dataset.data[i];
                                            percent       = String(Math.round(val / total * 100)) + "%";

                                            if (val !== 0) {
                                                ctx.fillText(dataset.data[i], model.x + x, model.y + y);
                                                // Display percent in another line, line break doesn't work for fillText
                                                ctx.fillText(percent, model.x + x, model.y + y + 15);
                                            }
                                        }
                                    });
                                }
                            }
                        }
                    });
                },
                renderHorizontalBarChart: function () {
                    var chartEl   = $(this.element);
                    var self      = this;
                    var max       = Math.max.apply(null, this.options.chartData.data);
                    var maxLength = max + Math.ceil(max / 5);

                    new Chart(chartEl, {
                        type: 'horizontalBar',
                        data: {
                            labels: self.options.chartData.labels,
                            datasets: [
                                {
                                    data: self.options.chartData.data,
                                    fill: true,
                                    backgroundColor: self.options.chartData.backgroundColors,
                                    borderWidth: 1
                                }
                            ]
                        },
                        options: {
                            legend: {
                                display: false
                            },
                            tooltips: {
                                enabled: false
                            },
                            scales: {
                                xAxes: [{
                                    ticks: {
                                        min: 0,
                                        max: maxLength,
                                        stepSize: 1
                                    }
                                }]
                            },
                            hover: {
                                animationDuration: 0
                            },
                            animation: {
                                duration: 1,
                                onComplete: function () {
                                    var chartInstance = this.chart,
                                        ctx           = chartInstance.ctx;

                                    ctx.font          = Chart.helpers.fontString(
                                        Chart.defaults.global.defaultFontSize + 2,
                                        Chart.defaults.global.defaultFontStyle,
                                        Chart.defaults.global.defaultFontFamily
                                    );
                                    ctx.fillStyle     = Chart.helpers.color;
                                    ctx.textAlign     = "center";
                                    ctx.textBaseline  = "bottom";

                                    this.data.datasets.forEach(function (dataset, i) {
                                        var meta = chartInstance.controller.getDatasetMeta(i);

                                        meta.data.forEach(function (bar, index) {
                                            var data = dataset.data[index] + ' ('
                                                + (dataset.data[index] * 100 / self.options.chartData.total).toFixed(2)
                                                + '%)';

                                            ctx.fillText(data, bar._model.x + 40, bar._model.y + 5);
                                        });
                                    });
                                }
                            }
                        }
                    });
                },
                renderVerticalBarChart: function () {
                    var chartEl = $(this.element);
                    var self    = this;

                    new Chart(chartEl, {
                        type: 'bar',
                        data: {
                            labels: self.options.chartData.labels,
                            datasets: self.options.chartData.datasets
                        },
                        options: {
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        min: 0,
                                        stepSize: 1
                                    }
                                }]
                            }
                        }
                    });
                },
                renderMap: function () {
                    var latData  = 0;
                    var lngData  = 0;
                    var zoom = +this.options.zoom || 1;
                    var positionData;
                    var map;

                    if (this.options.position) {
                        positionData = this.options.position.split(',');
                        latData         = +positionData[0].replace('(', '');
                        lngData          = +positionData[1].replace(')', '').trim();
                    }
                    map = new google.maps.Map(this.element[0], {
                        center: {lat: latData, lng: lngData},
                        zoom: zoom,
                        enabledMarker: true
                    });
                    _.each(this.options.chartData, function (count, position) {
                        var lat, lng;

                        position = position.split(',');
                        lat      = +position[0].replace('(', '');
                        lng      = +position[1].replace(')', '').trim();
                        position = new google.maps.LatLng(lat, lng);
                        new google.maps.Marker({
                            title: count === 1 ? $t(count + ' response') : $t(count + ' responses'),
                            position: position,
                            map: map
                        });
                    });
                }
            });

        return $.mageplaza.responsesSummary;
    }
);

