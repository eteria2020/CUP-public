/* global $ document userDetailsUrl */

$(document).ready(function () {
    'use strict';

    $('#profiling-banner').click(function (e) {
        var discouterUrl = $(this).attr('href');

        e.preventDefault();

        $.get(userDetailsUrl, function (data) {
            $.redirect(discouterUrl, {
                'utente': data.name,
                'email': data.email
            });
        });
    });
});
