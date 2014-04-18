$(document).ready(function() {
  $(document).tooltip();

  calculateDeliveryCharge();
  calculateOrderTotal();

  var deliveryPanelHeight = $("#deliveryPanel").height(),
      $celeb_date = $("input[name=celebration_date]").pickadate({
        clear: '',
        formatSubmit: 'yyyy-mm-dd',
        hiddenName: true,
        format: 'mmmm dd, yyyy',
        onClose: function() {
          if ($celeb_date.pickadate("get") == "") {
            $celeb_date.closest(".form-group").removeClass("has-success").addClass("has-error");
            $("#celebration_date_error").html("Please select a celebration date").slideDown("fast");
          } else {
            $celeb_date.closest(".form-group").removeClass("has-error").addClass("has-success");
            $("#celebration_date_error").slideUp("fast");
          }
        },
        onSet: function(context) {
          $("#celebration-date-review").html($celeb_date.pickadate("get"));
        }
      }),
      $dt_date = $("input[name=datetime_date]").pickadate({
        clear: '',
        formatSubmit: 'yyyy-mm-dd',
        hiddenName: true,
        format: 'mmmm dd, yyyy',
        onOpen: function() {
          $("#deliveryPanel").stop().animate({height: "500px"});
        },
        onClose: function() {
          $("#deliveryPanel").stop().animate({height: deliveryPanelHeight});
          if ($dt_date.pickadate("get") == "") {
            $dt_date.closest(".form-group").removeClass("has-success").addClass("has-error");
            $("#datetime_date_error").html("Please select a date").slideDown("fast");
          } else {
            $dt_date.closest(".form-group").removeClass("has-error").addClass("has-success");
            $("#datetime_date_error").slideUp("fast");
          }
        },
        onSet: function(context) {
          $("#datetime-review").html($dt_date.pickadate("get") + " " + $dt_time.pickatime("get"));
        }
      }),
      $dt_time = $("input[name=datetime_time]").pickatime({
        clear: '',
        hiddenName: true,
        format: 'h:i A',
        formatSubmit: 'HH:i:00',
        interval: 15,
        max: [18,0],
        min: [8,0],
        onOpen: function() {
          $("#deliveryPanel").stop().animate({height: "640px"});
        },
        onClose: function() {
          $("#deliveryPanel").stop().animate({height: deliveryPanelHeight});
          if ($dt_time.pickadate("get") == "") {
            $dt_time.closest(".form-group").removeClass("has-success").addClass("has-error");
            $("#datetime_time_error").html("Please select a date").slideDown("fast");
          } else {
            $dt_time.closest(".form-group").removeClass("has-error").addClass("has-success");
            $("#datetime_time_error").slideUp("fast");
          }
        },
        onSet: function(context) {
          $("#datetime-review").html($dt_date.pickadate("get") + " " + $dt_time.pickatime("get"));
        }
      });

  $("#theCakeNext").click(function() {
    var $celeb_date = $("#celebration_date_hidden");
    if ($("#comments").data("required") == "true") {
      var comm_check = validate.input("textarea#comments", "#comments_error");
      if (comm_check && $celeb_date.val() != "") {
        $("#theCake").collapse("hide");
        $("#uploadAPhoto").collapse("show");
      } else if ($celeb_date == "") {
        $celeb_date.closest(".form-group").removeClass("has-success").addClass("has-error");
        $("#celebration-date-error").html("Please select a celebration date").slideDown("fast");
      }
    } else {
      if ($celeb_date.val() != "") {
        $("#theCake").collapse("hide");
        $("#deliveryPanel").collapse("show");
      } else {
        $celeb_date.closest(".form-group").removeClass("has-success").addClass("has-error");
        $("#celebration_date_error").html("Please select a celebration date").slideDown("fast");
      }
    }
  });

  $("#deliveryNext").click(function() {
    var $datetime_date = $("#datetime_date_hidden"),
        $datetime_time = $("#datetime_time_hidden");
    if ($datetime_date.val() != "" && $datetime_time.val() != "") {
      $.ajax({
        type: 'post',
        url: '../lib/get-order.php',
        data: {order: QueryString.order},
        success: function(response) {
          var object         = JSON.parse(response),
              old_total      = parseInt(object.base_price) +
                               parseInt(object.filling_price) +
                               parseInt(object.decor_price) +
                               parseInt(object.delivery_charge),
              old_difference = parseInt(object.difference),
              new_total      = parseInt($("#total-html").html()),
              new_difference = old_total - new_total;
          $("#difference-html").html(old_difference + new_difference);
        }
      });
      $("#deliveryPanel").collapse("hide");
      $("#review").collapse("show");
    } else {
      if ($datetime_date.val() == "") {
        $datetime_date.closest(".form-group").removeClass("has-success").addClass("has-error");
        $("#datetime_date_error").html("Please select a date").slideDown("fast");
      }
      if ($datetime_time.val() == "") {
        $datetime_time.closest(".form-group").removeClass("has-success").addClass("has-error");
        $("#datetime_time_error").html("Please select a time").slideDown("fast");
      }
    }
  });

  $("#deliveryPrevious").click(function() {
    $("#theCake").collapse("show");
    $("#deliveryPanel").collapse("hide");
  });

  $("#reviewPrevious").click(function() {
    $("#deliveryPanel").collapse("show");
    $("#review").collapse("hide");
  });

  $("#edit-order-form").submit(function(e) {
    var difference = parseInt($("#difference-html").html());
    if (difference != 0) {
      if (difference < 0) {
        var text = 'you will owe us &pound;' + Math.abs(difference);
      } else if (difference > 0) {
        var text = 'we will owe you &pound;' + Math.abs(difference);
      }
      $('<div class="modal fade" style="overflow-y:auto;"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h4 class="modal-title">There\'s a difference!</h4>'+
        '</div><div class="modal-body"><p>Now that you\'ve edited your order, it seems that there\'s a difference of <b>&pound;' + difference +
        '</b> between the original order and the edited one.</p><p>This means that <b>' + text + '</b>.</p><p>We will contact you soon regarding this difference should ' +
        'you click the <b>Continue</b> button below, and in doing so accept the difference incurred.' +
        '</b></p></div><div class="modal-footer"><button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>' +
        '<button type="button" class="btn btn-primary" onclick="continueEdit()">Continue</button></div></div></div></div>').modal({
        backdrop: 'static',
        keyboard: 'false'
      });
      e.preventDefault();
    } else {
      NProgress.configure({
        trickleRate:  0.1,
        trickleSpeed: 500
      });
      NProgress.start();
    }
  });

  $("select[name=delivery]").change(function() {
    if ($(this).val() === "Deliver To Address") {
      calculateDeliveryCharge();
    }
  });
});

var QueryString = function () {
  var query_string = {};
  var query = window.location.search.substring(1);
  var vars = query.split("&");
  for (var i=0;i<vars.length;i++) {
    var pair = vars[i].split("=");
    if (typeof query_string[pair[0]] === "undefined") {
      query_string[pair[0]] = pair[1];
    } else if (typeof query_string[pair[0]] === "string") {
      var arr = [ query_string[pair[0]], pair[1] ];
      query_string[pair[0]] = arr;
    } else {
      query_string[pair[0]].push(pair[1]);
    }
  } 
    return query_string;
} ();

function continueEdit() {
  $("#edit-order-form").unbind("submit").submit();
  NProgress.configure({
    trickleRate:  0.1,
    trickleSpeed: 500
  });
  NProgress.start();
}
