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
          <label for="order_placed">Date/time order was placed</label>
          <input type="text" name="order_placed" id="order_placed" class="previous-date" onchange="validateInput('#order_placed', '#order_placed_error')">
        </div>
        <div id="order_placed_error" class="validate-error"></div>
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
        <input type="hidden" id="base-hidden" name="total-hidden" value="">
        <br />
        <br />
        <input type="submit" value="Add Order">
        <span class="ajax-load"></span>
      </form>
    </div>
<?php include("../lib/footer.php"); ?>
