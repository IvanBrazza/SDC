$(document).ready(function() {
  var wmOptions = {
        autoResize: true,
        itemWidth: 200,
        align: "center",
        direction: "right",
        flexibleWidth: true,
        offset: 0,
        verticalOffset: 0,
        fillEmptySapce: false
      },
      $slide1 = $("#slide1-wm-tiles li"),
      $slide2 = $("#slide2-wm-tiles li"),
      $slide3 = $("#slide3-wm-tiles li"),
      d = 0;
  $slide1.imagesLoaded(function() {
    $slide1.wookmark({
        autoResize: true,
        itemWidth: 200,
        align: "center",
        direction: "right",
        flexibleWidth: true,
        container: $("#slide1-wm"),
        offset: 0,
        verticalOffset: 0,
        fillEmptySapce: false
      }).hide().each(function() {
      $(this).delay(d).fadeIn();
      d += 100;
    });
    d = 0;
  });
  $slide2.imagesLoaded(function() {
    $slide2.wookmark({
        autoResize: true,
        itemWidth: 200,
        align: "center",
        direction: "right",
        flexibleWidth: true,
        container: $("#slide2-wm"),
        offset: 0,
        verticalOffset: 0,
        fillEmptySapce: false
      }).hide();
  });
  $slide3.imagesLoaded(function() {
    $slide3.wookmark({
        autoResize: true,
        itemWidth: 200,
        align: "center",
        direction: "right",
        flexibleWidth: true,
        container: $("#slide3-wm"),
        offset: 0,
        verticalOffset: 0,
        fillEmptySapce: false
      }).hide();
  });
  var slide = 1;
  $("#myCarousel").on('slid.bs.carousel', function() {
    if (slide < 3) {
      slide++;
      if (slide == 2) {
        $slide2.trigger("refreshWookmark").each(function() {
          $(this).delay(d).fadeIn();
          d += 100;
        });
        d = 0;
      } else if (slide == 3) {
        $slide3.trigger("refreshWookmark").each(function() {
          $(this).delay(d).fadeIn();
          d += 100;
        });
      }
    }
  });
});
