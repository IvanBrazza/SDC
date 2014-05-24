/**
  js/main.js - code included on every page
**/
// Set some vars: $buoop contains the settings for the browser check,
// konami and keys are for konami code
var $buoop = {
  vs: {i:9,f:15,o:10.6,s:4,n:10},
  reminder: 0,
  newwindow: true
  },
  konami = '38,38,40,40,37,39,37,39,66,65',
  keys   = [];
$(document).ready(function() {
  // Konami!
  $(document).keydown(function(e) {
    keys.push(e.keyCode);
    if (keys.toString().indexOf(konami) >=0) {
      $(".logo>img").animate({width: 500, height: 200}, function() {
        $(this).animate({width:80, height: 50});
      });
      setInterval(function() {
        $(".container").children().effect("shake", 400, function() {
          $(".container").children().effect("bounce", 400);
        });
      }, 800);
      keys = [];
    }
  });

  $('.nav li.dropdown').hover(function() {
    $(this).addClass('open');
  }, function() {
    $(this).removeClass('open');
  });

  $(document).ajaxStart(function() {
    NProgress.start();
  }).ajaxStop(function() {
    NProgress.done();
  }).ajaxError(function(event, jqxhr, settings, exception) {
    $("#error_modal .alert").html("<i class='fa fa-times-circle'></i><span>Oops! Something went wrong. Try again</span><br>" +
      "<b>Error: " + jqxhr.status + " (" + jqxhr.statusText + ")</b>");
    $("#error_modal").modal("show");
    setTimeout(function() {
      $("#error_modal").modal("hide");
    }, 1500);
  }).ajaxComplete(function(event, xhr, settings) {
    if (xhr.responseText == "") {
      $("#error_modal .alert").html("<i class='fa fa-times-circle'></i><span>Oops! Something went wrong. Try again</span><br>" +
        "<b>Error: No response returned</b>");
      $("#error_modal").modal("show");
      setTimeout(function() {
        $("#error_modal").modal("hide");
      }, 1500);
    }
  });

  // Enable clickable rows on order and customers tables
  $('table#orders-js>tbody>tr').click(function() {
    NProgress.start().done();
    window.location.href = $(this).find("a").attr("href");
  });

  // Paginate all tables except for single order tables
  $("table:not(#single_order, .uploaded-images)").tablePagination();

  // EU cookie stuffs
  // Split cookies into an object
  var pairs = document.cookie.split("; "),
      cookies = {};
  for (var i = 0; i < pairs.length; i++){
    var pair = pairs[i].split("=");
    cookies[pair[0]] = unescape(pair[1]);
  }

  // If the cookie message hasn't been accepted
  if (typeof cookies.cookie_accept == 'undefined') {
    // Append the cookie message to the body
    $("body").append("<div class='cookies'>We use cookies on this website in order to improve your experience. By continuing" +
                     " to use this website you agree to our <a href='/cookies/'>cookie policy</a>. <button class='btn btn-success accept-cookies'>Accept Cookies</button></div>");
    
    // Slide up the cookie message after a second
    setTimeout(function() {
      $(".cookies").animate({bottom: "-10px"});
    }, 1000);

    // If the Accept Cookies button is clicked, add a new cookie saving this and hide the cookie message
    $(".accept-cookies").click(function() {
      var d = new Date();
      d.setTime(d.getTime() + 31536000000);
      var expires = "expires=" + d.toGMTString();
      document.cookie = "cookie_accept=accepted;" + expires + "; path=/";
      $(".cookies").animate({bottom: "-180px"}, function() {
        $(this).remove();
      });
    });
  }
});
