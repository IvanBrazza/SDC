<?php
  /** 
    edit-order - a page where the user can edit
    their order.
  **/
  require("../lib/common.php");
  $page = "your-orders";
  $title = "Edit Order";

  if (empty($_SESSION)) 
  {
    header("Location: ../login");
    die();
  }
  
  // If the order is being updated (POST) else if we're getting
  // order details (GET)
  if (!empty($_POST))
  {
    $query = "
      SELECT
        cake_id
      FROM
        cakes
      WHERE
        cake_type = :cake_type
      AND
        cake_size = :cake_size
    ";

    $query_params = array(
      ':cake_type' => $_POST['cake_type'],
      ':cake_size' => $_POST['cake_size']
    );

    try
    {
      $stmt     = $db->prepare($query);
      $result   = $stmt->execute($query_params);
    }
    catch(PDOException $ex)
    {
      die("Failed to execute query:" . $ex->getMessage() . " query: " . $query);
    }

    $row      = $stmt->fetch();
    $cake_id  = $row['cake_id'];

    $query = "
      UPDATE
        orders
      SET
        datetime          = :datetime,
        celebration_date  = :celebration_date,
        comments          = :comments,
        filling           = :filling,
        decoration        = :decoration,
        cake_id           = :cake_id,
        delivery_type     = :delivery_type,
        base_price      = :base_price
      WHERE
        order_number = :order_number
    ";

    $query_params = array(
      ':datetime'         => $_POST['datetime'],
      ':celebration_date' => $_POST['celebration_date'],
      ':comments'         => $_POST['comments'],
      ':filling'          => $_POST['filling'],
      ':decoration'       => $_POST['decoration'],
      ':cake_id'          => $cake_id,
      ':delivery_type'    => $_POST['delivery'],
      ':order_number'     => $_POST['order_number'],
      ':base_price'     => $_POST['base-hidden']
    );

    try
    {
      $stmt     = $db->prepare($query);
      $result   = $stmt->execute($query_params);
    }
    catch(PDOException $ex)
    {
      die("Failed to execute query:" . $ex->getMessage() . " query: " . $query);
    }

    if ($_POST['delivery'] === "Deliver To Address")
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
        ':order_number' => $_POST['order_number']
      );
      
      try
      {
        $stmt     = $db->prepare($query);
        $result   = $stmt->execute($query_params);
      }
      catch(PDOException $ex)
      {
        die("Failed to execute query:" . $ex->getMessage() . " query: " . $query);
  
      }

      $row = $stmt->fetch();

      include "../lib/distance.php";
      $miles = calculateDistance($_SESSION['user']['address'], $_SESSION['user']['postcode']);
      $remaining_miles = $miles - 5;
      $remaining_miles = round($remaining_miles / 5) * 5;
      if ($remaining_miles <= 0)
      {
        $delivery_charge = 0;
      }
      else
      {
        for ($i = 5, $j = 3; $i <= 50; $i++, $j = $j + 3)
        {
          if ($remaining_miles == $i)
          {
            $delivery_charge = $j;
          }
        }
      }
      
      if ($row)
      {
        $query = "
          UPDATE
            delivery
          SET
            miles           = :miles,
            delivery_charge = :delivery_charge
          WHERE
            order_number = :order_number
        ";

        $query_params = array(
          ':miles'            => $miles,
          ':delivery_charge'  => $delivery_charge,
          ':order_number'     => $_POST['order_number']
        );
      }
      else
      {
        $query = "
          INSERT INTO delivery (
            order_number,
            miles,
            delivery_charge
          ) VALUES (
            :order_number,
            :miles,
            :delivery_charge
          )
        ";
  
        $query_params = array(
          ':order_number'     => $_POST['order_number'],
          ':miles'            => $miles,
          ':delivery_charge'  => $delivery_charge
        );
      }
      
      try
      {
        $stmt     = $db->prepare($query);
        $result   = $stmt->execute($query_params);
      }
      catch(PDOException $ex)
      {
        die("Failed to execute query:" . $ex->getMessage() . " query: " . $query);
  
      }
    }

    header("Location: ../your-orders/?order=" . $_POST['order_number']);
    die();
  }
  else if (!empty($_GET))
  {
    // Get all the order details
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
      die("Failed to execute query: " . $ex->getMessage() . " query: " . $query);
    }
  
    $row = $stmt->fetch();
    
    // If the order is not from the logged in customer, die
    if ($row['customer_id'] != $_SESSION['user']['customer_id'])
    {
      header("Location: ../home");
      die();
    }

    // If the order is archived, die. You can't edit an archived
    // order (is that not obvious?)
    if ($row['archived'] === "1")
    {
      header("Location: ../home");
      die();
    }

    // Check if we need to pull up delivery details
    if ($row['delivery_type'] === "Deliver To Address")
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
        ':order_number' => $_GET['order']
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
      
      $deliveryrow = $stmt->fetch();
    }

    // Pul up cake details
    $query = "
      SELECT
        *
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
    
    $cakerow = $stmt->fetch();
  }
  else
  {
    header("Location: ../home");
    die();
  }
