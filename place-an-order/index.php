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
    header("Location: ../login/?e=pao&redirect=" . $_SERVER["REQUEST_URI"]);
    die();
  }

  // Use HTTPS since secure data is being transferred
  forceHTTPS();

  // Generate a token
  $_SESSION['token'] = rtrim(base64_encode(md5(microtime())),"=");

  // Get user details to make sure they have been entered
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

  $db->runQuery($query, $query_params);

  $row = $db->fetch();
  
  // Don't let the users place an order until their details are entered
  if (empty($row['address']) or empty($row['postcode']) or empty($row['phone']) or empty($row['first_name']) or empty($row['last_name']))
  {
    $display_message = 'Please <a href="../edit-account">update your details</a> before placing an order.';
    $details_correct = false;
  }
  else
  {
    $details_correct = true;
  }

  if (!empty($_GET))
  {
    switch ($_GET['e'])
    {
      case "1":
        $display_message = "Image must be smaller than 5 megabytes.";
        break;
      case "2":
        $display_message = "File must be .jpg, .jpeg, .png or .gif.";
        break;
      case "3":
        $display_message = "Oops! Something went wrong. Try again.";
        break;
    }
  }
?>
<?php include("../lib/header.php"); ?>
  <h1>Place An Order</h1>
  <div class="error">
    <span class="error_message" id="error_message">
      <?php if (!$details_correct) : ?>
        <?php echo $display_message; ?>
        <script>
          var $details_correct = false;
        </script>
      <?php elseif (!empty($_GET)) : ?>
        <?php echo $display_message; ?>
        <script>
          var $details_correct = true;
        </script>
      <?php else : ?>
        <script>
          var $details_correct = true;
        </script>
      <?php endif; ?>
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
      <a href="javascript:" id="order-form-previous" class="order-form-navigation">Back</a>
      <a href="javascript:" id="order-form-next" class="order-form-navigation">Next</a>
      <div class='slide_content'>
        <div class='tabslider'>
          <ul>
            <li><!-- The Cake -->
              <div>
                <label for="celebration_date">Date of celebration <a href="javascript:" class="help" title="The date of the event you are ordering a cake for.">?</a></label>
                <input type="text" name="celebration_date" class="date" id="celebration_date" onchange="validate.input('#celebration_date', '#celebration_date_error')">
              </div>
              <div id="celebration_date_error" class="validate-error"></div>
              <div>
                <label for="filling">Filling <a href="javascript:" class="help" title="The filling you want your cake to have. If you choose 'Other' please specify the filling in the comments box.">?</a></label>
                <select name="filling" id="filling">
                  <option value="0">None</option>
                  <option value="1">Butter Cream</option>
                  <option value="2">Chocolate</option>
                  <option value="3">Other (specify in comments)</option>
                </select>
              </div>
              <div>
                <label for="decoration">Decoration <a href="javascript:" class="help" title="What you want your cake to be decorated in. If you choose 'Other' please specify the decoration in the comments box.">?</a></label>
                <select name="decoration" id="decoration">
                  <option value="0">None</option>
                  <option value="1">Royal Icing</option>
                  <option value="2">Regal Icing</option>
                  <option value="3">Butter Cream</option>
                  <option value="4">Chocolate</option>
                  <option value="5">Coconut</option>
                  <option value="6">Other (specify in comments)</option>
                </select>
              </div>
                <div>
                <label for="cake_size">Size of cake <a href="javascript:" class="help" title="The size you want the cake to be in inches.">?</a></label>
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
                <label for="comments">Comments <a href="javascript:" class="help" title="Any additional comments you may have to make or if you chose filling/decoration as 'Other'.">?</a></label>
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
                upload .jpg, .jpeg, .png or .gif files, and the image must be less than
                5 megabytes in size.
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
              <b>Base Price: &pound;<div id="base-price"></div></b>
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
