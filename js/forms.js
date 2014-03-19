var $password_check,
    $password2_check,
    $username_check,
    $email_check,
    $input_check,
    $postcode_check,
    $phone_check,
    $price_check,
    $add_existing_check = false;

$(document).ready(function() {
  if (window.location.pathname == "/place-an-order/") {
    calculateOrderTotal();
  }

//  if (window.location.pathname != "/testimonials/" &&
//      window.location.pathname != "/login/" &&
//      window.location.pathname != "/edit-account/") {
//    $(".date").datepicker({
//      minDate: 0,
//      dateFormat: "yy-mm-dd"
//    });
//
//    $(".previous-date").datepicker({
//      dateFormat: "yy-mm-dd"
//    });
//
//    $("#datetime").datetimepicker({
//      dateFormat: "yy-mm-dd",
//      timeFormat: "HH:mm",
//      minDate: 0
//    });
//  }
            
  $("#register-form").submit(function(e) {
    // Validate the fields
    validate.password();
    validate.username();
    validate.password2();
    validate.email();
    if ($password_check && $password2_check && $username_check && $email_check) {
      loader.Show();
      // Submit the form
      $.ajax({
        type: 'post',
        url: '../lib/form/register.php',
        data: $(this).serialize(),
        success: function(response) {
          if (response === "registered") {
            window.location.href = "../verify-email/?type=register";
          } else {
            $("#error_message").html(response);
            Recaptcha.reload();
            loader.Hide();
          }
        }
      });
      e.preventDefault();
    } else {
      e.preventDefault();
      validate.password();
      validate.username();
      validate.password2();
      validate.email();
    }
  });

  $("#login-form").submit(function(e) {
    // Validate the fields
    validate.password();
    validate.username();
    if ($password_check && $username_check) {
      loader.Show();
      // Submit the form
      $.ajax({
        type: 'post',
        url: '../lib/form/login.php',
        data: $(this).serialize(),
        success: function(response) {
          object = JSON.parse(response);
          if (object.status === 'success') {
            window.location.href = "../home/";
          } else {
            if (object.status === 'Incorrect username.') {
              $("#username").closest("div.form-group")
                            .removeClass("has-success")
                            .addClass("has-error");
              $("#error_message").html(object.status);
              loader.Hide();
              $("#token").val(object.token);
            } else if (object.status === 'Incorrect password.') {
              $("#password").removeClass("valid").addClass("invalid");
              $("#error_message").html(object.status);
              loader.Hide();
              $("#token").val(object.token);
            } else if (object.status  === 'redirect') {
              window.location.href = object.redirect;
            } else {
              $("#error_message").html(object.status);
              loader.Hide();
              $("#token").val(object.token);
            }
          }
        }
      });
      e.preventDefault();
    } else {
      // Don't submit the form
      e.preventDefault();
      validate.password();
      validate.username();
    }
  });

  $("#forgot-password-form").submit(function(e) {
    // Validate the fields
    validate.email();
    if ($email_check) {
      loader.Show();
      // Submit the form
      $.ajax({
        type: 'post',
        url: '../lib/form/forgot-password.php',
        data: $(this).serialize(),
        success: function(response) {
        console.log(response);
          object = JSON.parse(response);
          if (object.status === 'success') {
            $("#email").removeClass("valid").addClass("valid");
            $("#error_message").hide();
            $("#success_message").html("Password reset. Please check your emails for a new password.");
            loader.Hide();
          } else {
            if (object.status === 'Email doesn\'t exist.') {
              $("#email").removeClass("valid").addClass("invalid");
              $("#error_message").html(object.status);
              loader.Hide();
              $("#token").val(object.token);
            } else {
              $("#error_message").html(object.status);
              loader.Hide();
              $("#token").val(object.token);
            }
          }
        }
      });
      e.preventDefault();
    } else {
      // Don't submit the form
      e.preventDefault();
      validate.email();
    }
  });

  $("#order-form").submit(function(e) {
    // Validate form fields
    validate.input('#design', '#design_error');
    validate.input('#celebration_date', '#celebration_date_error');
    validate.input('textarea#order', '#order_error');
    validate.input('#datetime', '#datetime_error');
    loader.Show();
    if (!$input_check) {
      e.preventDefault();
      validate.input('#design', '#design_error');
      validate.input('#celebration_date', '#celebration_date_error');
      validate.input('textarea#order', '#order_error');
      validate.input('#datetime', '#datetime_error');
      loader.Hide();
    }
  });

  $("#edit-account-form").submit(function(e) {
    // Validate form inputs
    validate.email();
    validate.postcode();
    validate.phone();
    validate.input('#first_name', '#first_name_error');
    validate.input('#last_name', '#last_name_error');
    validate.input('#address', '#address_error');
    if ($input_check && $phone_check && $postcode_check && $email_check) {
      loader.Show();
      // Submit the form
      $.ajax({
        type: 'post',
        url: '../lib/form/edit-account.php',
        data: $(this).serialize(),
        success: function(response) {
          if (response === "success") {
            $("#success_message").html("Account updated.");
            loader.Hide();
          } else if (response === "email-verify") {
            window.location.href = "../verify-email/?type=edit";
          } else {
            $("#error_message").html(response);
            loader.Hide();
          }
        }
      });
      e.preventDefault();
    } else {
      // Don't submit the form
      e.preventDefault();
      validate.email();
      validate.postcode();
      validate.phone();
      validate.input('#first_name', '#first_name_error');
      validate.input('#last_name', '#last_name_error');
      validate.input('#address', '#address_error');
      loader.Hide();
    }
  });
  
  $("#delivery").change(function() {
    if ($("#delivery").val() === "Collection") {
      $("#datetime-label").html("Date/Time For Collection");
      $("#datetime-label-review").html("Date/time for collection: ");
      $("#delivery-charge").hide("fast");
      $("#delivery-charge-html").html(0);
      calculateOrderTotal();
    } else {
      $("#datetime-label").html("Date/Time For Delivery");
      $("#datetime-label-review").html("Date/time for delivery: ");
      calculateDeliveryCharge($("#delivery-charge-html"));
      $("#delivery-charge").show("fast");
    }
  });

  if ($("#delivery").val() === "Deliver To Address") {
    $("#delivery-charge").show("fast");
  }

  $("#cake_size").change(function() {
    calculateOrderTotal();
  });

  $("#cake_type").change(function() {
    calculateOrderTotal();
  });

  $("#filling").change(function() {
    calculateOrderTotal();
    if ($(this).val() == "3") {
      $("#comments").data("required", "true");
    } else if ($("#decoration").val() == "6") {
      $("#comments").data("required", "true");
    } else {
      $("#comments").data("required", "false");
    }
  });

  $("#decoration").change(function() {
    calculateOrderTotal();
    if ($(this).val() == "6") {
      $("#comments").data("required", "true");
    } else if ($("#filling").val() == "3") {
      $("#comments").data("required", "true");
    } else {
      $("#comments").data("required", "false");
    }
  });

  $("#existing_id").change(function() {
    if ($("#existing_id").val() !== "null") {
      var $first_name         = $("input[name=first_name]"),
          $first_name_error   = $("#first_name_error"),
          $last_name          = $("input[name=last_name]"),
          $last_name_error    = $("#last_name_error"),
          $address            = $("input[name=address]"),
          $address_error      = $("#address_error"),
          $postcode           = $("input[name=postcode]"),
          $postcode_error     = $("#postcode_error"),
          $phone              = $("input[name=phone]"),
          $phone_error        = $("#phone_error"),
          $email              = $("input[name=email]"),
          $email_error        = $("#email-error");
      
      $first_name.removeClass("invalid");
      $first_name_error.slideUp("fast");
      $last_name.removeClass("invalid");
      $last_name_error.slideUp("fast");
      $address.removeClass("invalid");
      $address_error.slideUp("fast");
      $postcode.removeClass("invalid");
      $postcode_error.slideUp("fast");
      $phone.removeClass("invalid");
      $phone_error.slideUp("fast");
      $email.removeClass("invalid");
      $email_error.slideUp("fast");
    }
  });

  $("#add-order-form").submit(function(e) {
    if ($add_existing_check) {
      if ($input_check) {
        loader.Show();
        $.ajax({
          type: 'post',
          url: '../lib/form/add-order.php',
          data: $(this).serialize(),
          success: function(response) {
            if (response === "success") {
              window.location.href = "../all-orders/?new-order=added";
            } else {
              loader.Hide();
            }
          }
        });
        e.preventDefault();
      } else {
        e.preventDefault();
        validate.input('#order_placed', '#order_placed_error');
        validate.input('#datetime', '#datetime_error');
        validate.input('#celebration_date', 'celebration_date_error');
        validate.input('textarea#order', '#order_error');
        validate.input('#design', '#design_error');
      }
    } else {
      if ($input_check && $phone_check && $email_check && $postcode_check) {
        loader.Show();
        $.ajax({
          type: 'post',
          url: '../lib/form/add-order.php',
          data: $(this).serialize(),
          success: function(response) {
            if (response === "success") {
              window.location.href = "../all-orders/?new-order=added";
            } else {
              loader.Hide();
            }
          }
        });
        e.preventDefault();
      } else {
        e.preventDefault();
        validate.phone();
        validate.email();
        validate.postcode();
        validate.input('#first_name', '#first_name_error');
        validate.input('#last_name', '#last_name_error');
        validate.input('#address', '#address_error');
        validate.input('#order_placed', '#order_placed_error');
        validate.input('#datetime', '#datetime_error');
        validate.input('#celebration_date', 'celebration_date_error');
        validate.input('textarea#order', '#order_error');
        validate.input('#design', '#design_error');
      }
    }
  });
});

