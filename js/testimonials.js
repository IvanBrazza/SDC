/**
  js/testimonials.js - code specific to the testimonials page
**/
$(document).ready(function() {
  // Show and scroll to the Submit Testimonial form if #submit
  if (window.location.hash == "#submit") {
    setTimeout(function() {
      $("#submit-testimonial-form").slideDown(400, function() {
        $("html, body").animate({ scrollTop: $(document).height() }, "slow");
      });
    }, 500);
  }

  // Hide the submit testimonial form, set some CSS for it,
  // and when the link is clicked to submit the testimonial,
  // show the form and scroll down to it.
  $("#submit-testimonial-form").hide();
  $("#submit-testimonial-form").css("margin", "10px 0 0 0");
  $("#submit-testimonial").click(function() {
    $("#submit-testimonial-form").slideDown(400, function() {
      $("html, body").animate({ scrollTop: $(document).height() }, "slow");
    });
  });

  // When the submit testimonial form is submitted
  $("#testimonial-form").submit(function(e) {
    // Validate the fields
    validate.email();
    validate.input('#name', '#name_error');
    validate.input('textarea#testimonial', '#testimonial_error');
    // Show the loading spinner
    // If the validation has passed, submit the form, otherwise
    // call the validation functions
    if ($input_check && $email_check) {
      // Submit the form via AJAX to lib/form/submit-testimonial.php.
      // If the testimonial was successfully added, hide the form
      // and add the testimonial to the page. Otherwise, show the
      // error message returned by the server
      $.ajax({
        type: 'post',
        url: '../lib/form/submit-testimonial.php',
        data: $(this).serialize(),
        success: function(response) {
          if (response.substring(0, 7) === 'success') {
            $("#submit-testimonial-form").slideUp();
            $("#error_message").hide();
            $("#testimonials").append("<div class='row' id='pending'>" + 
                                      "<div class='col-md-6 testimonial-col'>" +
                                      "<p class='testimonial unapproved'>" + $('textarea#testimonial').val()  + "</p>" + 
                                      "<div class='downarrow unapproved'></div>" +
                                      "<span class='testimonial-name'>" + 
                                      "<small>- " + 
                                      $('#name').val() +
                                      "</small>" +
                                      "</span>" + 
                                      "</div>" +
                                      "</div>");
            if ($("#location").val()) {
              $("#pending small").append("<i>, " + $('#location').val()  + "</i>");
              $("#location").val("");
            }
            $("#pending small").append("<span id='unapproved'><i> (unapproved)</i></span>");
            $('<div class="modal fade" id="testimonial-thanks-dialog">'
              + '<div class="modal-dialog">'
              + '<div class="modal-content">'
              + '<div class="modal-header">'
              + '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>'
              + '<h4 class="modal-title">Thanks!</h4>'
              + '</div>'
              + '<div class="modal-body">'
              + 'Thank you for submitting a testimonial. It has been sent for approval and will appear on this page once approved.'
              + '</div>'
              + '<div class="modal-footer">'
              + '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>'
              + '</div>'
              + '</div>'
              + '</div>'
              + '</div>')
            .modal({
              backdrop: "static"
            });
            $("#name").val("").removeClass("valid");
            $("#email").val("").removeClass("valid");
            $("textarea#testimonial").val("").removeClass("valid");
            Recaptcha.reload();
            $("#token").val(response.substring(8));
          } else {
            if (response == "reCAPTCHA incorrect.") {
              Recaptcha.reload();
            }
            $("#error_message").html(response);
          }
        }
      });
      e.preventDefault();
    } else {
      // Don't submit the form
      e.preventDefault();
      validate.email();
      validate.input('#name', '#name_error');
      validate.input('textarea#testimonial', '#testimonial_error');
    }
  });

  // When the admin clicks the button to delete a testimonial
  $(".delete_testimonial").click(function(e) {
    // Show the loader and set $button to the button clicked
    var $button = $(this);
    // Make an AJAX call to lib/delete-testimonial.php to delete
    // the testimonial, sending the testimonial ID and token as
    // the data. If successfully deleted, remove the testimonial,
    // otherwise show the error message returned by the server
    $.ajax({
      type: 'post',
      url: '../lib/delete-testimonial.php',
      data: {id: $(this).data("id"), token: $(this).data("token")},
      success: function(response) {
        object = JSON.parse(response);
        if (object.response === 'success') {
          $button.closest("div").slideUp(400, function() {
            $(this).remove();
          });
          $(".delete_testimonial").data("token", object.token);
        } else {
          $("#error_message").html(response);
        }
      }
    });
    e.preventDefault();
  });

  // When the admin clicks the button to approve a testimonial
  $(".approve_testimonial").click(function(e) {
    // Show the loader and set $button to the button clicked
    var $button = $(this);
    // Make an AJAX call to lib/approve-testimonial.php to approve
    // the testimonial, sending the testimonial ID and token as
    // the data. If successfully approved, update the testimonial,
    // classes to indicate approval otherwise show the error message
    // returned by the server
    $.ajax({
      type: 'post',
      url: '../lib/approve-testimonial.php',
      data: {id: $(this).data("id"), token: $(this).data("token")},
      success: function(response) {
        object = JSON.parse(response);
        if (object.response === 'success') {
          $button.closest("div").find(".unapproved").removeClass("unapproved").addClass("approved");
          $button.siblings("#unapproved").remove();
          $button.remove();
          $(".delete_testimonial, .approve_testimonial").data("token", object.token);
        } else {
          $("#error_message").html(response);
        }
      }
    });
    e.preventDefault();
  });
});
