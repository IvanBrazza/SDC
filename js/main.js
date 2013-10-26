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

  $('#celebration-carousel').flexslider({
    animation: "slide",
    controlNav: false,
    animationLoop: true,
    slideshow: false,
    itemWidth: 210,
    itemMargin: 5,
    asNavFor: '#celebration-slider'
  });
   
  $('#celebration-slider').flexslider({
    animation: "slide",
    controlNav: false,
    animationLoop: true,
    slideshowSpeed: 3000,
    slideshow: true,
    sync: "#celebration-carousel"
  });

  $('#cupcake-carousel').flexslider({
    animation: "slide",
    controlNav: false,
    animationLoop: true,
    slideshow: false,
    itemWidth: 210,
    itemMargin: 5,
    asNavFor: '#cupcake-slider'
  });
   
  $('#cupcake-slider').flexslider({
    animation: "slide",
    controlNav: false,
    animationLoop: true,
    slideshowSpeed: 3000,
    slideshow: true,
    sync: "#cupcake-carousel"
  });

  $('#other-carousel').flexslider({
    animation: "slide",
    controlNav: false,
    animationLoop: true,
    slideshow: false,
    itemWidth: 210,
    itemMargin: 5,
    asNavFor: '#other-slider'
  });
   
  $('#other-slider').flexslider({
    animation: "slide",
    controlNav: false,
    animationLoop: true,
    slideshowSpeed: 3000,
    slideshow: true,
    sync: "#other-carousel"
  });

  $(".date").datepicker({
    minDate: 0,
    dateFormat: "yy-mm-dd"
  });

  $("#datetime").datetimepicker({
    dateFormat: "yy-mm-dd",
    timeFormat: "HH:mm",
    minDate: 0
  });

  $("#comments").css("padding-bottom", "100px");
            
  $("#register-form").submit(function(e) {
    validatePassword();
    validateUsername();
    validatePassword2();
    validateEmail();
    
    if ($password_check && $password2_check && $username_check && $email_check) {
    } else {
      e.preventDefault();
      validatePassword();
      validateUsername();
      validatePassword2();
      validateEmail();
    }
  });

  $("#login-form").submit(function(e) {
    validatePassword();
    validateUsername();
    
    if ($password_check && $username_check) {
    } else {
      e.preventDefault();
      validatePassword();
      validateUsername();
    }
  });
  
  $("#order-form").submit(function(e) {
    validateInput('#design', '#design_error');
    validateInput('#celebration_date', '#celebration_date_error');
    validateInput('textarea#order', '#order_error');
    validateInput('#datetime', '#datetime_error');
    
    if ($input_check) {
    } else {
      e.preventDefault();
      validateInput('#design', '#design_error');
      validateInput('#celebration_date', '#celebration_date_error');
      validateInput('textarea#order', '#order_error');
      validateInput('#datetime', '#datetime_error');
    }
  });

  $("#testimonial-form").submit(function(e) {
    validateEmail();
    validateInput('#name', '#name_error');
    validateInput('textarea#testimonial', '#testimonial_error');
    
    if ($input_check && $email_check) {
    } else {
      e.preventDefault();
      validateEmail();
      validateInput('#name', '#name_error');
      validateInput('textarea#testimonial', '#testimonial_error');
    }
  });

  $("#edit-account-form").submit(function(e) {
    validateEmail();
    validatePostcode();
    validatePhone();
    validateInput('#first_name', '#first_name_error');
    validateInput('#last_name', '#last_name_error');
    validateInput('#address', '#address_error');
    
    if ($input_check && $phone_check && $postcode_check && $email_check) {
    } else {
      e.preventDefault();
      validateEmail();
      validatePostcode();
      validatePhone();
      validateInput('#first_name', '#first_name_error');
      validateInput('#last_name', '#last_name_error');
      validateInput('#address', '#address_error');
    }
  });
  
  $("#delivery").change(function() {
    if ($("#delivery").val() === "Collection") {
      $("#datetime-label").html("Date/time for collection");
      $("#delivery-charge").hide("fast");
      $("#delivery-charge-html").html(0);
      calculateOrderTotal();
    } else {
      $("#datetime-label").html("Date/time for delivery");
      calculateDeliveryCharge();
    }
  });

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
    console.log($add_existing_check);
    if ($add_existing_check) {
      if ($input_check && price_check) {
      } else {
        e.preventDefault();
        validatePrice('#agreed_price', '#agreed_price_error');
        validatePrice('#delivery_charge', '#delivery_charge_error');
        validateInput('#order_date', '#order_date_error');
        validateInput('#datetime', '#datetime_error');
        validateInput('#celebration_date', 'celebration_date_error');
        validateInput('textarea#order', '#order_error');
        validateInput('#design', '#design_error');
      }
    } else {
      if ($input_check && $phone_check && $email_check && $postcode_check && $price_check) {
      } else {
        e.preventDefault();
        validatePhone();
        validateEmail();
        validatePostcode();
        validatePrice('#agreed_price', '#agreed_price_error');
        validatePrice('#delivery_charge', '#delivery_charge_error');
        validateInput('#first_name', '#first_name_error');
        validateInput('#last_name', '#last_name_error');
        validateInput('#address', '#address_error');
        validateInput('#order_date', '#order_date_error');
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
    $password_error.slideDown("fast");
    $password_check = false;
  } else if (password.length < 5) {
    $password_error.html("Password must be at least 5 characters");
    $password.removeClass("valid");
    $password.addClass("invalid");
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
    $username_error.slideDown("fast");
    $username_check = false;
  } else if (username.length < 3) {
    $username_error.html("Username must be at least 3 characters");
    $username.removeClass("valid");
    $username.addClass("invalid");
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
    $phone_error.slideDown("fast");
    $phone_check = false;
  }
}

