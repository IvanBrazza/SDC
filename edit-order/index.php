<?php
  /** 
    edit-order - a page where the user can edit
    their order.
  **/
  require("../lib/common.php");
  $page = "your-orders";
  $title = "Edit Order";

  // Only logged in users can access this page
  if (empty($_SESSION['user'])) 
  {
    header("Location: ../login");
    die();
  }
  
  // If the order is being updated (POST) else if we're getting
  // order details (GET)
  if (!empty($_POST))
  {
    if ($_POST['token'] != $_SESSION['token'] or empty($_POST['token']))
    {
      echo "Invalid token.";
      die();
    }

    // Calculate old price
    $query = "
      SELECT
        a.base_price, a.delivery_type, a.difference,
        b.filling_price,
        c.decor_price
      FROM
        orders a, fillings b, decorations c
      WHERE
        a.order_number = :order_number
      AND
        b.filling_id = a.filling_id
      AND
        c.decor_id = a.decor_id
    ";

    $query_params = array(
      ':order_number' => $_POST['order_number']
    );

    $db->runQuery($query, $query_params);
    $row = $db->fetch();

    if ($row['delivery_type'] == "Deliver To Address")
    {
      $query = "
        SELECT
          delivery_charge
        FROM
          delivery
        WHERE
          order_number = :order_number
      ";

      $query_params = array(
        ':order_number' => $_POST['order_number']
      );

      $db->runQuery($query, $query_params);
      $deliveryrow            = $db->fetch();
      $row['delivery_charge'] = $deliveryrow['delivery_charge'];
    }
    else
    {
      $row['delivery_charge'] = 0;
    }

    $old_price      = $row['base_price'] + $row['filling_price'] + $row['decor_price'] + $row['delivery_charge'];
    $old_difference = $row['difference'];

    // If the delivery type is deliver rather than collection,
    // check if there is already a row in the delivery table
    // and if there is, update it, if not then add one
    if ($_POST['delivery'] === "Deliver To Address")
    {
      $query = "
        SELECT
          *
        FROM
          delivery
        WHERE
          order_number = :order_number
      ";

      $query_params = array(
        ':order_number' => $_POST['order_number']
      );
      
      $db->runQuery($query, $query_params);

      $row = $db->fetch();

      include "../lib/delivery.class.php";
      $delivery = new Delivery;
      $delivery->setAddress($_SESSION['user']['address']);
      $delivery->setPostcode($_SESSION['user']['postcode']);
      $delivery->calculateDistance();
      $delivery->calculateDeliveryCharge();
      $delivery_charge = $delivery->getDeliveryCharge();

      if ($row)
      {
        $query = "
          UPDATE
            delivery
          SET
            miles           = :miles,
            delivery_charge = :delivery_charge
          WHERE
            order_number = :order_number
        ";

        $query_params = array(
          ':miles'            => $miles,
          ':delivery_charge'  => $delivery_charge,
          ':order_number'     => $_POST['order_number']
        );
      }
      else
      {
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
  
        $query_params = array(
          ':order_number'     => $_POST['order_number'],
          ':miles'            => $miles,
          ':delivery_charge'  => $delivery_charge
        );
      }
      
      $db->runQuery($query, $query_params);
    }
    else
    {
      $delivery_charge = 0;
    }

    // Calculate base price and get cake id
    $query = "
      SELECT
        a.cake_id, a.cake_price,
        b.filling_price,
        c.decor_price
      FROM
        cakes a, fillings b, decorations c
      WHERE
        a.cake_type = :cake_type
      AND
        a.cake_size = :cake_size
      AND
        b.filling_id = :filling_id
      AND
        c.decor_id = :decor_id
    ";

    $query_params = array(
      ':cake_type'  => $_POST['cake_type'],
      ':cake_size'  => $_POST['cake_size'],
      ':filling_id' => $_POST['filling'],
      ':decor_id'   => $_POST['decoration']
    );

    $db->runQuery($query, $query_params);

    $row           = $db->fetch();
    $cake_id       = $row['cake_id'];
    $base_price    = $row['cake_price'];
    $filling_price = $row['filling_price'];
    $decor_price   = $row['decor_price'];

    // String together the datetime
    $datetime = $_POST['datetime_date'] . ' ' . $_POST['datetime_time'];

    // Calculate new price and the difference
    $new_price      = $base_price + $filling_price + $decor_price + $delivery_charge;
    $new_difference = $old_price - $new_price;
    $difference     = $old_difference + $new_difference;

    $query = "
      UPDATE
        orders
      SET
        datetime          = :datetime,
        celebration_date  = :celebration_date,
        comments          = :comments,
        filling_id        = :filling_id,
        decor_id          = :decor_id,
        cake_id           = :cake_id,
        delivery_type     = :delivery_type,
        base_price        = :base_price,
        difference        = :difference
      WHERE
        order_number = :order_number
    ";

    $query_params = array(
      ':datetime'         => $datetime,
      ':celebration_date' => $_POST['celebration_date'],
      ':comments'         => $_POST['comments'],
      ':filling_id'       => $_POST['filling'],
      ':decor_id'         => $_POST['decoration'],
      ':cake_id'          => $cake_id,
      ':delivery_type'    => $_POST['delivery'],
      ':order_number'     => $_POST['order_number'],
      ':base_price'       => $base_price,
      ':difference'       => $difference
    );

    $db->runQuery($query, $query_params);

    // Return back to order details after the update
    header("Location: ../your-orders/?order=" . $_POST['order_number'] . "&edit=success");
    die();
  }
  else if (!empty($_GET))
  {
    // Get all the order details
    $query = "
      SELECT
        a.*, b.*
      FROM
        orders a, cakes b
      WHERE
        order_number = :order_number
      AND
        b.cake_id = a.cake_id
    ";
  
    $query_params = array(
      ':order_number' => $_GET['order']
    );
  
    $db->runQuery($query, $query_params);
  
    $row = $db->fetch();
    
    // If the order is not from the logged in customer,
    // or the order is completed, die.
    if ($row['customer_id'] != $_SESSION['user']['customer_id'] or $row['completed'] === "1")
    {
      header("Location: ../home");
      die();
    }

    $datetime_date = substr($row['datetime'], 0, 10);
    $datetime_time = substr($row['datetime'], 11);

    // Get all fillings to be displayed
    $query = "
      SELECT
        *
      FROM
        fillings
    ";

    $db->runQuery($query, null);
    $fillings = $db->fetchAll();

    // Get all decorations to be displayed
    $query = "
      SELECT
        *
      FROM
        decorations
    ";

    $db->runQuery($query, null);
    $decorations = $db->fetchAll();
  }
  else
  {
    header("Location: ../home");
    die();
  }

  // Generate token
  $_SESSION['token'] = rtrim(base64_encode(md5(microtime())),"=");
