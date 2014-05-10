<?php
  /**
   edit-account/ - let the user edit account details
   i.e. name, address, postcode, phone number, email
   address, password
  **/
  require("../lib/common.php");
  $title = "Edit Account";
  $page = "edit-account";

  // Only logged in users can access this page
  if (empty($_SESSION['user']))
  {
    header("Location: ../login");
    die();
  }

  // Generate token
  $_SESSION['token'] = rtrim(base64_encode(md5(microtime())),"=");

  // Display messages
  if (!empty($_GET['update']))
  {
    if ($_GET['update'] === "success")
    {
      $display_message = "Account updated.";
    }
  }
  else if (!empty($_GET['e']))
  {
    if ($_GET['e'] === "email")
    {
      $display_message = "That email address is already in use.";
    }
  }
  else if (!empty($_GET['first']))
  {
    $display_message = "Please update your account details.";
  }

  // Get customer details to be displayed
  // in the HTML
  $query = "
    SELECT
      *
    FROM
      users
    WHERE
      customer_id = :id
  ";

  $query_params = array(
    ':id' => $_SESSION['user']['customer_id']
  );

  $db->runQuery($query, $query_params);

  $row = $db->fetch();
?>
<?php include("../lib/header.php"); ?>
<div class="modal fade" id="address-modal" role="modal" aria-hidden="true" aria-labelledby="myModalLabel">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Invalid Address!</h4>
      </div>
      <div class="modal-body"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary pull-right" data-dismiss="modal">Okay</button>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-12">
    <h1>Edit Account</h1>
  </div>
</div>
<div class="row">
  <div class="col-md-3 account-header">
    <span class="glyphicon glyphicon-user account-glyphicon"></span>
    <p>You</p>
  </div>
  <div class="col-md-6 well account-well">
    <h3 class="account-heading">Your personal details</h3>
    <p>Your first and last name are used to personalise your experience on the site, and for your orders. Usernames cannot be changed.</p>
    <hr class="fancy-line hidden-xs">
    <form id="edit-account-you-form" class="form" role="form">
      <div class="form-group">
        <label class="control-label">Username</label>
        <div class="row">
          <div class="col-md-7">
            <input type="text" class="form-control" value="<?php echo htmlentities($_SESSION['user']['username'], ENT_QUOTES, 'UTF-8'); ?>" disabled>
          </div>
        </div>
      </div>
      <div class="form-group">
        <label for="first_name" class="control-label">First Name</label>
        <div class="row">
          <div class="col-md-7">
            <input type="text" class="form-control" name="first_name" id="first_name" value="<?php echo htmlentities($_SESSION['user']['first_name'], ENT_QUOTES, 'UTF-8'); ?>" onchange="validate.input('#first_name', '#first_name_error', 'Please enter your first name')"> 
            <div id="first_name_error" class="validate-error"></div>
          </div>
        </div>
      </div>
      <div class="form-group">
        <label for="last_name" class="control-label">Last Name</label>
        <div class="row">
          <div class="col-md-7">
            <input type="text" class="form-control" name="last_name" id="last_name" value="<?php echo htmlentities($_SESSION['user']['last_name'], ENT_QUOTES, 'UTF-8'); ?>" onchange="validate.input('#last_name', '#last_name_error', 'Please enter your last name')"> 
            <div id="last_name_error" class="validate-error"></div>
          </div>
        </div>
      </div>
      <input type="hidden" value="<?php echo $_SESSION['token']; ?>" name="token">
      <input type="hidden" value="<?php echo $_SESSION['user']['customer_id']; ?>" name="id">
      <input type="hidden" value="personal" name="type">
      <button type="submit" class="btn btn-primary">Update Personal Details</button>
    </form>
  </div>
  <div class="col-md-3"></div>
</div>
<div class="row">
  <div class="col-md-3 account-header">
    <span class="glyphicon glyphicon-envelope account-glyphicon"></span>
    <p>Your Email</p>
  </div>
  <div class="col-md-6 well account-well">
    <h3 class="account-heading">Change your email address</h3>
    <p>
      Your current email address is <b><?php echo htmlentities($_SESSION['user']['email'], ENT_QUOTES, 'UTF-8'); ?></b>.
      We use your email address to send you updates about your order, therefore it is important that it is correct.
      You will need to reverify your email if you change it.
    </p>
    <hr class="fancy-line hidden-xs">
    <form id="edit-account-email-form" class="form" role="form">
      <div class="form-group">
        <label for="email" class="control-label">E-Mail Address</label>
        <div class="row">
          <div class="col-md-6">
            <input type="email" class="form-control" name="email" id="email" placeholder="Enter new email address" onchange="validate.email()"> 
            <div id="email-error" class="validate-error"></div>
          </div>
          <div class="col-md-7"></div>
        </div>
      </div>
      <input type="hidden" value="<?php echo $_SESSION['token']; ?>" name="token">
      <input type="hidden" value="<?php echo $_SESSION['user']['customer_id']; ?>" name="id">
      <input type="hidden" value="email" name="type">
      <button type="submit" class="btn btn-primary">Update Email Address</button>
    </form>
  </div>
  <div class="col-md-3"></div>