function checkExisting()
{
  if ($("#existing_id").val() !== "null"){
    $("#first_name").prop("disabled", true);
    $("#first_name").val("");
    $("#last_name").prop("disabled", true);
    $("#last_name").val("");
    $("#address").prop("disabled", true);
    $("#address").val("");
    $("#postcode").prop("disabled", true);
    $("#postcide").val("");
    $("#phone").prop("disabled", true);
    $("#phone").val("");
    $("#email").prop("disabled", true);
    $("#email").val("");
    $add_existing_check = true;
  } else {
    $("#first_name").prop("disabled", false);
    $("#last_name").prop("disabled", false);
    $("#address").prop("disabled", false);
    $("#postcode").prop("disabled", false);
    $("#phone").prop("disabled", false);
    $("#email").prop("disabled", false);
    $add_existing_check = false;
  }
}

var validate = {
  email: function() {
    var email             = $("input[name=email]").val(),
        $email            = $("input[name=email]").closest("div.form-group"),
        $email_error      = $("#email-error"),
        regex             = /^(\w+\.?\w*)@(.+){2,}\.(.+){2,}[^\.]$/;

    if (email === null) {
      $email_error.html("Please enter your email");
      $email.removeClass("has-success");
      $email.addClass("has-error");
      $email_error.slideUp("fast");
      $email_check = false;
    } else if (regex.test(email)){
      $email.removeClass("has-error");
      $email.addClass("has-success");
      $email_error.slideUp("fast");
      $email_check = true;
    } else {
      $email_error.html("Please enter a valid email");
      $email.removeClass("has-success");
      $email.addClass("has-error");
      $email_error.slideDown("fast");
      $email_check = false;
    }
  },
  password: function() {
    var password          = $("input[name=password]").val(),
        $password         = $("input[name=password]").closest("div.form-group"),
        $password_error   = $("#password-error");

    if (password === null) {
      $password_error.html("Please enter a password");
      $password.removeClass("has-success");
      $password.addClass("has-error");
      $password_error.slideDown("fast");
      $password_check = false;
    } else if (password.length < 5) {
      $password_error.html("Password must be at least 5 characters");
      $password.removeClass("has-success");
      $password.addClass("has-error");
      $password_error.slideDown("fast");
      $password_check = false;
    } else {
      $password.removeClass("has-error");
      $password.addClass("has-success");
      $password_error.slideUp("fast");
      $password_check = true;
    }
  },
  password2: function() {
    var password          = $("input[name=password]").val(),
        password2         = $("input[name=password2]").val(),
        $password2        = $("input[name=password2]").closest("div.form-group"),
        $password2_error  = $("#password2-error");

    if (password2 === null) {
      $password2_error.html("Please reenter your password");
      $password2.removeClass("has-error");
      $password2.addClass("has-success");
      $password2_error.slideDown("fast");
      $password2_check = false;
    } else if (password === password2) {
      $password2.removeClass("has-error");
      $password2.addClass("has-success");
      $password2_error.slideUp("fast");
      $password2_check = true;
    } else {
      $password2_error.html("Passwords do not match");
      $password2.removeClass("has-success");
      $password2.addClass("has-error");
      $password2_error.slideDown("fast");
      $password2_check = false;
    }
  },
  username: function() {
    var username          = $("input[name=username]").val(),
        $username         = $("input[name=username]").closest("div.form-group"),
        $username_error   = $("#username-error");

    if (username === null) {
      $username_error.html("Please enter a username");
      $username.removeClass("has-success");
      $username.addClass("has-error");
      $username_error.slideDown("fast");
      $username_check = false;
    } else if (username.length < 3) {
      $username_error.html("Username must be at least 3 characters");
      $username.removeClass("has-success");
      $username.addClass("has-error");
      $username_error.slideDown("fast");
      $username_check = false;
    } else {
      $username.removeClass("has-error");
      $username.addClass("has-success");
      $username_error.slideUp("fast");
      $username_check = true;
    }
  },
  input: function(inputParam, error) {
    var input  = $(inputParam).closest("div.form-group"),
        $input = $(inputParam).val(),
        $error = $(error);

    if ($input === "") {
      $error.html("This field cannot be blank");
      input.removeClass("has-success");
      input.addClass("has-error");
      $error.slideDown("fast");
      $input_check = false;
    } else {
      input.removeClass("has-error");
      input.addClass("has-success");
      $error.slideUp("fast");
      $input_check = true;
    }
  },
  postcode: function() {
    var postcode          = $("input[name=postcode]").val(),
        $postcode         = $("input[name=postcode]").closest("div.form-group"),
        $postcode_error   = $("#postcode_error"),
        regex             = /^[A-Za-z]{1,2}[0-9]{1,2}[A-Za-z]? ?[0-9][A-Za-z]{2}$/;

    if (postcode === null) {
      $postcode_error.html("Please enter your postcode");
      $postcode.removeClass("has-success");
      $postcode.addClass("has-error");
      $postcode_error.slideDown("fast");
      $postcode_check = false;
    } else if (regex.test(postcode)) {
      $postcode.removeClass("has-error");
      $postcode.addClass("has-success");
      $postcode_error.slideUp("fast");
      $postcode_check = true;
    } else {
      $postcode_error.html("Please enter a valid postcode");
      $postcode.removeClass("has-success");
      $postcode.addClass("has-error");
      $postcode_error.slideDown("fast");
      $postcode_check = false;
    }
  },
  phone: function() {
    var phone         = $("input[name=phone]").val(),
        $phone        = $("input[name=phone]").closest("div.form-group"),
        $phone_error  = $("#phone_error"),
        regex         = /0[ -]?[1-9](?:[ -]?\d){9}/;

    if (phone === null) {
      $phone_error.html("Please enter your phone number");
      $phone.removeClass("has-success");
      $phone.addClass("has-error");
      $phone_error.slideDown("fast");
      $phone_check = false;
    } else if (regex.test(phone)) {
      $phone.removeClass("has-error");
      $phone.addClass("has-success");
      $phone_error.slideUp("fast");
      $phone_check = true;
    } else {
      $phone_error.html("Please enter a valid phone number");
      $phone.removeClass("has-success");
      $phone.addClass("has-error");
      $phone_error.slideDown("fast");
      $phone_check = false;
    }
  }
}

