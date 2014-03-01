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
    $celebration.show();
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
      verticalOffset: 10
    });
    $("#cupcakes-load").hide();
    $cupcakes.show();
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
      verticalOffset: 10
    });
    $("#other-load").hide();
    $other.show();
  });
});
