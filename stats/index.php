<?php
  /**
    stats/ - display general statistics about the site
  **/
  require("../lib/common.php");
  $page = "stats";
  $title = "Stats";

  if (empty($_SESSION) or $_SESSION['user']['username'] != "admin")
  {
    header("Location: ../home/");
    die();
  }

  // Get all order details
  $query = "
    SELECT
      *
    FROM
      orders
  ";

  try
  {
    $stmt     = $db->prepare($query);
    $result   = $stmt->execute();
  }
  catch(PDOException $ex)
  {
    die("Failed to execute query: " . $ex->getMessage() . " query: " . $query);
  }

  $rows = $stmt->fetchAll();

  foreach ($rows as $row)
  {
    // Calculate orders per customer & archived/outstanding
    if ($row['archived'] === "0")
    {
      $outstanding_orders++;
    } 
    else if ($row['archived'] === "1")
    {
      $archived_orders++;
    }
    $orders++;
  }
?>
<?php include("../lib/header.php"); ?>
  <div id="browser-dialog" title="Canvas not supported">
    <p>Sorry! It seems that your browser doesn't support the HTML5 canvas element.
       This means that you probably won't be able to view the charts that should be
       displayed below. Please upgrade to a modern browser.</p>
  </div>
  <h1>Stats</h1>
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
      <th>Number of archived orders:</th>
      <td><?php echo $archived_orders; ?></td>
    </tr>
  </table>
  <div id="orders-chart" class="chart">
    <h2>Orders placed by month</h2>
    <canvas id="ordersChart" height="400px" width="400px"></canvas>
  </div>
  <div id="users-chart" class="chart">
    <h2>Orders placed by customer</h2>
    <canvas id="usersChart" height="350px" width="350px"></canvas>
  </div>
  <div id="fillings-chart" class="chart">
    <h2>Popularity of fillings</h2>
    <canvas id="fillingsChart" height="400px" width="400px"></canvas>
  </div>
  <div id="decorations-chart" class="chart">
    <h2>Popularity of decorations</h2>
    <canvas id="decorationsChart" height="400px" width="400px"></canvas>
  </div>
<?php include("../lib/footer.php"); ?>
