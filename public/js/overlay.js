jQuery(function($)
{
    var w = $(window),
        overlay = $('#sng-overlay'),
        slider = $('#sng-overlay-slider'),
        content = $('#sng-overlay-content1').find('.content-right'),
        isClosing = false,
        closeTimeout = null,
        closeOverlay = function()
        {
            if (!isClosing)
            {
                isClosing = true;
                if (closeTimeout !== null) clearTimeout(closeTimeout);
                overlay.perfectScrollbar('destroy');
                overlay.fadeOut(600, function()
                {
                    overlay.remove();
                });
            }
        };

    w.on('resize', function()
    {
        slider.find('.slide').height(content.outerHeight());
    });
    w.trigger('resize');

    slider.slick(
    {
        accessibility: false,
        autoplay: true,
        autoplaySpeed: 4000,
        arrows: false,
        fade: true,
        draggable: false,
        pauseOnHover: false,
        slide: '.slide',
        swipe: false,
        touchMove: false,
        infinite: false
    });

    overlay.perfectScrollbar(
    {
        suppressScrollX: true,
        swipePropagation: false,
        useKeyboard: false
    });

    overlay.find('.sng-overlay-close').on('click', function(e)
    {
        e.preventDefault();
        closeOverlay();
    });

    overlay.find('.more').on('click', function(e)
    {
        e.preventDefault();
        if (closeTimeout !== null) clearTimeout(closeTimeout);
        $('#sng-overlay-content1').slideUp(500, 'swing');
        overlay.stop().animate({scrollTop: 0}, 500, 'swing');
        $('#sng-overlay-content2').slideDown(500, 'swing', function()
        {
            overlay.perfectScrollbar('update');
        });
    });

    w.on('load', function()
    {
        //closeTimeout = setTimeout(closeOverlay, 15000);
        //overlay.find('.more').trigger('click');
    });
});