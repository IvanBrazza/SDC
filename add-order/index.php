<?php
  /** 
    add-order/ - allow the admin to add an order into
    the system manually
  **/
  require("../lib/common.php");
  $title = "Add Order";
  $page = "all-orders";

  // Only the admin can access this page
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
<div class="row">
  <div class="col-md-12">
    <h1>Add Order</h1>
  </div>
</div>
<div class="row">
  <div class="col-md-12">
    <form action="../lib/form/add-order.php" method="POST" class="form-horizontal" role="form">
      <div class="panel-group" id="accordion">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h4 class="panel-title">
              The Customer
            </h4>
          </div>
          <div id="theCustomer" class="panel-collapse collapse in">
            <div class="panel-body">
              <div class="col-md-2"></div>
              <div class="col-md-8">
                <div class="form-group">
                  <label for="existing_id" class="col-sm-4 control-label">Select existing customer</label>
                  <div class="col-sm-1"></div>
                  <div class="col-sm-7">
                    <select name="existing_id" class="form-control">
                      <option value="null">Select a customer</option>
                      <?php foreach ($existing_rows as $row) : ?>
                        <option value="<?php echo $row['customer_id']; ?>"><?php echo $row['first_name'] . " " . $row['last_name']; ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
                <div class="or">OR</div>
                <div class="form-group">
                  <label for="first_name" class="col-sm-4 control-label">First Name</label>
                  <div class="col-sm-1"></div>
                  <div class="col-sm-7">
                    <input type="text" class="form-control" name="first_name" id="first_name" onchange="validate.input('#first_name', '#first_name_error')">
                    <div id="first_name_error" class="validate-error"></div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="last_name" class="col-sm-4 control-label">Last Name</label>
                  <div class="col-sm-1"></div>
                  <div class="col-sm-7">
                    <input type="text" class="form-control" name="last_name" id="last_name" onchange="validate.input('#last_name', '#last_name_error')">
                    <div id="last_name_error" class="validate-error"></div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="address" class="col-sm-4 control-label">Address</label>
                  <div class="col-sm-1"></div>
                  <div class="col-sm-7">
                    <input type="text" class="form-control" name="address" id="address" onchange="validate.input('#address', '#address_error')">
                    <div id="address_error" class="validate-error"></div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="postcode" class="col-sm-4 control-label">Postcode</label>
                  <div class="col-sm-1"></div>
                  <div class="col-sm-7">
                    <input type="text" class="form-control" name="postcode" id="postcode" onchange="validate.postcode()">
                    <div id="postcode_error" class="validate-error"></div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="phone" class="col-sm-4 control-label">Phone Number</label>
                  <div class="col-sm-1"></div>
                  <div class="col-sm-7">
                    <input type="text" class="form-control" name="phone" id="phone" onchange="validate.phone()">
                    <div id="phone_error" class="validate-error"></div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="email" class="col-sm-4 control-label">Email</label>
                  <div class="col-sm-1"></div>
                  <div class="col-sm-7">
                    <input type="text" class="form-control" name="email" id="email" onchange="validate.email()">
                    <div id="email-error" class="validate-error"></div>
                  </div>
                </div>
              </div>
              <div class="col-md-2">
                <button type="button" id="theCustomerNext" class="btn btn-primary pull-right">
                  Next   <span class="glyphicon glyphicon-arrow-right"></span>
                </button>
              </div>
            </div>
          </div>
        </div>
        <div class="panel panel-default">
          <div class="panel-heading">
            <h4 class="panel-title">
              The Cake
            </h4>
          </div>
          <div id="theCake" class="panel-collapse collapse">
            <div class="panel-body">
              <div class="col-md-2">
                <button type="button" id="theCakePrevious" class="btn btn-primary">
                  <span class="glyphicon glyphicon-arrow-left"></span>   Previous
                </button>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <label class="col-sm-4 control-label">Date/time order was placed</label>
                  <div class="col-sm-1"></div>
                  <div class="col-sm-2">
                    <select name="placed_year" class="form-control">
                      <option>Year</option>
                      <option value="<?php echo date("Y") - 1; ?>"><?php echo date("Y") - 1; ?></option>
                      <option value="<?php echo date("Y"); ?>"><?php echo date("Y"); ?></option>
                    </select>
                  </div>
                  <div class="col-sm-3">
                    <select name="placed_month" class="form-control">
                      <option>Month</option>
                      <?php
                        $months = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                        for ($i = 00, $j = 1; $i < 12; $i++, $j++)
                        {
                          echo '<option value="' . sprintf("%02s", $j) . '">' . $months[$i] . '</option>';
                        }
                      ?>
                    </select>
                  </div>
                  <div class="col-sm-2">
                    <select name="placed_day" class="form-control">
                      <option>Day</option>
                      <?php
                        for ($i = 1; $i < 32; $i++)
                        {
                          echo '<option value="' . sprintf("%02s", $i) . '">' . sprintf("%02s", $i) . '</option>';
                        }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="form-group" id="datetime_time">
                  <label class="col-sm-4 control-label"></label>
                  <div class="col-sm-1"></div>
                  <div class="col-sm-3">
                    <select name="placed_hour" class="form-control">
                      <option>Hour</option>
                      <?php
                        for ($i = 0; $i < 24; $i++)
                        {
                          echo '<option value="' . sprintf("%02s", $i) . '">' . sprintf("%02s", $i) . '</option>';
                        }
                      ?>
                    </select>
                    <div id="placed_error" class="validate-error"></div>
                  </div>
                  <div class="col-sm-1"></div>
                  <div class="col-sm-3">
                    <select name="placed_minute" class="form-control">
                      <option>Minute</option>
                      <?php
                        for ($i = 0; $i < 60; $i++)
                        {
                          echo '<option value="' . sprintf("%02s", $i) . '">' . sprintf("%02s", $i) . '</option>';
                        }
                      ?>
                    </select>
                  </div>
                </div>
                <div id="order_placed_error" class="validate-error"></div>
                <div class="form-group">
                  <label for="filling" class="col-sm-4 control-label">Filling</label>
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
                  <label for="decoration" class="col-sm-4 control-label">Decoration</label>
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
                  <label for="cake_size" class="col-sm-4 control-label">Size of Cake</label>
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
                  <label for="cake_type" class="col-sm-4 control-label">Type of Cake</label>
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
                <div class="form-group" id="date">
                  <label for="celebration_date" class="col-sm-4 control-label">Date of celebration</label>
                  <div class="col-sm-1"></div>
                  <div class="col-sm-2">
                    <select name="date_year" class="form-control">
                      <option>Year</option>
                      <option value="<?php echo date("Y"); ?>"><?php echo date("Y"); ?></option>
                      <option value="<?php echo date("Y") + 1; ?>"><?php echo date("Y") + 1; ?></option>
                    </select>
                  </div>
                  <div class="col-sm-3">
                    <select name="date_month" class="form-control">
                      <option>Month</option>
                      <?php
                        $months = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                        for ($i = 00, $j = 1; $i < 12; $i++, $j++)
                        {
                          echo '<option value="' . sprintf("%02s", $j) . '">' . $months[$i] . '</option>';
                        }
                      ?>
                    </select>
                    <div id="date_error" class="validate-error"></div>
                  </div>
                  <div class="col-sm-2">
                    <select name="date_day" class="form-control">
                      <option>Day</option>
                      <?php
                        for ($i = 1; $i < 32; $i++)
                        {
                          echo '<option value="' . sprintf("%02s", $i) . '">' . sprintf("%02s", $i) . '</option>';
                        }
                      ?>
                    </select>
                  </div>
                </div>
                <div id="celebration_date_error" class="validate-error"></div>
                <div id="comments" class="form-group">
                  <label for="comments" class="col-sm-4 control-label">Comments</label>
                  <div class="col-sm-1"></div>
                  <div class="col-sm-7">
                    <textarea id="comments" cols="30" rows="6" name="comments" class="form-control" onchange="validate.input('textarea#comments', '#comments_error')"></textarea>
                  </div>
                </div>
                <div id="comments_error" class="validate-error"></div>
              </div>
              <div class="col-md-2">
                <button type="button" id="theCakeNext" class="btn btn-primary pull-right">
                  Next   <span class="glyphicon glyphicon-arrow-right"></span>
                </button>
              </div>
            </div>
          </div>
        </div>
        <div class="panel panel-default">
          <div class="panel-heading">
            <h4 class="panel-title">
              Delivery
            </h4>
          </div>
          <div id="delivery" class="panel-collapse collapse">
            <div class="panel-body">
              <div class="col-md-2">
                <button type="button" id="deliveryPrevious" class="btn btn-primary">
                  <span class="glyphicon glyphicon-arrow-left"></span>   Previous
                </button>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <label for="delivery" class="col-sm-4 control-label">Delivery options</label>
                  <div class="col-sm-1"></div>
                  <div class="col-sm-7">
                    <select name="delivery" class="form-control">
                      <option value="Collection">Collection</option>
                      <option value="Deliver To Address">Delivery</option>
                    </select>
                  </div>
                </div>
                <div class="form-group" id="datetime_date">
                  <label for="datetime" id="datetime-label" class="col-sm-4 control-label">Date/time for collection</label>
                  <div class="col-sm-1"></div>
                  <div class="col-sm-2">
                    <select name="datetime_year" class="form-control">
                      <option>Year</option>
                      <option value="<?php echo date("Y"); ?>"><?php echo date("Y"); ?></option>
                      <option value="<?php echo date("Y") + 1; ?>"><?php echo date("Y") + 1; ?></option>
                    </select>
                  </div>
                  <div class="col-sm-3">
                    <select name="datetime_month" class="form-control">
                      <option>Month</option>
                      <?php
                        $months = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                        for ($i = 00, $j = 1; $i < 12; $i++, $j++)
                        {
                          echo '<option value="' . sprintf("%02s", $j) . '">' . $months[$i] . '</option>';
                        }
                      ?>
                    </select>
                  </div>
                  <div class="col-sm-2">
                    <select name="datetime_day" class="form-control">
                      <option>Day</option>
                      <?php
                        for ($i = 1; $i < 32; $i++)
                        {
                          echo '<option value="' . sprintf("%02s", $i) . '">' . sprintf("%02s", $i) . '</option>';
                        }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="form-group" id="datetime_time">
                  <label class="col-sm-4 control-label"></label>
                  <div class="col-sm-1"></div>
                  <div class="col-sm-3">
                    <select name="datetime_hour" class="form-control">
                      <option>Hour</option>
                      <?php
                        for ($i = 9; $i < 18; $i++)
                        {
                          echo '<option value="' . sprintf("%02s", $i) . '">' . sprintf("%02s", $i) . '</option>';
                        }
                      ?>
                    </select>
                    <div id="datetime_error" class="validate-error"></div>
                  </div>
                  <div class="col-sm-1"></div>
                  <div class="col-sm-3">
                    <select name="datetime_minute" class="form-control">
                      <option>Minute</option>
                      <?php
                        for ($i = 0; $i < 60; $i++)
                        {
                          echo '<option value="' . sprintf("%02s", $i) . '">' . sprintf("%02s", $i) . '</option>';
                        }
                      ?>
                    </select>
                  </div>
                </div>
                <div id="datetime_error" class="validate-error"></div>
              </div>
              <div class="col-md-2">
                <button type="button" id="deliveryNext" class="btn btn-primary pull-right">
                  Next   <span class="glyphicon glyphicon-arrow-right"></span>
                </button>
              </div>
            </div>
          </div>
        </div>
        <div class="panel panel-default">
          <div class="panel-heading">
            <h4 class="panel-title">
              Review
            </h4>
          </div>
          <div id="review" class="panel-collapse collapse">
            <div class="panel-body">
              <div class="col-md-5">
                <script>
                  var $origins,
                      $destination;
                </script>
                <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCKeZpb8doUO3DbEqT3t-uRJYsbEPbD3AE&sensor=false"></script>
                <table class="table">
                  <caption>The Order</caption>
                  <tr>
                    <th>Date of celebration</th>
                    <td><span id="celebration-date-review"></span></td>
                  </tr>
                  <tr>
                    <th>Filling</th>
                    <td><span id="filling-review">None</span></td>
                  </tr>
                  <tr>
                    <th>Decoration</th>
                    <td><span id="decoration-review">None</span></td>
                  </tr>
                  <tr>
                    <th>Size of cake</th>
                    <td><span id="cake-size-review">6"</span></td>
                  </tr>
                  <tr>
                    <th>Type of cake</th>
                    <td><span id="cake-type-review">Sponge</span></td>
                  </tr>
                  <tr>
                    <th>Comments</th>
                    <td><span id="comments-review"></span></td>
                  </tr>
                  <tr>
                    <th>Delivery type</th>
                    <td><span id="delivery-review">Collection</span></td>
                  </tr>
                  <tr>
                    <th><span id="datetime-label-review">Date/time for collection:</span></th>
                    <td><span id="datetime-review"></span></td>
                  </tr>
                </table>
              </div>
              <div class="col-md-4">
                <table class="table">
                  <caption>The Customer</caption>
                  <tr>
                    <th>Name</th>
                    <td><span id="name-review"></span></td>
                  </tr>
                  <tr>
                    <th>Address</th>
                    <td><span id="address-review"></span></td>
                  </tr>
                  <tr>
                    <th>Postcode</th>
                    <td><span id="postcode-review"></span></td>
                  </tr>
                  <tr>
                    <th>Phone Number</th>
                    <td><span id="phone-review"></span></td>
                  </tr>
                  <tr>
                    <th>Email</th>
                    <td><span id="email-review"></span></td>
                  </tr>
                </table>
                <span id="delivery-charge"><b>Delivery: <div id="delivery-charge-html"></div></b></span>
                <br />
                <b>Base Price: &pound;<div id="base-price"></div></b>
                <br />
                <b>Grand Total: &pound;<div id="total-html"></div></b>
                <br /><br />
                <input type="hidden" value="<?php echo $_SESSION['token']; ?>" name="token">
                <button type="submit" class="btn btn-default">Add Order</button>
              </div>
              <div class="col-md-3">
                Something wrong? Want to make any changes?
                <button type="button" id="reviewPrevious" class="btn btn-primary">
                  <span class="glyphicon glyphicon-arrow-left"></span>   Go back
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
<?php include("../lib/footer.php"); ?>
