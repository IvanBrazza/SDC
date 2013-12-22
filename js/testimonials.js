$(document).ready(function() {
  $("#submit-testimonial-form").hide();
  $("#submit-testimonial-form").css("margin", "10px 0 0 0");
  $("#submit-testimonial").click(function() {
    $("#submit-testimonial-form").slideDown();
  });

  $("#testimonial-form").submit(function(e) {
    // Validate the fields
    validateEmail();
    validateInput('#name', '#name_error');
    validateInput('textarea#testimonial', '#testimonial_error');
    $(".ajax-load").css("display", "inline-block");
    if ($input_check && $email_check) {
      // Submit the form
      $.ajax({
        type: 'post',
        url: '../lib/form/submit-testimonial.php',
        data: $(this).serialize(),
        success: function(response) {
          if (response.substring(0, 7) === 'success') {
            $("#submit-testimonial-form").slideUp();
            $(".ajax-load").hide();
            $("#testimonials").append("<div>" + 
                                      "<p class='testimonial'>" + $('textarea#testimonial').val()  + "</p>" + 
                                      "<span class='testimonial-name'>" + 
                                      "<small>- " + 
                                      $('#name').val() +
                                      "</small>" +
                                      "</span>" + 
                                      "</div>");
            if ($("#location").val()) {
              $("#testimonials div:last-child > span > small").append("<i>, " + $('#location').val()  + "</i>");
              $("#location").val("");
            }
            $("#name").val("").removeClass("valid");
            $("#email").val("").removeClass("valid");
            $("textarea#testimonial").val("").removeClass("valid");
            Recaptcha.reload();
            $("#token").val(response.substring(8));
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
      validateInput('#name', '#name_error');
      validateInput('textarea#testimonial', '#testimonial_error');
      $(".ajax-load").hide();
    }
  });
});