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
  
  // Only logged in users can access this page
  if(empty($_SESSION['user']))
  {
    header("Location: ../login/?e=yo&redirect=" . $_SERVER["REQUEST_URI"]);
    die();
  }

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
        order_number, order_placed, datetime, status, completed
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
    <div class="row">
      <div class="col-md-12">
        <h1>Order <?php echo $row['order_number']; ?><?php if ($row['completed'] === "1") : ?> (completed)<?php else: ?> <a href="../edit-order/?order=<?php echo $row['order_number']; ?>" class="btn btn-info btn-xs"><span class="glyphicon glyphicon-pencil"></span>   Edit Order</a><?php endif; ?></h1>
        <?php if (!empty($row['image'])) : ?>
          <div class="image-view">
            <img src="<?php echo $row['image']; ?>" height="400px">
            <div class="close">X</div>
          </div>
        <?php endif; ?>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <table id="single_order" class="table table-condensed">
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
                <a href="../get-directions/"><button class="btn btn-info btn-xs"><span class="glyphicon glyphicon-road"></span>   Get Directions to collection point</button></a>
              <?php endif; ?>
            </td>
          </tr>
          <tr>
            <th>Grand Total</th>
            <td>&pound;<?php echo $row['base_price']+$deliveryrow['delivery_charge']+$row['filling_price']+$row['decor_price']; ?></td>
          </tr>
        </table>
      </div>
      <div class="col-md-6">
        <?php if (!empty($_GET['edit']) and $_GET['edit'] == "success") : ?>
          <div class="alert alert-success" style="display:block;">
            <span class="glyphicon glyphicon-ok"></span>
            Order successfully updated.
          </div>
        <?php endif; ?>
        <?php if ($row['difference'] != 0) : ?>
          <div class="alert alert-info" style="display:block;">
            <span class="glyphicon glyphicon-warning-sign"></span>
            There is a difference on this order of <strong>&pound;<?php echo $row['difference']; ?></strong>.
            <?php if ($row['difference'] > 0) : ?>
              This means that we owe you <b>&pound;<?php echo abs($row['difference']); ?></b>.
            <?php elseif ($row['difference'] < 0) : ?>
              This mean that you owe us <b>&pound;<?php echo abs($row['difference']); ?></b>.
            <?php endif; ?>
            A difference occurs if you edit your order after it has been placed, and the total between the original
            and the edited order has changed.
            If we have already discussed this difference with you, then please ingore this message.
          </div>
        <?php endif; ?>
      </div>
    </div>
  <?php else : ?>
    <div class="row">
      <div class="col-md-12">
        <h1><?php echo $_SESSION['user']['first_name'];?>'s Orders</h1>
        <div class="success">
          <span class="success_message">
            <?php echo $display_message; ?>
          </span>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <?php if (empty($rows)) : ?>
          <h3>You have no outstanding orders</h3>
        <?php else : ?>
          <table class="table table-hover" id="orders-js">
            <caption>Outstanding Orders</caption>
            <thead>
              <tr>
                <th>Order Number <span class="arrow"><a href="../your-orders/?sort=DESC&col=order_number"><span class="glyphicon glyphicon-chevron-up"></span></a> <a href="../your-orders/?sort=ASC&col=order_number"><span class="glyphicon glyphicon-chevron-down"></span></a></span></th>
                <th>Order Placed <span class="arrow"><a href="../your-orders/?sort=DESC&col=order_placed"><span class="glyphicon glyphicon-chevron-up"></span></a> <a href="../your-orders/?sort=ASC&col=order_placed"><span class="glyphicon glyphicon-chevron-down"></span></a></span></th>
                <th>Required Date <span class="arrow"><a href="../your-orders/?sort=DESC&col=datetime"><span class="glyphicon glyphicon-chevron-up"></span></a> <a href="../your-orders/?sort=ASC&col=datetime"><span class="glyphicon glyphicon-chevron-down"></span></a></span></th>
                <th>Status <span class="arrow"><a href="../your-orders/?sort=DESC&col=status"><span class="glyphicon glyphicon-chevron-up"></span></a> <a href="../your-orders/?sort=ASC&col=status"><span class="glyphicon glyphicon-chevron-down"></span></a></span></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($rows as $row): ?>
                <?php if ($row['completed'] == 0) : ?>
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
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <table id="orders-js" class="table table-hover">
          <caption>Completed Orders</caption>
          <thead>
            <tr>
              <th>Order Number <span class="arrow"><a href="../your-orders/?sort=DESC&col=order_number"><span class="glyphicon glyphicon-chevron-up"></span></a> <a href="../your-orders/?sort=ASC&col=order_number"><span class="glyphicon glyphicon-chevron-down"></span></a></span></th>
              <th>Order Placed <span class="arrow"><a href="../your-orders/?sort=DESC&col=order_placed"><span class="glyphicon glyphicon-chevron-up"></span></a> <a href="../your-orders/?sort=ASC&col=order_placed"><span class="glyphicon glyphicon-chevron-down"></span></a></span></th>
              <th>Required Date <span class="arrow"><a href="../your-orders/?sort=DESC&col=datetime"><span class="glyphicon glyphicon-chevron-up"></span></a> <a href="../your-orders/?sort=ASC&col=datetime"><span class="glyphicon glyphicon-chevron-down"></span></a></span></th>
              <th>Status <span class="arrow"><a href="../your-orders/?sort=DESC&col=status"><span class="glyphicon glyphicon-chevron-up"></span></a> <a href="../your-orders/?sort=ASC&col=status"><span class="glyphicon glyphicon-chevron-down"></span></a></span></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($rows as $row): ?>
              <?php if ($row['completed'] == 1) : ?>
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
      </div>
    </div>
  <?php endif; ?>
<?php include("../lib/footer.php"); ?>
