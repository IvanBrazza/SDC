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
  
  // Get number of orders placed
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

  foreach ($users as $user)
  {
    $query = "
      SELECT
        *
      FROM
        users
      WHERE
        customer_id = :customer_id
    ";

    $query_params = array(
      ':customer_id' => $user['customer_id']
    );

    try
    {
      $stmt     = $db->prepare($query);
      $result   = $stmt->execute($query_params);
    }
    catch(PDOException $ex)
    {
      die("Failed to execute query: " . $ex->getMessage() . " query: " . $query);
    }
    $row = $stmt->fetch();
    $users[$user['customer_id']]['first_name'] = $row['first_name'];
    $users[$user['customer_id']]['last_name'] = $row['last_name'];
  }
  
  for ($i = 0; $i < 12; $i++)
  {
    $query = "
      SELECT
        *
      FROM
        orders
      WHERE
        MONTH(order_placed) = $i
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
      $months[$i-1]++;
    }
  }
?>
<?php include("../lib/header.php"); ?>
  <script>
    var ordersData = {
      labels: ["January","February","March","April","May","June","July","August","September","October","November","December"],
      datasets: [
        {
          fillColor: "rgba(151,187,205,0.5)",
          strokeColor: "rgba(151,187,205,1)",
          pointColor: "rgba(151,187,205,1)",
          pointStrokeColor: "#fff",
          data: [
            <?php
              $largestMonth = $months[0];
              for ($i = 0; $i < 12; $i++)
              {
                if ($months[$i]) 
                {
                  echo $months[$i];
                } 
                else 
                {
                  echo "0";
                } 
                echo ",";

                if ($months[$i] > $largestMonth)
                {
                  $largestMonth = $months[$i];
                }
              }
            ?>
          ]
        }
      ]
    }

    var ordersOptions = {
        scaleOverride: true,
        scaleSteps: <?php echo $largestMonth + 1; ?>,
        scaleStepWidth: 1,
        bezierCurve: false,
        scaleStartValue: 0 
    }

    var usersData = [
      <?php
        foreach ($users as $user)
        {
          $colour = str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT) . str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT) . str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
          $users[$user['customer_id']]['colour'] = $colour;
          echo "{value:" . $user['orders'] . ",color:\"#" . $colour . "\"},";
        }
      ?>
    ]

    var usersOptions = {
      segmentStrokeColor: "#E2F8F8",
      animationSteps: 60
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
    <tr>
      <th>Number of orders by customer</th>
      <td>
        <?php foreach ($users as $user) : ?>
          <?php echo $user['first_name'] . " " . $user['last_name'] . ": " . $user['orders']; ?><br />
        <?php endforeach; ?>
      </td>
    </tr>
  </table>
  <div id="orders-chart">
    <h2>Orders placed by month</h2>
    <canvas id="ordersChart" height="400px" width="400px"></canvas>
  </div>
  <div id="users-chart">
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
<?php include("../lib/footer.php"); ?>
