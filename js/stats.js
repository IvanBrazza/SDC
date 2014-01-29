var ordersData,
    fillingsData,
    decorationsData,
    usersData,
    clear = false,
    object = {orders: "init"},
    fillColour;
$(document).ready(function() {
  if (Modernizr.canvas) {
    calculateWidth();
    getData();

//    window.setInterval(function() {
 //     getData();
  //  }, 5000);

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

function getData() {
  $.ajax({
    type: 'post',
    url: '../lib/stats.php',
    success: function(response) {
      if (JSON.stringify(object.orders) != JSON.stringify(JSON.parse(response).orders)) {
        object = JSON.parse(response);
        console.log(object);
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
function calculateWidth() {
  var width = $(".container").width() * 0.49;
  $("#ordersChart").attr("width", width * 0.90 + "px");
  $("#ordersChart").attr("height", width * 0.80 + "px");
  $("#orders-chart").width(width + "px");

  $("#cakesChart").attr("width", width * 0.90 + "px");
  $("#cakesChart").attr("height", width * 0.80 - 1 + "px");
  $("#cakes-chart").width(width + "px");

  $("#fillingsChart").attr("width", width * 0.90 + "px");
  $("#fillingsChart").attr("height", width * 0.80 + "px");
  $("#fillings-chart").width(width + "px");

  $("#decorationsChart").attr("width", width * 0.90 + "px");
  $("#decorationsChart").attr("height", width * 0.80 + "px");
  $("#decorations-chart").width(width + "px");
}

function drawCharts() {
  var ordersCan = $("#ordersChart"),
      ordersCtx = ordersCan[0].getContext("2d");
  drawLineChart(ordersData, ordersCtx, ordersCan);

  var cakesCtx = $("#cakesChart").get(0).getContext("2d");
  drawBarChart(cakesData, cakesCtx, document.getElementById("cakesChart"));

  var fillingsCtx = $("#fillingsChart").get(0).getContext("2d");
  drawBarChart(fillingsData, fillingsCtx, document.getElementById("fillingsChart"));

  var decorationsCtx = $("#decorationsChart").get(0).getContext("2d");
  drawBarChart(decorationsData, decorationsCtx, document.getElementById("decorationsChart"));
}

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

function drawPieChart(data, ctx, can) {
  if (clear) ctx.clearRect(0, 0, can.width, can.height);
  var radius = can.height / 3;
  var midX = can.width / 2;
  var midY = can.height / 2;
  var dataName = data.name;
  var dataValue = data.value;
  if (!clear) fillColour = data.fillColour;
  numSamples = dataValue.length;
  ctx.strokeStyle = "black";
  ctx.font = "10pt Open Sans";
  ctx.textAlign = "center";
  ctx.textBaseline = "middle";
  // calculate total value of pie
  var total = 0;
  for (var i = 0; i < numSamples; i++) {
    total += dataValue[i];
  }
  // get ready to draw
  ctx.clearRect(0, 0, can.width, can.height);
  var oldAngle = 0;
 
  // for each sample
  for (var i = 0; i < numSamples; i++) {
    // draw wedge
    var portion = dataValue[i] / total;
    var wedge = 2 * Math.PI * portion;
    ctx.beginPath();
    var angle = oldAngle + wedge;
    ctx.arc(midX, midY, radius, oldAngle, angle);
    ctx.lineTo(midX, midY);
    ctx.closePath();
    ctx.fillStyle = fillColour[i];
    ctx.fill();    // fill with wedge color
    ctx.strokeStyle = "#DFFAF8"; // Outline colour
    ctx.lineWidth = 2; // Width of outline
    ctx.stroke();
 
    // print label
    // set angle to middle of wedge
    var labAngle = oldAngle + wedge / 2;
    // set x, y for label outside center of wedge
    // adjust for fact text is wider than it is tall
    var labX = midX + Math.cos(labAngle) * radius * 1.5;
    var labY = midY + Math.sin(labAngle) * radius * 1.3 - 12;
    // print name and value with black shadow
    ctx.save();
    ctx.fillStyle = fillColour[i];
    ctx.fillText(dataName[i], labX, labY);
    ctx.fillText(dataValue[i] + " orders", labX, labY + 15);
    ctx.restore();
    // update beginning angle for next wedge
    oldAngle += wedge;
  }
}
