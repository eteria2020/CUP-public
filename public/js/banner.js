//Predefined callback function    
function setBanner(data) {
    if (typeof data.banner === 'undefined'){
        return false;
    }

    $.each(data.banner, function(key,banner){
        if (typeof banner.id === 'undefined'){
            return false;
        }

        if (typeof banner.html !== 'undefined'){
            $("#"+banner.id).html(banner.html);
        }

        if (typeof banner.img !== 'undefined'){
            $("#"+banner.id+" img").prop("src",banner.img);
        }

        if (typeof banner.link !== 'undefined'){
            $("#"+banner.id+" a").prop("href",banner.link);
        } 
    });
}
