$(document).ready(function() {
  $('#celebration-carousel').flexslider({
    animation: "slide",
    controlNav: false,
    animationLoop: true,
    slideshow: false,
    itemWidth: 210,
    itemMargin: 5,
    asNavFor: '#celebration-slider'
  });
   
  $('#celebration-slider').flexslider({
    animation: "slide",
    controlNav: false,
    animationLoop: true,
    slideshowSpeed: 3000,
    slideshow: true,
    sync: "#celebration-carousel"
  });

  $('#cupcake-carousel').flexslider({
    animation: "slide",
    controlNav: false,
    animationLoop: true,
    slideshow: false,
    itemWidth: 210,
    itemMargin: 5,
    asNavFor: '#cupcake-slider'
  });
   
  $('#cupcake-slider').flexslider({
    animation: "slide",
    controlNav: false,
    animationLoop: true,
    slideshowSpeed: 3000,
    slideshow: true,
    sync: "#cupcake-carousel"
  });

  $('#other-carousel').flexslider({
    animation: "slide",
    controlNav: false,
    animationLoop: true,
    slideshow: false,
    itemWidth: 210,
    itemMargin: 5,
    asNavFor: '#other-slider'
  });
   
  $('#other-slider').flexslider({
    animation: "slide",
    controlNav: false,
    animationLoop: true,
    slideshowSpeed: 3000,
    slideshow: true,
    sync: "#other-carousel"
  });
});
