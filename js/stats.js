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
    fillColour;
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
        cakesData = object.cakes;
        fillingsData = object.fillings;
        decorationsData = object.decorations;
        drawCharts();
      }
      clear = true;
    }
  });
}

// A function which calculates and sets the dimensions
// of the charts
function calculateWidth() {
  // Set var width to 49% of the available container width
  // since the the layout of the charts is 2x2
  var width = $(".container").width() * 0.49;

  // Set the width of the first chart to 90% of the
  // available width for one chart. Set the height to
  // 80% of the width variable. Set the width of the first
  // charts container to the full width of one chart
  $("#ordersChart").attr("width", width * 0.90 + "px");
  $("#ordersChart").attr("height", width * 0.80 + "px");
  $("#orders-chart").width(width + "px");

  // Set the width of the second chart to 90% of the
  // available width for one chart. Set the height to
  // 80% of the width variable. Set the width of the second
  // charts container to the full width of one chart
  $("#cakesChart").attr("width", width * 0.90 + "px");
  $("#cakesChart").attr("height", width * 0.80 - 1 + "px");
  $("#cakes-chart").width(width + "px");

  // Set the width of the third chart to 90% of the
  // available width for one chart. Set the height to
  // 80% of the width variable. Set the width of the third
  // charts container to the full width of one chart
  $("#fillingsChart").attr("width", width * 0.90 + "px");
  $("#fillingsChart").attr("height", width * 0.80 + "px");
  $("#fillings-chart").width(width + "px");

  // Set the width of the fourth chart to 90% of the
  // available width for one chart. Set the height to
  // 80% of the width variable. Set the width of the fourth
  // charts container to the full width of one chart
  $("#decorationsChart").attr("width", width * 0.90 + "px");
  $("#decorationsChart").attr("height", width * 0.80 + "px");
  $("#decorations-chart").width(width + "px");
}

// A function to draw all 4 charts
function drawCharts() {
  // Set the variables for the first chart and its context
  // and call the drawLineChart function
  var ordersCan = $("#ordersChart"),
      ordersCtx = ordersCan[0].getContext("2d");
  drawLineChart(ordersData, ordersCtx, ordersCan);

  // Set the context variable for the second chart and call
  // the drawBarChart function
  var cakesCtx = $("#cakesChart").get(0).getContext("2d");
  drawBarChart(cakesData, cakesCtx, document.getElementById("cakesChart"));

  // Set the context variable for the third chart and call
  // the drawBarChart function
  var fillingsCtx = $("#fillingsChart").get(0).getContext("2d");
  drawBarChart(fillingsData, fillingsCtx, document.getElementById("fillingsChart"));

  // Set the context variable for the fourth chart and call
  // the drawBarChart function
  var decorationsCtx = $("#decorationsChart").get(0).getContext("2d");
  drawBarChart(decorationsData, decorationsCtx, document.getElementById("decorationsChart"));
}

