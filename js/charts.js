$(document).ready(function() {
  var ordersCtx = $("#ordersChart").get(0).getContext("2d");
  var ordersChart = new Chart(ordersCtx).Bar(ordersData,ordersOptions);
  var usersCtx = $("#usersChart").get(0).getContext("2d");
  var usersChart = new Chart(usersCtx).Pie(usersData);
});
