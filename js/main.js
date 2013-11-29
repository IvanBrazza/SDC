var $buoop = {
  vs: {i:8,f:15,o:10.6,s:4,n:10},
  reminder: 0,
  newwindow: true
} 
$(document).ready(function() {
  $('table#orders-js>tbody>tr').click(function() {
    window.location=$(this).find("a").attr("href");
  }).hover(function() {
    $(this).toggleClass("hover");
  });

  $("#order_search").submit(function(e) {
    $(".ajax-load").css("display", "inline-block");
    $.ajax({
      type: 'post',
      url: '../lib/form/order-search.php',
      data: $(this).serialize(),
      success: function(response) {
        if (response.slice(0,13) === "../all-orders") {
          window.location.replace(response);
        } else {
          $("#error_message").html(response);
          $(".ajax-load").hide();
        }
      }
    });
    e.preventDefault();
  });
});