// A function which draws a line chart using the data
// passed to it to the canvas passed to it
function drawLineChart(data, ctx, can) {
  var xPadding = 30,
      yPadding = 30;

  // Returns the max Y value in our data list
  function getMaxY() {
    var max = 0;

    for(var i = 0; i < data.values.length; i ++) {
      if(data.values[i].Y > max) {
        max = data.values[i].Y;
      }
    }

    max += 10 - max % 10;
    return max;
  }

  // Return the x pixel for a graph point
  function getXPixel(val) {
    return ((can.width() - xPadding) / data.values.length) * val + (xPadding * 1.5);
  }

  // Return the y pixel for a graph point
  function getYPixel(val) {
    return can.height() - (((can.height() - yPadding) / getMaxY()) * val) - yPadding;
  }

  ctx.lineWidth = 2;
  ctx.strokeStyle = 'rgba(128,128,255, 0.2)';
  ctx.font = '8pt Open Sans';
  ctx.textAlign = "center";

  // Draw the axises
  ctx.beginPath();
  ctx.moveTo(xPadding, 0);
  ctx.lineTo(xPadding, can.height() - yPadding);
  ctx.lineTo(can.width(), can.height() - yPadding);
  ctx.stroke();

  // Draw the X value texts
  for(var i = 0; i < data.values.length; i ++) {
    ctx.fillText(data.values[i].X, getXPixel(i), can.height() - yPadding + 20);
  }

  // Draw the Y value texts
  ctx.textAlign = "right"
  ctx.textBaseline = "middle";
  ctx.lineWidth = 1;

  for(var i = 0; i < getMaxY(); i += 5) {
    ctx.fillText(i, xPadding - 10, getYPixel(i));
    ctx.beginPath();
    ctx.moveTo(xPadding, getYPixel(i));
    ctx.lineTo(can.width(), getYPixel(i));
    ctx.stroke();
  }

  ctx.lineWidth = 2;
  ctx.strokeStyle = 'rgba(151,187,205,1)';

  // Draw the line graph
  ctx.beginPath();
  ctx.moveTo(getXPixel(0), getYPixel(data.values[0].Y));
  for(var i = 1; i < data.values.length; i ++) {
    ctx.lineTo(getXPixel(i), getYPixel(data.values[i].Y));
  }
  ctx.stroke();

  // Draw the dots
  ctx.fillStyle = 'rgba(134, 168, 185, 1)';

  for(var i = 0; i < data.values.length; i ++) {
    ctx.beginPath();
    ctx.arc(getXPixel(i), getYPixel(data.values[i].Y), 3, 0, Math.PI * 2, true);
    ctx.fill();
  }
}

// A function which draws a bar chart using the data
// passed to it to the canvas passed to it
function drawBarChart(data, ctx, can) {
  if (clear) ctx.clearRect(0, 0, can.width, can.height);
  var y, tx, ty, metrics, words, line, testLine, testWidth;
  var dataName = data.name;
  var dataValue = data.value;
  var colHead = 50;
  var rowHead = 30;
  var margin = 10;
  var maxVal = Math.ceil(Math.max.apply(Math, dataValue)/5) * 5;
  var stepSize = 5;
  var yScalar = (can.height - colHead - margin) / (maxVal);
  var xScalar = (can.width - rowHead) / (dataName.length + 1);
  ctx.lineWidth = 0.5;
  ctx.strokeStyle = "rgba(128,128,255, 0.5)"; // light blue line
  ctx.beginPath();
  // print row header and draw horizontal grid lines
  ctx.font = "10pt Open Sans"
  var count =  0;
  for (scale = maxVal; scale >= 0; scale -= stepSize) {
    y = colHead + (yScalar * count * stepSize);
    ctx.fillText(scale, margin,y + margin);
    ctx.moveTo(rowHead, y + margin - 1)
    ctx.lineTo(can.width, y + margin -1)
    count++;
  }
  ctx.stroke();
  ctx.save();
  // set a color
  ctx.fillStyle = "rgba(151,187,205,1)";
  // translate to bottom of graph and scale x,y to match data
  ctx.translate(0, can.height - margin);
  ctx.scale(xScalar, -1 * yScalar);
  // draw bars
  for (i = 0; i < dataName.length; i++) {
    ctx.fillRect(i + 1, -2, 0.5, dataValue[i]);
  }
  ctx.restore();
 
  // label samples
  ctx.font = "8pt Open Sans";
  ctx.textAlign = "center";
  for (i = 0; i < dataName.length; i++) {
    calcY(dataValue[i]);
    ty = y - margin - 5;
    tx = xScalar * (i + 1) + 14;
    words = dataName[i].split(' ');
    line = '';
    for(var n = 0; n < words.length; n++) {
      testLine = line + words[n] + ' ';
      metrics = ctx.measureText(testLine);
      testWidth = metrics.width;
      if (testWidth > 20 && n > 0) {
        ctx.fillText(line, tx, ty - 8);
        line = words[n] + ' ';
      }
      else {
        line = testLine;
      }
    }
    ctx.fillText(line, tx, ty + 8);
  }
  function calcY(value) {
    y = can.height - value * yScalar;
  } 
}
