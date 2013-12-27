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
    <h1>Place An Order</h1>
    <div class="error">
      <span class="error_message" id="error_message">
      </span>
    </div>
    <form action="../lib/form/place-an-order.php" method="POST" id="order-form" enctype="multipart/form-data">
      <div class='tabbed_content'>
        <div class='tabs'>
          <div class='moving_bg'>
            &nbsp;
          </div>
          <span class='tab_item active'>The Cake</span>
          <span class='tab_item'>Upload A Photo</span>
          <span class='tab_item'>Delivery</span>
          <span class='tab_item'>Review</span>
        </div>
      <button id="order-form-previous" disabled>Back</button>
      <button id="order-form-next">Next</button>
        <div class='slide_content'>
          <div class='tabslider'>
            <ul>
              <li><!-- The Cake -->
                <div>
                  <label for="celebration_date">Date of celebration</label>
                  <input type="text" name="celebration_date" class="date" id="celebration_date" onchange="validate.input('#celebration_date', '#celebration_date_error')">
                </div>
                <div id="celebration_date_error" class="validate-error"></div>
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
                  <label for="decoration">Decoration</label>
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
                <div id="comments">
                  <label for="comments">Comments</label>
                  <textarea name="comments" id="comments" rows="6" cols="30" onchange="validate.input('textarea#comments', '#comments_error')"></textarea>
                </div>
                <div id="comments_error" class="validate-error"></div>
              </li>
            </ul>
            <ul>
              <li><!-- Upload A Photo -->
                <label for="fileupload" id="fileupload-label">
                  If you wish for your cake to have a picture printed onto edible paper, you
                  can upload it clicking the "Choose File" or "Browse" button below. Please
                  make sure that the picture is high quality and note that you can only
                  upload .jpg, .jpeg, .png or .gif files.
                </label>
                <br /><br /><br />
                <input type="hidden" name="MAX_FILE_SIZE" value="5242880">
                <input type="file" name="fileupload" id="fileupload" accept="image/*">
              </li>
            </ul>
            <ul>
              <li><!-- Delivery -->
                <div>
                  <label for="delivery">Delivery options</label>
                  <select name="delivery" id="delivery">
                    <option value="Collection">Collection</option>
                    <option value="Deliver To Address">Delivery</option>
                  </select>
                </div>
                <div>
                  <label for="datetime" id="datetime-label">Date/time for collection</label>
                  <input type="text" id="datetime" name="datetime" onchange="validate.input('#datetime', '#datetime_error')">
                </div>
                <div id="datetime_error" class="validate-error"></div>
              </li>
            </ul>
            <ul>
              <li><!-- Review -->
                <br />
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
                <input type="image" src="../img/paywithpp.gif" <?php if ($details_correct === false) : ?>disabled<?php endif; ?> />
              </li>
            </ul>
          </div>
        </div>
      </div>
    </form>
<?php include("../lib/footer.php");
