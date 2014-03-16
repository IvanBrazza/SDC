<?php
  /**
    stats/ - display general statistics about the site
  **/
  require("../lib/common.php");
  $page = "stats";
  $title = "Stats";

  // Only the admin user can access this page
  if (empty($_SESSION) or $_SESSION['user']['username'] != "admin")
  {
    header("Location: ../home/");
    die();
  }

  // Use HTTPS since secure data is being transferred
  forceHTTPS();

  // Get all order details
  $query = "
    SELECT
      *
    FROM
      orders
  ";

  $db->runQuery($query, null);

  $rows = $db->fetchAll();

  foreach ($rows as $row)
  {
    // Calculate orders per customer & completed/outstanding
    if ($row['completed'] === "0")
    {
      $outstanding_orders++;
    } 
    else if ($row['completed'] === "1")
    {
      $completed_orders++;
    }
    $orders++;
  }
?>
<?php include("../lib/header.php"); ?>
<div class="col-md-12">
  <div id="browser-dialog" title="Canvas not supported">
    <p>Sorry! It seems that your browser doesn't support the HTML5 canvas element.
       This means that you probably won't be able to view the charts that should be
       displayed below. Please upgrade to a modern browser.</p>
  </div>
  <h1>Stats</h1>
  <div class="container">
    <div class="row">
      <div class="col-md-6">
        <h2>Order Stats</h2>
        <table id="order-stats">
          <tr>
            <th>Total number of orders:</th>
            <td><?php echo $orders; ?></td>
          </tr>
          <tr>
            <th>Number of outstanding orders:</th>
            <td><?php echo $outstanding_orders; ?></td>
          </tr>
          <tr>
            <th>Number of completed orders:</th>
            <td><?php echo $completed_orders; ?></td>
          </tr>
        </table>
        </div>
      <div class="col-md-6"></div>
    </div>
    <div class="row">
      <div class="col-md-6">
          <h2>Orders placed by month</h2>
          <canvas id="ordersChart" height="400px" width="400px"></canvas>
      </div>
      <div class="col-md-6">
          <h2>Popularity of cake types</h2>
          <canvas id="cakesChart" height="350px" width="350px"></canvas>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
          <h2>Popularity of fillings</h2>
          <canvas id="fillingsChart" height="400px" width="400px"></canvas>
      </div>
      <div class="col-md-6">
          <h2>Popularity of decorations</h2>
          <canvas id="decorationsChart" height="400px" width="400px"></canvas>
      </div>
    </div>
  </div>
</div>
<?php include("../lib/footer.php"); ?>
