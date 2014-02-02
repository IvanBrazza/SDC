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
    header("Location: ../login/?redirect=" . $_SERVER["REQUEST_URI"]);
    die();
  }

  forceHTTPS();

  // If the user clicked on an order
  if (!empty($_GET['order']))
  {
    // Get order details based on the order number
    $query = "
      SELECT
        a.*, b.*, c.decor_name, c.decor_price, d.filling_name, d.filling_price
      FROM
        orders a, cakes b, decorations c, fillings d
      WHERE
        order_number = :order_number
      AND
        b.cake_id = a.cake_id
      AND
        a.decor_id = c.decor_id
      AND
        a.filling_id = d.filling_id
    ";

    $query_params = array(
      ':order_number' => $_GET['order']
    );

    $db->runQuery($query, $query_params);

    $row = $db->fetch();
    
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
          delivery_charge
        FROM
          delivery
        WHERE
          order_number = :order_number
      ";

      $query_params = array(
        ':order_number' => $_GET['order']
      );

      $db->runQuery($query, $query_params);

      $deliveryrow = $db->fetch();
      $row['delivery_charge'] = $deliveryrow['delivery_charge'];
    }
  }
  else
  {
    // Get all outstanding orders
    $query = "
      SELECT
        order_number, order_placed, datetime, status, archived
      FROM
        orders
      WHERE
        customer_id = :customer_id
    ";
    
    if (!empty($_GET['sort']))
    {
      $query .= "
        ORDER BY
          orders." . $_GET['col'] . " " . $_GET['sort']
      ;
    }
    else
    {
      $query .= "
        ORDER BY
          orders.order_placed DESC
      ";
    }

    $query_params = array(
      ':customer_id' => $_SESSION['user']['customer_id']
    );

    $db->runQuery($query, $query_params);

    $rows = $db->fetchAll();
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
        <td><?php echo htmlentities($row['filling_name'], ENT_QUOTES, 'UTF-8')." - &pound;".htmlentities($row['filling_price'], ENT_QUOTES, 'UTF-8'); ?></td>
      </tr>
      <tr>
        <th>Decoration</th>
        <td><?php echo htmlentities($row['decor_name'], ENT_QUOTES, 'UTF-8')." - &pound;".htmlentities($row['decor_price'], ENT_QUOTES, 'UTF-8'); ?></td>
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
      <?php if ($row['delivery_type'] == "Deliver To Address") : ?>
        <tr>
          <th>Delivery Charge</th>
          <td>&pound;<?php echo $row['delivery_charge']; ?></td>
        </tr>
      <?php endif; ?>
      <tr>
        <th>Delivery Type</th>
        <td>
          <?php echo htmlentities($row['delivery_type'], ENT_QUOTES, 'UTF-8'); ?>
          <?php if ($row['delivery_type'] === "Collection") : ?>
            <a href="../get-directions/">Get Directions</a>
          <?php endif; ?>
        </td>
      </tr>
      <tr>
        <th>Grand Total</th>
        <td>&pound;<?php echo $row['base_price']+$deliveryrow['delivery_charge']+$row['filling_price']+$row['decor_price']; ?></td>
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
            <?php if ($row['archived'] == 0) : ?>
              <tr>
                <td><a href="../your-orders/?order=<?php echo $row['order_number']; ?>"></a><?php echo $row['order_number']; ?></td>
                <td><?php echo substr(htmlentities($row['order_placed'], ENT_QUOTES, 'UTF-8'), 0, -3); ?></td>
                <td><?php echo substr(htmlentities($row['datetime'], ENT_QUOTES, 'UTF-8'), 0, -3); ?></td>
                <td><?php echo htmlentities($row['status'], ENT_QUOTES, 'UTF-8'); ?></td>
              </tr>
            <?php endif; ?>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
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
        <?php foreach($rows as $row): ?>
          <?php if ($row['archived'] == 1) : ?>
            <tr>
              <td><a href="../your-orders/?order=<?php echo $row['order_number']; ?>"></a><?php echo $row['order_number']; ?></td>
              <td><?php echo substr(htmlentities($row['order_placed'], ENT_QUOTES, 'UTF-8'), 0, -3); ?></td>
              <td><?php echo substr(htmlentities($row['datetime'], ENT_QUOTES, 'UTF-8'), 0, -3); ?></td>
              <td><?php echo htmlentities($row['status'], ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
          <?php endif; ?>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
<?php include("../lib/footer.php"); ?>
