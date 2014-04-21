/**
  js/stats.js - code specific to the stats page
**/
// Set some vars, declare ordersData, fillingsData, decorationsData
// and usersData as global so they can be used in all functions,
// intialise object and fillColour
if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
  var animation = false;
} else {
  var animation = true;
}
var ordersData,
    fillingsData,
    decorationsData,
    usersData,
    object = {orders: "init"},
    ordersOptions = {
      animation: animation,
      scaleOverride: true,
      scaleStepWidth: 1,
      scaleStartValue: 0
    },
    cakesOptions = {
      animation: animation,
      scaleOverride: true,
      scaleStepWidth: 1,
      scaleStartValue: 0
    },
    fillingsOptions = {
      animation: animation,
      scaleOverride: true,
      scaleStepWidth: 1,
      scaleStartValue: 0
    },
    decorationsOptions = {
      animation: animation,
      scaleOverride: true,
      scaleStepWidth: 1,
      scaleStartValue: 0
    } 
$(document).ready(function() {
  // Check if the browser supports canvas; if it does, start displaying the charts.
  // Otherwise show an error dialog asking to upgrade to a modern browser.
  if (Modernizr.canvas) {
    // Calculate the dimensions of the charts and get the data
    calculateWidth();
    getData();
    
    // Check for changes in the stats every 10 seconds
    window.setInterval(function() {
      getData();
    }, 10000);

    // If the user isn't on mobile, redraw the charts when the window is resized.
    // Constant canvas animations don't work as smoothly as they should on mobile devices.
    if (!/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
      $(window).resize(function() {
        calculateWidth();
        drawCharts();
      });
    }
  } else {
    // Display the unsupported browser dialog
    $("#unsupported_browser").modal("show");

    // Hide stats charts
    $("canvas").closest("div").remove();
    $("a[href=#stats-by-month], a[href=#stats-cake-types], a[href=#stats-fillings], a[href=#stats-decorations]").closest("li").remove();
  }
});

// A function which gets the data for the charts via AJAX, sets the appropriate vars,
// and draws the charts. 
function getData() {
  $.ajax({
    type: 'post',
    url: '../lib/stats.php',
    success: function(response) {
      // Parse the JSON data returned
      object = JSON.parse(response);

      // Set the vars for the chart data
      ordersData = object.orders;
      ordersOptions.scaleSteps = Math.max.apply(Math, ordersData.datasets[0].data) + 1;
      cakesData = object.cakes;
      cakesOptions.scaleSteps = Math.max.apply(Math, cakesData.datasets[0].data) + 1;
      fillingsData = object.fillings;
      fillingsOptions.scaleSteps = Math.max.apply(Math, fillingsData.datasets[0].data) + 1;
      decorationsData = object.decorations;
      decorationsOptions.scaleSteps = Math.max.apply(Math, decorationsData.datasets[0].data) + 1;

      // Draw the charts
      drawCharts();

      // Disable further chart animations
      ordersOptions.animation = false;
      cakesOptions.animation = false;
      fillingsOptions.animation = false;
      decorationsOptions.animation = false;
    }
  });
}

// A function which calculates and sets the dimensions of the charts.
// The height of the chart is the height of the window, minus 200px for niceties.
// The width of the chart is the width of the div it's in, which should be 100%.
function calculateWidth() {
  var height           = window.innerHeight - 200,
      ordersWidth      = $("#ordersChart").closest("div").width(),
      cakesWidth       = $("#cakesChart").closest("div").width(),
      fillingsWidth    = $("#fillingsChart").closest("div").width(),
      decorationsWidth = $("#decorationsChart").closest("div").width();

  $("#ordersChart").attr("width", ordersWidth + "px")
                   .attr("height", height + "px");

  $("#cakesChart").attr("width", cakesWidth + "px")
                  .attr("height", height + "px");

  $("#fillingsChart").attr("width", fillingsWidth + "px")
                     .attr("height", height + "px");

$("#decorationsChart").attr("width", decorationsWidth + "px")
                        .attr("height", height + "px");
}

// A function to set the contexts of the charts, and draw them with the charts plugin
function drawCharts() {
  var ordersCtx        = $("#ordersChart").get(0).getContext("2d"),
      cakesCtx         = $("#cakesChart").get(0).getContext("2d"),
      fillingsCtx      = $("#fillingsChart").get(0).getContext("2d"),
      decorationsCtx   = $("#decorationsChart").get(0).getContext("2d"),
      ordersChart      = new Chart(ordersCtx).Line(ordersData, ordersOptions),
      cakesChart       = new Chart(cakesCtx).Bar(cakesData, cakesOptions),
      fillingsChart    = new Chart(fillingsCtx).Bar(fillingsData, fillingsOptions),
      decorationsChart = new Chart(decorationsCtx).Bar(decorationsData, decorationsOptions);
}
