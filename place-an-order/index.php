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
        delivery_type,
        status,
        datetime,
        agreed_price
      ) VALUES (
        :customer_id,
        :order_number,
        :celebration_date,
        :comments,
        :decoration,
        :filling,
        :cake_id,
        :order_date,
        :delivery_type,
        :status,
        :datetime,
        :agreed_price
      )
    ";

    $order_number   = $_SESSION['user']['customer_id'] . rand(10000,99999);
    $order_date     = date('Y-m-d');
    $status         = "Processing";

    $query_params = array(
      ':customer_id'        => $_SESSION['user']['customer_id'],
      ':order_number'       => $order_number,
      ':celebration_date'   => $order_date,
      ':comments'           => $_POST['comments'],
      ':decoration'         => $_POST['decoration'],
      ':filling'            => $_POST['filling'],
      ':cake_id'            => $cake_id,
      ':order_date'         => $order_date,
      ':delivery_type'      => $_POST['delivery'],
      ':status'             => $status,
      ':datetime'           => $_POST['datetime'],
      ':agreed_price'       => $_POST['total-hidden']
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
        for ($i = 5, $j = 1; $i <= 50; $i = $i + 5, $j++)
        {
          if ($remaining_miles == $i)
          {
            $delivery_charge = $j;
          }
        }
      }

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

      $status = "Processing";

      $query_params = array(
        ':order_number'     => $order_number,
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

    include "../lib/email.php";
    emailOrder($_SESSION['user']['email'], 
               $_SESSION['user']['first_name'],
               $order_number,
               $order_date,
               $_POST["datetime"],
               $_POST["celebration_date"],
               $_POST["comments"],
               $_POST["filling"],
               $_POST["decoration"],
               $_POST["cake_type"],
               $_POST["cake_size"],
               $_POST["delivery"]);
    
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
          <option value="Other">Other (specify in comments)</option>
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
          <option value="Other">Other (specify in comments)</option>
        </select>
      </div>
      <div>
        <label for="cake_size">Size of cake</label>
        <select name="cake_size" id="cake_size">
          <option value='6"'>6"</option>
          <option value='8"'>8"</option>
          <option value='10"'>10"</option>
          <option value='12"'>12"</option>
          <option value='14"'>14"</option>
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
          <option value="Collection">Collection</option>
          <option value="Deliver To Address">Delivery</option>
        </select>
      </div>
      <div>
        <label for="datetime" id="datetime-label">Date/time for collection</label>
        <input type="text" id="datetime" name="datetime" onchange="validateInput('#datetime', '#datetime_error')">
      </div>
      <div id="datetime_error" class="validate-error"></div>
      <br />
      <b>Total: &pound;<div id="total-html"></div></b>
      <input type="hidden" id="total-hidden" name="total-hidden" value="">
      <br /><br />
      <input type="submit" value="Submit Order" <?php if ($details_correct === false) : ?>disabled<?php endif; ?> />
    </form>
  </div>
<?php include("../lib/footer.php");
