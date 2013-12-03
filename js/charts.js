$(document).ready(function() {
  calculateWidth();
  drawCharts();
  usersOptions.animation = false;

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
  drawBarChart(ordersDataName, ordersDataValue, ordersCtx, document.getElementById("ordersChart"));

  var usersCtx = $("#usersChart").get(0).getContext("2d");
  var usersChart = new Chart(usersCtx).Pie(usersData, usersOptions);

  var fillingsCtx = $("#fillingsChart").get(0).getContext("2d");
  drawBarChart(fillingsDataName, fillingsDataValue, fillingsCtx, document.getElementById("fillingsChart"));

  var decorationsCtx = $("#decorationsChart").get(0).getContext("2d");
  drawBarChart(decorationsDataName, decorationsDataValue, decorationsCtx, document.getElementById("decorationsChart"));
}

function drawBarChart(dataName, dataValue, ctx, can) {
  var y, tx, ty, metrics, words, line, testLine, testWidth;
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
  ctx.font = "10pt Helvetica"
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
  ctx.font = "8pt Helvetica";
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
        ctx.fillText(line, tx, ty - 15);
        line = words[n] + ' ';
      }
      else {
        line = testLine;
      }
    }
    ctx.fillText(line, tx, ty);
  }
  function calcY(value) {
    y = can.height - value * yScalar;
  } 
}
