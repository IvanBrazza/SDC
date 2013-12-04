var ordersData,
    fillingsData,
    decorationsData,
    usersData;
$(document).ready(function() {
  calculateWidth();
  // Get database data with AJAX
  $.ajax({
    type: 'post',
    url: '../lib/stats.php',
    success: function(response) {
      object = JSON.parse(response);
      ordersData = object.orders;
      usersData = object.users;
      fillingsData = object.fillings;
      decorationsData = object.decorations;
      drawCharts();
    }
  });

  $(window).resize(function() {
    calculateWidth();
    drawCharts();
  });

  $(".chart").each(function(i) {
    $(this).delay((i++) * 400).fadeTo(500, 1);
  });
});

function calculateWidth() {
  var width = $(".container").width() * 0.49;
  $("#ordersChart").attr("width", width * 0.90 + "px");
  $("#ordersChart").attr("height", width * 0.80 + "px");
  $("#orders-chart").width(width + "px");

  $("#usersChart").attr("width", width + "px");
  $("#usersChart").attr("height", width * 0.79 + "px");
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
  drawBarChart(ordersData, ordersCtx, document.getElementById("ordersChart"));

  var usersCtx = $("#usersChart").get(0).getContext("2d");
  drawPieChart(usersData, usersCtx, document.getElementById("usersChart"));

  var fillingsCtx = $("#fillingsChart").get(0).getContext("2d");
  drawBarChart(fillingsData, fillingsCtx, document.getElementById("fillingsChart"));

  var decorationsCtx = $("#decorationsChart").get(0).getContext("2d");
  drawBarChart(decorationsData, decorationsCtx, document.getElementById("decorationsChart"));
}

function drawBarChart(data, ctx, can) {
  var y, tx, ty, metrics, words, line, testLine, testWidth;
  var dataName = data.name;
  var dataValue = data.value;
  var colHead = 50;
  var rowHead = 30;
  var margin = 10;
  var maxVal = Math.max.apply(Math, dataValue) + 1;
  var stepSize = 1;
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
    ctx.moveTo(rowHead, y)
    ctx.lineTo(can.width, y)
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
    ctx.fillRect(i + 1, 0, 0.5, dataValue[i]);
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
        ctx.fillText(line, tx -5, ty - 15);
        line = words[n] + ' ';
      }
      else {
        line = testLine;
      }
    }
    ctx.fillText(line, tx - 5, ty);
  }
  function calcY(value) {
    y = can.height - value * yScalar;
  } 
}

function drawPieChart(data, ctx, can) {
  var radius = can.height / 3;
  var midX = can.width / 2;
  var midY = can.height / 2;
  var dataName = data.name;
  var dataValue = data.value;
  var fillColour = data.fillColour;
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
