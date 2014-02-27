/**
  js/gallery.js - code specific to the gallery page
**/
$(document).ready(function() {
  var $celebration = $("#celebration-tiles li"),
      $cupcakes    = $("#cupcakes-tiles li"),
      $other       = $("#other-tiles li")

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
