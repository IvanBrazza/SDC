<?php
  /**
    all-orders/ - display all orders, a specific order, or all orders
    by a specific customer number.
  **/
  require("../lib/common.php");
  $page = "all-orders";

  // Only the admin user can access this page
  if(empty($_SESSION['user']) or $_SESSION['user']['username'] !== "admin")
  {
    header("Location: ../login");
    die();
  }

  // Use HTTPS since secure content is being transferred
  forceHTTPS();

  // Display messages based on GET
  if (!empty($_GET['archive']))
  {
    switch ($_GET['archive']){
      case "success":
        $display_message = "Order archived.";
        break;
      case "fail":
        $display_message = "Archive failed.";
        break;
    }
  }
  else if (!empty($_GET['new-order']))
  {
    if ($_GET['new-order'] === "added")
    {
      $display_message = "Order added.";
    }
  }

  // If GET then run specific queries, otherwise
  // get all order details
  if ($_GET)
  {
    // If a single order is to be displayed, get
    // the details about that order, else if a
    // user ID is in the GET, then get all the
    // orders by that user
    if (!empty($_GET['order']))
    {
      $query = "
        SELECT
          a.*,
          b.filling_price, b.filling_name,
          c.decor_price, c.decor_name,
          d.cake_size, d.cake_type,
          e.first_name, e.last_name, e.address, e.postcode, e.phone, e.email
        FROM
          orders a,
          fillings b,
          decorations c,
          cakes d,
          users e
        WHERE
          order_number = :order_number
        AND
          a.filling_id = b.filling_id
        AND
          a.decor_id = c.decor_id
        AND
          a.cake_id = d.cake_id
        AND
          a.customer_id = e.customer_id
      ";

      $query_params = array(
        ':order_number' => $_GET['order']
      );
    }
    else if (!empty($_GET['id']))
    {
      $query = "
        SELECT
          a.order_number, a.order_placed, a.datetime, a.status, a.archived,
          b.first_name, b.last_name, b.address, b.postcode, b.phone, b.email
        FROM
          orders a,
          users b
        WHERE
          a.customer_id = :get_id
        AND
          b.customer_id = a.customer_id
      ";

      $query_params = array(
        ':get_id' => $_GET['id']
      );

      // If sort is in GET then sort the orders
      // by the GET details, otherwise sort by
      // most recent order at the top by default
      if (!empty($_GET['sort']))
      {
        $query .= "
          ORDER BY
            a." . $_GET['col'] . " " . $_GET['sort']
        ;
      }
      else
      {
        $query .= "
          ORDER BY
            a.order_placed ASC
        ";
      }
    }
  }
  else
  {
    $query = "
      SELECT
        order_number,
        order_placed,
        datetime,
        status,
        archived
      FROM
        orders
    ";

    // If sort is in GET then sort the orders
    // by the GET details, otherwise sort by
    // most recent order at the top by default
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
  }

  if (!empty($query_params))
  {
    $db->runQuery($query, $query_params);
  }
  else
  {
    $db->runQuery($query, null);
  }

  if (!empty($_GET['order']))
  {
    $row = $db->fetch();
    // If the delivery type is deliver rather than
    // collection, then get the delivery details
    if ($row['delivery_type'] == "Deliver To Address")
    {
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

      $temp = $db->fetch();
      $row['delivery_charge'] = $temp['delivery_charge'];
    }
  }
  else
  {
    $rows = $db->fetchAll();
  }

  // If a single order is being displayed or all orders
  // by a customer, create a new Delivery object and
  // calculate the distance to be displayed
  if (!empty($_GET['order']) or !empty($_GET['id']))
  {
    include("../lib/delivery.class.php");
    $delivery = new Delivery;

    if (!empty($_GET['id']))
    {
      $delivery->setAddress($rows[0]['address']);
      $delivery->setPostcode($rows[0]['postcode']);
    }
    else
    {
      $delivery->setAddress($row['address']);
      $delivery->setPostcode($row['postcode']);
    }
    $delivery->calculateDistance();
  }

  // Generate token
  $_SESSION['token'] = rtrim(base64_encode(md5(microtime())),"=");

  // Set the page title based on what is being displayed
  if (!$_GET or !empty($_GET['archive']) or !empty($_GET['sort']))
  {
    $title = "All Orders";
  }
  else
  {
    if (!empty($_GET['id']))
    {
      $title = $rows[0]['first_name'] . "'s Orders | Star Dream Cakes";
    }
    else
    {
      $title = $row['first_name'] . "'s Orders | Star Dream Cakes";
    }
  }
