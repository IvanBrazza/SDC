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

  $(document).ajaxStart(function() {
    NProgress.start();
  }).ajaxStop(function() {
    NProgress.done();
  }).ajaxError(function(event, jqxhr, settings, exception) {
    $("#error_modal .alert").html("<span class='glyphicon glyphicon-remove-circle'></span>   Oops! Something went wrong. Try again<br>" +
      "<b>Error: " + jqxhr.status + " (" + jqxhr.statusText + ")</b>");
    $("#error_modal").modal("show");
    setTimeout(function() {
      $("#error_modal").modal("hide");
    }, 1500);
  }).ajaxComplete(function(event, xhr, settings) {
    if (xhr.responseText == "") {
      $("#error_modal .alert").html("<span class='glyphicon glyphicon-remove-circle'></span>   Oops! Something went wrong. Try again<br>" +
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
  var cookieMessage = "We use cookies on this website in order to improve your experience. By continuing" +
                      " to use this website you agree to our <a href='{{cookiePolicyLink}}'>cookie policy</a>.",
      cookieAcceptButtonText = "Close this message";
  if ($(window).width() < 768) {
    $.cookieCuttr({
      cookieAcceptButtonText: cookieAcceptButtonText,
      cookieAnalytics: false,
      cookieMessage: cookieMessage,
      cookiePolicyLink: '/cookies/'
    });
  } else {
    $.cookieCuttr({
      cookieAcceptButtonText: cookieAcceptButtonText,
      cookieAnalytics: false,
      cookieMessage: cookieMessage,
      cookiePolicyLink: '/cookies/',
      cookieNotificationLocationBottom: true
    });
  }
});
