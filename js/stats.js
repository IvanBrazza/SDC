/**
  js/stats.js - code specific to the stats page
**/
// Set some vars, declare ordersData, fillingsData, decorationsData
// and usersData as global so they can be used in all functions, initialise
// clear to false, intialise object and fillColour
var ordersData,
    fillingsData,
    decorationsData,
    usersData,
    clear = false,
    object = {orders: "init"},
    ordersOptions = {
      animation: true,
      scaleOverride: true,
      scaleStepWidth: 1,
      scaleStartValue: 0
    },
    cakesOptions = {
      animation: true,
      scaleOverride: true,
      scaleStepWidth: 1,
      scaleStartValue: 0
    },
    fillingsOptions = {
      animation: true,
      scaleOverride: true,
      scaleStepWidth: 1,
      scaleStartValue: 0
    },
    decorationsOptions = {
      animation: true,
      scaleOverride: true,
      scaleStepWidth: 1,
      scaleStartValue: 0
    } 
$(document).ready(function() {
  // Check if the browser supports canvas using
  // Modernizr, if it does, start displaying the charts
  // otherwise show an error dialog asking to upgrade
  // to a modern browser
  if (Modernizr.canvas) {
    // Calculate the dimensions of the charts and get the data
    calculateWidth();
    getData();
    
    // Check for changes in the stats every 5 seconds
    window.setInterval(function() {
      getData();
    }, 5000);

    // Calculate the dimensions of the charts and redraw
    // them when the window resizes because canvas doesn't
    // like to be resized
    $(window).resize(function() {
      calculateWidth();
      drawCharts();
    });
  } else {
    $("#browser-dialog").dialog({
      buttons: [
        {
          text: "Get Google Chrome",
          click: function() {
            window.location.href="http://www.google.com/chrome";
          }
        },
        {
          text: "Close",
          click: function() {
            $(this).dialog("close");
          }
        }
      ],
      draggable: false,
      modal: true,
      position: {
        my: "center",
        at: "top+20%",
        of: window
      },
      resizable: false,
      width: 500
    });
  }
});

// A function which gets the data for the charts
// using an AJAX call, sets the appropriate vars,
// and draws the charts
function getData() {
  $.ajax({
    type: 'post',
    url: '../lib/stats.php',
    success: function(response) {
      if (JSON.stringify(object.orders) != JSON.stringify(JSON.parse(response).orders)) {
        object = JSON.parse(response);
        ordersData = object.orders;
        ordersOptions.scaleSteps = Math.max.apply(Math, ordersData.datasets[0].data) + 1;

        cakesData = object.cakes;
        cakesOptions.scaleSteps = Math.max.apply(Math, cakesData.datasets[0].data) + 1;

        fillingsData = object.fillings;
        fillingsOptions.scaleSteps = Math.max.apply(Math, fillingsData.datasets[0].data) + 1;

        decorationsData = object.decorations;
        decorationsOptions.scaleSteps = Math.max.apply(Math, decorationsData.datasets[0].data) + 1;

        drawCharts();
        ordersOptions.animation = false;
        cakesOptions.animation = false;
        fillingsOptions.animation = false;
        decorationsOptions.animation = false;
      }
      clear = true;
    }
  });
}

// A function which calculates and sets the dimensions
// of the charts
function calculateWidth() {
  var height = window.innerHeight - 200;

  // Set the width of the first chart to 90% of the
  // available width for one chart. Set the height to
  // 80% of the width variable. Set the width of the first
  // charts container to the full width of one chart
  var ordersWidth = $("#ordersChart").closest("div").width();
  $("#ordersChart").attr("width", ordersWidth + "px");
  $("#ordersChart").attr("height", height + "px");

  // Set the width of the second chart to 90% of the
  // available width for one chart. Set the height to
  // 80% of the width variable. Set the width of the second
  // charts container to the full width of one chart
  var cakesWidth = $("#cakesChart").closest("div").width();
  $("#cakesChart").attr("width", cakesWidth + "px");
  $("#cakesChart").attr("height", height + "px");

  // Set the width of the third chart to 90% of the
  // available width for one chart. Set the height to
  // 80% of the width variable. Set the width of the third
  // charts container to the full width of one chart
  var fillingsWidth = $("#fillingsChart").closest("div").width();
  $("#fillingsChart").attr("width", fillingsWidth + "px");
  $("#fillingsChart").attr("height", height + "px");

  // Set the width of the fourth chart to 90% of the
  // available width for one chart. Set the height to
  // 80% of the width variable. Set the width of the fourth
  // charts container to the full width of one chart
  var decorationsWidth = $("#decorationsChart").closest("div").width();
  $("#decorationsChart").attr("width", decorationsWidth + "px");
  $("#decorationsChart").attr("height", height + "px");
}

// A function to draw all 4 charts
function drawCharts() {
  // Set the variables for the first chart and its context
  // and call the drawLineChart function
  var ordersCtx = $("#ordersChart").get(0).getContext("2d"),
      ordersChart = new Chart(ordersCtx).Line(ordersData, ordersOptions);

  // Set the context variable for the second chart and call
  // the drawBarChart function
  var cakesCtx = $("#cakesChart").get(0).getContext("2d"),
      cakesChart = new Chart(cakesCtx).Bar(cakesData, cakesOptions);

  // Set the context variable for the third chart and call
  // the drawBarChart function
  var fillingsCtx = $("#fillingsChart").get(0).getContext("2d"),
      fillingsChart = new Chart(fillingsCtx).Bar(fillingsData, fillingsOptions);

  // Set the context variable for the fourth chart and call
  // the drawBarChart function
  var decorationsCtx = $("#decorationsChart").get(0).getContext("2d"),
      decorationsChart = new Chart(decorationsCtx).Bar(decorationsData, decorationsOptions);
}