function validatePrice(input, error)
{
  var input  = $(input),
      $input = $(input).val(),
      $error = $(error);

  if ($input === "") {
    $error.html("This field cannot be blank");
    input.removeClass("valid");
    input.addClass("invalid");
    $error.slideDown("fast");
    $price_check = false;
  } else if (/^[1-9][0-9]?\.?[0-9]{0,2}$/.test($input)) {
    input.removeClass("invalid");
    input.addClass("valid");
    $error.slideUp("fast");
    $price_check = true;
  } else {
    $error.html("Please enter a valid amount of money");
    input.removeClass("valid");
    input.addClass("invalid");
    $error.slideDown("fast");
    $price_check = false;
  }
}

function calculateOrderTotal()
{
  var $total         = $("#total-html"),
      $cake_size     = $("#cake_size").val(),
      $cake_type     = $("#cake_type").val(),
      $total_hidden  = $("input[id=total-hidden]");

  if ($("#delivery-charge-html").html()) {
    var $delivery_charge = $("#delivery-charge-html").html();
  } else {
    var $delivery_charge = 0;
  }

  if ($cake_size === '6"') {
    if ($cake_type === "Sponge"){
      $total.html(25 + parseInt($delivery_charge));
      $total_hidden.val($total.html());
    } else if ($cake_type === "Marble"){
      $total.html(30 + parseInt($delivery_charge));
      $total_hidden.val($total.html());
    } else if ($cake_type === "Chocolate") {
      $total.html(32 + parseInt($delivery_charge));
      $total_hidden.val($total.html());
    } else if ($cake_type === "Fruit"){
      $total.html(35 + parseInt($delivery_charge));
      $total_hidden.val($total.html());
    }
  } else if ($cake_size === '8"') {
    if ($cake_type === "Sponge"){
      $total.html(30 + parseInt($delivery_charge));
      $total_hidden.val($total.html());
    } else if ($cake_type === "Marble"){
      $total.html(35 + parseInt($delivery_charge));
      $total_hidden.val($total.html());
    } else if ($cake_type === "Chocolate") {
      $total.html(37 + parseInt($delivery_charge));
      $total_hidden.val($total.html());
    } else if ($cake_type === "Fruit"){
      $total.html(45 + parseInt($delivery_charge));
      $total_hidden.val($total.html());
    }
  } else if ($cake_size === '10"') {
    if ($cake_type === "Sponge"){
      $total.html(40 + parseInt($delivery_charge));
      $total_hidden.val($total.html());
    } else if ($cake_type === "Marble"){
      $total.html(45 + parseInt($delivery_charge));
      $total_hidden.val($total.html());
    } else if ($cake_type === "Chocolate") {
      $total.html(47 + parseInt($delivery_charge));
      $total_hidden.val($total.html());
    } else if ($cake_type === "Fruit"){
      $total.html(60 + parseInt($delivery_charge));
      $total_hidden.val($total.html());
    }
  } else if ($cake_size === '12"') {
    if ($cake_type === "Sponge"){
      $total.html(60 + parseInt($delivery_charge));
      $total_hidden.val($total.html());
    } else if ($cake_type === "Marble"){
      $total.html(65 + parseInt($delivery_charge));
      $total_hidden.val($total.html());
    } else if ($cake_type === "Chocolate") {
      $total.html(80 + parseInt($delivery_charge));
      $total_hidden.val($total.html());
    } else if ($cake_type === "Fruit"){
      $total.html(85 + parseInt($delivery_charge));
      $total_hidden.val($total.html());
    }
  } else if ($cake_size === '14"') {
    if ($cake_type === "Sponge"){
      $total.html(75 + parseInt($delivery_charge));
      $total_hidden.val($total.html());
    } else if ($cake_type === "Marble"){
      $total.html(80 + parseInt($delivery_charge));
      $total_hidden.val($total.html());
    } else if ($cake_type === "Chocolate") {
      $total.html(84 + parseInt($delivery_charge));
      $total_hidden.val($total.html());
    } else if ($cake_type === "Fruit"){
      $total.html(125 + parseInt($delivery_charge));
      $total_hidden.val($total.html());
    }
  }
}

function calculateDeliveryCharge()
{
  var $delivery_charge = $("#delivery-charge-html"),
      $delivery        = $("#delivery-charge");
  
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
      for (var i = 5, j = 1; i <= 50; i++, j++) {
        if (remaining_miles === i) {
          var delivery_charge = j;
        }
      }
    }

    $delivery_charge.html(delivery_charge);
    calculateOrderTotal();
    $delivery.show("fast");
  }
}
