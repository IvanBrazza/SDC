/**
  js/gallery.js - code specific to the gallery page
**/
$(document).ready(function() {
  // Define jQuery selector vars
  var $celebration = $("#celebration-tiles li"),
      $cupcakes    = $("#cupcakes-tiles li"),
      $other       = $("#other-tiles li")

  // For each thumbnail, give it a tooltip containing
  // the larger version of the img thumbnail and set
  // the width of the tootltip to the width of the img
  $(".thumb").tooltip({
    open: function(event, ui) {
      ui.tooltip.css("max-width", $(ui.tooltip).find("img").width() + "px");
    },
    position: { my: "left+15 center", at: "right center" },
    track: true
  })
  .each(function() {
    $content = "<img src='" + $(this).attr("src") + "'>";
    $(this).tooltip("option", "content", $content);
  });

  // When the images for the celebration section are
  // loaded, setup the masonry layout, hide the spinner,
  // and show the images once loaded
  $celebration.imagesLoaded(function() {
    $celebration.wookmark({
      autoResize: true,
      container: $("#celebration-container"),
      itemWidth: 200,
      align: "center",
      offset: 3,
      verticalOffset: 10,
      fillEmptySpace: true
    });
    $("#celebration-load").hide();
    $celebration.hide();
    $("#celebration-container .wookmark-placeholder").hide();
    var d = 0;
    $celebration.each(function() {
      $(this).delay(d).fadeIn();
      d += 100;
    });
    $("#celebration-container .wookmark-placeholder").each(function() {
      $(this).delay(d).fadeIn();
      d += 100;
    });
  });

  // When the images for the cupcakes section are
  // loaded, setup the masonry layout, hide the spinner,
  // and show the images once loaded
  $cupcakes.imagesLoaded(function() {
    $cupcakes.wookmark({
      autoResize: true,
      container: $("#cupcakes-container"),
      itemWidth: 200,
      align: "center",
      offset: 3,
      verticalOffset: 10,
      fillEmptySpace: true
    });
    $("#cupcakes-load").hide();
    $cupcakes.hide();
    $("#cupcakes-container .wookmark-placeholder").hide();
    var d = 0;
    $cupcakes.each(function() {
      $(this).delay(d).fadeIn();
      d += 100;
    });
    $("#cupcakes-container .wookmark-placeholder").each(function() {
      $(this).delay(d).fadeIn();
      d += 100;
    });
  });

  // When the images for the other section are
  // loaded, setup the masonry layout, hide the spinner,
  // and show the images once loaded
  $other.imagesLoaded(function() {
    $other.wookmark({
      autoResize: true,
      container: $("#other-container"),
      itemWidth: 200,
      align: "center",
      offset: 3,
      verticalOffset: 10,
      fillEmptySpace: true
    });
    $("#other-load").hide();
    $other.hide();
    $("#other-container .wookmark-placeholder").hide();
    var d = 0;
    $other.each(function() {
      $(this).delay(d).fadeIn();
      d += 100;
    });
    $("#other-container .wookmark-placeholder").each(function() {
      $(this).delay(d).fadeIn();
      d += 100;
    });
  });
});
