/* global $ window */

$(function () {
    "use strict";

    $(".buyPackage").magnificPopup({
        type: "ajax",
        callbacks: {
            ajaxContentAdded: function () {
                $("#close-btn").click(function (e) {
                    e.preventDefault();
                    $.magnificPopup.close();
                });

                $(".sng-pack-popup button#confirm-btn").click(function (e) {
                    var url = $(this).data("href"),
                        packageId = $(this).data("package-id");

                    e.preventDefault();
                    $.post(url, {
                        packageId: packageId
                    }).always(function () {
                        window.location.reload();
                    });

                    // disable button
                    $(this).attr("disabled", true);
                });
            }
        }
    });
});
