$(document).ready(function() {
  $("#edit-account-you-form").submit(function(e) {
    e.preventDefault();
    // Validate inputs
    var firs_check = validate.input('input[name=first_name]', '#first_name_error', 'Please enter your first name'),
        last_check = validate.input('input[name=last_name]', '#last_name_error', 'Please enter your last name');
    if (firs_check && last_check) {
      // Submit the form
      $.ajax({
        type: 'post',
        url: '../lib/form/edit-account.php',
        data: $(this).serialize(),
        success: function(response) {
          try {
            object = JSON.parse(response);
            if (object.status == "success") {
              $("input[name=token]").val(object.token);
              $("#success_modal .alert").html("<i class='fa fa-check-circle-o'></i>   Personal details updated");
              $("#success_modal").modal("show");
              setTimeout(function() {
                $("#success_modal").modal("hide");
              }, 1500);
            } else {
              $("input[name=token]").val(object.token);
              $("#error_modal .alert").html("<i class='fa fa-times-circle-o'></i>   " + object.error);
              $("#error_modal").modal("show");
              setTimeout(function() {
                $("#error_modal").modal("hide");
              }, 1500);
            }
          } catch(error) {
            $("#error_modal .alert").html("<i class='fa fa-times-circle-o'></i>   Oops! Something went wrong. Try again<br>" +
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

  $("#edit-account-email-form").submit(function(e) {
    e.preventDefault();
    // Validate inputs
    var emai_check = validate.email();
    if (emai_check) {
      // Submit the form
      $.ajax({
        type: 'post',
        url: '../lib/form/edit-account.php',
        data: $(this).serialize(),
        success: function(response) {
          try {
            object = JSON.parse(response);
            if (object.status == "verify-email") {
              window.location.href = "../verify-email/?type=edit";
            } else {
              $("input[name=token]").val(object.token);
              $("#error_modal .alert").html("<i class='fa fa-times-circle-o'></i>   " + object.error);
              $("#error_modal").modal("show");
              setTimeout(function() {
                $("#error_modal").modal("hide");
              }, 1500);
            }
          } catch(error) {
            $("#error_modal .alert").html("<i class='fa fa-times-circle-o'></i>   Oops! Something went wrong. Try again<br>" +
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

  $("#edit-account-password-form").submit(function(e) {
    e.preventDefault();
    // Validate inputs
    var pass_check = validate.password();
    if (pass_check) {
      // Submit the form
      $.ajax({
        type: 'post',
        url: '../lib/form/edit-account.php',
        data: $(this).serialize(),
        success: function(response) {
          try {
            object = JSON.parse(response);
            if (object.status == "success") {
              $("input[name=token]").val(object.token);
              $("#success_modal .alert").html("<i class='fa fa-check-circle-o'></i>   Password updated");
              $("#success_modal").modal("show");
              setTimeout(function() {
                $("#success_modal").modal("hide");
              }, 1500);
            } else {
              $("input[name=token]").val(object.token);
              $("#error_modal .alert").html("<i class='fa fa-times-circle-o'></i>   " + object.error);
              $("#error_modal").modal("show");
              setTimeout(function() {
                $("#error_modal").modal("hide");
              }, 1500);
            }
          } catch(error) {
            $("#error_modal .alert").html("<i class='fa fa-times-circle-o'></i>   Oops! Something went wrong. Try again<br>" +
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

  $("#edit-account-address-form").submit(function(e) {
    e.preventDefault();
    // Validate inputs
    var post_check = validate.postcode(),
        addr_check = validate.input('input[name=address]', '#address_error', 'Please enter your address')
        form       = this;
    if (post_check && addr_check) {
      var geocoder = new google.maps.Geocoder();;
      geocoder.geocode({
        'address': $("input[name=address]").val().replace(" ", ",") + "," + $("input[name=postcode]").val().replace(" ", ","),
      }, callback);

      function callback(response, status) {
        var address = response[0].formatted_address,
            numCommas = address.match(/,/g).length;
        if (numCommas < 3){
          $("input[name=address], input[name=postcode]").closest("div.form-group").switchClass("has-success", "has-error");
          var address_modal = $("#address-modal");
          address_modal.find(".modal-body").html("It seems that the address you inputted - <b>" + $("input[name=address]").val() + ", " + $("input[name=postcode]").val() + "</b> - isn't a real address. Please check the address you entered and try again.");
          address_modal.modal({
            backdrop: "static",
            keyboard: false
          });
        } else {
          // Submit the form
          $.ajax({
            type: 'post',
            url: '../lib/form/edit-account.php',
            data: $(form).serialize(),
            success: function(response) {
              try {
                object = JSON.parse(response);
                if (object.status == "success") {
                  $("input[name=token]").val(object.token);
                  $("#current-address").html($("input[name=address]").val() + ", " + $("input[name=postcode]").val());
                  $("#success_modal .alert").html("<i class='fa fa-check-circle-o'></i>   Address updated");
                  $("#success_modal").modal("show");
                  setTimeout(function() {
                    $("#success_modal").modal("hide");
                  }, 1500);
                } else {
                  $("input[name=token]").val(object.token);
                  $("#error_modal .alert").html("<i class='fa fa-times-circle-o'></i>   " + object.error);
                  $("#error_modal").modal("show");
                  setTimeout(function() {
                    $("#error_modal").modal("hide");
                  }, 1500);
                }
              } catch(error) {
                $("#error_modal .alert").html("<i class='fa fa-times-circle-o'></i>   Oops! Something went wrong. Try again<br>" +
                  "<b>Error: " + error.message + "</b>");
                $("#error_modal").modal("show");
                setTimeout(function() {
                  $("#error_modal").modal("hide");
                }, 1500);
              }
            }
          });
        }
      }
    }
  });

  $("#edit-account-phone-form").submit(function(e) {
    e.preventDefault();
    // Validate inputs
    var phon_check = validate.phone();
    if (phon_check) {
      // Submit the form
      $.ajax({
        type: 'post',
        url: '../lib/form/edit-account.php',
        data: $(this).serialize(),
        success: function(response) {
          try {
            object = JSON.parse(response);
            if (object.status == "success") {
              $("input[name=token]").val(object.token);
              $("#current-phone").html($("input[name=phone]").val());
              $("#success_modal .alert").html("<i class='fa fa-check-circle-o'></i>   Phone Number updated");
              $("#success_modal").modal("show");
              setTimeout(function() {
                $("#success_modal").modal("hide");
              }, 1500);
            } else {
              $("input[name=token]").val(object.token);
              $("#error_modal .alert").html("<i class='fa fa-times-circle-o'></i>   " + object.error);
              $("#error_modal").modal("show");
              setTimeout(function() {
                $("#error_modal").modal("hide");
              }, 1500);
            }
          } catch(error) {
            $("#error_modal .alert").html("<i class='fa fa-times-circle-o'></i>   Oops! Something went wrong. Try again<br>" +
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
});
