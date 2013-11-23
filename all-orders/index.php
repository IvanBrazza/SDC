<?php
  /**
    all-orders/ - display all orders, a specific order, or all orders
    by a specific customer number.
  **/
  require("../lib/common.php");
  $page = "all-orders";

  if(empty($_SESSION['user']) or $_SESSION['user']['username'] !== "admin")
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

    $customer_id = $row['customer_id'];
  }
  
  // Start the main query
  $query = "
    SELECT
      *
    FROM
      orders
    WHERE
      archived = 0
  ";
  
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
        AND
          order_number = :order_number
      ";

      $query_params = array(
        ':order_number' => $_GET['order']
      );
    } 
    else if (!empty($_GET['id']))
    {
      $query .= "
        AND
          customer_id = :get_id
      ";

      $query_params = array(
        ':get_id' => $_GET['id']
      );
    }
    else if (!empty($_GET['sort']))
    {
      $query .= "
        ORDER BY
          orders." . $_GET['col'] . " " . $_GET['sort']
      ;
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
    if (!empty($_GET['sort']))
    {
      $result = $stmt->execute();
    }
    else if ($_GET)
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

  // If we're pulling up one order, and it's archived, it won't be
  // in $rows, so search with the archived boolean set to true!
  if (empty($row) and !empty($_GET['order']))
  {
    $query = "
      SELECT
        *
      FROM
        orders
      WHERE
        archived = 1
      AND
        order_number = :order_number
      ORDER BY
        orders.customer_id ASC
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
      die("Failed to run query: " . $ex->getMessage() . "\nQuery: " . $query);
    }

    $row = $stmt->fetch();
  }

  // Get archived orders
  $query = "
    SELECT
      *
    FROM
      orders
    WHERE
      archived = 1
  ";
  
  // If the user clicked on a customer ID, get all archived
  // orders for that customer.
  if (!empty($_GET['id']))
  {
    $query .= "
      AND
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
  
  // Get details about the cake for an order
  if (!empty($_GET['order']))
  {
    $query = "
      SELECT
        cake_type,
        cake_size
      FROM
       cakes
      WHERE
        cake_id = :cake_id
    ";

    $query_params = array(
      ':cake_id' => $row['cake_id']
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

    $cake_row = $stmt->fetch();
  }

  // Get delivery details
  if (!empty($_GET['order']))
  {
    $query = "
      SELECT
        *
      FROM
        delivery
      WHERE
        order_number = :order_number
    ";

    $query_params = array(
      ':order_number' => $row['order_number']
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
    
    $delivery_row = $stmt->fetch();
  } 
  else
  {
    foreach ($rows as $row)
    {
      $query = "
        SELECT
          *
        FROM
          delivery
        WHERE
          order_number = :order_number
      ";

      $query_params = array(
        ':order_number' => $row['order_number']
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
    
      $delivery_row = $stmt->fetch();
    }
  }
  // Get customer details if the user clicked on an order number,
  // searched for an order, or clicked on a customer ID.
  if (!empty($_GET['order']) or !empty($_GET['id']))
  {
      $query = "
        SELECT
          first_name,
          last_name,
          address,
          postcode,
          phone,
          email
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

  if (!$_GET or !empty($_GET['archive']) or !empty($_GET['sort']))
  {
    $title = "All Orders";
  }
  else
  {
    $title = $userrow['first_name'] . "'s Orders | Star Dream Cakes";
  }
?>
<?php include("../lib/header.php"); ?>
  <?php if (empty($row) and !empty($_GET['order'])) : ?>
    <h1><span class="error_message">No order exists for order number <?php echo $_GET['order']; ?></span></h1>
  <!--Show all orders-->
  <?php elseif (!$_GET or !empty($_GET['archive']) or !empty($_GET['new-order']) or !empty($_GET['sort'])) : ?>
    <h1>All Orders</h1>
    <a href="../add-order">Add Order</a>
    <form action="../all-orders" method="GET" id="order_search">
      <input type="text" id="order_number" name="order" placeholder="Enter order number" />
      <input type="submit" value="Search all orders" />
      <span class="ajax-load"></span>
    </form>
    <div class="success">
      <span class="success_message">
        <?php echo $display_message; ?>
      </span>
    </div>
    <div class="error">
      <span class="error_message" id="error_message">
      </span>
    </div>
    <?php if (empty($rows) and empty($manual_rows)) : ?>
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
            <tr>
              <td><a href="../all-orders/?order=<?php echo $row['order_number']; ?>"></a><?php echo $row['order_number']; ?></td>
              <td><?php echo htmlentities($row['order_placed'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td><?php echo htmlentities($row['datetime'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td><?php echo htmlentities($row['status'], ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
    <?php if (empty($archived_rows)) : ?>
      <h3>There are no archived orders</h3>
    <?php else : ?>
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
          <?php foreach($archived_rows as $row): ?>
            <tr>
              <td><a href="../all-orders/?order=<?php echo $row['order_number']; ?>"></a><?php echo $row['order_number']; ?></td>
              <td><?php echo htmlentities($row['order_placed'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td><?php echo htmlentities($row['datetime'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td><?php echo htmlentities($row['status'], ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  <!-- if user clicked on order number or searched for an order -->
  <?php elseif (!empty($_GET['order'])) : ?>
    <h1>Order <?php echo $row['order_number']; ?><?php if ($row['archived'] === "0") : ?><form action="../lib/archive-order.php" method="POST" id="archive-order"><input type="hidden" value="<?php echo $row['order_number']; ?>" name="order_number" id="order_number"><input type="submit" value="Archive Order" class="delete_testimonial_btn"></form><?php else : ?> (archived)<?php endif; ?></h1>
      <p>Placed by <?php echo htmlentities($userrow['first_name'], ENT_QUOTES, 'UTF-8'); echo " "; echo htmlentities($userrow['last_name'], ENT_QUOTES, 'UTF-8'); ?></p>
      <br />
      <span class="title">Address:</span><br />
      <?php echo htmlentities($userrow['address'], ENT_QUOTES, 'UTF-8'); ?><br />
      <?php echo htmlentities($userrow['postcode'], ENT_QUOTES, 'UTF-8'); ?><br />
      <?php include "../lib/distance.php"; ?>
      <i>(<?php echo calculateDistance($userrow['address'], $userrow['postcode']); ?> miles away)</i><br/>
      <a href="../get-directions?id=<?php echo $row['customer_id']; ?>">Get directions</a>
      <br />
      <br />
      <span class="title">Phone: </span>
      <?php echo $userrow['phone']; ?><br />
      <br /><br />
    <table id="single_order">
      <tr>
        <th>Order Placed</th>
        <td><?php echo $row['order_placed']; ?></td>
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
              <input type="hidden" value="<?php echo $userrow['first_name']; ?>" name="first_name">
              <input type="hidden" value="<?php echo $userrow['email']; ?>" name="email">
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
        <td><?php echo htmlentities($row['filling'], ENT_QUOTES, 'UTF-8'); ?></td>
      </tr>
      <tr>
        <th>Decoration</th>
        <td><?php echo htmlentities($row['decoration'], ENT_QUOTES, 'UTF-8'); ?></td>
      </tr>
      <tr>
        <th>Cake Type</th>
        <td><?php echo htmlentities($cake_row['cake_type'], ENT_QUOTES, 'UTF-8'); ?></td>
      </tr>
      <tr>
        <th>Cake Size</th>
        <td><?php echo htmlentities($cake_row['cake_size'], ENT_QUOTES, 'UTF-8'); ?></td>
      </tr>
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
      <?php if (!empty($delivery_row)) : ?>
        <tr>
          <th>Delivery Charge</th>
          <td>
            &pound; 
            <?php if ($row['archived'] === "0") : ?>
              <form action="../lib/update-order.php" method="POST">
                <input type="hidden" value="<?php echo $row['order_number']; ?>" name="order_number">
                <input name="delivery_charge" type="text" value="<?php echo $delivery_row['delivery_charge']; ?>" style="width:50px;">
                <input type="submit" value="Update">
              </form>
            <?php else : ?>
              <?php echo $delivery_row['delivery_charge']; ?>
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
        <td>&pound;<?php echo $row['base_price']+$delivery_row['delivery_charge']; ?></td>
      </tr>
    </table>
  <!-- show all orders by a customer -->
  <?php elseif (!empty($_GET['id'])) : ?>
    <h1>Orders placed by <?php echo htmlentities($userrow['first_name'], ENT_QUOTES, 'UTF-8'); echo " "; echo htmlentities($userrow['last_name'], ENT_QUOTES, 'UTF-8'); ?></h1>
    <span class="title">Address:</span><br />
    <?php echo htmlentities($userrow['address'], ENT_QUOTES, 'UTF-8'); ?><br />
    <?php echo htmlentities($userrow['postcode'], ENT_QUOTES, 'UTF-8'); ?><br />
    <?php include "../lib/distance.php" ?>
    <i>(<?php echo calculateDistance($userrow['address'], $userrow['postcode']); ?> miles away)</i><br />
    <a href="../get-directions?id=<?php echo $row['customer_id']; ?>">Get directions</a>
    <br />
    <br />
    <span class="title">Phone: </span>
    <?php echo htmlentities($userrow['phone'], ENT_QUOTES, 'UTF-8'); ?><br />
    <br /><br />
    <?php if (empty($rows)) : ?>
      <h3>There are no outstanding orders</h3>
    <?php else : ?>
      <table class="orders-table" id="orders-js">
        <caption>Outstanding Orders</caption>
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
            <tr>
              <td><a href="../all-orders/?order=<?php echo $row['order_number']; ?>"></a><?php echo $row['order_number']; ?></td>
              <td><?php echo htmlentities($row['order_placed'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td><?php echo htmlentities($row['datetime'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td><?php echo htmlentities($row['status'], ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
    <?php if (empty($archived_rows)) : ?>
      <h3>There are no archived orders</h3>
    <?php else : ?>
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
          <?php foreach($archived_rows as $row): ?>
            <tr>
              <td><a href="../all-orders/?order=<?php echo $row['order_number']; ?>"></a><?php echo $row['order_number']; ?></td>
              <td><?php echo htmlentities($row['order_placed'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td><?php echo htmlentities($row['datetime'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td><?php echo htmlentities($row['status'], ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  <?php endif; ?>
<?php include("../lib/footer.php"); ?>
