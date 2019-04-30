/* global $ municipalitiesUrl birthTownValue */


$(function () {
    "use strict";

    $("#birthCountry").change(function (event, params) {
        var birthProvince = $("#birthProvince"),
                birthProvinceHidden = $("[type=hidden][name='user[birthProvince]'], [type=hidden][name='customer[birthProvince]']"),
                birthTownSelect = $("select#birthTown"),
                birthTownString = $("input#birthTown");
        if(typeof server_instance === "undefined"){
            var server_instance = "sk";
        }
        if ($(this).val() !== server_instance) {
            birthProvince.val("EE");
            birthProvince.prop("disabled", true);
            birthProvinceHidden.val("EE");
            birthTownSelect.hide();
            birthTownString.show();
            birthTownString.prop("disabled", false);

            if (typeof params !== "undefined" && params.hasOwnProperty("birthTownValue")) {
                birthTownString.attr("value", params.birthTownValue.toUpperCase());
            } else {
                birthTownString.attr("value", "");
            }
        } else {
            if (birthProvince.val() === "EE") {
                birthProvince.val(0);
            }

            birthProvince.prop("disabled", false);
            birthProvinceHidden.val("");
            birthTownSelect.show();
            birthTownString.hide();
            birthTownString.prop("disabled", true);
        }

        birthProvince.change();
    });
    if (typeof birthTownValue != "undefined") {
        $("#birthCountry").trigger("change", {
            birthTownValue: birthTownValue
        });
    }

    $("#birthProvince").change(function (event, params) {
        var province = $(this).val(),
                promise;

        // clear present options
        $("#birthTown option").remove();

        if (province !== 0 && province !== "0") {
            promise = $.get(municipalitiesUrl + "/" + province, function (data) {
                if (province === $("#birthProvince").val()) {
                    $.each(data, function (i, item) {
                        $("#birthTown").append($("<option>", {
                            value: item.name,
                            text: item.name
                        }));
                    });
                }
            });

            if (typeof params !== "undefined" && params.hasOwnProperty("birthTownValue")) {
                promise.done(function () {
                    $("select#birthTown").val(params.birthTownValue);
                });
            }
        } else {
            $("#birthTown").append($("<option>"));
        }
    });
    if (typeof $("#birthProvince").val() != "undefined") {
        $("#birthProvince").trigger("change", {
            birthTownValue: $("#birthProvince")
        });
    }

});
