/* global $ */

$(function () {
    "use strict";

    $("#birthCountry").change(function () {
        var birthProvince = $("#birthProvince");

        if ($(this).val() !== "it") {
            birthProvince.val("EE");
        } else if (birthProvince.val() === "EE") {
            birthProvince.val("AG");
        }
    });
});
