/* global pageLink, userId, bannerJsonpUrl */

/**
 * Predefined callback function
 */
var setBanner = function(data) {
    if (typeof data.banner === "undefined") {
        return false;
    }

    $.each(data.banner, function (key, banner) {
        if (typeof banner.id === "undefined") {
            return false;
        }

        if (typeof banner.html !== "undefined") {
            $("#" + banner.id).html(banner.html);
        }

        if (typeof banner.img !== "undefined") {
            $("#" + banner.id + " img").prop("src", banner.img);
        }

        if (typeof banner.link !== "undefined") {
            $("#" + banner.id + " a").prop("href", banner.link);
        }
    });
};

// Load Banner
$.ajax({
    url: bannerJsonpUrl,
    dataType: "jsonp",
    type: "POST",
    async: true,
    data: {
        id: userId,
        link: pageLink,
        callback: "setBanner"
    },
    crossDomain: true,
    success: setBanner
});