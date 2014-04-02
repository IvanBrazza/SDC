/**
  js/all-orders.js - code specific to the all-orders page
**/
$(document).ready(function() {
  // When the order search form is submitted
  $("#order_search").submit(function(e) {
    // Show the loading spinner
    loader.Show();
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
          $("#error_message").html("<span class='glyphicon glyphicon-remove-circle'></span>" + response).show();
          loader.Hide();
        }
      }
    });
    e.preventDefault();
  });

  // If not GET (i.e. displaying all orders and not
  // a specific order
  if (window.location.search == "" || window.location.search.indexOf("sort") >= 0) {
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

  $("#single_order_details").height($("#single_order").height());
});