?>

<?php include("../lib/header.php"); ?>
  <script>
    var $origins = <?php echo json_encode(str_replace(" ", "+", $_SESSION['user']['address']) . "," . str_replace(" ", "+", $_SESSION['user']['postcode'])); ?>,
        $destination = "95+Hoe+Lane,EN35SW";
  </script>
  <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCKeZpb8doUO3DbEqT3t-uRJYsbEPbD3AE&sensor=false"></script>
  <h1>Editing Order <?php echo $row['order_number']; ?><?php if ($row['archived'] === "1") : ?> (archived)<?php endif; ?></h1>
  <form action="index.php" method="POST">
    <input type="hidden" name="order_number" value="<?php echo $row['order_number']; ?>" />
    <table id="single_order">
      <tr>
        <th>Date Order Placed</th>
        <td><?php echo $row['order_date']; ?></td>
      </tr>
      <tr>
        <th id="datetime-label">Date/Time For <?php if ($row['delivery_type'] === "Collection") : ?>Collection<?php else : ?>Delivery<?php endif; ?></th>
        <td><input type="text" name="datetime" class="datetime" value="<?php echo $row['datetime']; ?>" id="datetime" onchange="validateInput('#datetime', '#datetime_error')"></td>
      </tr>
      <tr>
        <th>Date Of Celebration</th>
        <td><input type="text" name="celebration_date" value="<?php echo $row['celebration_date']; ?>" class="date" id="celebration_date" onchange="validateInput('#celebration_date', '#celebration_date_error')"></td>
      </tr>
      <tr>
        <th>Status</th>
        <td><?php echo $row['status']; ?></td>
      </tr>
      <tr>
        <th>Comments</th>
        <td><input type="text" name="comments" value="<?php echo htmlentities($row['comments'], ENT_QUOTES, 'UTF-8'); ?>" id="comments" onchange="validateInput('#comments', '#comments_error')"</td>
      </tr>
      <tr>
        <th>Filling</th>
        <td>
          <select name="filling" id="filling">
            <option value="None" <?php if ($row['filling'] === "None") : ?>selected="selected"<?php endif; ?>>None</option>
            <option value="Butter Cream" <?php if ($row['filling'] === "Butter Cream") : ?>selected="selected"<?php endif; ?>>Butter Cream</option>
            <option value="Chocolate" <?php if ($row['filling'] === "Chocolate") : ?>selected="selected"<?php endif; ?>>Chocolate</option>
            <option value="Other" <?php if ($row['filling'] === "Other") : ?>selected="selected"<?php endif; ?>>Other (specify in comments)</option>
          </select>
        </td>
      </tr>
      <tr>
        <th>Decoration</th>
        <td>
          <select name="decoration" id="decoration">
            <option value="None" <?php if ($row['decoration'] === "None") : ?>selected="selected"<?php endif; ?>>None</option>
            <option value="Royal Icing" <?php if ($row['decoration'] === "Royal Icing") : ?>selected="selected"<?php endif; ?>>Royal Icing</option>
            <option value="Regal Icing" <?php if ($row['decoration'] === "Regal Icing") : ?>selected="selected"<?php endif; ?>>Regal Icing</option>
            <option value="Butter Cream" <?php if ($row['decoration'] === "Butter Cream") : ?>selected="selected"<?php endif; ?>>Butter Cream</option>
            <option value="Chocolate" <?php if ($row['decoration'] === "Chocolate") : ?>selected="selected"<?php endif; ?>>Chocolate</option>
            <option value="Coconut" <?php if ($row['decoration'] === "Coconut") : ?>selected="selected"<?php endif; ?>>Coconut</option>
            <option value="Other" <?php if ($row['decoration'] === "Other") : ?>selected="selected"<?php endif; ?>>Other (specify in comments)</option>
          </select>
        </td>
      </tr>
      <tr>
        <th>Cake Size</th>
        <td>
          <select name="cake_size" id="cake_size">
            <option value='6"' <?php if ($cakerow['cake_size'] === '6"') : ?>selected="selected"<?php endif; ?>>6"</option>
            <option value='8"' <?php if ($cakerow['cake_size'] === '8"') : ?>selected="selected"<?php endif; ?>>8"</option>
            <option value='10"' <?php if ($cakerow['cake_size'] === '10"') : ?>selected="selected"<?php endif; ?>>10"</option>
            <option value='12"' <?php if ($cakerow['cake_size'] === '12"') : ?>selected="selected"<?php endif; ?>>12"</option>
            <option value='14"' <?php if ($cakerow['cake_size'] === '14"') : ?>selected="selected"<?php endif; ?>>14"</option>
          </select>
        </td>
      </tr>
      <tr>
        <th>Cake Type</th>
        <td>
          <select name="cake_type" id="cake_type">
            <option value="Sponge" <?php if ($cakerow['cake_type'] === "Sponge") : ?>selected="selected"<?php endif; ?>>Sponge</option>
            <option value="Marble" <?php if ($cakerow['cake_type'] === "Marble") : ?>selected="selected"<?php endif; ?>>Marble</option>
            <option value="Chocolate" <?php if ($cakerow['cake_type'] === "Chocolate") : ?>selected="selected"<?php endif; ?>>Chocolate</option>
            <option value="Fruit" <?php if ($cakerow['cake_type'] === "Fruit") : ?>selected="selected"<?php endif; ?>>Fruit</option>
          </select>
        </td>
      </tr>
      <tr>
        <th>Base Price</th>
        <td>&pound;<span id="base-price"><?php echo $row['base_price']; ?></span><input type="hidden" name="base-hidden" id="base-hidden" value="<?php echo $row['base_price']; ?>" /></td>
      </tr>
      <tr id="delivery-charge">
        <th>Delivery Charge</th>
        <td><span id="delivery-charge-html"><?php echo "&pound;" .  $deliveryrow['delivery_charge']; ?></span><input type="hidden" name="delivery_charge" id="delivery_charge" value="<?php echo $deliveryrow['delivery_charge']; ?>" />
      </tr>
      <tr>
        <th>Delivery Type</th>
        <td>
          <select name="delivery" id="delivery">
            <option value="Collection" <?php if ($row['delivery_type'] === "Collection") : ?>selected="selected"<?php endif; ?>>Collection</option>
            <option value="Deliver To Address" <?php if ($row['delivery_type'] === "Deliver To Address") : ?>selected="selected"<?php endif; ?>>Delivery</option>
          </select>
        </td>
      </tr>
      <tr>
        <th>Grand Total</th>
        <td>&pound;<span id="total-html"><?php echo "&pound;"; echo $row['base_price']+$row['delivery_charge']; ?></span></td>
      </tr>
    </table>
    <input type="submit" value="Update Order" />
  </form>
<?php include("../lib/footer.php"); ?>
