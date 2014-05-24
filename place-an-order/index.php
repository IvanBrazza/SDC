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

  // Generate a token
  $_SESSION['token'] = rtrim(base64_encode(md5(microtime())),"=");

  // Get user details to make sure they have been entered
  $query = "
    SELECT
      address, postcode, phone, first_name, last_name
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

  // Don't let the users place an order until their details are entered
  if (empty($row['address']) or empty($row['postcode']) or empty($row['phone']) or empty($row['first_name']) or empty($row['last_name']))
  {
    $display_message = 'Please <a href="//www.<?php echo $siteUrl; ?>/edit-account">update your details</a> before placing an order.';
    $details_correct = false;
  }
  else
  {
    $details_correct = true;
  }
?>
<?php include("../lib/header.php"); ?>
<div class="row">
  <div class="col-md-12">
    <h1>Place An Order</h1>
  </div>
</div>
<div class="row">
  <div class="col-md-12">
    <div class="alert alert-danger" id="error_message" style="max-height: 34px;padding-top: 6px;text-align: center;<?php if (!$details_correct) : ?>display:block;<?php endif; ?>">
      <?php if (!$details_correct) : ?>
        <i class="fa fa-warning"></i>
        <strong>Warning!</strong>
        <?php echo $display_message; ?>
        <script>
          var $details_correct = false;
        </script>
      <?php else : ?>
        <script>
          var $details_correct = true;
        </script>
      <?php endif; ?>
    </div>
    <form action="../lib/form/place-an-order.php" method="POST" class="form-horizontal" id="order-form" enctype="multipart/form-data" role="form">
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
                    <input name="celebration_date" class="form-control datepicker" placeholder="--Select A Date--">
                    <div id="celebration_date_error" class="validate-error"></div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="filling" class="col-sm-4 control-label">Filling <a href="javascript:" class="help" title="The filling you want your cake to have. If you choose 'Other' please specify the filling in the comments box.">?</a></label>
                  <div class="col-sm-1"></div>
                  <div class="col-sm-7">
                    <select name="filling" id="filling" class="form-control" onchange="validate.input('select[name=filling]', '#filling_error', 'Please choose a filling')">
                      <option value="null">--Select A Filling--</option>
                      <?php foreach ($fillings as $filling) : ?>
                        <option value="<?php echo $filling['filling_id']; ?>"><?php echo $filling['filling_name']; ?></option>
                      <?php endforeach; ?>
                    </select>
                    <div id="filling_error" class="validate-error"></div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="decoration" class="col-sm-4 control-label">Decoration <a href="javascript:" class="help" title="What you want your cake to be decorated in. If you choose 'Other' please specify the decoration in the comments box.">?</a></label>
                  <div class="col-sm-1"></div>
                  <div class="col-sm-7">
                    <select name="decoration" id="decoration" class="form-control" onchange="validate.input('select[name=decoration]', '#decoration_error', 'Please choose a decoration')">
                      <option value="null">--Select A Decoration--</option>
                      <?php foreach ($decorations as $decoration) : ?>
                        <option value="<?php echo $decoration['decor_id']; ?>"><?php echo $decoration['decor_name']; ?></option>
                      <?php endforeach; ?>
                    </select>
                    <div id="decoration_error" class="validate-error"></div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="cake_size" class="col-sm-4 control-label">Size of cake <a href="javascript:" class="help" title="The size you want the cake to be in inches.">?</a></label>
                  <div class="col-sm-1"></div>
                  <div class="col-sm-7">
                    <select name="cake_size" id="cake_size" class="form-control" onchange="validate.input('select[name=cake_size]', '#cake_size_error', 'Please choose a cake size')">
                      <option value="null">--Select A Cake Size--</option>
                      <option value='6"'>6"</option>
                      <option value='8"'>8"</option>
                      <option value='10"'>10"</option>
                      <option value='12"'>12"</option>
                      <option value='14"'>14"</option>
                    </select>
                    <div id="cake_size_error" class="validate-error"></div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="cake_type" class="col-sm-4 control-label">Type of cake</label>
                  <div class="col-sm-1"></div>
                  <div class="col-sm-7">
                    <select name="cake_type" id="cake_type" class="form-control" onchange="validate.input('select[name=cake_type]', '#cake_type_error', 'Please choose a cake type')">
                      <option value="null">--Select A Cake Type--</option>
                      <option value="Sponge">Sponge</option>
                      <option value="Marble">Marble</option>
                      <option value="Chocolate">Chocolate</option>
                      <option value="Fruit">Fruit</option>
                    </select>
                    <div id="cake_type_error" class="validate-error"></div>
                  </div>
                </div>
                <div id="comments" class="form-group">
                  <label for="comments" class="col-sm-4 control-label">Comments <a href="javascript:" class="help" title="Any additional comments you may have to make or if you chose filling/decoration as 'Other'.">?</a></label>
                  <div class="col-sm-1"></div>
                  <div class="col-sm-7">
                    <textarea name="comments" id="comments" rows="6" cols="30" class="form-control" onchange="validate.input('textarea#comments', '#comments_error', 'Please enter a comment')"></textarea>
                    <div id="comments_error" class="validate-error"></div>
                  </div>
                </div>
              </div>
              <div class="col-md-2">
                <button type="button" id="theCakeNext" class="btn btn-primary pull-right">
                  <span>Next</span>
                  <i class="fa fa-arrow-down"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
        <div class="panel panel-default">
          <div class="panel-heading" id="upload-a-photo-heading" style="background-color:#fcf8e3;">
            <h4 class="panel-title">
              Upload A Photo
            </h4>
          </div>
          <div id="uploadAPhoto" class="panel-collapse collapse">
            <div class="panel-body">
              <div class="col-md-2">
                <button type="button" id="uploadAPhotoPrevious" class="btn btn-primary">
                  <i class="fa fa-arrow-up"></i>
                  <span>Previous</span>
                </button>
              </div>
              <div class="col-md-8">
                <div class="panel panel-info">
                  <div class="panel-heading">
                    <h3 class="panel-title">Please Read</h3>
                  </div>
                  <div class="panel-body">
                    If you wish for your cake to have a picture printed onto edible paper,
                    you can upload it by clicking the <span class="btn btn-success" id="upload-fake">
                    <i class="fa fa-upload"></i>
                    <span>Choose Image...</span></span> button below.<br>
                    The picture you choose must match the following criteria:
                    <ul>
                      <li>The maximum filesize is <b>5MB</b></li>
                      <li>Only image files (<b>.JPG, .JPEG, .PNG, .GIF)</b> may be uploaded</li>
                      <li>The image must be high quality</li>
                    </ul>
                    You may also drag and drop the image from your computer onto this page.
                  </div>
                </div>
                <br>
                <div id="fileupload">
                  <div class="row fileupload-buttonbar">
                    <div class="col-lg-7">
                      <span class="btn btn-success fileinput-button">
                        <i class="fa fa-upload"></i>
                        <span>Choose Image...</span>
                        <input type="file" name="files[]" accept="image/*" id="fileid">
                        <input type="hidden" name="upload_dir" value="gallery/<?php echo $_SESSION['user']['customer_id']; ?>">
                      </span>
                      <span class="fileupload-process"></span>
                    </div>
                  </div>
                  <span id="uploadstatus"></span>
                  <div class="well well-sm">
                    <table role="presentation" class="uploaded-images table">
                      <tbody class="files"></tbody>
                    </table>
                  </div>
                </div>
                <!-- The template to display files available for upload -->
                <script id="template-upload" type="text/x-tmpl">
                {% for (var i=0, file; file=o.files[i]; i++) { %}
                  <tr class="template-upload fade">
                    <td>
                      <span class="preview"></span>
                    </td>
                    <td>
                      <p class="name">{%=file.name%}</p>
                      <strong class="error text-danger"></strong>
                    </td>
                    <td>
                      <p class="size">Processing...</p>
                      <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
                    </td>
                    <td>
                      {% if (!i) { %}
                        <button class="btn btn-warning cancel pull-right">
                          <i class="fa fa-ban"></i>
                          <span>Cancel</span>
                        </button>
                      {% } %}
                    </td>
                  </tr>
                {% } %}
                </script>
                <!-- The template to display files available for download -->
                <script id="template-download" type="text/x-tmpl">
                {% for (var i=0, file; file=o.files[i]; i++) { %}
                  <tr class="template-download fade">
                    <td>
                      <span class="preview">
                        <img src="https://s3.amazonaws.com/SDC-images/gallery/<?php echo $_SESSION['user']['customer_id']; ?>/{%=file.name%}" width="100px">
                      </span>
                    </td>
                    <td>
                      <p class="name" id="filename">
                        <span>{%=file.name%}</span>
                      </p>
                      {% if (file.error) { %}
                        <div><span class="label label-danger">Error</span> {%=file.error%}</div>
                      {% } %}
                    </td>
                    <td>
                      <span class="size">{%=o.formatFileSize(file.size)%}</span>
                    </td>
                    {% if (file.error) { %}
                      <td>
                        <button class="btn btn-warning cancel pull-right">
                          <i class="fa fa-ban"></i>
                          <span>Cancel</span>
                        </button>
                      </td>
                    {% } %}
                  </tr>
                {% } %}
                </script>
                <input type="hidden" name="fileupload" id="fileuploadhidden">
              </div>
              <div class="col-md-2">
                <button type="button" id="uploadAPhotoNext" class="btn btn-primary pull-right">
                  <span>Next</span>
                  <i class="fa fa-arrow-down"></i>
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
                  <i class="fa fa-arrow-up"></i>
                  <span>Previous</span>
                </button>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <label for="delivery" class="col-sm-4 control-label">Delivery options</label>
                  <div class="col-sm-1"></div>
                  <div class="col-sm-7">
                    <select name="delivery" id="delivery" class="form-control" onchange="validate.input('select[name=delivery]', '#delivery_error', 'Please choose a delivery option')">
                      <option value="null">--Select A Delivery Option--</option>
                      <option value="Collection">Collection</option>
                      <option value="Deliver To Address">Delivery</option>
                    </select>
                    <div id="delivery_error" class="validate-error"></div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="datetime" id="datetime-label" class="col-sm-4 control-label">Date/time for collection</label>
                  <div class="col-sm-1"></div>
                  <div class="col-sm-7">
                    <input name="datetime_date" class="form-control datepicker" placeholder="Date">
                    <div id="datetime_date_error" class="validate-error"></div>
                    <input name="datetime_time" class="form-control timepicker" placeholder="Time">
                    <div id="datetime_time_error" class="validate-error"></div>
                  </div>
                </div>
              </div>
              <div class="col-md-2">
                <button type="button" id="deliveryNext" class="btn btn-primary pull-right">
                  <span>Next</span>
                  <i class="fa fa-arrow-down"></i>
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
                </table>
                <input type="hidden" value="<?php echo $_SESSION['token']; ?>" name="token">
              </div>
              <div class="col-md-4">
                <button type="button" id="reviewPrevious" class="btn btn-primary pull-left">
                  <i class="fa fa-arrow-up"></i>
                  <span>Go back</span>
                </button>
                <input type="image" class="pull-right" src="//www.<?php echo $siteUrl; ?>/img/paywithpp.gif" <?php if ($details_correct === false) : ?>disabled<?php endif; ?>>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
<?php include("../lib/footer.php");
