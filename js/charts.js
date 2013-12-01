$(document).ready(function() {
  calculateWidth();
  drawCharts();

  $(window).resize(function() {
    calculateWidth();
    drawCharts();
  });
});

function calculateWidth() {
  var width = $(".container").width() * 0.49;
  $("#ordersChart").attr("width", width * 0.90 + "px");
  $("#ordersChart").attr("height", width * 0.80 + "px");
  $("#usersChart").attr("width", width * 0.70 + "px");
  $("#usersChart").attr("height", width * 0.70 + "px");
  $("#users-chart").width(width + "px");
  $("#orders-chart").width(width + "px");
}

function drawCharts() {
  var ordersCtx = $("#ordersChart").get(0).getContext("2d");
  var ordersChart = new Chart(ordersCtx).Bar(ordersData, ordersOptions);
  var usersCtx = $("#usersChart").get(0).getContext("2d");
  var usersChart = new Chart(usersCtx).Pie(usersData, usersOptions);
}