function calculateOrderTotal()
{
  var $total            = $("#total-html"),
      $cake_size        = $("#cake_size").val(),
      $cake_type        = $("#cake_type").val(),
      fillingId         = $("#filling").val(),
      fillingPrice      = 0,
      decorationId      = $("#decoration").val(),
      decorationPrice   = 0,
      $base             = $("#base-price");

  if ($("#delivery-charge-html").html()) {
    var $delivery_charge = $("#delivery-charge-html").html().replace(/\u00A3/g, '');
  } else {
    var $delivery_charge = 0;
  }

  $.ajax({
    type: 'post',
    url: '../lib/get-cake.php',
    data: {size: $cake_size, type: $cake_type},
    success: function(response) {
      object = JSON.parse(response);
      if (object.status === "success") {
        $total.html(parseInt(object.price) + parseInt($delivery_charge));
        $base.html(parseInt(object.price));
      }
    }
  });

  $.ajax({
    type: 'post',
    url: '../lib/get-fillingdecor.php',
    data: {type: "filling", id: fillingId},
    success: function(response) {
      object = JSON.parse(response);
      fillingPrice = parseInt(object.price);
      $("#filling-review").html(object.name + " - &pound;" + object.price);
    }
  });

  $.ajax({
    type: 'post',
    url: '../lib/get-fillingdecor.php',
    data: {type: "decor", id: decorationId},
    success: function(response) {
      object = JSON.parse(response);
      decorationPrice = parseInt(object.price);
      $("#decoration-review").html(object.name + " - &pound;" + object.price);
    }
  });
  setTimeout(function() {
    $total.html(parseInt($total.html()) + decorationPrice + fillingPrice);
  }, 1000);
}

