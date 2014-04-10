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

  // Enable clickable rows on order and customers tables
  $('table#orders-js>tbody>tr').click(function() {
    window.location.href = $(this).find("a").attr("href");
  });

  // Float the userbox right
  $(".header > .user").css("float", "right");

  // Show the order image when the link is clicked
  $("#image-link").click(function() {
    $("div.image-view").effect("slide");
  });

  // Hide the order image when the close button is clicked
  $("div.image-view>.close").click(function() {
    $("div.image-view").slideUp();
  });

  // Paginate all tables except for single order tables
  $("table:not(#single_order)").tablePagination();

  // Run the init for the loader
  loader.Init();

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

var loader = {
  Init: function() {
    $("#loading-spinner-dialog").modal({
      backdrop: 'static',
      keyboard: false,
      show: false,
    });
  },
  Show: function() {
    $("#loading-spinner-dialog").modal("show");
  },
  Hide: function() {
    $("#loading-spinner-dialog").modal("hide");
  }
};
