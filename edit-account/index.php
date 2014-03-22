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

  // Use HTTPS since secure data is being transferred
  forceHTTPS();

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
<div class="row">
  <div class="col-md-12">
    <h1>Edit Account</h1>
  </div>
</div>
<div class="row">
  <div class="col-md-3"></div>
  <div class="col-md-6">
    <div class="alert alert-success" id="success_message" <?php if (!empty($_GET) and !empty($_GET['update'])) : ?>style="display:block;"<?php endif; ?>>
      <span class="glyphicon glyphicon-ok-circle"></span>
      <?php if (!empty($_GET) and !empty($_GET['update'])) {echo $display_message;} ?>
    </div>
    <div class="alert alert-warning" <?php if (!empty($_GET) and !empty($_GET['first'])) : ?>style="display:block;"<?php endif; ?>>
      <span class="glyphicon glyphicon-warning-sign"></span>
      <?php if (!empty($_GET) and !empty($_GET['first'])) {echo $display_message;} ?>
    </div>
    <div class="alert alert-danger" id="error_message"<?php if (!empty($_GET) and !empty($_GET['e'])) : ?>style="display:block;"<?php endif; ?>>
      <span class="glyphicon glyphicon-remove-circle"></span>
      <?php if (!empty($_GET) and !empty($_GET['e'])) {echo $display_message;} ?>
    </div>
    <form action="index.php" method="POST" id="edit-account-form" class="form-horizontal" role="form">
      <div class="form-group">
        <label class="col-sm-3 control-label">Username</label>
        <div class="col-sm-9">
          <b class="form-control-static pull-right"><?php echo htmlentities($_SESSION['user']['username'], ENT_QUOTES, 'UTF-8'); ?></b>
        </div>
      </div>
      <div class="form-group">
        <label for="first_name" class="col-sm-3 control-label">First Name</label>
        <div class="col-sm-2"></div>
        <div class="col-sm-7">
          <input type="text" class="form-control" name="first_name" id="first_name" value="<?php echo htmlentities($_SESSION['user']['first_name'], ENT_QUOTES, 'UTF-8'); ?>" onchange="validate.input('#first_name', '#first_name_error')"> 
        </div>
      </div>
      <div id="first_name_error" class="validate-error"></div>
      <div class="form-group">
        <label for="last_name" class="col-sm-3 control-label">Last Name</label>
        <div class="col-sm-2"></div>
        <div class="col-sm-7">
          <input type="text" class="form-control" name="last_name" id="last_name" value="<?php echo htmlentities($_SESSION['user']['last_name'], ENT_QUOTES, 'UTF-8'); ?>" onchange="validate.input('#last_name', '#last_name_error')"> 
        </div>
      </div>
      <div id="last_name_error" class="validate-error"></div>
      <div class="form-group">
        <label for="email" class="col-sm-3 control-label">E-Mail Address</label>
        <div class="col-sm-2"></div>
        <div class="col-sm-7">
          <input type="text" class="form-control" name="email" id="email" value="<?php echo htmlentities($_SESSION['user']['email'], ENT_QUOTES, 'UTF-8'); ?>" onchange="validate.email()"> 
        </div>
      </div>
      <div id="email-error" class="validate-error"></div>
      <div class="form-group">
        <label for="password" class="col-sm-3 control-label">Password</label>
        <div class="col-sm-2"></div>
        <div class="col-sm-7">
          <input type="password" class="form-control" name="password" id="password" value="" /> 
          <small><i>(leave blank if you do not want to change your password)</i></small>
        </div>
      </div>
      <div class="form-group">
        <label for="address" class="col-sm-3 control-label">Address</label>
        <div class="col-sm-2"></div>
        <div class="col-sm-7">
          <input type="text" class="form-control" name="address" id="address" value="<?php echo htmlentities($_SESSION['user']['address'], ENT_QUOTES, 'UTF-8'); ?>" onchange="validate.input('#address', '#address_error')">
        </div>
      </div>
      <div id="address_error" class="validate-error"></div>
      <div class="form-group">
        <label for="postcode" class="col-sm-3 control-label">Postcode</label>
        <div class="col-sm-2"></div>
        <div class="col-sm-7">
          <input type="text" class="form-control" name="postcode" id="postcode" value="<?php echo htmlentities($_SESSION['user']['postcode'], ENT_QUOTES, 'UTF-8'); ?>" onchange="validate.postcode()">
        </div>
      </div>
      <div id="postcode_error" class="validate-error"></div>
      <div class="form-group">
        <label for="phone" class="col-sm-3 control-label">Phone number</label>
        <div class="col-sm-2"></div>
        <div class="col-sm-7">
          <input type="text" class="form-control" name="phone" id="phone" value="<?php echo $_SESSION['user']['phone'] ?>" onchange="validate.phone()">
        </div>
      </div>
      <div id="phone_error" class="validate-error"></div>
      <input type="hidden" value="<?php echo $_SESSION['token']; ?>" name="token">
      <button type="submit" class="btn btn-default">Update Account</button>
    </form>
  </div>
  <div class="col-md-3"></div>
</div>
<?php include("../lib/footer.php"); ?>
