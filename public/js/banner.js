/* global $ */

/**
 * Predefined callback function
 */
var setBanner = function (data) {
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

/**
 * This function call a ajax to a bannerJsonpUrl that will return
 * a JSONp containing the data needed by the setBanner function to
 * compose the page banners.
 *
 *  @param  bannerJsonpUrl  The URL that return the JSONp
 *  @param  userId  The id of the user
 *  @param  pageLink  The link of the page that is calling this function
 */
var loadBanner = function (bannerJsonpUrl, userId, pageLink) {
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
};