$(document).ready(function() {
  var deliveryPanelHeight = $("#deliveryPanel").height(),
      $placed_date = $("input[name=placed_date]").pickadate({
        clear: '',
        formatSubmit: 'yyyy-mm-dd',
        hiddenName: true,
        format: 'mmmm dd, yyyy',
        min: true,
        onClose: function() {
          if ($placed_date.pickadate("get") == "") {
            $placed_date.closest(".form-group").removeClass("has-success").addClass("has-error");
            $("#placed_date_error").html("Please select a date").slideDown("fast");
          } else {
            $placed_date.closest(".form-group").removeClass("has-error").addClass("has-success");
            $("#placed_date_error").slideUp("fast");
          }
        },
        onSet: function(context) {
          $("#order-placed-review").html($placed_date.pickadate("get") + " " + $placed_time.pickatime("get"));
        }
      }),
      $placed_time = $("input[name=placed_time]").pickatime({
        clear: '',
        hiddenName: true,
        format: 'h:i A',
        formatSubmit: 'HH:i:00',
        interval: 15,
        max: [18,0],
        min: [8,0],
        onClose: function() {
          if ($placed_time.pickatime("get") == "") {
            $placed_time.closest(".form-group").removeClass("has-success").addClass("has-error");
            $("#placed_time_error").html("Please select a time").slideDown("fast");
          } else {
            $placed_time.closest(".form-group").removeClass("has-error").addClass("has-success");
            $("#placed_time_error").slideUp("fast");
          }
        },
        onSet: function(context) {
          $("#order-placed-review").html($placed_date.pickadate("get") + " " + $placed_time.pickatime("get"));
        }
      }),
      $celeb_date = $("input[name=celebration_date]").pickadate({
        clear: '',
        formatSubmit: 'yyyy-mm-dd',
        hiddenName: true,
        format: 'mmmm dd, yyyy',
        min: true,
        onClose: function() {
          if ($celeb_date.pickadate("get") == "") {
            $celeb_date.closest(".form-group").removeClass("has-success").addClass("has-error");
            $("#celebration_date_error").html("Please select a date").slideDown("fast");
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
        formatSubmit: 'hh:i:00',
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
      }),
      completeColour   = "#dff0d8",
      incompleteColour = "#fcf8e3",
      progressColour   = "#d9edf7",
      errorColour      = "#f2dede";

  $("#theCustomerNext").click(function() {
    if ($("select[name=existing_id]").val() !== "null") {
      $origins = $("input[name=address]").val().replace(/\ /g, "+") + "," + $("input[name=postcode]").val().replace(/\ /g, "");
      $destination = "95+Hoe+Lane,EN35SW";
      $("#theCustomer").collapse("hide");
      $("#theCake").collapse("show");
      $("#the-customer-heading").animate({backgroundColor: completeColour});
      $("#the-cake-heading").animate({backgroundColor: progressColour});
    } else {
      var phon_check = validate.phone(),
          emai_check = validate.email(),
          post_check = validate.postcode(),
          firs_check = validate.input('#first_name', '#first_name_error', 'Please enter a first name'),
          last_check = validate.input('#last_name', '#last_name_error', 'Please enter a last name'),
          addr_check = validate.input('#address', '#address_error', 'Please enter an address');
      if (phon_check && emai_check && post_check && firs_check && last_check && addr_check) { 
        $origins = $("input[name=address]").val().replace(/\ /g, "+") + "," + $("input[name=postcode]").val().replace(/\ /g, "");
        $destination = "95+Hoe+Lane,EN35SW";
        $("#theCustomer").collapse("hide");
        $("#theCake").collapse("show");
        $("#the-customer-heading").animate({backgroundColor: completeColour});
        $("#the-cake-heading").animate({backgroundColor: progressColour});
      } else {
        $("#the-customer-heading").animate({backgroundColor: errorColour});
      }
    }
  });

  $("#theCakePrevious").click(function() {
    $("#theCake").collapse("hide");
    $("#theCustomer").collapse("show");
    $("#the-cake-heading").animate({backgroundColor: incompleteColour});
    $("#the-customer-heading").animate({backgroundColor: progressColour});
  });

  $("#theCakeNext").click(function() {
    var $plac_date  = $("#placed_date_hidden"),
        $plac_time  = $("#placed_time_hidden"),
        $celeb_date = $("#celebration_date_hidden")
        $fill_check = validate.input('select[name=filling]', '#filling_error', 'Please choose a filling'),
        $deco_check = validate.input('select[name=decoration]', '#decoration_error', 'Please choose a decoration'),
        $size_check = validate.input('select[name=cake_size]', '#cake_size_error', 'Please choose a cake size'),
        $type_check = validate.input('select[name=cake_type]', '#cake_type_error', 'Please choose a cake type');
    if ($plac_date.val() != "" && $plac_time.val() != "" && $celeb_date.val() != "" && $fill_check && $deco_check && $size_check && $type_check) {
      if ($("#comments").data("required") == "true") {
        console.log("required");
        var comm_check = validate.input("textarea#comments", "#comments_error", "Please enter a comment");
        if (comm_check) {
          $("#theCake").collapse("hide");
          $("#deliveryPanel").collapse("show");
          $("#the-cake-heading").animate({backgroundColor: completeColour});
          $("#delivery-heading").animate({backgroundColor: progressColour});
        }
      } else {
        $("#theCake").collapse("hide");
        $("#deliveryPanel").collapse("show");
        $("#the-cake-heading").animate({backgroundColor: completeColour});
        $("#delivery-heading").animate({backgroundColor: progressColour});
      }
    } else {
      $("#the-cake-heading").animate({backgroundColor: errorColour});
      if ($plac_date.val() == "") {
        $plac_date.closest(".form-group").removeClass("has-success").addClass("has-error");
        $("#placed_date_error").html("Please select a date").slideDown("fast");
      }
      if ($plac_time.val() == "") {
        $plac_time.closest(".form-group").removeClass("has-success").addClass("has-error");
        $("#placed_time_error").html("Please select a time").slideDown("fast");
      }
      if ($celeb_date.val() == "") {
        $celeb_date.closest(".form-group").removeClass("has-success").addClass("has-error");
        $("#celebration_date_error").html("Please select a date").slideDown("fast");
      }
    }
  });

  $("#deliveryPrevious").click(function() {
    $("#deliveryPanel").collapse("hide");
    $("#theCake").collapse("show");
    $("#delivery-heading").animate({backgroundColor: incompleteColour});
    $("#the-cake-heading").animate({backgroundColor: progressColour});
  });

  $("#deliveryNext").click(function() {
    var $datetime_date  = $("#datetime_date_hidden"),
        $datetime_time  = $("#datetime_time_hidden"),
        $delivery_check = validate.input('select[name=delivery]', '#delivery_error', 'Please choose a delivery option');
    if ($datetime_date.val() != "" && $datetime_time.val() != "" && $delivery_check) {
      calculateOrderTotal();
    } else {
      $("#delivery-heading").animate({backgroundColor: errorColour});
      if ($datetime_date.val() == "") {
        $datetime_date.closest(".form-group").removeClass("has-success").addClass("has-error");
        $("#datetime_date_error").html("Please select a date").slideDown("fast");
      }
      if ($datetime_time.val() == "") {
        $datetime_time.closest(".form-group").removeClass("has-success").addClass("has-error");
        $("#datetime_time_error").html("Please select a time").slideDown("fast");
      }
    }
  });

  $("#reviewPrevious").click(function() {
    $("#review").collapse("hide");
    $("#deliveryPanel").collapse("show");
    $("#review-heading").animate({backgroundColor: incompleteColour});
    $("#delivery-heading").animate({backgroundColor: progressColour});
  });

  $("select[name=existing_id]").change(function() {
    if ($(this).val() !== "null"){
      $("#theCustomer").find("input")
                       .prop("disabled", "true")
                       .closest(".form-group")
                       .removeClass("has-error");
      $("#theCustomer").find(".validate-error")
                       .slideUp();
      $.ajax({
        type: 'post',
        url: "../lib/get-user-details.php",
        data: {id: $("select[name=existing_id]").val()},
        success: function(response) {
          object = JSON.parse(response);
          $("input[name=first_name]").val(object.first_name);
          $("input[name=last_name]").val(object.last_name);
          $("input[name=address]").val(object.address);
          $("input[name=postcode]").val(object.postcode);
          $("input[name=phone]").val(object.phone);
          $("input[name=email]").val(object.email);
          $("#name-review").html(object.first_name + " " + object.last_name);
          $("#address-review").html(object.address);
          $("#postcode-review").html(object.postcode);
          $("#phone-review").html(object.phone);
          $("#email-review").html(object.email);
        }
      });
    } else {
      $("#theCustomer").find("input").prop("disabled", false).val("");
      $("#name-review, #address-review, #postcode-review, #phone-review, #email-review").html("");
    }
  });

  $("#theCustomer").find("input").change(function() {
    $("#name-review").html($("input[name=first_name]").val() + " " + $("input[name=last_name]").val());
    $("#address-review").html($("input[name=address]").val());
    $("#postcode-review").html($("input[name=postcode]").val());
    $("#phone-review").html($("input[name=phone]").val());
    $("#email-review").html($("input[name=email]").val());
  });

  $("#add-order-form").submit(function() {
    $("#review-heading").animate({backgroundColor: completeColour});
    NProgress.configure({
      trickleRate:  0.1,
      trickleSpeed: 500
    });
    NProgress.start();
  });
});
