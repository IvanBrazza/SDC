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

  // Generate a token
  $_SESSION['token'] = rtrim(base64_encode(md5(microtime())),"=");

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
  
  // Don't let the users place an order until their details are entered
  if (empty($row['address']) or empty($row['postcode']) or empty($row['phone']) or empty($row['first_name']) or empty($row['last_name']))
  {
    $display_message = 'Please <a href="../edit-account">update your details</a> before placing an order.';
    $details_correct = false;
  }
?>
<?php include("../lib/header.php"); ?>
  <div class="form">
    <h1>Place An Order</h1>
    <div class="error">
      <span class="error_message" id="error_message">
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
      <script>
        var $origins = <?php echo json_encode(str_replace(" ", "+", $_SESSION['user']['address']) . "," . str_replace(" ", "+", $_SESSION['user']['postcode'])); ?>,
            $destination = "95+Hoe+Lane,EN35SW";
      </script>
      <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCKeZpb8doUO3DbEqT3t-uRJYsbEPbD3AE&sensor=false"></script>
      <span id="delivery-charge"><b>Delivery: <div id="delivery-charge-html"></div></b></span>
      <br />
      <b>Grand Total: &pound;<div id="total-html"></div></b>
      <input type="hidden" id="base-hidden" name="base-hidden" value="">
      <br /><br />
      <input type="hidden" value="<?php echo $_SESSION['token']; ?>" name="token">
      <input type="submit" value="Submit Order" <?php if ($details_correct === false) : ?>disabled<?php endif; ?> />
      <span class="ajax-load"></span>
    </form>
  </div>
<?php include("../lib/footer.php");
