<?php
  /**
    all-orders/ - display all orders, a specific order, or all orders
    by a specific customer number.
  **/
  require("../lib/common.php");
  $page = "all-orders";

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
  else if (!empty($_GET['new-order']))
  {
    if ($_GET['new-order'] === "added")
    {
      $display_message = "Order added.";
    }
  }
  
  // Get the customer_id from the order_number
  if (!empty($_GET['order']))
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
      $stmt     = $db->prepare($query);
      $result   = $stmt->execute($query_params);
    }
    catch(PDOException $ex)
    {
      die("Failed to execute query: " . $ex->getMessage() . "\nQuery: " . $query);
    }

    $row = $stmt->fetch();
    
    // If a customer ID isn't returned from the orders table,
    // try and get the ID from the archived_orders table.
    if (!$row)
    {
      $query = "
        SELECT
          customer_id
        FROM
          archived_orders
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
        die("Failed to execute query: " . $ex->getMessage() . "\nQuery: " . $query);
      }

      $row = $stmt->fetch();
    }

    $customer_id = $row['customer_id'];
  }
  
  // Start the main query
  $query = "
    SELECT
      *
    FROM
  ";
  
  // If user clicked on an archived order number,
  // get the order from the archived_orders table.
  // Else if the user searched for an order and it's
  // a manual order, get the order from the manual_orders
  // table. Else get the order from the orders table.
  if (!empty($_GET['archived']))
  {
    $query .= "
        archived_orders
    ";
  }
  else
  {
    $query .= "
        orders
    ";
  }
  
  // If the user searched for an order, clicked an order
  // number, or clicked on a customer ID. Else we're displaying
  // all orders, and thus need to sort the results by customer_id
  // ascending.
  if ($_GET)
  {
    // If the user clicked on an order number, get the order.
    // Else if the user clicked on a customer ID, get all
    // orders by that customer.
    if (!empty($_GET['order']))
    {
      $query .= "
        WHERE
          order_number = :order_number
      ";

      $query_params = array(
        ':order_number' => $_GET['order']
      );
    } 
    else if (!empty($_GET['id']))
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
    die("Failed to run query: " . $ex->getMessage() . "\nQuery: " . $query);
  }

  // If we're pulling up just one order, else pull for all orders
  if ($_GET and !empty($_GET['order']))
  {
    $row = $stmt->fetch();
  }
  else
  {
    $rows = $stmt->fetchAll();
  }
  
  // If we're searching orders, the order isn't manual
  // and the order isn't in the orders table then search
  // for it in the archived_orders table instead.
  if (empty($row) and !empty($_GET['type']) and $_GET['type'] === "search")
  {
    $query = "
      SELECT
        *
      FROM
        archived_orders
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
      die("Failed to execute query: " . $ex->getMessage() . "\nQuery: " . $query);
    }

    $row = $stmt->fetch();
    
    if ($row)
    {
      $_GET['archived'] = "true";
    }
  }

  // Get archived orders
  $query = "
    SELECT
      *
    FROM
      archived_orders
  ";
  
  // If the user clicked on a customer ID, get all archived
  // orders for that customer.
  if (!empty($_GET['id']))
  {
    $query .= "
      WHERE
        customer_id = :customer_id
    ";

    $query_params = array(
      ':customer_id' => $_GET['id']
    );
  }

  $query .= "
    ORDER BY
      customer_id ASC
  ";

  try
  {
    $stmt = $db->prepare($query);
    if (!empty($_GET['id']))
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
    die("Failed to execute query: " . $ex->getMessage() . "\nQuery: " . $query);
  }

  $archived_rows = $stmt->fetchAll();

  // Get customer details if the user clicked on an order number,
  // searched for an order, or clicked on a customer ID.
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
        $stmt     = $db->prepare($query);
        $result   = $stmt->execute($query_params);
      }
      catch(PDOException $ex)
      {
        die("Failed to run query: " . $ex->getMessage() . "\nQuery: " . $query);
      }
  
      $userrow = $stmt->fetch();
    
  }

  if (!$_GET or !empty($_GET['archive']))
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
      <?php elseif (!$_GET or !empty($_GET['archive']) or !empty($_GET['new-order'])) : ?>
        <h1>All Orders</h1>
        <a href="../add-order">Add Order</a>
        <form action="../all-orders" method="GET">
          <input type="text" id="order_number" name="order" placeholder="Enter order number" />
          <input type="hidden" name="type" value="search" />
          <input type="submit" value="Search all orders" />
        </form>
        <div class="success">
          <span class="success_message">
            <?php echo $display_message; ?>
          </span>
        </div>
        <?php if (empty($rows) and empty($manual_rows)) : ?>
          <h3>There are no outstanding orders</h3>
        <?php else : ?>
          <table class="orders-table">
            <caption>Outstanding Orders</caption>
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
                <td><a href="../all-orders/?order=<?php echo $row['order_number']; ?>"><?php echo $row['order_number']; ?></a></td>
                <td><?php echo htmlentities($row['order_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlentities($row['datetime'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlentities($row['status'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlentities($row['customer_order'], ENT_QUOTES, 'UTF-8'); ?></td>
              </tr>
            <?php endforeach; ?>
            <?php foreach ($manual_rows as $row) : ?>
              <tr>
                <td>Manual Order</td>
                <td><a href="../all-orders/?order=<?php echo $row['order_number']; ?>"><?php echo $row['order_number']; ?></a></td>
                <td><?php echo htmlentities($row['order_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlentities($row['datetime'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlentities($row['status'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlentities($row['customer_order'], ENT_QUOTES, 'UTF-8'); ?></td>
              </tr>
            <?php endforeach; ?>
          </table>
        <?php endif; ?>
        <?php if (empty($archived_rows)) : ?>
          <h3>There are no archived orders</h3>
        <?php else : ?>
          <table>
            <caption>Archived Orders</caption>
            <tr>
              <th>Customer ID</th>
              <th>Order Number</th>
              <th>Order Date</th>
              <th>Required Date</th>
              <th>Status</th>
              <th>Order</th>
            </tr>
            <?php foreach($archived_rows as $row): ?>
              <tr>
                <td><a href="../all-orders/?id=<?php echo $row['customer_id']; ?>"><?php echo $row['customer_id']; ?></a></td>
                <td><a href="../all-orders/?order=<?php echo $row['order_number']; ?>&archived=true"><?php echo $row['order_number']; ?></a></td>
                <td><?php echo htmlentities($row['order_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlentities($row['datetime'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlentities($row['status'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlentities($row['customer_order'], ENT_QUOTES, 'UTF-8'); ?></td>
              </tr>
            <?php endforeach; ?>
          </table>
        <?php endif; ?>
      <!-- if user clicked on order number or searched for an order -->
      <?php elseif (!empty($_GET['order'])) : ?>
        <h1>Order <?php echo $row['order_number']; ?><?php if (empty($_GET['archived'])) : ?><form action="../lib/archive-order.php" method="POST" id="archive-order"><input type="hidden" value="<?php echo $row['order_number']; ?>" name="order_number" id="order_number"><input type="submit" value="Archive Order" class="delete_testimonial_btn"></form><?php else : ?> (archived)<?php endif; ?></h1>
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
        <table class="orders-table">
          <caption>Outstanding Orders</caption>
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
        <table>
          <caption>Archived Orders</caption>
          <tr>
            <th>Order Number</th>
            <th>Order Date</th>
            <th>Required Date</th>
            <th>Status</th>
            <th>Customers Order</th>
          </tr>
          <?php foreach($archived_rows as $row): ?>
            <tr>
              <td><?php echo htmlentities($row['order_number'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td><?php echo htmlentities($row['order_date'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td><?php echo htmlentities($row['datetime'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td><?php echo htmlentities($row['status'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td><?php echo htmlentities($row['customer_order'], ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
          <?php endforeach; ?>
        </table>
      <?php endif; ?>
    </div>
  </div>
<?php include("../lib/footer.php"); ?>
