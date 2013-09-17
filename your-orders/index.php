<?php
  require("../lib/common.php");
  
  if(empty($_SESSION['user']))
  {
    header("Location: login.php");
    die("Redirecting to login.php");
  }
  
  $query = "
    SELECT
      customer_id,
      order_number,
      order_date,
      required_date,
      status,
      customer_order
    FROM
      orders
    WHERE
      customer_id = :customer_id
  ";

  $query_params = array(
    ':customer_id' => $_SESSION['user']['customer_id']
  );

  try
  {
    $stmt = $db->prepare($query);
    $result = $stmt->execute($query_params);
  }
  catch(PDOException $ex)
  {
    die("Failed to run query: " . $ex->getMessage());
  }

  $rows = $stmt->fetchAll();
?>
<?php include("../lib/header.php"); ?>
  <div class="container">
    <div class="orders">
      <h1><?php echo $_SESSION['user']['username'];?>'s Orders</h1>
      <table style="">
        <tr>
          <th>Order Number</th>
          <th>Order Date</th>
          <th>Required Date</th>
          <th>Status</th>
          <th>Your Order</th>
        </tr>
        <?php foreach($rows as $row): ?>
          <tr>
            <td><?php echo htmlentities($row['order_number'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlentities($row['order_date'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlentities($row['required_date'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlentities($row['status'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td style="word-wrap: break-word;"><?php echo htmlentities($row['customer_order'], ENT_QUOTES, 'UTF-8'); ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>
  </div>
<?php include("../lib/footer.php"); ?>
