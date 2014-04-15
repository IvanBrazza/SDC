$(document).ready(function() {
  $(document).tooltip();

  $("#theCakeNext").click(function() {
    // If the link isn't disabled, go to the next tab
    var date_check = validate.date();
    if ($("#comments").data("required") == "true") {
      var comm_check = validate.input("textarea#comments", "#comments_error");
      if (comm_check && date_check) {
        $("#theCake").collapse("hide");
        $("#uploadAPhoto").collapse("show");
      }
    } else {
      if (date_check) {
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
    var datt_check = validate.datetime();
    if (datt_check) {
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

  $("#order-form").submit(function() {
    NProgress.configure({
      trickleRate:  0.1,
      trickleSpeed: 500
    });
    NProgress.start();
  });

  $("select[name=delivery]").change(function() {
    if ($(this).val() === "Deliver To Address") {
      calculateDeliveryCharge();
    }
  });
});
