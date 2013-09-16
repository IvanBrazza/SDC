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
    timeFormat: "HH-mm-ss",
    minDate: 0
  });

  $("#order").css("padding-bottom", "80px");
});
