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
            elementTmpl: 'Mageplaza_CustomForm/form/field/map',
            zoom: 1
        },
        initObservable: function () {
            this._super();
            this.value(this.address);
            this.addFieldToProvider();
            this.dependencyObs();
            return this;
        },
        initMap: function () {
            var elf = this;

            require([
                'https://maps.googleapis.com/maps/api/js?key=' + this.mpGoogleMapKey + '&libraries=places'
            ], function () {
                var self    = this;
                var fieldEl = $('[data-ns="' + this.ns + '"]').find('[name="' + this.dataScope + '"]');
                var mapEl   = fieldEl.find('.google-map');
                var latLng;
                var geocoder;
                var input;
                var searchBox;

                if (!mapEl.length) {
                    return;
                }
                latLng      = this.getLatLng();
                this.map    = new google.maps.Map(mapEl[0], {
                    center: latLng,
                    zoom: +this.zoom,
                    enabledMarker: true
                });
                this.marker = new google.maps.Marker();
                if (this.position) {
                    this.marker.setPosition(this.map.center);
                    this.marker.setMap(this.map);
                }
                google.maps.event.addListener(self.map, 'zoom_changed', function () {
                    fieldEl.find('input.map-zoom').val(self.map.zoom);
                });

                geocoder  = new google.maps.Geocoder();
                input     = fieldEl.find('.map-search')[0];
                searchBox = new google.maps.places.SearchBox(input);
                this.map.controls[google.maps.ControlPosition.BOTTOM].push(input);
                searchBox.addListener('places_changed', function () {
                    var bounds = new google.maps.LatLngBounds();

                    var places = searchBox.getPlaces();

                    if (places.length === 0) {
                        return;
                    }
                    self.marker.setMap(null);
                    places.forEach(function (place) {
                        if (!place.geometry) {
                            console.log("Returned place contains no geometry");
                            return;
                        }

                        self.marker.setMap(null);
                        self.marker = new google.maps.Marker({
                            position: place.geometry.location,
                            map: self.map
                        });
                        fieldEl.find('input.map-position').val(place.geometry.location);
                        if (place.geometry.viewport) {
                            bounds.union(place.geometry.viewport);
                        } else {
                            bounds.extend(place.geometry.location);
                        }
                    });
                    self.map.fitBounds(bounds);
                    fieldEl.find('input.map-zoom').val(self.map.zoom);
                });

                google.maps.event.addListener(self.map, 'click', function (event) {
                    self.marker.setMap(null);
                    self.marker = new google.maps.Marker({
                        position: event.latLng,
                        map: self.map
                    });
                    geocoder.geocode({'latLng': event.latLng}, function (results, status) {
                        var address = 'Unknown';

                        // eslint-disable-next-line eqeqeq
                        if (status == google.maps.GeocoderStatus.OK) {
                            if (results[0]) {
                                address = results[0].formatted_address;
                            }
                        }
                        fieldEl.find('input.map-search').val(address);
                    });
                    fieldEl.find('input.map-position').val(event.latLng);
                    elf.value(event.latLng);
                });
            }.bind(this));
        },
        resetField: function () {
            var latLng = this.getLatLng();
            var fieldEl = $('[data-ns="' + this.ns + '"]').find('[name="' + this.dataScope + '"]');

            this.map.setOptions({
                center: latLng,
                zoom: +this.zoom,
                enabledMarker: true
            });
            this.marker.setMap(null);
            if (this.position) {
                this.marker = new google.maps.Marker({
                    position: latLng,
                    map: this.map
                });
            }
            fieldEl.find('input.map-search').val(this.address);
            this.value(this.address);
        },
        getLatLng: function () {
            var lat = 0;
            var lng = 0;
            var position;

            if (this.position) {
                position = this.position.split(',');
                lat          = +position[0].replace('(', '');
                lng          = +position[1].replace(')', '').trim();
            }
            return {lat: lat, lng: lng};
        }
    });
});
