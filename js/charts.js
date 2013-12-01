$(document).ready(function() {
  calculateWidth();
  drawCharts();
  ordersOptions.animation = false;
  usersOptions.animation = false;
  fillingsOptions.animation = false;
  decorationsOptions.animation = false;

  $(window).resize(function() {
    calculateWidth();
    drawCharts();
  });
});

function calculateWidth() {
  var width = $(".container").width() * 0.49;
  $("#ordersChart").attr("width", width * 0.90 + "px");
  $("#ordersChart").attr("height", width * 0.80 + "px");
  $("#orders-chart").width(width + "px");

  $("#usersChart").attr("width", width * 0.70 + "px");
  $("#usersChart").attr("height", width * 0.70 + "px");
  $("#users-chart").width(width + "px");

  $("#fillingsChart").attr("width", width * 0.90 + "px");
  $("#fillingsChart").attr("height", width * 0.80 + "px");
  $("#fillings-chart").width(width + "px");

  $("#decorationsChart").attr("width", width * 0.90 + "px");
  $("#decorationsChart").attr("height", width * 0.80 + "px");
  $("#decorations-chart").width(width + "px");
}

function drawCharts() {
  var ordersCtx = $("#ordersChart").get(0).getContext("2d");
  var ordersChart = new Chart(ordersCtx).Bar(ordersData, ordersOptions);

  var usersCtx = $("#usersChart").get(0).getContext("2d");
  var usersChart = new Chart(usersCtx).Pie(usersData, usersOptions);

  var fillingsCtx = $("#fillingsChart").get(0).getContext("2d");
  var fillingsChart = new Chart(fillingsCtx).Bar(fillingsData, fillingsOptions);

  var decorationsCtx = $("#decorationsChart").get(0).getContext("2d");
  var decorationsChart = new Chart(decorationsCtx).Bar(decorationsData, decorationsOptions);
}
