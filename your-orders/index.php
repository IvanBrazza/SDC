<?php
  /**
   your-orders/ - display a list of all the orders placed
   by a certain user's ID number.
  **/
  require("../lib/common.php");
  $page = "your-orders";
  if ($_GET and empty($_GET['sort']))
  {
    $title = "Order " . $_GET['order'];
  }
  else
  {
    $title = $_SESSION['user']['first_name'] . "'s Orders";
  }
  
  if(empty($_SESSION['user']))
  {
    header("Location: ../login");
    die();
  }

  forceHTTPS();

  // If the user clicked on an order
  if (!empty($_GET['order']))
  {
    // Get order details based on the order number
    $query = "
      SELECT
        a.*, b.*
      FROM
        orders a, cakes b
      WHERE
        order_number = :order_number
      AND
        b.cake_id = a.cake_id
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
    
    // If the order being pulled doesn't belong to
    // the logged in user redirect them back to the
    // your-orders page
    if ($row['customer_id'] !== $_SESSION['user']['customer_id'])
    {
      header("Location: ../your-orders");
      die();
    }

    // If the order was a delivery
    if ($row['delivery_type'] === "Deliver To Address")
    {
      // Get the delivery details for the order
      $query = "
        SELECT
          *
        FROM
          delivery
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
        die("Failed to run query: " . $ex->getMessage() . " query: " . $query);
      }

      $deliveryrow = $stmt->fetch();
    }
  }
  else
  {
    // Get all outstanding orders
    $query = "
      SELECT
        order_number, order_placed, datetime, status
      FROM
        orders
      WHERE
        customer_id = :customer_id
      AND
        archived = 0
    ";
    
    if (!empty($_GET['sort']))
    {
      $query .= "
        ORDER BY
          orders." . $_GET['col'] . " " . $_GET['sort']
      ;
    }

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
        order_number, order_placed, datetime, status
      FROM
        orders
      WHERE 
        customer_id = :customer_id
      AND
        archived = 1
    ";
  
    if (!empty($_GET['sort']))
    {
      $query .= "
        ORDER BY
          orders." . $_GET['col'] . " " . $_GET['sort']
      ;
    }
    
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
  <?php if (!empty($_GET['order'])) : ?>
    <h1>Order <?php echo $row['order_number']; ?><?php if ($row['archived'] === "1") : ?> (archived)<?php else: ?> <a href="../edit-order/?order=<?php echo $row['order_number']; ?>" class="small-link">Edit</a><?php endif; ?></h1>
    <?php if (!empty($row['image'])) : ?>
      <div class="image-view">
        <img src="<?php echo $row['image']; ?>" height="400px">
        <div class="close">X</div>
      </div>
    <?php endif; ?>
    <table id="single_order">
      <tr>
        <th>Order Placed</th>
        <td><?php echo substr(htmlentities($row['order_placed'], ENT_QUOTES, 'UTF-8'), 0, -3); ?></td>
      </tr>
      <tr>
        <th>Required Date</th>
        <td><?php echo substr(htmlentities($row['datetime'], ENT_QUOTES, 'UTF-8'), 0, -3); ?> </td>
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
        <td><?php echo htmlentities($row['comments'], ENT_QUOTES, 'UTF-8'); ?></td>
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
        <th>Cake Size</th>
        <td><?php echo htmlentities($row['cake_size'], ENT_QUOTES, 'UTF-8'); ?></td>
      </tr>
      <tr>
        <th>Cake Type</th>
        <td><?php echo htmlentities($row['cake_type'], ENT_QUOTES, 'UTF-8'); ?></td>
      </tr>
      <?php if (!empty($row['image'])) : ?>
        <tr>
          <th>Image</th>
          <td><a href="javascript:" id="image-link">Click here to view image</a></td>
        </tr>
      <?php endif; ?>
      <tr>
        <th>Base Price</th>
        <td>&pound;<?php echo $row['base_price']; ?></td>
      </tr>
      <?php if (!empty($deliveryrow)) : ?>
        <tr>
          <th>Delivery Charge</th>
          <td>&pound;<?php echo $deliveryrow['delivery_charge']; ?></td>
        </tr>
      <?php endif; ?>
      <tr>
        <th>Delivery Type</th>
        <td><?php echo htmlentities($row['delivery_type'], ENT_QUOTES, 'UTF-8'); ?></td>
      </tr>
      <tr>
        <th>Grand Total</th>
        <td>&pound;<?php echo $row['base_price']+$deliveryrow['delivery_charge']; ?></td>
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
      <table class="orders-table" id="orders-js">
        <caption>Outstanding Orders</caption>
        <thead>
          <tr>
            <th>Order Number <span class="arrow"><a href="../your-orders/?sort=DESC&col=order_number">&#9650;</a> <a href="../your-orders/?sort=ASC&col=order_number">&#9660;</a></span></th>
            <th>Order Placed <span class="arrow"><a href="../your-orders/?sort=DESC&col=order_placed">&#9650;</a> <a href="../your-orders/?sort=ASC&col=order_placed">&#9660;</a></span></th>
            <th>Required Date <span class="arrow"><a href="../your-orders/?sort=DESC&col=datetime">&#9650;</a> <a href="../your-orders/?sort=ASC&col=datetime">&#9660;</a></span></th>
            <th>Status <span class="arrow"><a href="../your-orders/?sort=DESC&col=status">&#9650;</a> <a href="../your-orders/?sort=ASC&col=status">&#9660;</a></span></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($rows as $row): ?>
            <tr>
              <td><a href="../your-orders/?order=<?php echo $row['order_number']; ?>"></a><?php echo $row['order_number']; ?></td>
              <td><?php echo substr(htmlentities($row['order_placed'], ENT_QUOTES, 'UTF-8'), 0, -3); ?></td>
              <td><?php echo substr(htmlentities($row['datetime'], ENT_QUOTES, 'UTF-8'), 0, -3); ?></td>
              <td><?php echo htmlentities($row['status'], ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
    <?php if (empty($archived_rows)) : ?>
      <h3>You have no archived orders</h3>
    <?php else : ?>
      <table id="orders-js">
        <caption>Archived Orders</caption>
        <thead>
          <tr>
            <th>Order Number <span class="arrow"><a href="../your-orders/?sort=DESC&col=order_number">&#9650;</a> <a href="../your-orders/?sort=ASC&col=order_number">&#9660;</a></span></th>
            <th>Order Placed <span class="arrow"><a href="../your-orders/?sort=DESC&col=order_placed">&#9650;</a> <a href="../your-orders/?sort=ASC&col=order_placed">&#9660;</a></span></th>
            <th>Required Date <span class="arrow"><a href="../your-orders/?sort=DESC&col=datetime">&#9650;</a> <a href="../your-orders/?sort=ASC&col=datetime">&#9660;</a></span></th>
            <th>Status <span class="arrow"><a href="../your-orders/?sort=DESC&col=status">&#9650;</a> <a href="../your-orders/?sort=ASC&col=status">&#9660;</a></span></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($archived_rows as $row): ?>
            <tr>
              <td><a href="../your-orders/?order=<?php echo $row['order_number']; ?>"></a><?php echo $row['order_number']; ?></td>
              <td><?php echo substr(htmlentities($row['order_placed'], ENT_QUOTES, 'UTF-8'), 0, -3); ?></td>
              <td><?php echo substr(htmlentities($row['datetime'], ENT_QUOTES, 'UTF-8'), 0, -3); ?></td>
              <td><?php echo htmlentities($row['status'], ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  <?php endif; ?>
<?php include("../lib/footer.php"); ?>
