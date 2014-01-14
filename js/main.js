var $buoop = {
  vs: {i:9,f:15,o:10.6,s:4,n:10},
  reminder: 0,
  newwindow: true
  },
  konami = '38,38,40,40,37,39,37,39,66,65',
  keys   = [];
$(document).ready(function() {
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

  $('table#orders-js>tbody>tr').click(function() {
    window.location.href = $(this).find("a").attr("href");
  }).hover(function() {
    $(this).toggleClass("hover");
  });

  $(".header > .user").css("float", "right");

  $("#image-link").click(function() {
    $("div.image-view").effect("slide");
  });

  $("div.image-view>.close").click(function() {
    $("div.image-view").slideUp();
  });

  $("table:not(#single_order)").tablePagination();

  loader.Init();
});

var loader = {
  Init: function() {
    $("#loading-spinner-dialog").dialog({
      closeOnEscape: false,
      draggable: false,
      height: 107,
      modal: true,
      resizable: false,
      width: 90
    }).parent().children("div:first-child").css("display", "none");
    $("#loading-spinner-dialog").dialog("option", "height", 107)
                                .dialog("close")
                                .dialog("option", "show", {effect: "fade", duration: 500});
  },
  Show: function() {
    $("#loading-spinner-dialog").dialog("open");
  },
  Hide: function() {
    $("#loading-spinner-dialog").dialog("close");
  }
};
