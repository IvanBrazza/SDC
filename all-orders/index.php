<?php
  require("../lib/common.php");

  if(empty($_SESSION['user']))
  {
    header("Location: ../login");
    die();
  }

  if($_SESSION['user']['username'] !== "admin")
  {
    header("Location: login.php");
    die("Forbidden");
  }

  if (!empty($_GET['type']))
  {
    $query = "
      SELECT
        customer_id
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
      $stmt = $db->prepare($query);
      $result = $stmt->execute($query_params);
    }
    catch(PDOException $ex)
    {
      die("Failed to execute query: " . $ex->getMessage());
    }

    $row = $stmt->fetch();

    $customer_id = $row['customer_id'];
  }

  $query = "
    SELECT
      *
    FROM
      orders
  ";

  if ($_GET)
  {
    if (!empty($_GET['order']))
    {
      $query .= "
        WHERE
          order_number = :order_number
      ";

      $query_params = array(
        ':order_number' => $_GET['order']
      );
    } else if (!empty($_GET['id']))
    {
      $query .= "
        WHERE
          customer_id = :get_id
      ";

      $query_params = array(
        ':get_id' => $_GET['id']
      );
    }
  }
  else
  {
    $query .= "
      ORDER BY
        orders.customer_id ASC
    ";
  }

  try
  {
    $stmt = $db->prepare($query);
    if ($_GET)
    {
      $result = $stmt->execute($query_params);
    }
    else 
    {
      $result = $stmt->execute();
    }
  }
  catch(PDOException $ex)
  {
    die("Failed to run query: " . $ex->getMessage());
  }

  /* if we're pulling up just one order, else pull for all orders */
  if ($_GET and !empty($_GET['order']))
  {
    $row = $stmt->fetch();
  } else
  {
    $rows = $stmt->fetchAll();
  }

  if ($_GET)
  {
    $query = "
      SELECT
        first_name,
        last_name,
        address,
        postcode,
        phone
      FROM
        users
      WHERE
        customer_id = :get_id
    ";
    
    if (!empty($_GET['id']))
    {
      $query_params = array(
        ':get_id' => $_GET['id']
      );
    } else
    {
      $query_params = array(
        ':get_id' => $customer_id
      );
    }

    try
    {
      $stmt = $db->prepare($query);
      $result = $stmt->execute($query_params);
    }
    catch(PDOException $ex)
    {
      die("Failed to run query: " . $ex->getMessage());
    }

    $userrow = $stmt->fetch();
  }

  if (!$_GET)
  {
    $title = "All Orders";
  }
  else
  {
    $title = $userrow['first_name'] . "'s Orders | Star Dream Cakes";
  }
?>
<?php include("../lib/header.php"); ?>
  <div class="container">
    <div class="orders">
      <?php if (empty($row) and !empty($_GET['order'])) : ?>
        <h1><span class="error_message">No order exists for order number <?php echo $_GET['order']; ?></span></h1>
      <!--Show all orders-->
      <?php elseif (!$_GET) : ?>
        <h1>All Orders</h1>
        <form action="allorders.php" method="GET">
          <input type="text" id="order_number" name="order" placeholder="Enter order number" />
          <input type="hidden" name="type" value="search" />
          <input type="submit" value="Search all orders" />
        </form>
        <table>
          <tr>
            <th>Customer ID</th>
            <th>Order Number</th>
            <th>Order Date</th>
            <th>Required Date</th>
            <th>Status</th>
            <th>Order</th>
          </tr>
          <?php foreach($rows as $row): ?>
            <tr>
              <td><a href="../all-orders/?id=<?php echo $row['customer_id']; ?>"><?php echo $row['customer_id']; ?></a></td>
              <td><a href="../all-orders/?order=<?php echo $row['order_number']; ?>&id=<?php echo $row['customer_id']; ?>"><?php echo $row['order_number']; ?></a></td>
              <td><?php echo htmlentities($row['order_date'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td><?php echo htmlentities($row['datetime'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td><?php echo htmlentities($row['status'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td><?php echo htmlentities($row['customer_order'], ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
          <?php endforeach; ?>
        </table>
      <!-- if user clicked on order number or searched for an order -->
      <?php elseif (!empty($_GET['order'])) : ?>
        <h1>Order <?php echo $row['order_number']; ?></h1>
        <p>Placed by <?php echo htmlentities($userrow['first_name'], ENT_QUOTES, 'UTF-8'); echo " "; echo htmlentities($userrow['last_name'], ENT_QUOTES, 'UTF-8'); ?></p>
        <br />
        <b>Address:</b><br />
        <?php echo htmlentities($userrow['address'], ENT_QUOTES, 'UTF-8'); ?><br />
        <?php echo htmlentities($userrow['postcode'], ENT_QUOTES, 'UTF-8'); ?><br />
        <br />
        <b>Phone: </b>
        <?php echo $userrow['phone']; ?><br />
        <br /><br />
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
            <td>&pound; <form action="../lib/update-order.php" method="POST"><input type="hidden" value="<?php echo $_GET['id']; ?>" name="id"><input type="hidden" value="<?php echo $row['order_number']; ?>" name="order_number"><input name="agreed_price" type="text" value="<?php echo $row['agreed_price']; ?>" style="width:50px;"><input type="submit" value="Update"></form></td>
          </tr>
          <tr>
            <th>Delivery Charge</th>
            <td>&pound; <form action="../lib/update-order.php" method="POST"><input type="hidden" value="<?php echo $_GET['id']; ?>" name="id"><input type="hidden" value="<?php echo $row['order_number']; ?>" name="order_number"><input name="delivery_charge" type="text" value="<?php echo $row['delivery_charge']; ?>" style="width:50px;"><input type="submit" value="Update"></form></td>
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
      <!-- show all orders by a customer -->
      <?php elseif (!empty($_GET['id'])) : ?>
        <h1>Orders placed by <?php echo htmlentities($userrow['first_name'], ENT_QUOTES, 'UTF-8'); echo " "; echo htmlentities($userrow['last_name'], ENT_QUOTES, 'UTF-8'); ?></h1>
        <b>Address:</b><br />
        <?php echo htmlentities($userrow['address'], ENT_QUOTES, 'UTF-8'); ?><br />
        <?php echo htmlentities($userrow['postcode'], ENT_QUOTES, 'UTF-8'); ?><br />
        <br />
        <b>Phone: </b>
        <?php echo htmlentities($userrow['phone'], ENT_QUOTES, 'UTF-8'); ?><br />
        <br /><br />
        <table>
          <tr>
            <th>Order Number</th>
            <th>Order Date</th>
            <th>Required Date</th>
            <th>Status</th>
            <th>Customers Order</th>
            <th>Update Status</th>
          </tr>
          <?php foreach($rows as $row): ?>
            <tr>
              <td><?php echo htmlentities($row['order_number'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td><?php echo htmlentities($row['order_date'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td><?php echo htmlentities($row['datetime'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td><?php echo htmlentities($row['status'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td><?php echo htmlentities($row['customer_order'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td>
                <form action="../lib/update-order.php" method="POST">
                  <select name="status">
                    <option value="Processing">Processing</option>
                    <option value="Dispatched">Dispatched</option>
                    <option value="Complete">Complete</option>
                  </select>
                  <input type="hidden" value="<?php echo htmlentities($row['order_number'], ENT_QUOTES, 'UTF-8'); ?>" name="order_number" />
                  <input type="hidden" value="<?php echo $_GET['id'] ?>" name="id" />
                  <input type="submit" value="Update" />
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </table>
      <?php endif; ?>
    </div>
  </div>
<?php include("../lib/footer.php"); ?>
