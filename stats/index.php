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

    // Calculate popular filling & decoration
    $fillings[$row['filling']]['name'] = $row['filling'];
    $fillings[$row['filling']]['amount']++;
    $largestFilling = 0;
    foreach ($fillings as $filling)
    {
      $largestFilling = max($largestFilling, $filling['amount']);
    }

    $decorations[$row['decoration']]['name'] = $row['decoration'];
    $decorations[$row['decoration']]['amount']++;
    $largestDecoration = 0;
    foreach ($decorations as $decoration)
    {
      $largestDecoration = max($largestDecoration, $decoration['amount']);
    }
  }
  
  // Get first & last name for each customer to display
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
  
  // Calculate orders placed per month
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
    var ordersDataName = ["January","February","March","April","May","June","July","August","September","October","November","December"];
    var ordersDataValue = [
            <?php
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
              }
            ?>
          ];

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

    var fillingsDataName = ["None", "Butter Cream", "Chocolate", "Other"];
    var fillingsDataValue = [
            <?php
              if ($fillings['None']['amount']) {
                echo $fillings['None']['amount'] . ",";
              } else {
                echo "0,";
              }
              if ($fillings['Butter Cream']['amount']) {
                echo $fillings['Butter Cream']['amount'] . ",";
              } else {
                echo "0,";
              }
              if ($fillings['Chocolate']['amount']) {
                echo $fillings['Chocolate']['amount'] . ",";
              } else {
                echo "0,";
              }
              if ($fillings['Other']['amount']) {
                echo $fillings['Other']['amount'];
              } else {
                echo "0";
              }
            ?>
          ];

    var decorationsDataName = ["None", "Royal Icing", "Regal Icing", "Butter Cream", "Chocolate", "Coconut", "Other"];
    var decorationsDataValue = [
            <?php
              if ($decorations['None']['amount']) {
                echo $decorations['None']['amount'] . ",";
              } else {
                echo "0,";
              }
              if ($decorations['Royal Icing']['amount']) {
                echo $decorations['Royal Icing']['amount'] . ",";
              } else {
                echo "0,";
              }
              if ($decorations['Regal Icing']['amount']) {
                echo $decorations['Regal Icing']['amount'] . ",";
              } else {
                echo "0,";
              }
              if ($decorations['Butter Cream']['amount']) {
                echo $decorations['Butter Cream']['amount'] . ",";
              } else {
                echo "0,";
              }
              if ($decorations['Chocolate']['amount']) {
                echo $decorations['Chocolate']['amount'] . ",";
              } else {
                echo "0,";
              }
              if ($decorations['Coconut']['amount']) {
                echo $decorations['Coconut']['amount'] . ",";
              } else {
                echo "0,";
              }
              if ($decorations['Other']['amount']) {
                echo $decorations['Other']['amount'];
              } else {
                echo "0";
              }
            ?>
          ];
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
