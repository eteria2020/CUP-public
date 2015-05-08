$(document).ready(function() {
	  $('.single-item').slick({
		  dots: false,
		  infinite: true,
		  speed: 900,
		  slidesToShow: 1,
		  slidesToScroll: 1
	  });
	  $('.single-item-autoplay').slick({
		  dots: false,
		  infinite: true,
		  speed: 900,
		  slidesToShow: 1,
		  autoplay: true,
          autoplaySpeed: 2000,
		  slidesToScroll: 1
	  });
      $('.slideshow').slick({
		  dots: false,
		  infinite: true,
		  speed:900,
		  slidesToShow: 1,
		  slidesToScroll: 1,
          fade: true,	
          autoplay: true,
          arrows: false,
          autoplaySpeed: 2000,
          cssEase: 'linear'
	  });
	  $('.slider-place').slick({
		  dots: false,
		  infinite: true,
		  speed: 900,
		  slidesToShow: 4,
		  slidesToScroll: 1,         
		  responsive: [{
			  breakpoint: 1024,
			  settings: {
				  slidesToShow: 3,
				  slidesToScroll: 1		  
				  
			  }
		  }, {
			  breakpoint: 600,
			  settings: {
				  slidesToShow: 2,
				  slidesToScroll: 1
			  }
		  }, {
			  breakpoint: 480,
			  settings: {
				  slidesToShow: 1,
				  slidesToScroll: 1
			  }
		  }]
	  });				
});