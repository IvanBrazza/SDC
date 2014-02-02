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

  // Generate token
  $_SESSION['token'] = rtrim(base64_encode(md5(microtime())),"=");

  // Get a list of all customers
  $query = "
    SELECT
      *
    FROM
      users
    ORDER BY
      customer_id ASC
  ";

  $db->runQuery($query, null);

  $existing_rows = $db->fetchAll();
?>
<?php include("../lib/header.php"); ?>
  <h1>Add Order</h1>
  <form action="index.php" method="POST" id="add-order-form">
    <div class="tabbed_content">
      <div class='tabs'>
        <div class='moving_bg'>
          &nbsp;
        </div>
        <span class='tab_item active'>The Customer</span>
        <span class='tab_item'>The Cake</span>
        <span class='tab_item'>Delivery</span>
        <span class='tab_item'>Review</span>
      </div>
      <a href="javascript:" id="order-form-previous" class="order-form-navigation">Back</a>
      <a href="javascript:" id="order-form-next" class="order-form-navigation">Next</a>
      <div class='slide_content'>
        <div class='tabslider'>
          <ul><!-- The Customer -->
            <li>
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
                <input type="text" name="first_name" id="first_name" onchange="validate.input('#first_name', '#first_name_error')">
              </div>
              <div id="first_name_error" class="validate-error"></div>
              <div>
                <label for="last_name">Last Name</label>
                <input type="text" name="last_name" id="last_name" onchange="validate.input('#last_name', '#last_name_error')">
              </div>
              <div id="last_name_error" class="validate-error"></div>
              <div>
                <label for="address">Address</label>
                <input type="text" name="address" id="address" onchange="validate.input('#address', '#address_error')">
              </div>
              <div id="address_error" class="validate-error"></div>
              <div>
                <label for="postcode">Postcode</label>
                <input type="text" name="postcode" id="postcode" onchange="validate.postcode()">
              </div>
              <div id="postcode_error" class="validate-error"></div>
              <div>
                <label for="phone">Phone Number</label>
                <input type="text" name="phone" id="phone" onchange="validate.phone()">
              </div>
              <div id="phone_error" class="validate-error"></div>
              <div>
                <label for="email">Email</label>
                <input type="text" name="email" id="email" onchange="validate.email()">
              </div>
              <div id="email-error" class="validate-error"></div>
            </li>
          </ul>
          <ul><!-- The Cake -->
            <li>
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
                <input type="text" name="order_placed" id="order_placed" class="previous-date" onchange="validate.input('#order_placed', '#order_placed_error')">
              </div>
              <div id="order_placed_error" class="validate-error"></div>
              <div>
                <label for="celebration_date">Date of celebration</label>
                <input type="text" name="celebration_date" id="celebration_date" class="date" onchange="validate.input('#celebration_date', '#celebration_date_error')">
              </div>
              <div id="celebration_date_error" class="validate-error"></div>
              <div id="comments">
                <label for="comments">Comments</label>
                <textarea id="comments" cols="30" rows="6" name="comments" onchange="validate.input('textarea#comments', '#comments_error')"></textarea>
              </div>
              <div id="comments_error" class="validate-error"></div>
            </li>
          </ul>
          <ul><!-- Delivery -->
            <li>
              <div>
                <label for="delivery">Delivery options</label>
                <select name="delivery" id="delivery">
                  <option value="Collection">Collection</option>
                  <option value="Deliver To Address">Delivery</option>
                </select>
              </div>
              <div>
                <label for="datetime" id="datetime-label">Date/time for collection</label>
                <input type="text" name="datetime" id="datetime" class="datetime" onchange="validate.input('#datetime', '#datetime_error')">
              </div>
              <div id="datetime_error" class="validate-error"></div>
            </li>
          </ul>
          <ul><!-- Review -->
            <li>
              <script>
                var $origins = <?php echo json_encode(str_replace(" ", "+", $_SESSION['user']['address']) . "," . str_replace(" ", "+", $_SESSION['user']['postcode'])); ?>,
                    $destination = "95+Hoe+Lane,EN35SW";
              </script>
              <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCKeZpb8doUO3DbEqT3t-uRJYsbEPbD3AE&sensor=false"></script>
              <table>
              <tr>
                <th>Date of celebration:</th>
                <td>
                  <span id="celebration-date-review"></span>
                </td>
              </tr>
              <tr>
                <th>Filling:</th>
                <td>
                  <span id="filling-review">None</span>
                </td>
              </tr>
              <tr>
                <th>Decoration:</th>
                <td>
                  <span id="decoration-review">None</span>
                </td>
              </tr>
              <tr>
                <th>Size of cake:</th>
                <td>
                  <span id="cake-size-review">6"</span>
                </td>
              </tr>
              <tr>
                <th>Type of cake:</th>
                <td>
                  <span id="cake-type-review">Sponge</span>
                </td>
              </tr>
              <tr>
                <th>Comments:</th>
                <td>
                  <span id="comments-review"></span>
                </td>
              </tr>
              <tr>
                <th>Photo upoaded:</th>
                <td>
                  <span id="fileupload-review">No</span>
                </td>
              </tr>
              <tr>
                <th>Delivery type:</th>
                <td>
                  <span id="delivery-review">Collection</span>
                </td>
              </tr>
              <tr>
                <th>
                  <span id="datetime-label-review">Date/time for collection:</span>
                </th>
                <td>
                  <span id="datetime-review"></span>
                </td>
              </tr>
              </table>
              <span id="delivery-charge"><b>Delivery: <div id="delivery-charge-html"></div></b></span>
              <br />
              <b>Grand Total: &pound;<div id="total-html"></div></b>
              <br /><br />
              <input type="hidden" value="<?php echo $_SESSION['token']; ?>" name="token">
              <input type="submit" value="Add Order">
            </li>
          </ul>
        </div>
      </div>
    </div>
  </form>
<?php include("../lib/footer.php"); ?>
