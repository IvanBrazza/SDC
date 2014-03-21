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
<div class="row">
  <div class="col-md-12">
    <h1>Place An Order</h1>
  </div>
</div>
<div class="row">
  <div class="col-md-2"></div>
  <div class="col-md-8">
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
    <form action="../lib/form/place-an-order.php" method="POST" class="form-horizontal" id="order-form" enctype="multipart/form-data" role="form">
      <ul class="nav nav-tabs">
        <li class="active"><a href="#thecake" data-toggle="tab">The Cake</a></li>
        <li><a href="#uploadaphoto" data-toggle="tab">Upload A Photo</a></li>
        <li><a href="#delivery" data-toggle="tab">Delivery</a></li>
        <li><a href="#review" data-toggle="tab">Review</a></li>
      </ul>
      <div class="tab-content">
        <div class="tab-pane fade in active" id="thecake">
          <div class="form-group">
            <label for="celebration_date" class="col-sm-4 control-label">Date of celebration <a href="javascript:" class="help" title="The date of the event you are ordering a cake for.">?</a></label>
            <div class="col-sm-1"></div>
            <div class="col-sm-7">
              <input type="text" class="form-control" name="celebration_date" class="date" id="celebration_date" onchange="validate.input('#celebration_date', '#celebration_date_error')">
              <div id="celebration_date_error" class="validate-error"></div>
            </div>
          </div>
          <div class="form-group">
            <label for="filling" class="col-sm-4 control-label">Filling <a href="javascript:" class="help" title="The filling you want your cake to have. If you choose 'Other' please specify the filling in the comments box.">?</a></label>
            <div class="col-sm-1"></div>
            <div class="col-sm-7">
              <select name="filling" id="filling" class="form-control">
                <option value="0">None</option>
                <option value="1">Butter Cream</option>
                <option value="2">Chocolate</option>
                <option value="3">Other (specify in comments)</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="decoration" class="col-sm-4 control-label">Decoration <a href="javascript:" class="help" title="What you want your cake to be decorated in. If you choose 'Other' please specify the decoration in the comments box.">?</a></label>
            <div class="col-sm-1"></div>
            <div class="col-sm-7">
              <select name="decoration" id="decoration" class="form-control">
                <option value="0">None</option>
                <option value="1">Royal Icing</option>
                <option value="2">Regal Icing</option>
                <option value="3">Butter Cream</option>
                <option value="4">Chocolate</option>
                <option value="5">Coconut</option>
                <option value="6">Other (specify in comments)</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="cake_size" class="col-sm-4 control-label">Size of cake <a href="javascript:" class="help" title="The size you want the cake to be in inches.">?</a></label>
            <div class="col-sm-1"></div>
            <div class="col-sm-7">
              <select name="cake_size" id="cake_size" class="form-control">
                <option value='6"'>6"</option>
                <option value='8"'>8"</option>
                <option value='10"'>10"</option>
                <option value='12"'>12"</option>
                <option value='14"'>14"</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="cake_type" class="col-sm-4 control-label">Type of cake</label>
            <div class="col-sm-1"></div>
            <div class="col-sm-7">
              <select name="cake_type" id="cake_type" class="form-control">
                <option value="Sponge">Sponge</option>
                <option value="Marble">Marble</option>
                <option value="Chocolate">Chocolate</option>
                <option value="Fruit">Fruit</option>
              </select>
            </div>
          </div>
          <div id="comments" class="form-group">
            <label for="comments" class="col-sm-4 control-label">Comments <a href="javascript:" class="help" title="Any additional comments you may have to make or if you chose filling/decoration as 'Other'.">?</a></label>
            <div class="col-sm-1"></div>
            <div class="col-sm-7">
              <textarea name="comments" id="comments" rows="6" cols="30" class="form-control" onchange="validate.input('textarea#comments', '#comments_error')"></textarea>
              <div id="comments_error" class="validate-error"></div>
            </div>
          </div>
        </div>
        <div class="tab-pane fade in" id="uploadaphoto">
          <iframe src="fileupload.html" id="ifileupload"></iframe>
          <input type="hidden" name="fileupload" id="fileuploadhidden">
        </div>
        <div class="tab-pane fade in" id="delivery">
          <div class="form-group">
            <label for="delivery" class="col-sm-4 control-label">Delivery options</label>
            <div class="col-sm-1"></div>
            <div class="col-sm-7">
              <select name="delivery" id="delivery" class="form-control">
                <option value="Collection">Collection</option>
                <option value="Deliver To Address">Delivery</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="datetime" id="datetime-label" class="col-sm-4 control-label">Date/time for collection</label>
            <div class="col-sm-1"></div>
            <div class="col-sm-7">
              <input type="text" id="datetime" class="form-control" name="datetime" onchange="validate.input('#datetime', '#datetime_error')">
            </div>
          </div>
          <div id="datetime_error" class="validate-error"></div>
        </div>
        <div class="tab-pane fade in" id="review">
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
        </div>
      </div>
    </form>
  </div>
  <div class="col-md-2"></div>
</div>
<?php include("../lib/footer.php");