?>
<?php include("../lib/header.php"); ?>
  <!--Show all orders-->
  <?php if (!$_GET or !empty($_GET['archive']) or !empty($_GET['new-order'])) : ?>
    <h1>All Orders</h1>
    <a href="../add-order">Add Order</a>
    <form action="../all-orders" method="GET" id="order_search">
      <input type="hidden" id="token" name="token" value="<?php echo $_SESSION['token']; ?>" />
      <input type="text" id="order_number" name="order" placeholder="Enter order number" />
      <input type="submit" value="Search all orders" />
    </form>
    <script>
      var orderNumbers = [
        <?php
          foreach ($rows as $row)
          {
            echo "\"" . $row['order_number'] . "\",";
          }
        ?>
      ];
    </script>
    <div class="success">
      <span class="success_message">
        <?php echo $display_message; ?>
      </span>
    </div>
    <div class="error">
      <span class="error_message" id="error_message">
      </span>
    </div>
    <?php if (empty($rows)) : ?>
      <h3>There are no outstanding orders</h3>
    <?php else : ?>
      <table class="orders-table" id="orders-js">
        <caption>Outstanding Orders</caption>
        <thead>
          <tr>
            <th>Order Number <span class="arrow"><a href="../all-orders/?sort=DESC&col=order_number">&#9650;</a> <a href="../all-orders/?sort=ASC&col=order_number">&#9660;</a></span></th>
            <th>Order Placed <span class="arrow"><a href="../all-orders/?sort=DESC&col=order_placed">&#9650;</a> <a href="../all-orders/?sort=ASC&col=order_placed">&#9660;</a></span></th>
            <th>Required Date <span class="arrow"><a href="../all-orders/?sort=DESC&col=datetime">&#9650;</a> <a href="../all-orders/?sort=ASC&col=datetime">&#9660;</a></span></th>
            <th>Status <span class="arrow"><a href="../all-orders/?sort=DESC&col=status">&#9650;</a> <a href="../all-orders/?sort=ASC&col=status">&#9660;</a></span></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($rows as $row): ?>
            <?php if ($row['archived'] == 0) : ?>
              <tr>
                <td><a href="../all-orders/?order=<?php echo $row['order_number']; ?>"></a><?php echo $row['order_number']; ?></td>
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
          <th>Order Number <span class="arrow"><a href="../all-orders/?sort=DESC&col=order_number">&#9650;</a> <a href="../all-orders/?sort=ASC&col=order_number">&#9660;</a></span></th>
          <th>Order Placed <span class="arrow"><a href="../all-orders/?sort=DESC&col=order_placed">&#9650;</a> <a href="../all-orders/?sort=ASC&col=order_placed">&#9660;</a></span></th>
          <th>Required Date <span class="arrow"><a href="../all-orders/?sort=DESC&col=datetime">&#9650;</a> <a href="../all-orders/?sort=ASC&col=datetime">&#9660;</a></span></th>
          <th>Status <span class="arrow"><a href="../all-orders/?sort=DESC&col=status">&#9650;</a> <a href="../all-orders/?sort=ASC&col=status">&#9660;</a></span></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($rows as $row): ?>
          <?php if ($row['archived'] == 1) : ?>
            <tr>
              <td><a href="../all-orders/?order=<?php echo $row['order_number']; ?>"></a><?php echo $row['order_number']; ?></td>
              <td><?php echo substr(htmlentities($row['order_placed'], ENT_QUOTES, 'UTF-8'), 0, -3); ?></td>
              <td><?php echo substr(htmlentities($row['datetime'], ENT_QUOTES, 'UTF-8'), 0, -3); ?></td>
              <td><?php echo htmlentities($row['status'], ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
          <?php endif; ?>
        <?php endforeach; ?>
      </tbody>
    </table>
  <!-- if user clicked on order number or searched for an order -->
  <?php elseif (!empty($_GET['order'])) : ?>
    <h1>Order <?php echo $row['order_number']; ?><?php if ($row['archived'] === "0") : ?><form action="../lib/archive-order.php" method="POST" id="archive-order"><input type="hidden" value="<?php echo $row['order_number']; ?>" name="order_number" id="order_number"><input type="submit" value="Archive Order" class="delete_testimonial_btn"></form><?php else : ?> (archived)<?php endif; ?></h1>
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
        <td>
          <?php echo $row['status']; ?>
          <?php if ($row['archived'] === "0") : ?>
            <form action="../lib/update-order.php" method="POST" style="margin-left:10px;">
              <select name="status">
                <option value="Processing">Processing</option>
                <option value="Dispatched">Dispatched</option>
                <option value="Complete">Complete</option>
              </select>
              <input type="hidden" value="<?php echo $row['order_number']; ?>" name="order_number">
              <input type="hidden" value="<?php echo $row['first_name']; ?>" name="first_name">
              <input type="hidden" value="<?php echo $row['email']; ?>" name="email">
              <input type="submit" value="Update">
            </form>
          <?php endif; ?>
        </td>
      </tr>
      <tr>
        <th>Comments</th>
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
        <th>Cake Type</th>
        <td><?php echo htmlentities($row['cake_type'], ENT_QUOTES, 'UTF-8'); ?></td>
      </tr>
      <tr>
        <th>Cake Size</th>
        <td><?php echo htmlentities($row['cake_size'], ENT_QUOTES, 'UTF-8'); ?></td>
      </tr>
      <?php if (!empty($row['image'])) : ?>
        <tr>
          <th>Image</th>
          <td><a href="javascript:" id="image-link">Click here to view image</a></td>
        </tr>
      <?php endif; ?>
      <tr>
        <th>Base Price</th>
        <td>
          &pound; 
          <?php if ($row['archived'] === "0") : ?>
            <form action="../lib/update-order.php" method="POST">
              <input type="hidden" value="<?php echo $row['order_number']; ?>" name="order_number">
              <input name="base_price" type="text" value="<?php echo $row['base_price']; ?>" style="width:50px;">
              <input type="submit" value="Update">
            </form>
          <?php else : ?>
            <?php echo $row['base_price']; ?>
          <?php endif; ?>
        </td>
      </tr>
      <?php if ($row['delivery_type'] == "Deliver To Address") : ?>
        <tr>
          <th>Delivery Charge</th>
          <td>
            &pound; 
            <?php if ($row['archived'] === "0") : ?>
              <form action="../lib/update-order.php" method="POST">
                <input type="hidden" value="<?php echo $row['order_number']; ?>" name="order_number">
                <input name="delivery_charge" type="text" value="<?php echo $row['delivery_charge']; ?>" style="width:50px;">
                <input type="submit" value="Update">
              </form>
            <?php else : ?>
              <?php echo $row['delivery_charge']; ?>
            <?php endif; ?>
          </td>
        </tr>
      <?php endif; ?>
      <tr>
        <th>Delivery Type</th>
        <td><?php echo htmlentities($row['delivery_type'], ENT_QUOTES, 'UTF-8'); ?></td>
      </tr>
      <tr>
        <th>Grand Total</th>
        <td>&pound;<?php echo $row['base_price']+$row['delivery_charge']+$row['filling_price']+$row['decor_price']; ?></td>
      </tr>
    </table>
    <div id="single_order_details">
      <p>Placed by <?php echo htmlentities($row['first_name'], ENT_QUOTES, 'UTF-8'); echo " "; echo htmlentities($row['last_name'], ENT_QUOTES, 'UTF-8'); ?></p>
      <br />
      <span class="title">Address:</span><br />
      <?php echo htmlentities($row['address'], ENT_QUOTES, 'UTF-8'); ?><br />
      <?php echo htmlentities($row['postcode'], ENT_QUOTES, 'UTF-8'); ?><br />
      <i>(<?php echo $delivery->getDistance(); ?> miles away)</i><br/>
      <a href="../get-directions?id=<?php echo $row['customer_id']; ?>">Get directions</a>
      <br />
      <br />
      <span class="title">Phone: </span>
      <?php echo $row['phone']; ?><br />
      <br /><br />
      <?php if (!empty($row['image'])) : ?>
        <div class="image-view">
          <img src="<?php echo $row['image']; ?>" height="400px">
          <div class="close">X</div>
        </div>
      <?php endif; ?>
    </div>
  <!-- show all orders by a customer -->
  <?php elseif (!empty($_GET['id'])) : ?>
    <h1>Orders placed by <?php echo htmlentities($rows[0]['first_name'], ENT_QUOTES, 'UTF-8') . " " . htmlentities($rows[0]['last_name'], ENT_QUOTES, 'UTF-8'); ?></h1>
    <span class="title">Address:</span><br />
    <?php echo htmlentities($rows[0]['address'], ENT_QUOTES, 'UTF-8'); ?><br />
    <?php echo htmlentities($rows[0]['postcode'], ENT_QUOTES, 'UTF-8'); ?><br />
    <i>(<?php echo $delivery->getDistance(); ?> miles away)</i><br />
    <a href="../get-directions?id=<?php echo $rows[0]['customer_id']; ?>">Get directions</a>
    <br />
    <br />
    <span class="title">Phone: </span>
    <?php echo htmlentities($rows[0]['phone'], ENT_QUOTES, 'UTF-8'); ?><br />
    <br /><br />
    <?php if (empty($rows)) : ?>
      <h3>There are no outstanding orders</h3>
    <?php else : ?>
      <table class="orders-table" id="orders-js">
        <caption>Outstanding Orders</caption>
        <thead>
          <tr>
            <th>Order Number <span class="arrows"><a href="../all-orders/?id=<?php echo $_GET['id']; ?>&sort=DESC&col=order_number">&#9650;</a> <a href="../all-orders/?sort=ASC&col=order_number">&#9660;</a></span></th>
            <th>Order Placed <span class="arrows"><a href="../all-orders/?id=<?php echo $_GET['id']; ?>&sort=DESC&col=order_placed">&#9650;</a> <a href="../all-orders/?sort=ASC&col=order_placed">&#9660;</a></span></th>
            <th>Required Date <span class="arrows"><a href="../all-orders/?id=<?php echo $_GET['id']; ?>&sort=DESC&col=datetime">&#9650;</a> <a href="../all-orders/?sort=ASC&col=datetime">&#9660;</a></span></th>
            <th>Status <span class="arrows"><a href="../all-orders/?id=<?php echo $_GET['id']; ?>&sort=DESC&col=status">&#9650;</a> <a href="../all-orders/?sort=ASC&col=status">&#9660;</a></span></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($rows as $row): ?>
            <?php if ($row['archived'] == 0) : ?>
              <tr>
                <td><a href="../all-orders/?order=<?php echo $row['order_number']; ?>"></a><?php echo $row['order_number']; ?></td>
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
          <th>Order Number <span class="arrow"><a href="../all-orders/?id=<?php echo $row['customer_id']; ?>&sort=DESC&col=order_number">&#9650;</a> <a href="../all-orders/?sort=ASC&col=order_number">&#9660;</a></span></th>
          <th>Order Placed <span class="arrow"><a href="../all-orders/?id=<?php echo $row['customer_id']; ?>&sort=DESC&col=order_placed">&#9650;</a> <a href="../all-orders/?sort=ASC&col=order_placed">&#9660;</a></span></th>
          <th>Required Date <span class="arrow"><a href="../all-orders/?id=<?php echo $row['customer_id']; ?>&sort=DESC&col=datetime">&#9650;</a> <a href="../all-orders/?sort=ASC&col=datetime">&#9660;</a></span></th>
          <th>Status <span class="arrow"><a href="../all-orders/?id=<?php echo $row['customer_id']; ?>&sort=DESC&col=status">&#9650;</a> <a href="../all-orders/?sort=ASC&col=status">&#9660;</a></span></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($rows as $row): ?>
          <?php if ($row['archived'] == 1) : ?>
            <tr>
              <td><a href="../all-orders/?order=<?php echo $row['order_number']; ?>"></a><?php echo $row['order_number']; ?></td>
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