function calculateDeliveryCharge(original_html)
{
  var delivery_charge;

  if (original_html.html() === "" || original_html.html() === "0") {
    var $delivery_charge = original_html;
  
    var service = new google.maps.DistanceMatrixService();
    service.getDistanceMatrix(
      {
        origins: [$origins],
        destinations: [$destination],
        travelMode: google.maps.TravelMode.DRIVING,
        unitSystem: google.maps.UnitSystem.IMPERIAL
      }, callback);

      function callback(response, status) {
        var origins = response.originAddresses;
        var destinations = response.destinationAddresses;

        for (var i = 0; i < origins.length; i++) {
          var results = response.rows[i].elements;
          for (var j = 0; j < results.length; j++) {
          var element = results[j];
          var distance = element.distance.value;
        }
      }
    
      var miles = distance*0.000621371;
      miles = Math.round(miles);
      var remaining_miles = miles - 5;
      remaining_miles = Math.round(remaining_miles / 5) * 5;

      if (remaining_miles <= 0) {
        delivery_charge = 0;
      } else {
        recursiveDelivery(remaining_miles, 0, 0);
      }

      $delivery_charge.html("&pound;" + delivery_charge);
      calculateOrderTotal();
    }
  } else {
    return null;
  }

  function recursiveDelivery(miles, i, j) {
    i += 5;
    j += 3;

    if (i == 50) {
      delivery_charge = "Collection only";
      return;
    }

    if (miles == i) {
      delivery_charge = j;
      return;
    } else {
      recursiveDelivery(miles, i, j);
    }
  }
}