</div>
<div class="row">
  <div class="col-md-3 account-header">
    <span class="glyphicon glyphicon-cog account-glyphicon"></span>
    <p>Your Password</p>
  </div>
  <div class="col-md-6 well account-well">
    <h3 class="account-heading">Change your password</h3>
    <p>Passwords must be minimum 5 characters.</p>
    <hr class="fancy-line hidden-xs">
    <form id="edit-account-password-form" class="form" role="form">
      <div class="form-group">
        <label for="password" class="control-label">Password</label>
        <div class="row">
          <div class="col-md-7">
            <input type="password" class="form-control" name="password" id="password" placeholder="Enter new password" onchange="validate.password()">
            <div id="password-error" class="validate-error"></div>
          </div>
        </div>
      </div>
      <input type="hidden" value="<?php echo $_SESSION['token']; ?>" name="token">
      <input type="hidden" value="<?php echo $_SESSION['user']['customer_id']; ?>" name="id">
      <input type="hidden" value="password" name="type">
      <button type="submit" class="btn btn-primary">Update Password</button>
    </form>
  </div>
</div>
<div class="row">
  <div class="col-md-3 account-header">
    <span class="glyphicon glyphicon-home account-glyphicon"></span>
    <p>Your Address</p>
  </div>
  <div class="col-md-6 well account-well">
    <h3 class="account-heading">Change your address</h3>
    <p>
      Your current address is <b id="current-address"><?php echo htmlentities($_SESSION['user']['address'], ENT_QUOTES, 'UTF-8') . ", " . htmlentities($_SESSION['user']['postcode'], ENT_QUOTES, 'UTF-8'); ?></b>.
      We need a correct address in order for your order to be delivered to the right place.
    </p>
    <hr class="fancy-line hidden-xs">
    <form id="edit-account-address-form" class="form" role="form">
      <div class="form-group">
        <label for="address" class="control-label">Address</label>
        <div class="row">
          <div class="col-md-7">
            <input type="text" class="form-control" name="address" id="address" value="<?php echo htmlentities($_SESSION['user']['address'], ENT_QUOTES, 'UTF-8'); ?>" onchange="validate.input('#address', '#address_error', 'Please enter your address')">
            <div id="address_error" class="validate-error"></div>
          </div>
        </div>
      </div>
      <div class="form-group">
        <label for="postcode" class="control-label">Postcode</label>
        <div class="row">
          <div class="col-md-7">
            <input type="text" class="form-control" name="postcode" id="postcode" value="<?php echo htmlentities($_SESSION['user']['postcode'], ENT_QUOTES, 'UTF-8'); ?>" onchange="validate.postcode()">
            <div id="postcode_error" class="validate-error"></div>
          </div>
        </div>
      </div>
      <input type="hidden" value="<?php echo $_SESSION['token']; ?>" name="token">
      <input type="hidden" value="<?php echo $_SESSION['user']['customer_id']; ?>" name="id">
      <input type="hidden" value="address" name="type">
      <button type="submit" class="btn btn-primary">Update Address</button>
    </form>
  </div>
</div>
<div class="row">
  <div class="col-md-3 account-header">
    <span class="glyphicon glyphicon-earphone account-glyphicon"></span>
    <p>Your Phone Number</p>
  </div>
  <div class="col-md-6 well account-well">
    <h3 class="account-heading">Change your phone number</h3>
    <p>
      Your current phone number is <b id="current-phone"><?php echo htmlentities($_SESSION['user']['phone'], ENT_QUOTES, 'UTF-8'); ?></b>.
      We need your phone number in case we need to contact you about your order.
    </p>
    <hr class="fancy-line hidden-xs">
    <form id="edit-account-phone-form" class="form" role="form">
      <div class="form-group">
        <label for="phone" class="control-label">Phone number</label>
        <div class="row">
          <div class="col-sm-7">
            <input type="tel" class="form-control" name="phone" id="phone" placeholder="Enter new phone number" onchange="validate.phone()">
            <div id="phone_error" class="validate-error"></div>
          </div>
        </div>
      </div>
      <input type="hidden" value="<?php echo $_SESSION['token']; ?>" name="token">
      <input type="hidden" value="<?php echo $_SESSION['user']['customer_id']; ?>" name="id">
      <input type="hidden" value="phone" name="type">
      <button type="submit" class="btn btn-primary">Update Phone Number</button>
    </form>
  </div>
  <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCKeZpb8doUO3DbEqT3t-uRJYsbEPbD3AE&sensor=false"></script>
</div>
<?php include("../lib/footer.php"); ?>
