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
        'mage/translate',
        'Magento_Ui/js/modal/modal',
    ], function ($, $t, modal) {

        $.widget(
            'mageplaza.response', {
                _create: function () {
                    var self = this;

                    this.agreementObs();
                    require([
                        'https://maps.googleapis.com/maps/api/js?key='
                        + self.options.mpGoogleMapKey + '&libraries=places'
                    ], function () {
                        $('.google-map').each(function () {
                            self.initMap($(this).parents('.field-container'));
                        });
                    });
                },
                initMap: function(fieldEl){
                    var mapEl = fieldEl.find('.google-map');
                    var lat   = 0;
                    var lng   = 0;
                    var zoom;
                    var position;
                    var map;

                    if (!mapEl.length) {
                        return;
                    }
                    zoom     = +fieldEl.find('input.map-zoom').val() || 1;
                    position = fieldEl.find('input.map-position').val();
                    if (position) {
                        position = position.split(',');
                        lat      = +position[0].replace('(', '');
                        lng      = +position[1].replace(')', '').trim();
                    }
                    map = new google.maps.Map(mapEl[0], {
                        center: {lat: lat, lng: lng},
                        zoom: zoom,
                        enabledMarker: true
                    });
                    new google.maps.Marker({
                        position: map.center,
                        map: map
                    });
                },
                agreementObs: function () {
                    $('.open-agreement-modal').on('click', function () {
                        var options = {
                            'type': 'popup',
                            'responsive': true,
                            'innerScroll': true,
                            'title': $(this).parents('.field-val-wrapper').find('.modal-title').val(),
                            'buttons': []
                        };

                        modal(
                            options,
                            '<div>' + $(this).parents('.field-val-wrapper').find('.modal-content').val() + '</div>'
                        ).openModal();
                    });
                }
            });

        return $.mageplaza.response;
    }
);

