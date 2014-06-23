/**
  js/gallery.js - code specific to the gallery page
**/
$(document).ready(function() {

  $(".tab-pane").each(function() {
    var $li = $(this).find("li"),
        $load = $(this).find(".load"),
        $container = $(this).find(".gallery_container"),
        $placeholder = $(this).find(".gallery_container .wookmark-placeholder");
    $li.imagesLoaded(function() {
      $li.wookmark({
        autoResize: true,
        container: $container,
        itemWidth: 200,
        align: "center",
        offset: 3,
        verticalOffset: 10,
        fillEmptySpace: true
      });
      $li.hide();
      $placeholder.hide();
      loadFirst();
    });
  });

  // Let the users know they can click an image
  setTimeout(function() {
    $(".alert").slideDown();
  }, 5000);

  $(".nav-tabs li a ").click(function() {
    var $gallery = $(this).attr("href"),
        $li = $($gallery).find("li"),
        $container = $($gallery).find(".gallery_container"),
        $load = $($gallery).find(".load"),
        $placeholder = $container.find(".wookmark-placeholder");
    setTimeout(function() {
      $container.trigger("refreshWookmark");
      $load.hide();
      var d = 0;
      $li.each(function() {
        $(this).delay(d).fadeIn();
        d += 100;
      });
      $placeholder.each(function() {
        $(this).delay(d).fadeIn();
        d += 100;
      });
    }, 500);
  });
});

function loadFirst() {
  var d = 0;
  $(".tab-pane:first .load").hide();
  $(".tab-pane:first li, .tab-pane:first .wookmark-placeholder").each(function() {
      $(this).delay(d).fadeIn();
      d += 100;
  });
}
