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
var ordersChart,
    cakesChart,
    fillingsChart,
    decorationsChart,
    ordersOptions = {
      chart: {
        backgroundColor: null,
        type: "line"
      },
      credits: {enabled: false},
      legend: {enabled: false},
      title: {text: "Orders by month"},
      subtitle: {text: "How many orders were placed in each month?"},
      xAxis: {categories: ""},
      yAxis: {
        title: {text: "Number of orders"},
        min: 0,
        plotLines: [{
          width: 1,
          color: "#21a2e6"
        }]
      },
      tooltip: {
        formatter: function() {
          if (this.y == 0) {
            return "There were <b>no orders</b> placed in <b>" + this.x + "</b>";
          } else if (this.y == 1) {
            return "There was just <b>" + this.y + " order</b> placed in <b>" + this.x + "</b>";
          } else {
            return "There were <b>" + this.y + " orders</b> placed in <b>" + this.x + "</b>";
          }
        }
      },
      series: [{
        name: "Orders",
        data: ""
      }]
    },
    cakesOptions = {
      chart: {
        backgroundColor: null,
        type: "column"
      },
      credits: {enabled: false},
      legend: {enabled: false},
      title: {text: "Cake type popularity"},
      subtitle: {text: "How popular are all the cake types?"},
      xAxis: {categories: ""},
      yAxis: {
        title: {text: "Number of orders"},
        min: 0,
        plotLines: [{
          width: 1,
          color: "#21a2e6"
        }]
      },
      tooltip: {
        formatter: function() {
          if (this.y == 0) {
            return "The <b>" + this.x + "</b> cake type has <b>never been chosen</b>";
          } else if (this.y == 1) {
            return "The <b>" + this.x + "</b> cake type has only been chosen <b>once</b>";
          } else {
            return "The <b>" + this.x + "</b> cake type has been chosen <b>" + this.y + " times</b>";
          }
        }
      },
      series: [{
        name: "Cake Types",
        data: ""
      }]
    },
    fillingsOptions = {
      chart: {
        backgroundColor: null,
        type: "column"
      },
      credits: {enabled: false},
      legend: {enabled: false},
      title: {text: "Filling Popularity"},
      subtitle: {text: "What's your most popular filling?"},
      xAxis: {categories: ""},
      yAxis: {
        title: {text: "Times filling was chosen"},
        min: 0,
        plotLines: [{
          width: 1,
          color: "#21a2e6"
        }]
      },
      tooltip: {
        formatter: function() {
          if (this.y == 0) {
            return "The <b>" + this.x + "</b> filling has <b>never been chosen</b>";
          } else if (this.y == 1) {
            return "<b>" + this.x + "</b> has only filled a cake <b>once</b>";
          } else {
            return "<b>" + this.x + "</b> has been a filling in <b>" + this.y + " orders</b>";
          }
        }
      },
      series: [{
        name: "Fillings",
        data: ""
      }]
    },
    decorationsOptions = {
      chart: {
        backgroundColor: null,
        type: "column"
      },
      credits: {enabled: false},
      legend: {enabled: false},
      title: {text: "Decoration popularity"},
      subtitle: {text: "What's your most popular decoration?"},
      xAxis: {categories: ""},
      yAxis: {
        title: {text: "Times decoration was chosen"},
        min: 0,
        plotLines: [{
          width: 1,
          color: "#21a2e6"
        }]
      },
      tooltip: {
        formatter: function() {
          if (this.y == 0) {
            return "The <b>" + this.x + "</b> decoration has <b>never been chosen</b>";
          } else if (this.y == 1) {
            return "The <b>" + this.x + "</b> decoration has only decorated <b>one</b> order";
          } else {
            return "<b>" + this.x + "</b> has decorated <b>" + this.y + " orders</b>";
          }
        }
      },
      series: [{
        name: "Decorations",
        data: ""
      }]
    };
$(document).ready(function() {
  // Check if the browser supports canvas; if it does, start displaying the charts.
  // Otherwise show an error dialog asking to upgrade to a modern browser.
  if (Modernizr.canvas) {
    // Calculate the dimensions of the charts and get the data
    calculateWidth();
    getData();

    // If the user isn't on mobile, redraw the charts when the window is resized.
    // Constant canvas animations don't work as smoothly as they should on mobile devices.
    // Also update chart data every 10 seconds, this isn't practical on mobile.
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
  if (ordersChart) ordersChart.showLoading();
  if (cakesChart) cakesChart.showLoading();
  if (fillingsChart) fillingsChart.showLoading();
  if (decorationsChart) decorationsChart.showLoading();
  $.ajax({
    type: 'post',
    url: '../lib/stats.php',
    success: function(response) {
      // Parse the JSON data returned
      object = JSON.parse(response);
      
      // Set the vars for the chart data
      ordersOptions.series[0].data        = object.orders.data;
      ordersOptions.xAxis.categories      = object.orders.labels;
      cakesOptions.series[0].data         = object.cakes.data;
      cakesOptions.xAxis.categories       = object.cakes.labels;
      fillingsOptions.series[0].data      = object.fillings.data;
      fillingsOptions.xAxis.categories    = object.fillings.labels;
      decorationsOptions.series[0].data   = object.decorations.data;
      decorationsOptions.xAxis.categories = object.decorations.labels;

      // Draw the charts
      drawCharts();
    }
  });
}

// A function which calculates and sets the dimensions of the charts.
// The height of the chart is the height of the window, minus 200px for niceties.
// The width of the chart is the width of the div it's in, which should be 100%.
function calculateWidth() {
  var height = window.innerHeight - 200,
      width  = $("#stats").width();

  $("#ordersChart, #cakesChart, #fillingsChart, #decorationsChart").width(width).height(height);
}

// A function to draw the charts
function drawCharts() {
  // Hide the "loading" overlay
  if (ordersChart) ordersChart.hideLoading();
  if (cakesChart) cakesChart.hideLoading();
  if (fillingsChart) fillingsChart.hideLoading();
  if (decorationsChart) decorationsChart.hideLoading();

  // Draw the charts
  $("#ordersChart").highcharts(ordersOptions);
  $("#cakesChart").highcharts(cakesOptions);
  $("#fillingsChart").highcharts(fillingsOptions);
  $("#decorationsChart").highcharts(decorationsOptions);

  // Set the vars
  ordersChart      = $("#ordersChart").highcharts();
  cakesChart       = $("#cakesChart").highcharts();
  fillingsChart    = $("#fillingsChart").highcharts();
  decorationsChart = $("#decorationsChart").highcharts();
}
