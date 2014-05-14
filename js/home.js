var wmOptions = {
      autoResize: true,
      itemWidth: 200,
      align: "center",
      flexibleWidth: "100%",
      container: "",
      offset: 0,
      verticalOffset: 0,
      fillEmptySapce: false
    },
    curSlide  = 1,
    curWidth  = window.innerWidth,
    d         = 0,
    $slide1,
    $slide2,
    $slide3;
$(document).ready(function() {
  howManyImages();
  $slide1.data("refresh", "true").find("img.lazy").unveil();
  $slide2.data("refresh", "true").find("img.lazy").unveil();
  $slide3.data("refresh", "true").find("img.lazy").unveil();

  $slide1.imagesLoaded(function() {
    wmOptions.container = $("#slide1-wm");
    $slide1.wookmark(wmOptions).hide().each(function() {
      $(this).delay(d).fadeIn();
      d += 100;
    });
    d = 0;
  });

  $slide2.imagesLoaded(function() {
    wmOptions.container = $("#slide2-wm");
    $slide2.wookmark(wmOptions).hide();
  });

  $slide3.imagesLoaded(function() {
    wmOptions.container = $("#slide3-wm");
    $slide3.wookmark(wmOptions).hide();
  });

  $("#myCarousel").on('slid.bs.carousel', function() {
    var curSlide = $(".carousel-inner").find(".active").index() + 1;
    switch (curSlide) {
      case 1:
        if ($slide1.data("refresh") == "true") {
          $slide1.trigger("refreshWookmark").each(function() {
            $(this).delay(d).fadeIn();
            d += 100;
          });
          d = 0;
          $slide1.data("refresh", "false");
        }
        break;
      case 2:
        if ($slide2.data("refresh") == "true") {
          $slide2.trigger("refreshWookmark").each(function() {
            $(this).delay(d).fadeIn();
            d += 100;
          });
          d = 0;
          $slide2.data("refresh", "false");
        }
        break;
      case 3:
        if ($slide3.data("refresh") == "true") {
          var lastIndex = $slide3.last().index();
          $slide3.trigger("refreshWookmark").each(function() {
            $(this).delay(d).fadeIn();
            d += 100;
            if ($(this).index() == lastIndex) {
              $(".get-started").delay(d + 1000).fadeIn("slow");
            }
          });
          d = 0;
          $slide3.data("refresh", "false");
        }
        break;
    }
  });

  $(window).resize(function() {
    if (curWidth != window.innerWidth) {
      $slide1.hide();
      $slide2.hide();
      $slide3.hide();
      howManyImages();
      $slide1.data("refresh", "true").find("img.lazy").unveil();
      $slide2.data("refresh", "true").find("img.lazy").unveil();
      $slide3.data("refresh", "true").find("img.lazy").unveil();
      $slide1.imagesLoaded(function() {
        wmOptions.container = $("#slide1-wm");
        $slide1.wookmark(wmOptions);
      });
      $slide2.imagesLoaded(function() {
        wmOptions.container = $("#slide2-wm");
        $slide2.wookmark(wmOptions);
      });
      $slide3.imagesLoaded(function() {
        wmOptions.container = $("#slide3-wm");
        $slide3.wookmark(wmOptions);
      });
      curWidth = window.innerWidth;
    }
  });
});

function howManyImages() {
  if (window.innerWidth < 768) {
    $slide1 = $("#slide1-wm-tiles li:lt(8)"),
    $slide2 = $("#slide2-wm-tiles li:lt(8)"),
    $slide3 = $("#slide3-wm-tiles li:lt(8)");
  } else if (window.innerWidth < 992) {
    $slide1 = $("#slide1-wm-tiles li:lt(10)"),
    $slide2 = $("#slide2-wm-tiles li:lt(11)"),
    $slide3 = $("#slide3-wm-tiles li:lt(11)");
  } else {
    $slide1 = $("#slide1-wm-tiles li"),
    $slide2 = $("#slide2-wm-tiles li"),
    $slide3 = $("#slide3-wm-tiles li");
  }
}
