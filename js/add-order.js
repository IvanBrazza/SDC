$(document).ready(function() {
  calculateOrderTotal();

  $("#theCustomerNext").click(function() {
    if ($("select[name=existing_id]").val() !== "null") {
      $origins = $("input[name=address]").val().replace(/\ /g, "+") + "," + $("input[name=postcode]").val().replace(/\ /g, "");
      $destination = "95+Hoe+Lane,EN35SW";
      $("#theCustomer").collapse("hide");
      $("#theCake").collapse("show");
    } else {
      var phon_check = validate.phone(),
          emai_check = validate.email(),
          post_check = validate.postcode(),
          firs_check = validate.input('#first_name', '#first_name_error'),
          last_check = validate.input('#last_name', '#last_name_error'),
          addr_check = validate.input('#address', '#address_error');
      if (phon_check && emai_check && post_check && firs_check && last_check && addr_check) { 
        $origins = $("input[name=address]").val().replace(/\ /g, "+") + "," + $("input[name=postcode]").val().replace(/\ /g, "");
        $destination = "95+Hoe+Lane,EN35SW";
        $("#theCustomer").collapse("hide");
        $("#theCake").collapse("show");
      }
    }
  });

  $("#theCakePrevious").click(function() {
    $("#theCake").collapse("hide");
    $("#theCustomer").collapse("show");
  });

  $("#theCakeNext").click(function() {
    var plac_check = validate.placeddatetime(),
        date_check = validate.date();
    if (plac_check && date_check) {
      if ($("#comments").data("required") == "true") {
        console.log("required");
        var comm_check = validate.input("textarea#comments", "#comments_error");
        if (comm_check) {
          $("#theCake").collapse("hide");
          $("#delivery").collapse("show");
        }
      } else {
        $("#theCake").collapse("hide");
        $("#delivery").collapse("show");
      }
    }
  });

  $("#deliveryPrevious").click(function() {
    $("#delivery").collapse("hide");
    $("#theCake").collapse("show");
  });

  $("#deliveryNext").click(function() {
    var datt_check = validate.datetime();
    if (datt_check) {
      calculateDeliveryCharge();
      calculateOrderTotal();
      $("#delivery").collapse("hide");
      $("#review").collapse("show");
    }
  });

  $("#reviewPrevious").click(function() {
    $("#review").collapse("hide");
    $("#delivery").collapse("show");
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
});
