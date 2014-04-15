$(document).ready(function() {
  if (window.location.pathname == "/place-an-order/") {
    calculateOrderTotal();
  }

  $("#register-form").submit(function(e) {
    // Validate the fields
    var pass_check = validate.password(),
        user_check = validate.username(),
        pas2_check = validate.password2(),
        emai_check = validate.email();
    if (pass_check && pas2_check && user_check && emai_check) {
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
          }
        }
      });
      e.preventDefault();
    }
  });

  $("#login-form").submit(function(e) {
    // Validate the fields
    var user_check = validate.username(),
        pass_check = validate.password();
    if (user_check && pass_check) {
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
              $("#error_message").html("<span class='glyphicon glyphicon-remove-circle'></span>" + object.status).show();
              $("#token").val(object.token);
            } else if (object.status === 'Incorrect password.') {
              $("#password").closest(".form-group")
                            .removeClass("has-success")
                            .addClass("has-error");
              $("#error_message").html("<span class='glyphicon glyphicon-remove-circle'></span>" + object.status).show();
              $("#token").val(object.token);
            } else if (object.status  === 'redirect') {
              window.location.href = object.redirect;
            } else {
              $("#error_message").html("<span class='glyphicon glyphicon-remove-circle'></span>" + object.status).show();
              $("#token").val(object.token);
            }
          }
        }
      });
    }
    e.preventDefault();
  });

  $("#forgot-password-form").submit(function(e) {
    // Validate the fields
    var emai_check = validate.email();
    if (emai_check) {
      // Submit the form
      $.ajax({
        type: 'post',
        url: '../lib/form/forgot-password.php',
        data: $(this).serialize(),
        success: function(response) {
        console.log(response);
          object = JSON.parse(response);
          if (object.status === 'success') {
            $("#email").closest(".form-group")
                       .removeClass("has-error")
                       .addClass("has-success");
            $("#error_message").hide();
            $("#success_message").html("Password reset. Please check your emails for a new password.");
          } else {
            if (object.status === 'Email doesn\'t exist.') {
              $("#email").closest(".form-group")
                         .removeClass("has-success")
                         .addClass("has-error");
              $("#error_message").html(object.status);
              $("#token").val(object.token);
            } else {
              $("#error_message").html(object.status);
              $("#token").val(object.token);
            }
          }
        }
      });
    }
    e.preventDefault();
  });

  $("#edit-account-form").submit(function(e) {
    // Validate form inputs
    var emai_check = validate.email(),
        post_check = validate.postcode(),
        phon_check = validate.phone(),
        firs_check = validate.input('#first_name', '#first_name_error'),
        last_check = validate.input('#last_name', '#last_name_error'),
        addr_check = validate.input('#address', '#address_error');

    if ($("#password").val() == "") {
      if (firs_check && phon_check && post_check && emai_check && last_check && addr_check) {
        // Submit the form
        $.ajax({
          type: 'post',
          url: '../lib/form/edit-account.php',
          data: $(this).serialize(),
          success: function(response) {
            if (response === "success") {
              $("#success_message").html("Account updated.").show();
            } else if (response === "email-verify") {
              window.location.href = "../verify-email/?type=edit";
            } else {
              $("#error_message").html(response);
            }
          }
        });
      }
    } else {
      var pass_check = validate.password();
      if (firs_check && phon_check && post_check && emai_check && last_check && addr_check && pass_check) {
        // Submit the form
        $.ajax({
          type: 'post',
          url: '../lib/form/edit-account.php',
          data: $(this).serialize(),
          success: function(response) {
            if (response === "success") {
              $("#success_message").html("Account updated.").show();
            } else if (response === "email-verify") {
              window.location.href = "../verify-email/?type=edit";
            } else {
              $("#error_message").html(response);
            }
          }
        });
      }
    }
    e.preventDefault();
  });
  
  $("select[name=delivery]").change(function() {
    if ($("select[name=delivery]").val() === "Collection") {
      $("#datetime-label").html("Date/Time For Collection");
      $("#datetime-label-review").html("Date/time for collection: ");
      $("#delivery-charge").hide("fast");
      $("#delivery-charge-html").html(0);
      calculateOrderTotal();
    } else {
      $("#datetime-label").html("Date/Time For Delivery");
      $("#datetime-label-review").html("Date/time for delivery: ");
      $("#delivery-charge").show("fast");
    }
    $("#delivery-review").html($(this).val());
  });

  $("#date").find("select").change(function() {
    $("#celebration-date-review").html($("select[name=date_year]").val() + "/" +
                                       $("select[name=date_month]").val() + "/" +
                                       $("select[name=date_day]").val());
  });

  $("#datetime_date, #datetime_time").find("select").change(function() {
    $("#datetime-review").html($("select[name=datetime_year]").val() + "/" +
                               $("select[name=datetime_month]").val() + "/" +
                               $("select[name=datetime_day]").val() + " " +
                               $("select[name=datetime_hour]").val() + ":" +
                               $("select[name=datetime_minute]").val());
  });

  $("select[name=cake_size], select[name=cake_type]").change(function() {
    calculateOrderTotal();
  });

  $("select[name=filling]").change(function() {
    calculateOrderTotal();
    if ($(this).val() == "3") {
      $("#comments").data("required", "true");
    } else if ($("#decoration").val() == "6") {
      $("#comments").data("required", "true");
    } else {
      $("#comments").data("required", "false");
    }
  });

  $("select[name=decoration]").change(function() {
    calculateOrderTotal();
    if ($(this).val() == "6") {
      $("#comments").data("required", "true");
    } else if ($("#filling").val() == "3") {
      $("#comments").data("required", "true");
    } else {
      $("#comments").data("required", "false");
    }
  });

  $("select[name=existing_id]").change(function() {
    if ($(this).val() !== "null") {
      $("input[name=first_name],input[name=last_name],input[name=address],input[name=postcode],input[name=phone],input[name=email]").closest(".form-group").removeClass("has-error");
      $("#first_name_error,#last_name_error,#address_error,#postcode_error,#phone_error,#email-error").slideUp();
    }
  });

  $("#add-order-form").submit(function(e) {
    var plac_check = validate.placeddatetime(),
        datt_check = validate.datetime(),
        date_check = validate.date();
    if (checkExisting()) {
      if (plac_check && datt_check && date_check) {
        $.ajax({
          type: 'post',
          url: '../lib/form/add-order.php',
          data: $(this).serialize(),
          success: function(response) {
            if (response === "success") {
              window.location.href = "../all-orders/?new-order=added";
            } else {
            }
          }
        });
      }
    } else {
      var phon_check = validate.phone(),
          emai_check = validate.email(),
          post_check = validate.postcode(),
          firs_check = validate.input('#first_name', '#first_name_error'),
          last_check = validate.input('#last_name', '#last_name_error'),
          addr_check = validate.input('#address', '#address_error');
      if (phon_check && emai_check && post_check && firs_check && last_check && addr_check && plac_check && datt_check && date_check) {
        $.ajax({
          type: 'post',
          url: '../lib/form/add-order.php',
          data: $(this).serialize(),
          success: function(response) {
            if (response === "success") {
              window.location.href = "../all-orders/?new-order=added";
            } else {
            }
          }
        });
      }
    }
    e.preventDefault();
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
    return true;
  } else {
    $("#first_name").prop("disabled", false);
    $("#last_name").prop("disabled", false);
    $("#address").prop("disabled", false);
    $("#postcode").prop("disabled", false);
    $("#phone").prop("disabled", false);
    $("#email").prop("disabled", false);
    return false;
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
      return false;
    } else if (regex.test(email)){
      $email.removeClass("has-error");
      $email.addClass("has-success");
      $email_error.slideUp("fast");
      return true;
    } else {
      $email_error.html("Please enter a valid email");
      $email.removeClass("has-success");
      $email.addClass("has-error");
      $email_error.slideDown("fast");
      return false;
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
      return false;
    } else if (password.length < 5) {
      $password_error.html("Password must be at least 5 characters");
      $password.removeClass("has-success");
      $password.addClass("has-error");
      $password_error.slideDown("fast");
      return false;
    } else {
      $password.removeClass("has-error");
      $password.addClass("has-success");
      $password_error.slideUp("fast");
      return true;
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
      return false;
    } else if (password === password2) {
      $password2.removeClass("has-error");
      $password2.addClass("has-success");
      $password2_error.slideUp("fast");
      return true;
    } else {
      $password2_error.html("Passwords do not match");
      $password2.removeClass("has-success");
      $password2.addClass("has-error");
      $password2_error.slideDown("fast");
      return false;
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
      return false;
    } else if (username.length < 3) {
      $username_error.html("Username must be at least 3 characters");
      $username.removeClass("has-success");
      $username.addClass("has-error");
      $username_error.slideDown("fast");
      return false;
    } else {
      $username.removeClass("has-error");
      $username.addClass("has-success");
      $username_error.slideUp("fast");
      return true;
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
      return false;
    } else {
      input.removeClass("has-error");
      input.addClass("has-success");
      $error.slideUp("fast");
      return true;
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
      return false;
    } else if (regex.test(postcode)) {
      $postcode.removeClass("has-error");
      $postcode.addClass("has-success");
      $postcode_error.slideUp("fast");
      return true;
    } else {
      $postcode_error.html("Please enter a valid postcode");
      $postcode.removeClass("has-success");
      $postcode.addClass("has-error");
      $postcode_error.slideDown("fast");
      return false;
    }
  },
  phone: function() {
    var phone         = $("input[name=phone]").val(),
        $phone        = $("input[name=phone]").closest("div.form-group"),
        $phone_error  = $("#phone_error"),
        regex         = /^0[ -]?[1-9](?:[ -]?\d){9}$/;

    if (phone === null) {
      $phone_error.html("Please enter your phone number");
      $phone.removeClass("has-success");
      $phone.addClass("has-error");
      $phone_error.slideDown("fast");
      return false;
    } else if (regex.test(phone)) {
      $phone.removeClass("has-error");
      $phone.addClass("has-success");
      $phone_error.slideUp("fast");
      return true;
    } else {
      $phone_error.html("Please enter a valid phone number");
      $phone.removeClass("has-success");
      $phone.addClass("has-error");
      $phone_error.slideDown("fast");
      return false;
    }
  },
  date: function() {
    var date_day     = $("select[name=date_day]").val(),
        date_month   = $("select[name=date_month]").val(),
        date_year    = $("select[name=date_year]").val(),
        $group       = $("select[name=date_day]").closest("div.form-group"),
        $date_error  = $("#date_error")

    if (date_day === "Day" || date_month === "Month" || date_year === "Year") {
      $date_error.html("Please select a date").slideDown("fast");
      $group.removeClass("has-success");
      $group.addClass("has-error");
      return false;
    } else {
      $date_error.slideUp("fast");
      $group.addClass("has-success");
      $group.removeClass("has-error");
      return true;
    }
  },
  datetime: function() {
    var datetime_day      = $("select[name=datetime_day]").val(),
        datetime_month    = $("select[name=datetime_month]").val(),
        datetime_year     = $("select[name=datetime_year]").val(),
        datetime_hour     = $("select[name=datetime_hour]").val(),
        datetime_minute   = $("select[name=datetime_minute]").val(),
        $group            = $("select[name=datetime_day]").closest("div.form-group"),
        $group2           = $("select[name=datetime_hour]").closest("div.form-group"),
        $datetime_error   = $("#datetime_error")

    if (datetime_day === "Day" || datetime_month === "Month" || datetime_year === "Year" || datetime_hour === "Hour" || datetime_minute === "Minute") {
      $datetime_error.html("Please select a date and time").slideDown("fast");
      $group.removeClass("has-success");
      $group.addClass("has-error");
      $group2.removeClass("has-success");
      $group2.addClass("has-error");
      return false;
    } else {
      $datetime_error.slideUp("fast");
      $group.addClass("has-success");
      $group.removeClass("has-error");
      $group2.addClass("has-success");
      $group2.removeClass("has-error");
      return true;
    }
  },
  placeddatetime: function() {
    var placed_day      = $("select[name=placed_day]").val(),
        placed_month    = $("select[name=placed_month]").val(),
        placed_year     = $("select[name=placed_year]").val(),
        placed_hour     = $("select[name=placed_hour]").val(),
        placed_minute   = $("select[name=placed_minute]").val(),
        $group          = $("select[name=placed_day]").closest("div.form-group"),
        $group2         = $("select[name=placed_hour]").closest("div.form-group"),
        $placed_error   = $("#placed_error")

    if (placed_day === "Day" || placed_month === "Month" || placed_year === "Year" || placed_hour === "Hour" || placed_minute === "Minute") {
      $placed_error.html("Please select a date and time").slideDown("fast");
      $group.removeClass("has-success");
      $group.addClass("has-error");
      $group2.removeClass("has-success");
      $group2.addClass("has-error");
      return false;
    } else {
      $placed_error.slideUp("fast");
      $group.addClass("has-success");
      $group.removeClass("has-error");
      $group2.addClass("has-success");
      $group2.removeClass("has-error");
      return true;
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
        $("#cake-size-review").html($cake_size);
        $("#cake-type-review").html($cake_type);
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

function calculateDeliveryCharge()
{
  var delivery_charge,
      original_html = $("#delivery-charge-html");

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
