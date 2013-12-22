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
  calculateOrderTotal();

  if (window.location.pathname != "/testimonials/" && window.location.pathname != "/login/") {
    $(".date").datepicker({
      minDate: 0,
      dateFormat: "yy-mm-dd"
    });

    $(".previous-date").datepicker({
      dateFormat: "yy-mm-dd"
    });

    $("#datetime").datetimepicker({
      dateFormat: "yy-mm-dd",
      timeFormat: "HH:mm",
      minDate: 0
    });
  }
            
  $("#register-form").submit(function(e) {
    // Validate the fields
    validatePassword();
    validateUsername();
    validatePassword2();
    validateEmail();
    $(".ajax-load").css("display", "inline-block");
    if ($password_check && $password2_check && $username_check && $email_check) {
      // Submit the form
      $.ajax({
        type: 'post',
        url: '../lib/form/register.php',
        data: $(this).serialize(),
        success: function(response) {
          if (response === "registered") {
            window.location.href = "../verify-email/";
          } else {
            $("#error_message").html(response);
            $(".ajax-load").hide();
          }
        }
      });
      e.preventDefault();
    } else {
      e.preventDefault();
      validatePassword();
      validateUsername();
      validatePassword2();
      validateEmail();
    }
  });

  $("#login-form").submit(function(e) {
    // Validate the fields
    validatePassword();
    validateUsername();
    $(".ajax-load").css("display", "inline-block");
    if ($password_check && $username_check) {
      // Submit the form
      $.ajax({
        type: 'post',
        url: '../lib/form/login.php',
        data: $(this).serialize(),
        success: function(response) {
          if (response === 'logged-in') {
            window.location.href = "../home/";
          } else {
            $("#error_message").html(response);
            $(".ajax-load").hide();
          }
        }
      });
      e.preventDefault();
    } else {
      // Don't submit the form
      e.preventDefault();
      validatePassword();
      validateUsername();
    }
  });

  $("#order-form").submit(function(e) {
    // Validate form fields
    validateInput('#design', '#design_error');
    validateInput('#celebration_date', '#celebration_date_error');
    validateInput('textarea#order', '#order_error');
    validateInput('#datetime', '#datetime_error');
    $(".ajax-load").css("display", "inline-block");
    if (!$input_check) {
      e.preventDefault();
      validateInput('#design', '#design_error');
      validateInput('#celebration_date', '#celebration_date_error');
      validateInput('textarea#order', '#order_error');
      validateInput('#datetime', '#datetime_error');
      $(".ajax-load").hide();
    }
  });

  $("#delete_testimonial").submit(function(e) {
    $(".ajax-load").css("display", "inline-block");
    var $form = $(this);
    $.ajax({
      type: 'post',
      url: '../lib/delete-testimonial.php',
      data: $(this).serialize(),
      success: function(response) {
        if (response === 'success') {
          $form.closest("div").remove();
        } else {
          $("#error_message").html(response);
        }
      }
    });
    e.preventDefault();
  });

  $("#edit-account-form").submit(function(e) {
    // Validate form inputs
    validateEmail();
    validatePostcode();
    validatePhone();
    validateInput('#first_name', '#first_name_error');
    validateInput('#last_name', '#last_name_error');
    validateInput('#address', '#address_error');
    $(".ajax-load").css("display", "inline-block");
    if ($input_check && $phone_check && $postcode_check && $email_check) {
      // Submit the form
      $.ajax({
        type: 'post',
        url: '../lib/form/edit-account.php',
        data: $(this).serialize(),
        success: function(response) {
          if (response === "success") {
            $("#success_message").html("Account updated.");
            $(".ajax-load").hide();
          } else if (response === "email-verify") {
            window.location.href = "../verify-email/?type=edit";
          } else {
            $("#error_message").html(response);
            $(".ajax-load").hide();
          }
        }
      });
      e.preventDefault();
    } else {
      // Don't submit the form
      e.preventDefault();
      validateEmail();
      validatePostcode();
      validatePhone();
      validateInput('#first_name', '#first_name_error');
      validateInput('#last_name', '#last_name_error');
      validateInput('#address', '#address_error');
      $(".ajax-load").hide();
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
    $(".ajax-load").css("display", "inline-block");
    if ($add_existing_check) {
      if ($input_check) {
        $.ajax({
          type: 'post',
          url: '../lib/form/add-order.php',
          data: $(this).serialize(),
          success: function(response) {
            if (response === "success") {
              window.location.href = "../all-orders/?new-order=added";
            } else {
              $(".ajax-load").hide();
            }
          }
        });
        e.preventDefault();
      } else {
        e.preventDefault();
        validateInput('#order_placed', '#order_placed_error');
        validateInput('#datetime', '#datetime_error');
        validateInput('#celebration_date', 'celebration_date_error');
        validateInput('textarea#order', '#order_error');
        validateInput('#design', '#design_error');
      }
    } else {
      if ($input_check && $phone_check && $email_check && $postcode_check) {
        $.ajax({
          type: 'post',
          url: '../lib/form/add-order.php',
          data: $(this).serialize(),
          success: function(response) {
            if (response === "success") {
              window.location.href = "../all-orders/?new-order=added";
            } else {
              $(".ajax-load").hide();
            }
          }
        });
        e.preventDefault();
      } else {
        e.preventDefault();
        validatePhone();
        validateEmail();
        validatePostcode();
        validateInput('#first_name', '#first_name_error');
        validateInput('#last_name', '#last_name_error');
        validateInput('#address', '#address_error');
        validateInput('#order_placed', '#order_placed_error');
        validateInput('#datetime', '#datetime_error');
        validateInput('#celebration_date', 'celebration_date_error');
        validateInput('textarea#order', '#order_error');
        validateInput('#design', '#design_error');
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

function validateEmail()
{
  var email             = $("input[name=email]").val(),
      $email            = $("input[name=email]"),
      $email_error      = $("#email-error");

  if (email === null) {
    $email_error.html("Please enter your email");
    $email.removeClass("invalid");
    $email.addClass("valid");
    $email.effect("shake", {}, 500);
    $email_error.slideUp("fast");
    $email_check = false;
  } else if (/^(\w+)@(.+){2,}\.(.+){2,}$/.test(email)){
    $email.removeClass("invalid");
    $email.addClass("valid");
    $email_error.slideUp("fast");
    $email_check = true;
  } else {
    $email_error.html("Please enter a valid email");
    $email.removeClass("valid");
    $email.addClass("invalid");
    $email.effect("shake", {}, 500);
    $email_error.slideDown("fast");
    $email_check = false;
  }
}

function validatePassword()
{
  var password          = $("input[name=password]").val(),
      $password         = $("input[name=password]"),
      $password_error   = $("#password-error");

  if (password === null) {
    $password_error.html("Please enter a password");
    $password.removeClass("valid");
    $password.addClass("invalid");
    $passowrd.effect("shake", {}, 500);
    $password_error.slideDown("fast");
    $password_check = false;
  } else if (password.length < 5) {
    $password_error.html("Password must be at least 5 characters");
    $password.removeClass("valid");
    $password.addClass("invalid");
    $password.effect("shake", {}, 500);
    $password_error.slideDown("fast");
    $password_check = false;
  } else {
    $password.removeClass("invalid");
    $password.addClass("valid");
    $password_error.slideUp("fast");
    $password_check = true;
  }
}

function validatePassword2()
{
  var password          = $("input[name=password]").val(),
      password2         = $("input[name=password2]").val(),
      $password2        = $("input[name=password2]"),
      $password2_error  = $("#password2-error");

  if (password2 === null) {
    $password2_error.html("Please reenter your password");
    $password2.removeClass("invalid");
    $password2.addClass("valid");
    $password2.effect("shake", {}, 500);
    $password2_error.slideDown("fast");
    $password2_check = false;
  } else if (password === password2) {
    $password2.removeClass("invalid");
    $password2.addClass("valid");
    $password2_error.slideUp("fast");
    $password2_check = true;
  } else {
    $password2_error.html("Passwords do not match");
    $password2.removeClass("valid");
    $password2.addClass("invalid");
    $password2.effect("shake", {}, 500);
    $password2_error.slideDown("fast");
    $password2_check = false;
  }
}

function validateUsername()
{
  var username          = $("input[name=username]").val(),
      $username         = $("input[name=username]"),
      $username_error   = $("#username-error");

  if (username === null) {
    $username_error.html("Please enter a username");
    $username.removeClass("valid");
    $username.addClass("invalid");
    $username.effect("shake", {}, 500);
    $username_error.slideDown("fast");
    $username_check = false;
  } else if (username.length < 3) {
    $username_error.html("Username must be at least 3 characters");
    $username.removeClass("valid");
    $username.addClass("invalid");
    $username.effect("shake", {}, 500);
    $username_error.slideDown("fast");
    $username_check = false;
  } else {
    $username.removeClass("invalid");
    $username.addClass("valid");
    $username_error.slideUp("fast");
    $username_check = true;
  }
}

function validateInput(input, error)
{
  var input  = $(input),
      $input = $(input).val(),
      $error = $(error);

  if ($input === "") {
    $error.html("This field cannot be blank");
    input.removeClass("valid");
    input.addClass("invalid");
    input.effect("shake", {}, 500);
    $error.slideDown("fast");
    $input_check = false;
  } else {
    input.removeClass("invalid");
    input.addClass("valid");
    $error.slideUp("fast");
    $input_check = true;
  }
}

function validatePostcode()
{
  var postcode          = $("input[name=postcode]").val(),
      $postcode         = $("input[name=postcode]"),
      $postcode_error   = $("#postcode_error");

  if (postcode === null) {
    $postcode_error.html("Please enter your postcode");
    $postcode.removeClass("valid");
    $postcode.addClass("invalid");
    $postcode.effect("shake", {}, 500);
    $postcode_error.slideDown("fast");
    $postcode_check = false;
  } else if (/^[A-Za-z]{1,2}[0-9]{1,2}[A-Za-z]? ?[0-9][A-Za-z]{2}$/.test(postcode)) {
    $postcode.removeClass("invalid");
    $postcode.addClass("valid");
    $postcode_error.slideUp("fast");
    $postcode_check = true;
  } else {
    $postcode_error.html("Please enter a valid postcode");
    $postcode.removeClass("valid");
    $postcode.addClass("invalid");
    $postcode.effect("shake", {}, 500);
    $postcode_error.slideDown("fast");
    $postcode_check = false;
  }
}

function validatePhone()
{
  var phone         = $("input[name=phone]").val(),
      $phone        = $("input[name=phone]"),
      $phone_error  = $("#phone_error");

  if (phone === null) {
    $phone_error.html("Please enter your phone number");
    $phone.removeClass("valid");
    $phone.addClass("invalid");
    $phone.effect("shake", {}, 500);
    $phone_error.slideDown("fast");
    $phone_check = false;
  } else if (/0[ -]?[1-9](?:[ -]?\d){9}/.test(phone)) {
    $phone.removeClass("invalid");
    $phone.addClass("valid");
    $phone_error.slideUp("fast");
    $phone_check = true;
  } else {
    $phone_error.html("Please enter a valid phone number");
    $phone.removeClass("valid");
    $phone.addClass("invalid");
    $phone.effect("shake", {}, 500);
    $phone_error.slideDown("fast");
    $phone_check = false;
  }
}

function calculateOrderTotal()
{
  var $total         = $("#total-html"),
      $cake_size     = $("#cake_size").val(),
      $cake_type     = $("#cake_type").val(),
      $base          = $("#base-price");

  if ($("#delivery-charge-html").html()) {
    var $delivery_charge = $("#delivery-charge-html").html().replace(/\u00A3/g, '');
  } else {
    var $delivery_charge = 0;
  }

  if ($cake_size === '6"') {
    if ($cake_type === "Sponge"){
      $total.html(25 + parseInt($delivery_charge));
      $base.html(25);
    } else if ($cake_type === "Marble"){
      $total.html(30 + parseInt($delivery_charge));
      $base.html(30);
    } else if ($cake_type === "Chocolate") {
      $total.html(32 + parseInt($delivery_charge));
      $base.html(32);
    } else if ($cake_type === "Fruit"){
      $total.html(35 + parseInt($delivery_charge));
      $base.html(35);
    }
  } else if ($cake_size === '8"') {
    if ($cake_type === "Sponge"){
      $total.html(30 + parseInt($delivery_charge));
      $base.html(30);
    } else if ($cake_type === "Marble"){
      $total.html(35 + parseInt($delivery_charge));
      $base.html(35);
    } else if ($cake_type === "Chocolate") {
      $total.html(37 + parseInt($delivery_charge));
      $base.html(37);
    } else if ($cake_type === "Fruit"){
      $total.html(45 + parseInt($delivery_charge));
      $base.html(45);
    }
  } else if ($cake_size === '10"') {
    if ($cake_type === "Sponge"){
      $total.html(40 + parseInt($delivery_charge));
      $base.html(40);
    } else if ($cake_type === "Marble"){
      $total.html(45 + parseInt($delivery_charge));
      $base.html(45);
    } else if ($cake_type === "Chocolate") {
      $total.html(47 + parseInt($delivery_charge));
      $base.html(47);
    } else if ($cake_type === "Fruit"){
      $total.html(60 + parseInt($delivery_charge));
      $base.html(60);
    }
  } else if ($cake_size === '12"') {
    if ($cake_type === "Sponge"){
      $total.html(60 + parseInt($delivery_charge));
      $base.html(60);
    } else if ($cake_type === "Marble"){
      $total.html(65 + parseInt($delivery_charge));
      $base.html(65);
    } else if ($cake_type === "Chocolate") {
      $total.html(80 + parseInt($delivery_charge));
      $base.html(80);
    } else if ($cake_type === "Fruit"){
      $total.html(85 + parseInt($delivery_charge));
      $base.html(85);
    }
  } else if ($cake_size === '14"') {
    if ($cake_type === "Sponge"){
      $total.html(75 + parseInt($delivery_charge));
      $base.html(75);
    } else if ($cake_type === "Marble"){
      $total.html(80 + parseInt($delivery_charge));
      $base.html(80);
    } else if ($cake_type === "Chocolate") {
      $total.html(84 + parseInt($delivery_charge));
      $base.html(84);
    } else if ($cake_type === "Fruit"){
      $total.html(125 + parseInt($delivery_charge));
      $base.html(125);
    }
  }
}

function calculateDeliveryCharge(original_html)
{
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
        var delivery_charge = 0;
      } else {
        for (var i = 5, j = 3; i <= 50; i++, j=j+3) {
          if (remaining_miles === i) {
            var delivery_charge = j;
            break;
          } else if (i === 50) {
            var delivery_charge = "Collection only";
          }
        }
      }

      $delivery_charge.html("&pound;" + delivery_charge);
      calculateOrderTotal();
    }
  } else {
    return null;
  }
}
