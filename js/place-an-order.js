$(document).ready(function() {
  $(document).tooltip();

  calculateOrderTotal();

  var deliveryPanelHeight = $("#deliveryPanel").height(),
      $celeb_date = $("input[name=celebration_date]").pickadate({
        clear: '',
        formatSubmit: 'yyyy-mm-dd',
        hiddenName: true,
        format: 'mmmm dd, yyyy',
        min: true,
        onClose: function() {
          if ($celeb_date.pickadate("get") == "") {
            $celeb_date.closest(".form-group").removeClass("has-success").addClass("has-error");
            $("#celebration_date_error").html("Please select a celebration date").slideDown("fast");
          } else {
            $celeb_date.closest(".form-group").removeClass("has-error").addClass("has-success");
            $("#celebration_date_error").slideUp("fast");
          }
        },
        onSet: function(context) {
          $("#celebration-date-review").html($celeb_date.pickadate("get"));
        }
      }),
      $dt_date = $("input[name=datetime_date]").pickadate({
        clear: '',
        formatSubmit: 'yyyy-mm-dd',
        hiddenName: true,
        format: 'mmmm dd, yyyy',
        min: true,
        onOpen: function() {
          $("#deliveryPanel").stop().animate({height: "500px"});
        },
        onClose: function() {
          $("#deliveryPanel").stop().animate({height: deliveryPanelHeight});
          if ($dt_date.pickadate("get") == "") {
            $dt_date.closest(".form-group").removeClass("has-success").addClass("has-error");
            $("#datetime_date_error").html("Please select a date").slideDown("fast");
          } else {
            $dt_date.closest(".form-group").removeClass("has-error").addClass("has-success");
            $("#datetime_date_error").slideUp("fast");
          }
        },
        onSet: function(context) {
          $("#datetime-review").html($dt_date.pickadate("get") + " @ " + $dt_time.pickatime("get"));
        }
      }),
      $dt_time = $("input[name=datetime_time]").pickatime({
        clear: '',
        hiddenName: true,
        format: 'h:i A',
        formatSubmit: 'HH:i:00',
        interval: 15,
        max: [18,0],
        min: [8,0],
        onOpen: function() {
          $("#deliveryPanel").stop().animate({height: "640px"});
        },
        onClose: function() {
          $("#deliveryPanel").stop().animate({height: deliveryPanelHeight});
          if ($dt_time.pickadate("get") == "") {
            $dt_time.closest(".form-group").removeClass("has-success").addClass("has-error");
            $("#datetime_time_error").html("Please select a date").slideDown("fast");
          } else {
            $dt_time.closest(".form-group").removeClass("has-error").addClass("has-success");
            $("#datetime_time_error").slideUp("fast");
          }
        },
        onSet: function(context) {
          $("#datetime-review").html($dt_date.pickadate("get") + " @ " + $dt_time.pickatime("get"));
        }
      });

  $("#theCakeNext").click(function() {
    var $celeb_date = $("#celebration_date_hidden"),
        $fill_check = validate.input('select[name=filling]', '#filling_error', 'Please choose a filling'),
        $deco_check = validate.input('select[name=decoration]', '#decoration_error', 'Please choose a decoration'),
        $size_check = validate.input('select[name=cake_size]', '#cake_size_error', 'Please choose a cake size'),
        $type_check = validate.input('select[name=cake_type]', '#cake_type_error', 'Please choose a cake type');
    if ($("#comments").data("required") == "true") {
      var comm_check = validate.input("textarea#comments", "#comments_error", 'Please enter a comment');
      if (comm_check && $fill_check && $deco_check && $size_check && $type_check && $celeb_date.val() != "") {
        $("#theCake").collapse("hide");
        $("#uploadAPhoto").collapse("show");
      } else if ($celeb_date.val() == "") {
        $celeb_date.closest(".form-group").removeClass("has-success").addClass("has-error");
        $("#celebration-date-error").html("Please select a celebration date").slideDown("fast");
      }
    } else {
      if ($fill_check && $deco_check && $size_check && $type_check && $celeb_date.val() != "") {
        $("#theCake").collapse("hide");
        $("#uploadAPhoto").collapse("show");
      }
      if ($celeb_date.val() == "") {
        $celeb_date.closest(".form-group").removeClass("has-success").addClass("has-error");
        $("#celebration_date_error").html("Please select a celebration date").slideDown("fast");
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
    var $datetime_date  = $("#datetime_date_hidden"),
        $datetime_time  = $("#datetime_time_hidden"),
        $delivery_check = validate.input('select[name=delivery]', '#delivery_error', 'Please choose a delivery option');
    if ($datetime_date.val() != "" && $datetime_time.val() != "" && $delivery_check) {
      $("#deliveryPanel").collapse("hide");
      $("#review").collapse("show");
    }
    if ($datetime_date.val() == "") {
      $datetime_date.closest(".form-group").removeClass("has-success").addClass("has-error");
      $("#datetime_date_error").html("Please select a date").slideDown("fast");
    }
    if ($datetime_time.val() == "") {
      $datetime_time.closest(".form-group").removeClass("has-success").addClass("has-error");
      $("#datetime_time_error").html("Please select a time").slideDown("fast");
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
