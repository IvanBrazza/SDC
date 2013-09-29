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
          <label for="order_date">Date order was placed</label>
          <input type="text" name="order_date" id="order_date" class="date" onchange="validateInput('#order_date', '#order_date_error')">
        </div>
        <div id="order_date_error" class="validate-error"></div>
        <div>
          <label for="datetime">Date and time to collect/deliver order</label>
          <input type="text" name="datetime" id="datetime" class="datetime" onchange="validateInput('#datetime', '#datetime_error')">
        </div>
        <div id="datetime_error" class="validate-error"></div>
        <div>
          <label for="celebration_date">Date of celebration</label>
          <input type="text" name="celebration_date" id="celebration_date" class="date" onchange="validateInput('#celebration_date', '#celebration_date_error')">
        </div>
        <div id="celebration_date_error" class="validate-error"></div>
        <div id="order">
          <label for="order">Order</label>
          <textarea id="order" cols="30" rows="6" name="order" onkeyup="validateInput('textarea#order', '#order_error')" onchange="validateInput('textarea#order', '#order_error')"></textarea>
        </div>
        <div id="order_error" class="validate-error"></div>
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
          <label for="filling">Filling</label>
          <select name="filling" id="filling">
            <option value="None">None</option>
            <option value="Butter Cream">Butter Cream</option>
            <option value="Chocolate">Chocolate</option>
          </select>
        </div>
        <div>
          <label for="delivery">Delivery options</label>
          <select name="delivery" id="delivery">
            <option value="Collection">The cake will be collected</option>
            <option value="Deliver To Address">The cake will be delivered</option>
          </select>
        </div>
        <div>
          <label for="agreed_price">Agreed Price</label>
          <input type="text" name="agreed_price" id="agreed_price" onkeyup="validatePrice('#agreed_price', '#agreed_price_error')" onchange="validatePrice('#agreed_price', '#agreed_price_error')"><span class="pound">&pound;</span>
        </div>
        <div id="agreed_price_error" class="validate-error"></div>
        <div>
          <label for="delivery_charge">Delivery Charge</label>
          <input type="text" name="delivery_charge" id="delivery_charge" onkeyup="validatePrice('#delivery_charge', '#delivery_charge_error')" onchange="validatePrice('#delivery_charge', '#delivery_charge_error')"><span class="pound">&pound;</span>
        </div>
        <div id="delivery_charge_error" class="validate-error"></div>
        <div>
          <label for="grand_total">Grand Total</label>
          <div id="grand_total" name="grand_total"></div>
        </div>
        <input type="submit" value="Add Order">
      </form>
    </div>
  </div>
<?php include("../lib/footer.php"); ?>
