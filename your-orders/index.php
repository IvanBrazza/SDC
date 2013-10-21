<?php
  /**
   your-orders/ - display a list of all the orders placed
   by a certain user's ID number.
  **/
  require("../lib/common.php");
  $page = "your-orders";
  
  if(empty($_SESSION['user']))
  {
    header("Location: ../login");
    die();
  }

  if (!empty($_GET['archive']))
  {
    if ($_GET['archive'] === "success")
    {
      $display_message = "Order archived.";
    }
    else if ($_GET['archive'] === "fail")
    {
      $display_message = "Archive failed.";
    }
  }

  if (!empty($_GET['order']))
  {
    $query = "
      SELECT
        *
      FROM
        orders
      WHERE
        order_number = :order_number
    ";

    $query_params = array(
      ':order_number' => $_GET['order']
    );
    
    try
    {
      $stmt     = $db->prepare($query);
      $result   = $stmt->execute($query_params);
    }
    catch(PDOException $ex)
    {
      die("Failed to run query: " . $ex->getMessage());
    }

    $row = $stmt->fetch();
  }
  else
  {
    // Get all outstanding orders
    $query = "
      SELECT
        order_number,
        order_date,
        datetime,
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
      $stmt     = $db->prepare($query);
      $result   = $stmt->execute($query_params);
    }
    catch(PDOException $ex)
    {
      die("Failed to run query: " . $ex->getMessage());
    }
  
    $rows = $stmt->fetchAll();
    
    // Get archived orders
    $query = "
      SELECT
        order_number,
        order_date,
        datetime,
        status,
        customer_order
      FROM
        archived_orders
      WHERE 
        customer_id = :customer_id
    ";
  
    $query_params = array(
      ':customer_id' => $_SESSION['user']['customer_id']
    );
  
    try
    {
      $stmt     = $db->prepare($query);
      $result   = $stmt->execute($query_params);
    }
    catch(PDOException $ex)
    {
      die("Failed to run query: " . $ex->getMessage());
    }
  
    $archived_rows = $stmt->fetchAll();
  }
?>
<?php include("../lib/header.php"); ?>
    <div class="orders">
      <?php if (!empty($_GET['order'])) : ?>
        <h1>Order <?php echo $row['order_number']; ?><?php if (empty($_GET['archived'])) : ?><form action="../lib/archive-order.php" method="POST" id="archive-order"><input type="hidden" value="<?php echo $row['order_number']; ?>" name="order_number" id="order_number"><input type="hidden" value="customer" name="user"><input type="submit" value="Archive Order" class="delete_testimonial_btn"></form><?php else : ?> (archived)<?php endif; ?></h1>
        <table id="single_order">
          <tr>
            <th>Date Order Placed</th>
            <td><?php echo $row['order_date']; ?></td>
          </tr>
          <tr>
            <th>Required Date</th>
            <td><?php echo $row['datetime']; ?> </td>
          </tr>
          <tr>
            <th>Date Of Celebration</th>
            <td><?php echo $row['celebration_date']; ?></td>
          </tr>
          <tr>
            <th>Status</th>
            <td><?php echo $row['status']; ?></td>
          </tr>
          <tr>
            <th>Order</th>
            <td><?php echo htmlentities($row['customer_order'], ENT_QUOTES, 'UTF-8'); ?></td>
          </tr>
          <tr>
            <th>Filling</th>
            <td><?php echo htmlentities($row['filling'], ENT_QUOTES, 'UTF-8'); ?></td>
          </tr>
          <tr>
            <th>Decoration</th>
            <td><?php echo htmlentities($row['decoration'], ENT_QUOTES, 'UTF-8'); ?></td>
          </tr>
          <tr>
            <th>Agreed Price</th>
            <td>&pound;<?php echo $row['agreed_price']; ?></td>
          </tr>
          <tr>
            <th>Delivery Charge</th>
            <td>&pound;<?php echo $row['delivery_charge']; ?></td>
          </tr>
          <tr>
            <th>Delivery Type</th>
            <td><?php echo htmlentities($row['delivery'], ENT_QUOTES, 'UTF-8'); ?></td>
          </tr>
          <tr>
            <th>Grand Total</th>
            <td>&pound;<?php echo $row['agreed_price']+$row['delivery_charge']; ?></td>
          </tr>
        </table>
      <?php else : ?>
        <h1><?php echo $_SESSION['user']['first_name'];?>'s Orders</h1>
        <div class="success">
          <span class="success_message">
            <?php echo $display_message; ?>
          </span>
        </div>
        <?php if (empty($rows)) : ?>
          <h3>You have no outstanding orders</h3>
        <?php else : ?>
          <table class="orders-table">
            <caption>Outstanding Orders</caption>
            <tr>
              <th>Order Number</th>
              <th>Order Date</th>
              <th>Required Date</th>
              <th>Status</th>
              <th>Order</th>
            </tr>
            <?php foreach($rows as $row): ?>
              <tr>
                <td><a href="../your-orders/?order=<?php echo $row['order_number']; ?>"><?php echo $row['order_number']; ?></a></td>
                <td><?php echo $row['order_date']; ?></td>
                <td><?php echo $row['datetime']; ?></td>
                <td><?php echo htmlentities($row['status'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td style="word-wrap: break-word;"><?php echo htmlentities($row['customer_order'], ENT_QUOTES, 'UTF-8'); ?></td>
              </tr>
            <?php endforeach; ?>
          </table>
        <?php endif; ?>
        <?php if (empty($archived_rows)) : ?>
          <h3>You have no archived orders</h3>
        <?php else : ?>
          <table>
            <caption>Archived Orders</caption>
            <tr>
              <th>Order Number</th>
              <th>Order Date</th>
              <th>Required Date</th>
              <th>Status</th>
              <th>Order</th>
            </tr>
            <?php foreach($archived_rows as $row): ?>
              <tr>
                <td><a href="../your-orders/?order=<?php echo $row['order_number']; ?>&archived=true"><?php echo $row['order_number']; ?></a></td>
                <td><?php echo $row['order_date']; ?></td>
                <td><?php echo $row['datetime']; ?></td>
                <td><?php echo htmlentities($row['status'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td style="word-wrap: break-word;"><?php echo htmlentities($row['customer_order'], ENT_QUOTES, 'UTF-8'); ?></td>
              </tr>
            <?php endforeach; ?>
          </table>
        <?php endif; ?>
      <?php endif; ?>
    </div>
<?php include("../lib/footer.php"); ?>
