define([
    'jquery',
    'calendar'
], function ($, calendar) {
    'use strict';

    $.widget('mage.awCtqCalendar', calendar.calendar, {
        dateTimeFormat: {
            date: {
                'yy': 'y'
            }
        }
    });

    return $.mage.awCtqCalendar;
});
