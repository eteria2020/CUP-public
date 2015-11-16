/* global $ */

$(function () {
    "use strict";

    $("#birthCountry").change(function () {
        var birthProvince = $("#birthProvince");
        var birthProvinceHidden = $("[type=hidden][name='user[birthProvince]']");

        if ($(this).val() !== "it") {
            birthProvince.val("EE");
            birthProvince.prop( "disabled", true );
            birthProvinceHidden.val("EE");
        } else {
            if (birthProvince.val() === "EE")
                birthProvince.val("AG");

            birthProvince.prop( "disabled", false );
            birthProvinceHidden.val("");
        }
    });
    $("#birthCountry").change();
});
