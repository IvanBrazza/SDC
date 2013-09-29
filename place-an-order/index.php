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
      INSERT INTO orders (
        customer_id,
        order_number,
        order_date,
        datetime,
        celebration_date,
        status,
        customer_order,
        filling,
        size,
        design,
        decoration,
        delivery
      ) VALUES (
        :customer_id,
        :order_number,
        :order_date,
        :datetime,
        :celebration_date,
        :status,
        :customer_order,
        :filling,
        :size,
        :design,
        :decoration,
        :delivery
      )
    ";

    $order_number   = $_SESSION['user']['customer_id'] . rand(10000,99999);
    $order_date     = date('Y-m-d');
    $status         = "Processing";

    $query_params = array(
      ':customer_id'        => $_SESSION['user']['customer_id'],
      ':order_number'       => $order_number,
      ':order_date'         => $order_date,
      ':datetime'           => $_POST['datetime'],
      ':celebration_date'   => $_POST['celebration_date'],
      ':status'             => $status,
      ':customer_order'     => $_POST['order'],
      ':filling'            => $_POST['filling'],
      ':size'               => $_POST['size'],
      ':design'             => $_POST['design'],
      ':decoration'         => $_POST['decoration'],
      ':delivery'           => $_POST['delivery']
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
        <label for="size">Size of cake</label>
        <select name="size" id="size">
          <option value='10"'>10"</option>
          <option value='12"'>12"</option>
          <option value='14"'>14"</option>
          <option value='16"'>16"</option>
          <option value='18"'>18"</option>
          <option value='R'>R</option>
          <option value='S'>S</option>
        </select>
      </div>
      <div>
        <label for="design">Design</label>
        <input type="text" name="design" id="design" onkeyup="validateInput('#design', '#design_error')" onchange="validateInput('#design', '#design_error')">
      </div>
      <div id="design_error" class="validate-error"></div>
      <div>
        <label for="celebration_date">Date of celebration</label>
        <input type="text" name="celebration_date" class="date" id="celebration_date" onchange="validateInput('#celebration_date', '#celebration_date_error')">
      </div>
      <div id="celebration_date_error" class="validate-error"></div>
      <div id="order">
        <label for="order">Your order</label>
        <textarea name="order" id="order" rows="6" cols="30" onkeyup="validateInput('textarea#order', '#order_error')" onchange="validateInput('textarea#order', '#order_error')"></textarea>
      </div>
      <div id="order_error" class="validate-error"></div>
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
