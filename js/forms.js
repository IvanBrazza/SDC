$(document).ready(function() {
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
                            .addClass("has-error")
                            .find(".input-group-addon")
                            .html("<span class='glyphicon glyphicon-remove'></span>");
              $("#error_message").html("<span class='glyphicon glyphicon-remove-circle'></span>" + object.status).show();
              $("#token").val(object.token);
            } else if (object.status === 'Incorrect password.') {
              $("#password").closest(".form-group")
                            .removeClass("has-success")
                            .addClass("has-error")
                            .find(".input-group-addon")
                            .html("<span class='glyphicon glyphicon-remove'></span>");
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
                       .addClass("has-success")
                       .find(".input-group-addon")
                       .html("<span class='glyphicon glyphicon-ok'></span>");
            $("#error_message").hide();
            $("#success_message").html("<span class='glyphicon glyphicon-ok'></span>   Password reset. Please check your emails for a new password.").show();
          } else {
            if (object.status === 'Email doesn\'t exist.') {
              $("#email").closest(".form-group")
                         .removeClass("has-success")
                         .addClass("has-error")
                         .find(".input-group-addon")
                         .html("<span class='glyphicon glyphicon-remove'></span>");
              $("#error_message").html("<span class='glyphicon glyphicon-remove-circle'></span>   " + object.status).show();
              $("#token").val(object.token);
            } else {
              $("#error_message").html("<span class='glyphicon glyphicon-remove-circle'></span>   " + object.status).show();
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

  $("select[name=cake_size],select[name=cake_type],select[name=filling],select[name=decoration]").change(function() {
    calculateOrderTotal();
  });

  $("select[name=filling]").change(function() {
    if ($(this).val() == "3") {
      $("#comments").data("required", "true");
    } else if ($("#decoration").val() == "6") {
      $("#comments").data("required", "true");
    } else {
      $("#comments").data("required", "false");
    }
  });

  $("select[name=decoration]").change(function() {
    if ($(this).val() == "6") {
      $("#comments").data("required", "true");
    } else if ($("#filling").val() == "3") {
      $("#comments").data("required", "true");
    } else {
      $("#comments").data("required", "false");
    }
  });
});

var validate = {
  email: function() {
    var email             = $("input[name=email]").val(),
        $email            = $("input[name=email]").closest("div.form-group"),
        $email_error      = $("#email-error"),
        regex             = /^(\w+\.?\w*)@(.+){2,}\.(.+){2,}[^\.]$/;

    if (email == "") {
      $email_error.html("Please enter your email")
                  .slideDown("fast");
      $email.removeClass("has-success")
            .addClass("has-error")
            .find(".input-group-addon")
            .html("<span class='glyphicon glyphicon-remove'></span>");
      return false;
    } else if (regex.test(email)){
      $email_error.slideUp("fast");
      $email.removeClass("has-error")
            .addClass("has-success")
            .find(".input-group-addon")
            .html("<span class='glyphicon glyphicon-ok'></span>");
      return true;
    } else {
      $email_error.html("Please enter a valid email")
                  .slideDown("fast");
      $email.removeClass("has-success")
            .addClass("has-error")
            .find(".input-group-addon")
            .html("<span class='glyphicon glyphicon-remove'></span>");
      return false;
    }
  },
  password: function() {
    var password          = $("input[name=password]").val(),
        $password         = $("input[name=password]").closest("div.form-group"),
        $password_error   = $("#password-error");

    if (password == "") {
      $password_error.html("Please enter a password")
                     .slideDown("fast");
      $password.removeClass("has-success")
               .addClass("has-error")
               .find(".input-group-addon")
               .html("<span class='glyphicon glyphicon-remove'></span>");
      return false;
    } else if (password.length < 5) {
      $password_error.html("Password must be at least 5 characters")
                     .slideDown("fast");
      $password.removeClass("has-success")
               .addClass("has-error")
               .find(".input-group-addon")
               .html("<span class='glyphicon glyphicon-remove'></span>");
      return false;
    } else {
      $password_error.slideUp("fast");
      $password.removeClass("has-error")
               .addClass("has-success")
               .find(".input-group-addon")
               .html("<span class='glyphicon glyphicon-ok'></span>");
      return true;
    }
  },
  password2: function() {
    var password          = $("input[name=password]").val(),
        password2         = $("input[name=password2]").val(),
        $password2        = $("input[name=password2]").closest("div.form-group"),
        $password2_error  = $("#password2-error");

    if (password2 == "") {
      $password2_error.html("Please reenter your password")
                      .slideDown("fast");
      $password2.removeClass("has-success")
                .addClass("has-error")
                .find(".input-group-addon")
                .html("<span class='glyphicon glyphicon-remove'></span>");
      return false;
    } else if (password === password2) {
      $password2_error.slideUp("fast");
      $password2.removeClass("has-error")
                .addClass("has-success")
                .find(".input-group-addon")
                .html("<span class='glyphicon glyphicon-ok'></span>");
      return true;
    } else {
      $password2_error.html("Passwords do not match")
                      slideDown("fast");
      $password2.removeClass("has-success")
                .addClass("has-error")
                .find(".input-group-addon")
                .html("<span class='glyphicon glyphicon-remove'></span>");
      return false;
    }
  },
  username: function() {
    var username          = $("input[name=username]").val(),
        $username         = $("input[name=username]").closest("div.form-group"),
        $username_error   = $("#username-error");

    if (username === null) {
      $username_error.html("Please enter a username")
                     .slideDown("fast");
      $username.removeClass("has-success")
               .addClass("has-error")
               .find(".input-group-addon")
               .html("<span class='glyphicon glyphicon-remove'></span>");
      return false;
    } else if (username.length < 3) {
      $username_error.html("Username must be at least 3 characters")
                     .slideDown("fast");
      $username.removeClass("has-success")
               .addClass("has-error")
               .find(".input-group-addon")
               .html("<span class='glyphicon glyphicon-remove'></span>");
      return false;
    } else {
      $username_error.slideUp("fast");
      $username.removeClass("has-error")
               .addClass("has-success")
               .find(".input-group-addon")
               .html("<span class='glyphicon glyphicon-ok'></span>");
      return true;
    }
  },
  input: function(inputParam, error) {
    var input  = $(inputParam).closest("div.form-group"),
        $input = $(inputParam).val(),
        $error = $(error);

    if ($input === "") {
      $error.html("This field cannot be blank")
            .slideDown("fast");
      input.removeClass("has-success")
           .addClass("has-error")
           .find(".input-group-addon")
           .html("<span class='glyphicon glyphicon-remove'></span>");
      return false;
    } else {
      $error.slideUp("fast");
      input.removeClass("has-error")
           .addClass("has-success")
           .find(".input-group-addon")
           .html("<span class='glyphicon glyphicon-ok'></span>");
      return true;
    }
  },
  postcode: function() {
    var postcode          = $("input[name=postcode]").val(),
        $postcode         = $("input[name=postcode]").closest("div.form-group"),
        $postcode_error   = $("#postcode_error"),
        regex             = /^[A-Za-z]{1,2}[0-9]{1,2}[A-Za-z]? ?[0-9][A-Za-z]{2}$/;

    if (postcode == "") {
      $postcode_error.html("Please enter your postcode")
                     .slideDown("fast");
      $postcode.removeClass("has-success")
               .addClass("has-error")
               .find(".input-group-addon")
               .html("<span class='glyphicon glyphicon-remove'></span>");
      return false;
    } else if (regex.test(postcode)) {
      $postcode_error.slideUp("fast");
      $postcode.removeClass("has-error")
               .addClass("has-success")
               .find(".input-group-addon")
               .html("<span class='glyphicon glyphicon-ok'></span>");
      return true;
    } else {
      $postcode_error.html("Please enter a valid postcode")
                     .slideDown("fast");
      $postcode.removeClass("has-success")
               .addClass("has-error")
               .find(".input-group-addon")
               .html("<span class='glyphicon glyphicon-remove'></span>");
      return false;
    }
  },
  phone: function() {
    var phone         = $("input[name=phone]").val(),
        $phone        = $("input[name=phone]").closest("div.form-group"),
        $phone_error  = $("#phone_error"),
        regex         = /^0[ -]?[1-9](?:[ -]?\d){9}$/;

    if (phone === null) {
      $phone_error.html("Please enter your phone number")
                  .slideDown("fast");
      $phone.removeClass("has-success")
            .addClass("has-error")
            .find(".input-group-addon")
            .html("<span class='glyphicon glyphicon-remove'></span>");
      return false;
    } else if (regex.test(phone)) {
      $phone_error.slideUp("fast");
      $phone.removeClass("has-error")
            .addClass("has-success")
            .find(".input-group-addon")
            .html("<span class='glyphicon glyphicon-ok'></span>");
      return true;
    } else {
      $phone_error.html("Please enter a valid phone number")
                  .slideDown("fast");
      $phone.removeClass("has-success")
            .addClass("has-error")
            .find(".input-group-addon")
            .html("<span class='glyphicon glyphicon-remove'></span>");
      return false;
    }
  }
}

function calculateOrderTotal() {
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

function calculateDeliveryCharge() {
  var delivery_charge;
  
  if ($("select[name=delivery]").val() === "Deliver To Address") {
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

      $("#delivery-charge-html").html("&pound;" + delivery_charge);
      $("#delivery-charge").show();
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
