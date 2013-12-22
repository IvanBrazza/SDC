var $buoop = {
  vs: {i:8,f:15,o:10.6,s:4,n:10},
  reminder: 0,
  newwindow: true
} 
$(document).ready(function() {
  $('table#orders-js>tbody>tr').click(function() {
    window.location.href = $(this).find("a").attr("href");
  }).hover(function() {
    $(this).toggleClass("hover");
  });

  $(".header > .user").css("float", "right");

  $("#image-link").click(function() {
    $("div.image-view").slideDown();
  });

  $("div.image-view>.close").click(function() {
    $("div.image-view").slideUp();
  });
});
