/**
  js/all-orders.js - code specific to the all-orders page
**/
var orderNumbers = false;
$(document).ready(function() {
  // When the order search form is submitted
  $("#order_search").submit(function(e) {
    // Show the loading spinner
    // Make an AJAX call to lib/form/order-search.php
    // to see if the order exists. If it does, redirect
    // to that page, otherwise show an error message
    $.ajax({
      type: 'post',
      url: '../lib/form/order-search.php',
      data: $(this).serialize(),
      success: function(response) {
        if (response.slice(0,13) === "../all-orders") {
          window.location.href = response;
        } else {
          $("#error_message").html("<i class='fa fa-times-circle'></i>" + response).show();
        }
      }
    });
    e.preventDefault();
  });

  // If not GET (i.e. displaying all orders and not
  // a specific order
  if (orderNumbers) {
    // Enable autocomplete for the order search form
    // with all of the order numbers. If the user
    // clicks on one of these autocompletes, insert
    // that value into the form and submit it
    $("#order_number").autocomplete({
      source: orderNumbers,
      select: function(event, ui) {
        $("#order_number").val(ui.item.value);
        $("#order_search").submit();
      }
    });
  }

  $("#complete-order").submit(function(e) {
    e.preventDefault();
    $.ajax({
      type: 'post',
      url: '../lib/complete-order.php',
      data: $(this).serialize(),
      success: function(response) {
        if (response === "success") {
          window.location.href = "../all-orders/?completed=success";
        } else {
          $("#error_message").html(response).show();
        }
      }
    });
  });

  $(".update").submit(function(e) {
    e.preventDefault();
    $.ajax({
      type: 'post',
      url: '../lib/update-order.php',
      data: $(this).serialize(),
      success: function(response) {
        object = JSON.parse(response);
        if (object.status === "success") {
          $("#success_message").html(object.message).show();
          $("input[name=token]").val(object.token);
        } else {
          $("#error_message").html(object.status).show();
          $("#success_message").hide();
        }
      }
    });
  });
});
