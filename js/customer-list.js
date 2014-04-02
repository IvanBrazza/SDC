$(document).ready(function() {
  // When the customer search form is submitted
  $("#customer_search").submit(function(e) {
    // Show the loading spinner
    loader.Show();
    // Make an AJAX call to lib/form/order-search.php
    // to see if the order exists. If it does, redirect
    // to that page, otherwise show an error message
    $.ajax({
      type: 'post',
      url: '../lib/form/customer-search.php',
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

  // Enable autocomplete for the order search form
  // with all of the order numbers. If the user
  // clicks on one of these autocompletes, insert
  // that value into the form and submit it
  $("#customer_name").autocomplete({
    source: customerNames,
    select: function(event, ui) {
      $("#customer_name").val(ui.item.value);
      $("#customer_search").submit();
    }
  });
});
