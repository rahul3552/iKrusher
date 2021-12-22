define([
    'jquery',
    'Aheadworks_Ctq/js/quote/edit/scripts'
], function (jQuery) {
    'use strict';

    var $el = jQuery('#edit_form'),
        config,
        baseUrl,
        quote;

    if (!$el.length || !$el.data('quote-config')) {
        return;
    }

    config = $el.data('quote-config');
    baseUrl = $el.data('load-base-url');

    quote = new AwCtqQuote(config);
    quote.setLoadBaseUrl(baseUrl);

    window.quote = quote;
});

