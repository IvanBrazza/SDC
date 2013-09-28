$(document).ready(function() {
  $("form").parsley('addListener', {
    onFieldError: function ( elem ) {
      $(".form>form>div").css("margin-bottom", "20px");
    }
  });

  $(".date").datepicker({
    minDate: 0,
    dateFormat: "yy-mm-dd"
  });

  $("#datetime").datetimepicker({
    dateFormat: "yy-mm-dd",
    timeFormat: "HH:mm:ss",
    minDate: 0
  });

  $("#order").css("padding-bottom", "100px");
            
  $("#agreed_price, #delivery_charge").bind("keyup", function() {
    $agreed_price = $("#agreed_price").val();
    $delivery_charge = $("#delivery_charge").val();
    $result = Number($agreed_price) + Number($delivery_charge);
    $("#grand_total").html("<b>&pound;" + $result + "</b>");
  });
});

function checkExisting()
{
  if ($("#existing_id").val() !== "null")
  {
    $("#first_name").prop("disabled", true);
    $("#first_name").val("");

    $("#last_name").prop("disabled", true);
    $("#last_name").val("");

    $("#address").prop("disabled", true);
    $("#address").val("");

    $("#postcode").prop("disabled", true);
    $("#postcide").val("");

    $("#phone").prop("disabled", true);
    $("#phone").val("");

    $("#email").prop("disabled", true);
    $("#email").val("");
  }
  else
  {
    $("#first_name").prop("disabled", false);

    $("#last_name").prop("disabled", false);

    $("#address").prop("disabled", false);

    $("#postcode").prop("disabled", false);

    $("#phone").prop("disabled", false);

    $("#email").prop("disabled", false);
  }
}
