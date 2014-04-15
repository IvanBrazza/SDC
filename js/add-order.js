$(document).ready(function() {
  $("#theCustomerNext").click(function() {
    if (checkExisting()) { 
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
      $("#delivery").collapse("hide");
      $("#review").collapse("show");
    }
  });

  $("#reviewPrevious").click(function() {
    $("#review").collapse("hide");
    $("#delivery").collapse("show");
  });

  $("select[name=delivery]").change(function() {
    if ($(this).val() === "Deliver To Address" && checkExisting() == true) {
      $.ajax({
        type: 'post',
        url: "../lib/form/get-address.php",
        data: {id: $("select[name=existing_id]").val()},
        success: function(response) {
          object = JSON.parse(response);
          $origins = object.address.replace(/\ /g, "+") + "," + object.postcode.replace(/\ /g, "");
          $destination = "95+Hoe+Lane,EN35SW";
          calculateDeliveryCharge();
        }
      });
    } else if ($(this).val() === "Deliver To Address" && checkExisting() == false) {
      $origins = $("input[name=address]").val().replace(/\ /g, "+") + "," + $("input[name=postcode]").val().replace(/\ /g, "");
      $destination = "95+Hoe+Lane,EN35SW";
      calculateDeliveryCharge();
    }
  });
});
