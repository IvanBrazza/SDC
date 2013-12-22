$(document).ready(function() {
  $("#order_search").submit(function(e) {
    $(".ajax-load").css("display", "inline-block");
    $.ajax({
      type: 'post',
      url: '../lib/form/order-search.php',
      data: $(this).serialize(),
      success: function(response) {
        if (response.slice(0,13) === "../all-orders") {
          window.location.href = response;
        } else {
          $("#error_message").html(response);
          $(".ajax-load").hide();
        }
      }
    });
    e.preventDefault();
  });

  $("#single_order_details").height($("#single_order").height());
});
