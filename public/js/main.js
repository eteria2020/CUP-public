$(window).load(function(){

// add height main content
function resize()
{
    var hmap = $(window).height() - $('#header').height() - 20;
    $('.detect-map-height').height(hmap);
}

$(window).resize(function () {
	resize();
});

resize();


// show column bar
$('.js-toogle-column-app').on('click', function(e) {
	$('.module-wrapper-app').toggleClass("hide-side-bar");
    $('#outer').toggleClass("hide-side-bar");
	$(this).toggleClass("side-close");
    e.preventDefault();
});

// add class on touch
$('.block-data-table-row-group').on('touchend', function(e) {
	'use strict';
	var link = $(this);
	if (link.hasClass('over')) {
	   return true;
	} else {
	   link.addClass('over');
	   $('.block-data-table-row-group').not(this).removeClass('over');
	   e.preventDefault();
	   return false; //extra, and to make sure the function has consistent return points
    }
});

// PLACEOLDER
//Assign to those input elements that have 'placeholder' attribute
if($.browser.msie){
   $('input[placeholder]').each(function(){

        var input = $(this);

        $(input).val(input.attr('placeholder'));

        $(input).focus(function(){
             if (input.val() == input.attr('placeholder')) {
                 input.val('');
             }
        });

        $(input).blur(function(){
            if (input.val() === '' || input.val() == input.attr('placeholder')) {
                input.val(input.attr('placeholder'));
            }
        });
    });
};

// SHOW BOX

$(".js-collapse-box").hide();
$(".js-collapse-box").removeClass( "hidden" );
$(".js-show-element").click(function(){
	$(this).next().slideToggle('fast', function(){
		$(this).prev(".js-show-element").toggleClass("active");
	});
	return false;
});

// TOGGLE MAP HEIGHT

$('.js-toggle-map-height').on('click', function(e) {
    $('.module-car-map').toggleClass("small-height");
    $(this).toggleClass("active");
    e.preventDefault();
});

});
