<?php
  /** 
    add-order/ - allow the admin to add an order into
    the system manually
  **/
  require("../lib/common.php");
  $title = "Add Order";
  $page = "place-an-order";

  if ($_SESSION['user']['username'] !== "admin")
  {
    die("Forbidden");
  }

  if ($_POST)
  {
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

    // Insert the new order into the orders table
    $query = "
      INSERT INTO orders(
        customer_id,
        order_number,
        order_date,
        datetime,
        celebration_date,
        customer_order,
        decoration,
        size,
        status,
        design,
        filling,
        delivery,
        agreed_price,
        delivery_charge
      ) VALUES (
        :customer_id,
        :order_number,
        :order_date,
        :datetime,
        :celebration_date,
        :customer_order,
        :decoration,
        :size,
        :status,
        :design,
        :filling,
        :delivery,
        :agreed_price,
        :delivery_charge
      )
    ";
    
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

    $query_params = array(
      ':customer_id'      => $customer_id,
      ':order_number'     => $order_number,
      ':order_date'       => $order_date,
      ':datetime'         => $_POST['datetime'],
      ':celebration_date' => $_POST['celebration_date'],
      ':customer_order'   => $_POST['order'],
      ':decoration'       => $_POST['decoration'],
      ':size'             => $_POST['size'],
      ':status'           => $status,
      ':design'           => $_POST['design'],
      ':filling'          => $_POST['filling'],
      ':delivery'         => $_POST['delivery'],
      ':agreed_price'     => $_POST['agreed_price'],
      ':delivery_charge'  => $_POST['delivery_charge']
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
  <div class="container">
    <div class="form">
      <h1>Add Order</h1>
      <h3>Customer Details</h3>
      <form action="index.php" method="POST" data-validate="parsley">
        <div>
          <label for="existing_id">Select existing customer</label>
          <div class="parsley-container">
            <select onchange="checkExisting()" id="existing_id" name="existing_id">
              <option value="null"></option>
              <?php foreach ($existing_rows as $row) : ?>
                <option value="<?php echo $row['customer_id']; ?>"><?php echo $row['customer_id'] . " - " . $row['first_name'] . " " . $row['last_name']; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="or">OR</div>
        <div>
          <label for="first_name">First Name</label>
          <div class="parsley-container">
            <input type="text" name="first_name" id="first_name" data-trigger="change keyup" data-error-message="Please enter a first name">
          </div>
        </div>
        <div>
          <label for="last_name">Last Name</label>
          <div class="parsley-container">
            <input type="text" name="last_name" id="last_name" data-trigger="change keyup" data-error-message="Please enter a last name">
          </div>
        </div>
        <div>
          <label for="address">Address</label>
          <div class="parsley-container">
            <input type="text" name="address" id="address" data-trigger="change keyup" data-error-message="Please enter an address">
          </div>
        </div>
        <div>
          <label for="postcode">Postcode</label>
          <div class="parsley-container">
            <input type="text" name="postcode" id="postcode" data-trigger="change keyup" data-error-message="Please enter a postcode">
          </div>
        </div>
        <div>
          <label for="phone">Phone Number</label>
          <div class="parsley-container">
            <input type="text" name="phone" id="phone" data-trigger="change keyup" data-error-message="Please enter a phone number" data-type="number">
          </div>
        </div>
        <div>
          <label for="email">Email</label>
          <div class="parsley-container">
            <input type="text" name="email" id="email" data-type="email" data-trigger="change keyup" data-error-message="Please enter a valid email">
          </div>
        </div>
      <h3>Order Details</h3>
        <div>
          <label for="order_date">Date order was placed</label>
          <div class="parsley-container">
            <input type="text" name="order_date" id="order_date" data-required="true" data-trigger="change keyup" data-error-message="Please enter a date" data-type="dateIso" class="date">
          </div>
        </div>
        <div>
          <label for="datetime">Date and time to collect/deliver order</label>
          <div class="parsley-container">
            <input type="text" name="datetime" id="datetime" data-required="true" data-trigger="change keyup" data-error-message="Please enter a date and time" class="datetime">
          </div>
        </div>
        <div>
          <label for="celebration_date">Date of celebration</label>
          <div class="parsley-container">
            <input type="text" name="celebration_date" id="celebration_date" data-required="true" data-trigger="change keyup" data-error-message="Please enter a date" class="date" data-type="dateIso">
          </div>
        </div>
        <div id="order">
          <label for="order">Order</label>
          <div class="parsley-container">
            <textarea id="order" data-error-message="Please enter your order" data-required="true" data-trigger="change keyup" cols="30" rows="6" name="order"></textarea>
          </div>
        </div>
        <div>
          <label for="decoration">Cake to be decorated in</label>
          <div class="parsley-container">
            <select name="decoration" id="decoration">
              <option value="None">None</option>
              <option value="Royal Icing">Royal Icing</option>
              <option value="Regal Icing">Regal Icing</option>
              <option value="Butter Cream">Butter Cream</option>
              <option value="Chocolate">Chocolate</option>
              <option value="Coconut">Coconut</option>
            </select>
          </div>
        </div>
        <div>
          <label for="size">Size of cake</label>
          <div class="parsley-container">
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
        </div>
        <div>
          <label for="design">Design</label>
          <input type="text" name="design" id="design" data-required="true" data-error-message="Please enter a design" data-trigger="change keyup">
        </div>
        <div>
          <label for="filling">Filling</label>
          <div class="parsley-container">
            <select name="filling" id="filling">
              <option value="None">None</option>
              <option value="Butter Cream">Butter Cream</option>
              <option value="Chocolate">Chocolate</option>
            </select>
          </div>
        </div>
        <div>
          <label for="delivery">Delivery options</label>
          <div class="parsley-container">
            <select name="delivery" id="delivery">
              <option value="Collection">The cake will be collected</option>
              <option value="Deliver To Address">The cake will be delivered</option>
            </select>
          </div>
        </div>
        <div>
          <label for="agreed_price">Agreed Price</label>
          <div class="parsley-container">
            <input type="text" name="agreed_price" id="agreed_price" data-required="true" data-error-message="Please enter the agreed price" data-trigger="change keyup" data-type="number" data-minlength="1">
          </div>
        </div>
        <div>
          <label for="delivery_charge">Delivery Charge</label>
          <div class="parsley-container">
            <input type="text" name="delivery_charge" id="delivery_charge" data-required="true" data-error-message="Please enter the delivery charge" data-trigger="change keyup" data-type="number" data-minlength="1">
          </div>
        </div>
        <div>
          <label for="grand_total">Grand Total</label>
          <div class="parsley-container"id="grand_total" name="grand_total">
            
          </div>
        </div>
        <input type="submit" value="Add Order">
      </form>
    </div>
  </div>
<?php include("../lib/footer.php"); ?>
