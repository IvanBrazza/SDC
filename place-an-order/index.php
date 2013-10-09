<?php
  /**
    place-an-order/ - display a form to the user so they
    can place their order.
  **/
  require("../lib/common.php");
  $title = "Place An Order";
  $page = "place-an-order";

  if(empty($_SESSION['user']))
  {
    header("Location: ../login/?e=pao");
    die();
  }

  if (!empty($_POST))
  {
    $query = "
      SELECT
        cake_id
      FROM
        cakes
      WHERE
        cake_size = :cake_size
      AND
        cake_type = :cake_type
    ";

    $query_params = array(
      ':cake_size'  => $_POST['cake_size'],
      ':cake_type'  => $_POST['cake_type']
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
    $cake_id = $row['cake_id'];

    $query = "
      INSERT INTO orders (
        customer_id,
        order_number,
        celebration_date,
        comments,
        decoration,
        filling,
        cake_id,
        order_date,
        delivery_type
      ) VALUES (
        :customer_id,
        :order_number,
        :celebration_date,
        :comments,
        :decoration,
        :filling,
        :cake_id,
        :order_date,
        :delivery_type
      )
    ";

    $order_number   = $_SESSION['user']['customer_id'] . rand(10000,99999);
    $order_date     = date('Y-m-d');
    $status         = "Processing";
    if ($_POST['delivery'] === "Collection") 
    {
      $delivery_charge = 0;
    }
    else
    {
      require("../lib/calculate-distance.php");
      $remaining_miles = $miles - 5;
      $remaining_miles = round($remaining_miles / 5) * 5;
      echo "remaining_miles: $remaining_miles\n";
      if ($remaining_miles <= 0)
      {
        $delivery_charge = 0;
      }
      else
      {
        echo "remaining_miles: $remaining_miles\n";
        for ($i = 5, $j = 1; $i <= 50; $i = $i + 5, $j++)
        {
          echo "i = $i j = $j remaining_miles = $remaining_miles\n";
          if ($remaining_miles == $i)
          {
            $delivery_charge = $j;
            echo "delivery_charge = $delivery_charge\n";
          }
        }
      }
    }

    $query_params = array(
      ':customer_id'        => $_SESSION['user']['customer_id'],
      ':order_number'       => $order_number,
      ':celebration_date'   => $order_date,
      ':comments'           => $_POST['comments'],
      ':decoration'         => $_POST['decoration'],
      ':filling'            => $_POST['filling'],
      ':cake_id'            => $cake_id,
      ':order_date'         => $order_date,
      ':delivery_type'      => $_POST['delivery']
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

    if ($_POST['delivery'] === "Deliver To Address")
    {
      $query = "
        INSERT INTO delivery (
          order_number,
          datetime,
          status,
          miles,
          delivery_charge
        ) VALUES (
          :order_number,
          :datetime,
          :status,
          :miles,
          :delivery_charge
        )
      ";

      $status = "Processing";

      $query_params = array(
        ':order_number'     => $order_number,
        ':datetime'         => $_POST['datetime'],
        ':status'           => $status,
        ':miles'            => $miles,
        ':delivery_charge'  => $delivery_charge
      );

      try
      {
        $stmt     = $db->prepare($query);
        $result   = $stmt->execute($query_params);
      }
      catch(PDOException $ex)
      {
        die("Failed to run query: " . $ex->getMessage() . "query: " . $query);
      }
    }

    include("../lib/email-order.php");
    
    header("Location: ../order-placed");
    die();
  }
  else
  {
    $query = "
      SELECT
        *
      FROM
        users
      WHERE
        username = :username
    ";

    $query_params = array(
      ':username' => $_SESSION['user']['username']
    );

    try
    {
      $stmt     = $db->prepare($query);
      $result   = $stmt->execute($query_params);
    }
    catch(PDOException $ex)
    {
      die("Failed to execute query: " . $ex->getMessage());
    }

    $row = $stmt->fetch();

    if (empty($row['address']) or empty($row['postcode']) or empty($row['phone']) or empty($row['first_name']) or empty($row['last_name']))
    {
      $display_message = 'Please <a href="../edit-account">update your details</a> before placing an order.';
      $details_correct = false;
    }
  }
?>
<?php include("../lib/header.php"); ?>
  <div class="container">
  <div class="form">
    <h1>Place An Order</h1>
    <div class="error">
      <span class="error_message">
        <?php echo $display_message; ?>
      </span>
    </div>
    <form action="index.php" method="POST" id="order-form">
      <div>
        <label for="filling">Filling</label>
        <select name="filling" id="filling">
          <option value="None">None</option>
          <option value="Butter Cream">Butter Cream</option>
          <option value="Chocolate">Chocolate</option>
        </select>
      </div>
      <div>
        <label for="decoration">Cake to be decorated in</label>
        <select name="decoration" id="decoration">
          <option value="None">None</option>
          <option value="Royal Icing">Royal Icing</option>
          <option value="Regal Icing">Regal Icing</option>
          <option value="Butter Cream">Butter Cream</option>
          <option value="Chocolate">Chocolate</option>
          <option value="Coconut">Coconut</option>
        </select>
      </div>
      <div>
        <label for="cake_size">Size of cake</label>
        <select name="cake_size" id="cake_size">
          <option value='10"'>10"</option>
          <option value='12"'>12"</option>
          <option value='14"'>14"</option>
          <option value='16"'>16"</option>
        </select>
      </div>
      <div>
        <label for="cake_type">Type of cake</label>
        <select name="cake_type" id="cake_type">
          <option value="Sponge">Sponge</option>
          <option value="Marble">Marble</option>
          <option value="Chocolate">Chocolate</option>
          <option value="Fruit">Fruit</option>
        </select>
      </div>
      <div>
        <label for="celebration_date">Date of celebration</label>
        <input type="text" name="celebration_date" class="date" id="celebration_date" onchange="validateInput('#celebration_date', '#celebration_date_error')">
      </div>
      <div id="celebration_date_error" class="validate-error"></div>
      <div id="comments">
        <label for="comments">Comments</label>
        <textarea name="comments" id="comments" rows="6" cols="30" onkeyup="validateInput('textarea#comments', '#comments_error')" onchange="validateInput('textarea#comments', '#comments_error')"></textarea>
      </div>
      <div id="comments_error" class="validate-error"></div>
      <div>
        <label for="delivery">Delivery options</label>
        <select name="delivery" id="delivery">
          <option value="Collection">You will collect the cake</option>
          <option value="Deliver To Address">The cake will be delivered to your address</option>
        </select>
      </div>
      <div>
        <label for="datetime">Date and time to collect/deliver order</label>
        <input type="text" id="datetime" name="datetime" onchange="validateInput('#datetime', '#datetime_error')">
      </div>
      <div id="datetime_error" class="validate-error"></div>
      <br /><br />
      <input type="submit" value="Submit Order" <?php if ($details_correct === false) : ?>disabled<?php endif; ?> />
    </form>
  </div>
  </div>
<?php include("../lib/footer.php");
