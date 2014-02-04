/**
  js/tabbed-order.js - code which creates the tabbed
  order form
**/
$(document).ready(function() {
  // Disable the "previous" link and enable the "next"
  // link by default since we're on the first tab
  $("#order-form-previous").data("disabled", "true");
  $("#order-form-next").data("disabled", "false");

  // When the "next" link is clicked
  $("#order-form-next").click(function() {
    // If the link isn't disabled, go to the next tab
    if ($(this).data("disabled") == "false") {
      var next = $(".tabs").find(".active").next();
      var background = $(".tabs").find(".moving_bg");

      $(background).stop().animate({left: next.position()['left']}, {duration: 300});

      slideContent(next);
    }
  });

  // When the "previous" link is clicked
  $("#order-form-previous").click(function() {
    // If the link isn't disabled, go to the previous tab
    if ($(this).data("disabled") == "false") {
      var previous = $(".tabs").find(".active").prev();
      var background = $(".tabs").find(".moving_bg");

      $(background).stop().animate({left: previous.position()['left']}, {duration: 300});

      slideContent(previous);
    }
  });

  // Update the review tab details with details of the
  // order from the form
  $("#celebration_date").change(function() {
    $("#celebration-date-review").html($("#celebration_date").val());
  });

  $("#cake_size").change(function() {
    $("#cake-size-review").html($("#cake_size").val());
  });

  $("#cake_type").change(function() {
    $("#cake-type-review").html($("#cake_type").val());
  });

  $("textarea#comments").change(function() {
    $("#comments-review").html($("textarea#comments").val());
  });

  $("#delivery").change(function() {
    $("#delivery-review").html($("#delivery").val());
  });

  $("#datetime").change(function() {
    $("#datetime-review").html($("#datetime").val());
  });

  $("input:file").change(function() {
    if ($(this).val()) {
      $("#fileupload-review").html("Yes");
    } else {
      $("#fileupload-review").html("No");
    }
  });
});

// A function which slides the tab content
function slideContent(obj) {
  var margin = $(".slide_content").width();
  margin = margin * ($(obj).prevAll().size() - 1);
  margin = margin * -1;

  // If we're on the first tab, disable the "previous" link
  // and enable the "next" link. Else if we're on the last
  // tab, disable the "next" link and enable the "previous"
  // link. Else if we're in between the first and last tab,
  // enable both links
  if ($(obj).prevAll().size() == 1) {
    $("#order-form-previous").data("disabled", "true");
    $("#order-form-next").data("disabled", "false");
  } else if ($(obj).prevAll().size() == 4) {
    $("#order-form-next").data("disabled", "true");
    $("#order-form-previous").data("disabled", "false");
  } else {
    $("#order-form-previous").data("disabled", "false");
    $("#order-form-next").data("disabled", "false");
  }

  // Slide the content by animating the margin change over 300ms
  $(".tabslider").stop().animate({marginLeft: margin + "px"}, {duration: 300});

  // Remove the active class for all tabs then add the
  // active class to the current tab
  $(".tabs").children().removeClass("active");
  $(obj).addClass("active");

  // Get the height of the content in the current slide, and change the
  // height of the content to match, animated over 300ms
  var height = $(".tabslider").children(":nth-child(" + $(obj).prevAll().size() + ")").height();
  $(".slide_content").stop().animate({height: height}, {duration: 300});
}
