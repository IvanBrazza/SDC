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
    $users[$row['customer_id']]['orders']++;
    $users[$row['customer_id']]['customer_id'] = $row['customer_id'];
  }
?>
<?php include("../lib/header.php"); ?>
  <script>
    var usersData = [
      <?php
        $colours = array();
        $colour_unique = false;
        foreach ($users as $user)
        {
          do
          {
            $new_colour = str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT) . str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT) . str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
            if (!in_array($new_colour, $colours))
            {
              array_push($colours, $new_colour);
              $colour = $new_colour;
              $colour_unique = true;
            }
          }
          while ($colour_unique === false);
          $users[$user['customer_id']]['colour'] = $colour;
          echo "{value:" . $user['orders'] . ",color:\"#" . $colour . "\"},";
        }
      ?>
    ]

    var usersOptions = {
      segmentStrokeColor: "#E2F8F8",
      animationSteps: 60,
      animation: true
    }
  </script>
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
    <div id="users-chart-key">
      <?php foreach ($users as $user) : ?>
        <div>
          <div class="pie-key" style="background-color:#<?php echo $user['colour']; ?>;"></div>
          <span><?php echo $user['first_name'] . " " . $user['last_name'] . " - " . $user['orders'] . " orders"; ?></span>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <div id="fillings-chart" class="chart">
    <h2>Popularity of fillings</h2>
    <canvas id="fillingsChart" height="400px" width="400px"></canvas>
  </div>
  <div id="decorations-chart" class="chart">
    <h2>Popularity of decorations</h2>
    <canvas id="decorationsChart"></canvas>
  </div>
<?php include("../lib/footer.php"); ?>
