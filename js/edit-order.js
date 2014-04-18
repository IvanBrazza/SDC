$(document).ready(function() {
  $(document).tooltip();

  calculateDeliveryCharge();
  calculateOrderTotal();

  $("#theCakeNext").click(function() {
    // If the link isn't disabled, go to the next tab
    var date_check = validate.date();
    if ($("#comments").data("required") == "true") {
      var comm_check = validate.input("textarea#comments", "#comments_error");
      if (comm_check && date_check) {
        $("#theCake").collapse("hide");
        $("#deliveryPanel").collapse("show");
      }
    } else {
      if (date_check) {
        $("#theCake").collapse("hide");
        $("#deliveryPanel").collapse("show");
      }
    }
  });

  $("#deliveryNext").click(function() {
    var datt_check = validate.datetime();
    if (datt_check) {
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
