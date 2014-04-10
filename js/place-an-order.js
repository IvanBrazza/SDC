$(document).ready(function() {
  $(document).tooltip();

  $("#theCakeNext").click(function() {
    // If the link isn't disabled, go to the next tab
    validate.date();
    if ($("#comments").data("required") == "true") {
      validate.input("textarea#comments", "#comments_error");
      if ($input_check && $date_check) {
        $("#theCake").collapse("hide");
        $("#uploadAPhoto").collapse("show");
      }
    } else {
      if ($date_check) {
        $("#theCake").collapse("hide");
        $("#uploadAPhoto").collapse("show");
      }
    }
  });

  $("#uploadAPhotoNext").click(function() {
    $("#deliveryPanel").collapse("show");
    $("#uploadAPhoto").collapse("hide");
  });

  $("#uploadAPhotoPrevious").click(function() {
    $("#theCake").collapse("show");
    $("#uploadAPhoto").collapse("hide");
  });

  $("#deliveryNext").click(function() {
    validate.datetime();
    if ($datetime_check) {
      $("#deliveryPanel").collapse("hide");
      $("#review").collapse("show");
    }
  });

  $("#deliveryPrevious").click(function() {
    $("#uploadAPhoto").collapse("show");
    $("#deliveryPanel").collapse("hide");
  });

  $("#reviewPrevious").click(function() {
    $("#deliveryPanel").collapse("show");
    $("#review").collapse("hide");
  });
});