?>

<?php include("../lib/header.php"); ?>
<div class="row">
  <div class="col-md-12">
    <h1>Editing Order <?php echo $row['order_number']; ?></h1>
  </div>
</div>
<div class="row">
  <div class="col-md-12">
    <form action="index.php" method="POST" class="form-horizontal" id="edit-order-form" role="form">
      <div class="panel-group" id="accordion">
        <div class="panel panel-default">
          <div class="panel-heading" id="the-cake-heading" style="background-color:#d9edf7;">
            <h4 class="panel-title">
              The Cake
            </h4>
          </div>
          <div id="theCake" class="panel-collapse collapse in">
            <div class="panel-body">
              <div class="col-md-2"></div>
              <div class="col-md-8">
                <div class="form-group" id="date">
                  <label for="celebration_date" class="col-sm-4 control-label">Date of celebration <a href="javascript:" class="help" title="The date of the event you are ordering a cake for.">?</a></label>
                  <div class="col-sm-1"></div>
                  <div class="col-sm-7">
                    <input name="celebration_date" class="form-control datepicker" placeholder="Date" data-value="<?php echo $row['celebration_date']; ?>">
                    <div id="celebration_date_error" class="validate-error"></div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="filling" class="col-sm-4 control-label">Filling <a href="javascript:" class="help" title="The filling you want your cake to have. If you choose 'Other' please specify the filling in the comments box.">?</a></label>
                  <div class="col-sm-1"></div>
                  <div class="col-sm-7">
                    <select name="filling" id="filling" class="form-control">
                      <?php foreach ($fillings as $filling) : ?>
                        <option value="<?php echo $filling['filling_id']; ?>" <?php if ($row['filling_id'] == $filling['filling_id']) {echo "selected";} ?>><?php echo $filling['filling_name']; ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <label for="decoration" class="col-sm-4 control-label">Decoration <a href="javascript:" class="help" title="What you want your cake to be decorated in. If you choose 'Other' please specify the decoration in the comments box.">?</a></label>
                  <div class="col-sm-1"></div>
                  <div class="col-sm-7">
                    <select name="decoration" id="decoration" class="form-control">
                      <?php foreach ($decorations as $decoration) : ?>
                        <option value="<?php echo $decoration['decor_id']; ?>" <?php if ($row['decor_id'] == $decoration['decor_id']) {echo "selected";} ?>><?php echo $decoration['decor_name']; ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <label for="cake_size" class="col-sm-4 control-label">Size of cake <a href="javascript:" class="help" title="The size you want the cake to be in inches.">?</a></label>
                  <div class="col-sm-1"></div>
                  <div class="col-sm-7">
                    <select name="cake_size" id="cake_size" class="form-control">
                      <option value='6"' <?php if ($row['cake_size'] == '6"') {echo "selected";} ?>>6"</option>
                      <option value='8"' <?php if ($row['cake_size'] == '8"') {echo "selected";} ?>>8"</option>
                      <option value='10"' <?php if ($row['cake_size'] == '10"') {echo "selected";} ?>>10"</option>
                      <option value='12"' <?php if ($row['cake_size'] == '12"') {echo "selected";} ?>>12"</option>
                      <option value='14"' <?php if ($row['cake_size'] == '14"') {echo "selected";} ?>>14"</option>
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <label for="cake_type" class="col-sm-4 control-label">Type of cake</label>
                  <div class="col-sm-1"></div>
                  <div class="col-sm-7">
                    <select name="cake_type" id="cake_type" class="form-control">
                      <option value="Sponge" <?php if ($row['cake_type'] == 'Sponge') {echo "selected";} ?>>Sponge</option>
                      <option value="Marble" <?php if ($row['cake_type'] == 'Marble') {echo "selected";} ?>>Marble</option>
                      <option value="Chocolate" <?php if ($row['cake_type'] == 'Chocolate') {echo "selected";} ?>>Chocolate</option>
                      <option value="Fruit" <?php if ($row['cake_type'] == 'Fruit') {echo "selected";} ?>>Fruit</option>
                    </select>
                  </div>
                </div>
                <div id="comments" class="form-group">
                  <label for="comments" class="col-sm-4 control-label">Comments <a href="javascript:" class="help" title="Any additional comments you may have to make or if you chose filling/decoration as 'Other'.">?</a></label>
                  <div class="col-sm-1"></div>
                  <div class="col-sm-7">
                    <textarea name="comments" id="comments" rows="6" cols="30" class="form-control" onchange="validate.input('textarea#comments', '#comments_error', 'Please enter a comment')"><?php echo $row['comments']; ?></textarea>
                    <div id="comments_error" class="validate-error"></div>
                  </div>
                </div>
              </div>
              <div class="col-md-2">
                <button type="button" id="theCakeNext" class="btn btn-primary pull-right">
                  Next   <i class="fa fa-arrow-down"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
        <div class="panel panel-default">
          <div class="panel-heading" id="delivery-heading" style="background-color:#fcf8e3;">
            <h4 class="panel-title">
              Delivery
            </h4>
          </div>
          <div id="deliveryPanel" class="panel-collapse collapse">
            <div class="panel-body">
              <div class="col-md-2">
                <button type="button" id="deliveryPrevious" class="btn btn-primary">
                  <i class="fa fa-arrow-up"></i>   Previous
                </button>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <label for="delivery" class="col-sm-4 control-label">Delivery options</label>
                  <div class="col-sm-1"></div>
                  <div class="col-sm-7">
                    <select name="delivery" id="delivery" class="form-control">
                      <option value="Collection" <?php if ($row['delivery_type'] == 'Collection') {echo "selected";} ?>>Collection</option>
                      <option value="Deliver To Address" <?php if ($row['delivery_type'] == 'Deliver To Address') {echo "selected";} ?>>Delivery</option>
                    </select>
                  </div>
                </div>
                <div class="form-group" id="datetime_date">
                  <label for="datetime" id="datetime-label" class="col-sm-4 control-label">Date/time for collection</label>
                  <div class="col-sm-1"></div>
                  <div class="col-sm-7">
                    <input name="datetime_date" class="form-control datepicker" placeholder="Date" data-value="<?php echo $datetime_date; ?>">
                    <div id="datetime_date_error" class="validate-error"></div>
                    <input name="datetime_time" class="form-control timepicker" placeholder="Time" data-value="<?php echo $datetime_time; ?>">
                    <div id="datetime_time_error" class="validate-error"></div>
                  </div>
                </div>
              </div>
              <div class="col-md-2">
                <button type="button" id="deliveryNext" class="btn btn-primary pull-right">
                  Next   <i class="fa fa-arrow-down"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
        <div class="panel panel-default">
          <div class="panel-heading" id="review-heading" style="background-color:#fcf8e3;">
            <h4 class="panel-title">
              Review
            </h4>
          </div>
          <div id="review" class="panel-collapse collapse">
            <div class="panel-body">
              <div class="calculating">
                <img src="../img/spinner.gif">
                <p>Calculating Order Total...</p>
              </div>
              <div class="col-md-4">
                <script>
                  var $address = "<?php echo $_SESSION['user']['address']; ?>",
                      $postcode = "<?php echo $_SESSION['user']['postcode']; ?>";
                </script>
                <table class="table">
                <caption>Your Order</caption>
                <tr>
                  <th>Date of celebration:</th>
                  <td>
                    <span id="celebration-date-review"><?php echo $row['celebration_date']; ?></span>
                  </td>
                </tr>
                <tr>
                  <th>Filling:</th>
                  <td>
                    <span id="filling-review"><?php echo $row['filling_name']; ?></span>
                  </td>
                </tr>
                <tr>
                  <th>Decoration:</th>
                  <td>
                    <span id="decoration-review"><?php echo $row['decor_name']; ?></span>
                  </td>
                </tr>
                <tr>
                  <th>Size of cake:</th>
                  <td>
                    <span id="cake-size-review"><?php echo $row['cake_size']; ?></span>
                  </td>
                </tr>
                <tr>
                  <th>Type of cake:</th>
                  <td>
                    <span id="cake-type-review"><?php echo $row['cake_type']; ?></span>
                  </td>
                </tr>
                <tr>
                  <th>Comments:</th>
                  <td>
                    <span id="comments-review"><?php echo $row['comments']; ?></span>
                  </td>
                </tr>
                <tr>
                  <th>Delivery type:</th>
                  <td>
                    <span id="delivery-review"><?php echo $row['delivery_type']; ?></span>
                  </td>
                </tr>
                <tr>
                  <th>
                    <span id="datetime-label-review">Date/time for collection:</span>
                  </th>
                  <td>
                    <span id="datetime-review"><?php echo $row['datetime']; ?></span>
                  </td>
                </tr>
                </table>
              </div>
              <div class="col-md-4">
                <table class="table">
                  <caption>Summary</caption>
                  <tr>
                    <th>Base Price</th>
                    <td>
                      &pound;<span id="base-price"></span>
                    </td>
                  </tr>
                  <tr>
                    <th>Filling</th>
                    <td>
                      &pound;<span id="filling-html"></span>
                    </td>
                  </tr>
                  <tr>
                    <th>Decoration</th>
                    <td>
                      &pound;<span id="decoration-html"></span>
                    </td>
                  </tr>
                  <tr id="delivery-charge">
                    <th>Delivery</th>
                    <td>
                      <span id="delivery-charge-html"></span>
                    </td>
                  </tr>
                  <tr>
                    <th>Grand Total</th>
                    <td>
                      &pound;<span id="total-html"></span>
                    </td>
                  </tr>
                  <tr>
                    <th>Difference</th>
                    <td>
                      &pound;<span id="difference-html"></span>
                    </td>
                  </tr>
                </table>
                <input type="hidden" value="<?php echo $_SESSION['token']; ?>" name="token">
                <input type="hidden" value="<?php echo $_GET['order']; ?>" name="order_number">
              </div>
              <div class="col-md-4">
                <button type="button" id="reviewPrevious" class="btn btn-primary pull-left">
                  <i class="fa fa-arrow-up"></i>
                  <span>Go back</span>
                </button>
                <button type="submit" class="btn btn-success pull-right">
                  <i class="fa fa-pencil"></i>
                  <span>Edit Order</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
<script>
  var $token = "<?php echo $_SESSION['token']; ?>";
</script>
<?php include("../lib/footer.php"); ?>
