<?php
  /** 
    add-order/ - allow the admin to add an order into
    the system manually
  **/
  require("../lib/common.php");
  $title = "Add Order";
  $page = "all-orders";

  if (empty($_SESSION['user']) or $_SESSION['user']['username'] !== "admin") {
    header("Location: ../login");
    die();
  }

  // If the form was submitted
  if ($_POST)
  {
    // If we're inserting an order for a customer that
    // isn't registered on the site
    if ($_POST['existing_id'] === "null")
    {
      // Insert the customer into the users table
      $query = "
        INSERT INTO users(
          first_name,
          last_name,
          address,
          postcode,
          phone,
          email
        ) VALUES (
          :first_name,
          :last_name,
          :address,
          :postcode,
          :phone,
          :email
        )
      ";
  
      $query_params = array(
          ':first_name' => $_POST['first_name'],
          ':last_name'  => $_POST['last_name'],
          ':address'    => $_POST['address'],
          ':postcode'   => $_POST['postcode'],
          ':phone'      => $_POST['phone'],
          ':email'      => $_POST['email']
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
  
      // Get the customer_id of the new user we just created
      // so we can use it in the orders table
      $query = "
        SELECT
          *
        FROM
          users
        ORDER BY
          customer_id DESC
        LIMIT
          1
      ";
  
      try
      {
        $stmt = $db->prepare($query);
        $result = $stmt->execute();
      }
      catch(PDOException $ex)
      {
        die("Failed to execute query: " . $ex->getMessage() . "Query: " . $query);
      }
  
      $row = $stmt->fetch();
    }
    else
    {
      $query = "
        SELECT
          address,
          postcode
        FROM
          users
        WHERE
          customer_id = :customer_id
      ";

      $query_params = array(
        ':customer_id' => $_POST['existing_id']
      );
  
      try
      {
        $stmt = $db->prepare($query);
        $result = $stmt->execute($query_params);
      }
      catch(PDOException $ex)
      {
        die("Failed to execute query: " . $ex->getMessage() . "Query: " . $query);
      }
  
      $userrow = $stmt->fetch();
    }

    // Get the cake ID of the cake based on
    // the type and size given by the user
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
      ':cake_size' => $_POST['cake_size'],
      ':cake_type' => $_POST['cake_type']
    );

    try
    {
      $stmt = $db->prepare($query);
      $result = $stmt->execute($query_params);
    }
    catch(PDOException $ex)
    {
      die("Failed to execute query: " . $ex->getMessage() . "query: " . $query);
    }

    $cake_row = $stmt->fetch();

    // Insert the new order into the orders table
    $query = "
      INSERT INTO orders(
        customer_id,
        order_number,
        celebration_date,
        comments,
        decoration,
        filling,
        cake_id,
        agreed_price,
        order_date,
        delivery_type,
        status,
        datetime
      ) VALUES (
        :customer_id,
        :order_number,
        :celebration_date,
        :comments,
        :decoration,
        :filling,
        :cake_id,
        :agreed_price,
        :order_date,
        :delivery_type,
        :status,
        :datetime
      )
    ";
    
    // If new customer use the ID from the DB,
    // Else use the one from the form
    if ($_POST['existing_id'] === "null")
    {
      $customer_id = $row['customer_id'];
    }
    else
    {
      $customer_id = $_POST['existing_id'];
    }
    $order_number   = "m" . rand(10000,99999);
    $order_date     = date('Y-m-d');
    $status         = "Processing";
    $cake_id        = $cake_row['cake_id'];

    $query_params = array(
      ':customer_id'      => $customer_id,
      ':order_number'     => $order_number,
      ':celebration_date' => $_POST['celebration_date'],
      ':comments'         => $_POST['comments'],
      ':decoration'       => $_POST['decoration'],
      ':filling'          => $_POST['filling'],
      ':cake_id'          => $cake_id,
      ':agreed_price'     => $_POST['total-hidden'],
      ':order_date'       => $order_date,
      ':delivery_type'    => $_POST['delivery'],
      ':status'           => $status,
      ':datetime'         => $_POST['datetime']
    );

    try
    {
      $stmt     = $db->prepare($query);
      $result   = $stmt->execute($query_params);
    }
    catch(PDOException $ex)
    {
      die("Failed to execute query: " . $ex->getMessage() . "Query: " . $query);
    }

    // If the order is for delivery
    if ($_POST['delivery'] === "Deliver To Address")
    {
      // Calculate the delivery charge
      include "../lib/distance.php";
      $miles = calculateDistance($userrow['address'], $userrow['postcode']);
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

      // Insert the delivery details into the "delivery" DB table
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

    header("Location: ../all-orders/?new-order=added");
    die();
  }
  else
  {
    // Get a list of all customers
    $query = "
      SELECT
        *
      FROM
        users
      ORDER BY
        customer_id ASC
    ";

    try
    {
      $stmt     = $db->prepare($query);
      $result   = $stmt->execute();
    }
    catch(PDOException $ex)
    {
      die("Failed to execute query: " . $ex->getMessage() . "Query: " . $query);
    }

    $existing_rows = $stmt->fetchAll();
  }
?>
<?php include("../lib/header.php"); ?>
    <div class="form">
      <h1>Add Order</h1>
      <h3>Customer Details</h3>
      <form action="index.php" method="POST" id="add-order-form">
        <div>
          <label for="existing_id">Select existing customer</label>
          <select onchange="checkExisting()" id="existing_id" name="existing_id">
            <option value="null"></option>
            <?php foreach ($existing_rows as $row) : ?>
              <option value="<?php echo $row['customer_id']; ?>"><?php echo $row['customer_id'] . " - " . $row['first_name'] . " " . $row['last_name']; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="or">OR</div>
        <div>
          <label for="first_name">First Name</label>
          <input type="text" name="first_name" id="first_name" onkeyup="validateInput('#first_name', '#first_name_error')" onchange="validateInput('#first_name', '#first_name_error')">
        </div>
        <div id="first_name_error" class="validate-error"></div>
        <div>
          <label for="last_name">Last Name</label>
          <input type="text" name="last_name" id="last_name" onkeyup="validateInput('#last_name', '#last_name_error')" onchange="validateInput('#last_name', '#last_name_error')">
        </div>
        <div id="last_name_error" class="validate-error"></div>
        <div>
          <label for="address">Address</label>
          <input type="text" name="address" id="address" onkeyup="validateInput('#address', '#address_error')" onchange="validateInput('#address', '#address_error')">
        </div>
        <div id="address_error" class="validate-error"></div>
        <div>
          <label for="postcode">Postcode</label>
          <input type="text" name="postcode" id="postcode" onkeyup="validatePostcode()" onchange="validatePostcode()">
        </div>
        <div id="postcode_error" class="validate-error"></div>
        <div>
          <label for="phone">Phone Number</label>
          <input type="text" name="phone" id="phone" onkeyup="validatePhone()" onchange="validatePhone()">
        </div>
        <div id="phone_error" class="validate-error"></div>
        <div>
          <label for="email">Email</label>
          <input type="text" name="email" id="email" onkeyup="validateEmail()" onchange="validateEmail()">
        </div>
        <div id="email-error" class="validate-error"></div>
      <h3>Order Details</h3>
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
          <label for="cake_size">Size of Cake</label>
          <select name="cake_size" id="cake_size">
            <option value='6"'>6"</option>
            <option value='8"'>8"</option>
            <option value='10"'>10"</option>
            <option value='12"'>12"</option>
            <option value='14"'>14"</option>
          </select>
        </div>
        <div>
          <label for="cake_type">Type of Cake</label>
          <select name="cake_type" id="cake_type">
            <option value="Sponge">Sponge</option>
            <option value="Marble">Marble</option>
            <option value="Chocolate">Chocolate</option>
            <option value="Fruit">Fruit</option>
          </select>
        </div>
        <div>
          <label for="order_date">Date order was placed</label>
          <input type="text" name="order_date" id="order_date" class="date" onchange="validateInput('#order_date', '#order_date_error')">
        </div>
        <div id="order_date_error" class="validate-error"></div>
        <div>
          <label for="celebration_date">Date of celebration</label>
          <input type="text" name="celebration_date" id="celebration_date" class="date" onchange="validateInput('#celebration_date', '#celebration_date_error')">
        </div>
        <div id="celebration_date_error" class="validate-error"></div>
        <div id="comments">
          <label for="comments">Comments</label>
          <textarea id="comments" cols="30" rows="6" name="comments" onkeyup="validateInput('textarea#comments', '#comments_error')" onchange="validateInput('textarea#comments', '#comments_error')"></textarea>
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
          <input type="text" name="datetime" id="datetime" class="datetime" onchange="validateInput('#datetime', '#datetime_error')">
        </div>
        <div id="datetime_error" class="validate-error"></div>
        <b>Total: &pound;<div id="total-html"></div></b>
        <input type="hidden" id="total-hidden" name="total-hidden" value="">
        <br />
        <br />
        <input type="submit" value="Add Order">
      </form>
    </div>
<?php include("../lib/footer.php"); ?>
