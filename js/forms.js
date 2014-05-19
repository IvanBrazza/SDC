$(document).ready(function() {
  $("#register-form").submit(function(e) {
    e.preventDefault();
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
          try {
            object = JSON.parse(response);
            if (object.status == "success") {
              window.location.href = "../verify-email/?type=register";
            } else if (object.status == "error") {
              switch (object.code) {
                case "002":
                  Recaptcha.reload();
                  break;
                case "003":
                  $("input[name=username]").closest(".input-group")
                                           .removeClass("has-success")
                                           .addClass("has-error")
                                           .find(".input-group-addon")
                                           .html("<span class='glyphicon glyphicon-remove'></span>");
                  break;
                case "004":
                  $("input[name=email]").closest(".input-group")
                                        .removeClass("has-success")
                                        .addClass("has-error")
                                        .find(".input-group-addon")
                                        .html("<span class='glyphicon glyphicon-remove'></span>");
              }
              if (object.code != "001") $("input[name=token]").val(object.token);
              $("#error_message").html("<span class='glyphicon glyphicon-remove-circle'></span>" + object.error).show();
            }
          } catch(error) {
            $("#error_modal .alert").html("<span class='glyphicon glyphicon-remove-circle'></span>   Oops! Something went wrong. Try again<br>" +
              "<b>Error: " + error.message + "</b>");
            $("#error_modal").modal("show");
            setTimeout(function() {
              $("#error_modal").modal("hide");
            }, 1500);
          }
        }
      });
    }
  });

  $("#login-form").submit(function(e) {
    e.preventDefault();
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
          try {
            object = JSON.parse(response);
            if (object.status == 'success') {
              window.location.href = "../";
            } else if (object.status == 'redirect') {
              window.location.href = object.redirect;
            } else if (object.status == 'error') {
              switch (object.code) {
                case "002":
                  $("#username").closest("div.form-group")
                                .removeClass("has-success")
                                .addClass("has-error")
                                .find(".input-group-addon")
                                .html("<span class='glyphicon glyphicon-remove'></span>");
                  break;
                case "003":
                  $("#password").closest(".form-group")
                                .removeClass("has-success")
                                .addClass("has-error")
                                .find(".input-group-addon")
                                .html("<span class='glyphicon glyphicon-remove'></span>");
                  break;
              }
              if (object.code != "001") $("input[name=token]").val(object.token);
              $("#error_message").html("<span class='glyphicon glyphicon-remove-circle'></span>" + object.error).show();
            }
          } catch(error) {
            $("#error_modal .alert").html("<span class='glyphicon glyphicon-remove-circle'></span>   Oops! Something went wrong. Try again<br>" +
              "<b>Error: " + error.message + "</b>");
            $("#error_modal").modal("show");
            setTimeout(function() {
              $("#error_modal").modal("hide");
            }, 1500);
          }
        }
      });
    }
  });

  $("#forgot-password-form").submit(function(e) {
    e.preventDefault();
    // Validate the fields
    var emai_check = validate.email();
    if (emai_check) {
      // Submit the form
      $.ajax({
        type: 'post',
        url: '../lib/form/forgot-password.php',
        data: $(this).serialize(),
        success: function(response) {
          try {
            object = JSON.parse(response);
            if (object.status == 'success') {
              $("#email").closest(".form-group")
                         .removeClass("has-error")
                         .addClass("has-success")
                         .find(".input-group-addon")
                         .html("<span class='glyphicon glyphicon-ok'></span>");
              $("#error_message").hide();
              $("#success_message").html("<span class='glyphicon glyphicon-ok'></span>   Password reset. Please check your emails for a new password.").show();
            } else if (object.status == 'error') {
              switch (object.code) {
                case "002":
                  $("#email").closest(".form-group")
                             .removeClass("has-success")
                             .addClass("has-error")
                             .find(".input-group-addon")
                             .html("<span class='glyphicon glyphicon-remove'></span>");
                  break;
              }
              $("#error_message").html("<span class='glyphicon glyphicon-remove-circle'></span>   " + object.error).show();
              $("#token").val(object.token);
            }
          } catch(error) {
            $("#error_modal .alert").html("<span class='glyphicon glyphicon-remove-circle'></span>   Oops! Something went wrong. Try again<br>" +
              "<b>Error: " + error.message + "</b>");
            $("#error_modal").modal("show");
            setTimeout(function() {
              $("#error_modal").modal("hide");
            }, 1500);
          }
        }
      });
    }
  });

  $("select[name=delivery]").change(function() {
    if ($("select[name=delivery]").val() === "Collection") {
      $("#datetime-label").html("Date/Time For Collection");
      $("#datetime-label-review").html("Date/time for collection: ");
      $("#delivery-charge").hide("fast");
      $("#delivery-charge-html").html(0);
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
      $email_error.html("Please enter an email")
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
                      .slideDown("fast");
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

    if (username == "") {
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
  input: function(inputParam, error, errorMessage) {
    var input  = $(inputParam).closest("div.form-group"),
        $input = $(inputParam).val(),
        $error = $(error);

    if ($input === "" || $input == "null") {
      $error.html(errorMessage)
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
      $postcode_error.html("Please enter a postcode")
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

    if (phone == "") {
      $phone_error.html("Please enter a phone number")
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

function calculateOrderTotal(callback) {
  callback = typeof callback !== 'undefined' ? callback : false;
  $(".calculating").fadeIn();
  $("#deliveryPanel").collapse("hide");
  $("#review").collapse("show");
  $("#delivery-heading").animate({backgroundColor: "#dff0d8"});
  $("#review-heading").animate({backgroundColor: "#d9edf7"});
  $.ajax({
    type: 'post',
    url: '../lib/form/calculate-total.php',
    data: {
      token:     $("input[name=token]").val(),
      cakeSize:  $("#cake_size").val(),
      cakeType:  $("#cake_type").val(),
      fillingId: $("#filling").val(),
      decorId:   $("#decoration").val(),
      delivery:  $("select[name=delivery]").val(),
      address:   $address,
      postcode:  $postcode
    },
    success: function(response) {
      try {
        object = JSON.parse(response);
        if (object.status == "success") {
          $("input[name=token]").val(object.token)
          $("#base-price").html(parseInt(object.basePrice));
          $("#cake-size-review").html($("#cake_size").val());
          $("#cake-type-review").html($("#cake_type").val());
          $("#filling-review").html(object.fillingName);
          $("#filling-html").html(object.fillingPrice);
          $("#decoration-review").html(object.decorName);
          $("#decoration-html").html(object.decorPrice);
          if (object.deliveryCharge == "Collection only") {
            $("select[name=delivery] option[value=Collection]").attr("selected", "true");
            $("#delivery-charge-html").html("");
            $("#delivery-review").html("Collection");
            $("#datetime-label").html("Date/Time For Collection");
            $("#delivery-charge").hide();
            $('<div class="modal fade" style="overflow-y:auto;"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h4 class="modal-title">You live too far away!</h4>'+
              '</div><div class="modal-body"><p>You chose to have your order delivered to you, however you live over 50 miles away, which is outside of our delivery radius.</p>' +
              '<p>Because of this, you can only collect your order from our collection point.</p>' +
              '</div><div class="modal-footer"><button type="button" class="btn btn-default pull-right" data-dismiss="modal">Okay</button></div></div></div></div>').modal({
              backdrop: 'static',
              keyboard: 'false'
            });
          } else {
            $("#delivery-charge-html").html("&pound;" + object.deliveryCharge);
            $("#delivery-charge").show();
          }
          $("#total-html").html(object.total);
          if (callback != false) {
            callback();
          } else {
            $(".calculating").fadeOut();
          }
        }
      } catch(error) {
        $("#error_modal .alert").html("<span class='glyphicon glyphicon-remove-circle'></span>   Oops! Something went wrong. Try again<br>" +
          "<b>Error: " + error.message + "</b>");
        $("#error_modal").modal("show");
        setTimeout(function() {
          $("#error_modal").modal("hide");
        }, 1500);
      }
    }
  });
}
