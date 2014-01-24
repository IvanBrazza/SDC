$(document).ready(function() {
  $(".tab_item").click(function() {
    $(".moving_bg").stop().animate({left: $(this).position()['left']}, {duration: 300});

    slideContent($(this));
  });

  $("#order-form-previous").data("disabled", "true");
  $("#order-form-next").data("disabled", "false");

  $("#order-form-next").click(function() {
    if ($(this).data("disabled") == "false") {
      var next = $(".tabs").find(".active").next();
      var background = $(".tabs").find(".moving_bg");

      $(background).stop().animate({left: next.position()['left']}, {duration: 300});

      slideContent(next);
    }
  });
  
  $("#order-form-previous").click(function() {
    if ($(this).data("disabled") == "false") {
      var previous = $(".tabs").find(".active").prev();
      var background = $(".tabs").find(".moving_bg");

      $(background).stop().animate({left: previous.position()['left']}, {duration: 300});

      slideContent(previous);
    }
  });

  $("#celebration_date").change(function() {
    $("#celebration-date-review").html($("#celebration_date").val());
  });

  $("#filling").change(function() {
    $("#filling-review").html($("#filling").val() + " - &pound;5");
  });

  $("#decoration").change(function() {
    $("#decoration-review").html($("#decoration").val() + " - &pound;5");
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

function slideContent(obj) {
  var margin = $(".slide_content").width();
  margin = margin * ($(obj).prevAll().size() - 1);
  margin = margin * -1;

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

  $(".tabslider").stop().animate({marginLeft: margin + "px"}, {duration: 300});

  $(".tabs").children().removeClass("active");
  $(obj).addClass("active");

  var height = $(".tabslider").children(":nth-child(" + $(obj).prevAll().size() + ")").height();
  $(".slide_content").stop().animate({height: height}, {duration: 300});
}
